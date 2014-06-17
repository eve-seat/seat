@extends('layouts.masterLayout')

@section('html_title', 'View Character')

@section('page_content')

{{-- open a empty form to get a crsf token --}}
{{ Form::open(array()) }} {{ Form::close() }}

{{-- character information --}}
<div class="row">
	<div class="col-md-4">
		<div class="box box-primary">
		    <div class="box-body">
		    	<p class="text-center">
			    	<img src='//image.eveonline.com/Character/{{ $character->characterID }}_256.jpg' class='img-circle'>
			    </p>
			    <p class="text-center lead">{{ $character->characterName }}</p>
		    </div><!-- /.box-body -->

		</div><!-- ./box -->
	</div>
	<div class="col-md-4">
		<div class="box box-primary">
		    <div class="box-body">
            	@if (count($other_characters) > 0)
	                @foreach ($other_characters as $alt)
		                <div class="row">
		               		<a href="{{ action('CharacterController@getView', array('characterID' => $alt->characterID )) }}" style="color:inherit;">
		                		<div class="col-md-2">
							<img src="//image.eveonline.com/Character/{{ $alt->characterID }}_64.jpg" class="img-circle">
						</div>
						<div class="col-md-5">
							<ul class="list-unstyled">
								<li><b>Name: </b>{{ $alt->characterName }}</li>
								<li><b>Corp: </b>{{ $alt->corporationName }}</li>
								<li>
									@if (strlen($alt->trainingEndTime) > 0)
										<b>Training Ends: </b> {{ Carbon\Carbon::parse($alt->trainingEndTime)->diffForHumans() }}
									@endif
								</li>
							</ul>
						</div>
						</a>
						</div><!-- ./row -->
	                @endforeach
	            @else
	            	No other known characters on this key.
	           	@endif
		    </div><!-- /.box-body -->
		    <div class="box-footer">
				<p class="text-center lead">{{ count($other_characters) }} other characters on this API Key</p>
		    </div><!-- /.box-footer-->
		</div><!-- ./box -->
	</div>
	<div class="col-md-4">
		<div class="box box-primary">
		    <div class="box-body">
            	@if (count($people) > 0)
            		<div class="row">
                		@foreach (array_chunk($people, (count($people) / 2) > 1 ? count($people) / 2 : 2) as $other_alts)
                			<div class="col-md-6">
	                    		<ul class="list-unstyled">
					                @foreach ($other_alts as $person)
					                	<li>
											<a href="{{ action('CharacterController@getView', array('characterID' => $person->characterID)) }}">
												<img src='//image.eveonline.com/Character/{{ $person->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
												{{ $person->characterName }}
											</a>				                	
										</li>
					                @endforeach
					            </ul>
					        </div>
				        @endforeach
				    </div> <!-- ./row -->
	            @else
	            	No other characters in a person group
	           	@endif
		    </div><!-- /.box-body -->
		    <div class="box-footer">
				<p class="text-center lead">{{ count($people) }} other characters on this people group</p>
		    </div><!-- /.box-footer-->
		</div><!-- ./box -->
	</div>
</div>

{{-- details such as character sheet, skills, mail, wallet etc etc here --}}
<div class="row">
	<div class="col-md-12">
	    <!-- Custom Tabs -->
	    <div class="nav-tabs-custom">
	        <ul class="nav nav-tabs">
	            <li class="active"><a href="#character_sheet" data-toggle="tab">Character Sheet</a></li>
	            <li><a href="#character_skills" data-toggle="tab">Character Skills</a></li>
	            <li id="journal_graph"><a href="#wallet_journal" data-toggle="tab">Wallet Journal</a></li>
	            <li><a href="#wallet_transactions" data-toggle="tab">Wallet Transactions</a></li>
	            <li><a href="#mail" data-toggle="tab">Mail</a></li>
	            <li><a href="#notifications" data-toggle="tab">Notifications</a></li>
	            <li><a href="#assets" data-toggle="tab">Assets</a></li>
	            <li><a href="#contacts" data-toggle="tab">Contacts</a></li>
	            <li><a href="#contracts" data-toggle="tab">Contracts</a></li>
	            <li><a href="#market_orders" data-toggle="tab">Market Orders</a></li>
	            <li><a href="#calendar_events" data-toggle="tab">Calendar Events</a></li>
	            <li><a href="#standings" data-toggle="tab">Standings</a></li>
	            <li class="pull-right">
	            	<a href="{{ action('ApiKeyController@getDetail', array('keyID' => $character->keyID)) }}" class="text-muted" data-toggle="tooltip" title="" data-placement="top" data-original-title="API Key Details">
	            		<i class="fa fa-gear"></i>
	            	</a>
	            </li>
	        </ul>
	        <div class="tab-content">
				@include('character.view.character_sheet')
				@include('character.view.character_skills')
				@include('character.view.wallet_journal')
				@include('character.view.wallet_transactions')
				@include('character.view.mail')
				@include('character.view.notifications')
				@include('character.view.assets')
				@include('character.view.contacts')
				@include('character.view.contracts')          
				@include('character.view.market_orders')
				@include('character.view.calendar_events')
				@include('character.view.character_standings')
	        </div><!-- /.tab-content -->
	    </div><!-- nav-tabs-custom -->
	</div><!-- ./col-md-12 -->  
</div><!-- ./row -->
@stop

@section('javascript')
<script type="text/javascript">
	// First Hide all contents. Not very clean to add a fake class.. TODO: Think another way to do this
	$(".tbodycontent").hide(); 
	// on button click. Not very clean to add a fake class.. TODO: Think another way to do this
	$(".viewcontent").on("click", function( event ){ 
		// get the tag direct after the button
		if($(this).hasClass('contracts')){
			// if we are in Contracts view, we check the next Div Tag
			var contents = $(this).closest( "table").next( "div" ); 
		} else {
			// if we are in Asset view, we check the next Tbody tag
			var contents = $(this).closest( "tbody").next( "tbody" ); 
		}
		
		// Show or hide
		contents.toggle();

		// some code for stylish
		if (contents.is(":visible")){
			$(this).removeClass('fa-plus').addClass('fa-minus');
			$(this).closest("tr").css( "background-color", "#EBEBEB" ); // change the background color of container (for easy see where we are)
			contents.css( "background-color", "#EBEBEB" ); // change the background color of content (for easy see where we are)
		} else {
			$(this).removeClass('fa-minus').addClass('fa-plus'); 
			$(this).closest("tr").css( "background-color", "#FFFFFF" ); // reset the background color on container when we hide content
		}
	});
	
	// $(function () {
	$("li#journal_graph").click(function() {
		var options = { chart: {
			renderTo: 'chart',
			type: 'line',
			zoomType: 'x',
			},
			title: {
				text: 'Daily ISK Delta',
			},
			xAxis: {
				title: {
					text: 'Time'
				},
				labels: {
					enabled: false
				},
			},
			yAxis: {
				title: {
					text: 'Amount'
				},
				labels: {
					enabled: false
				},
			},
			series: [{}]
		};

		var data;
		$.getJSON("{{ action('CharacterController@getWalletDelta', array('characterID' => $character->characterID)) }}",function(json){

			var deltas = [];
			for (i in json) {
				deltas.push([json[i]['day'], parseInt(json[i]['daily_delta'])]);
			}

			options.series[0].name = "Delta";
			options.series[0].data = deltas;

			var chart = new Highcharts.Chart(options);

			// Trigger a fake resize to get the chart to calculate the width
			// correctly
			$(window).trigger('resize');
		});
	});
	$( document ).ready(function() {
		var items = [];
		var arrays = [], size = 250;

		$('[rel="id-to-name"]').each( function(){
		//add item to array
			items.push( $(this).text() );
		});

		var items = $.unique( items );

		while (items.length > 0)
			arrays.push(items.splice(0, size));

		$.each(arrays, function( index, value ) {

			$.ajax({
				type: 'POST',
				url: "{{ action('HelperController@postResolveNames') }}",
				data: {
					'ids': value.join(',')
				},
				success: function(result){
					$.each(result, function(id, name) {

						$("span:contains('" + id + "')").html(name);
					})
				},
				error: function(xhr, textStatus, errorThrown){
					console.log(xhr);
					console.log(textStatus);
					console.log(errorThrown);
				}
			});
		});
	});
</script>
@stop
