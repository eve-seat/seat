@extends('layouts.masterLayout')

@section('html_title', 'People')

@section('page_content')

<h3>{{ count($people) }} People Groups Defined</h3>

<div class="row">
	<div class="col-md-9">

		@foreach(array_chunk($people, 4) as $people_chunk)
			<div class="row">

				@foreach($people_chunk as $personID => $personData)
					<div class="col-md-3">
						<div class="box box-solid">
						    <div class="box-header">
						        <h3 class="box-title">
									<a href="{{ action('CharacterController@getView', array('characterID' => $personData[0]['main']->characterID )) }}">
										<img src='//image.eveonline.com/Character/{{ $personData[0]['main']->characterID }}_32.jpg' class='img-circle'>
									</a>
						        	{{ $personData[0]['main']->characterName }}
						        	<small class="text-muted">({{ count($personData) }} keys)</small>
						        </h3>
						    </div>
						    <div class="box-body">
						    	<ul class="list-unstyled">
									@foreach ($personData as $characterInfo)
										<li>
											<span class="text-muted">
												KeyID: {{ $characterInfo['keyID'] }} <a href="{{ action('ApiKeyController@getDeleteFromGroup', array('keyID' => $characterInfo['keyID'] )) }}" class="pull-right">Delete Key from Group</a>
											</span>
										</li>
										@foreach($characterInfo['characters'] as $character)
											<li>
												<a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}">
													<img src='//image.eveonline.com/Character/{{ $character->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
												</a>
												{{ $character->characterName }}
												@if ($personData[0]['main']->characterID <> $character->characterID)
													<a href="{{ action('ApiKeyController@getSetGroupMain', array('personid' => $personData[0]['personID'], 'characterid' => $character->characterID)) }}" class="pull-right">Set as Main</a>
												@endif

											</li>
										@endforeach
									@endforeach
								</ul>
						    </div><!-- /.box-body -->
						</div>
					</div><!-- ./md-4 -->
				@endforeach

			</div> <!-- ./row -->
		@endforeach

	</div> <!-- ./col-md-9 -->
	<div class="col-md-3">

		<div class="box box-solid">
		    <div class="box-header">
		        <h3 class="box-title">Unaffiliated Keys &amp; Characters ({{ count($unaffiliated) }})</h3>
		    </div>
		    <div class="box-body">
				<ul class="list-unstyled">
					@foreach($unaffiliated as $key => $characters)
						<li>
							<!-- <i class="fa fa-plus" data-toggle="tooltip" data-placement="left" data-original-title="Start a New Person Group with this key as the main"></i> -->
							<span class="text-muted">
								KeyID: {{ $key }} <a id="existing" data-toggle="modal" data-target="#existing-modal" class="pull-right" a-keyid="{{ $key }}">Add to existing group</a>
							</span>
						</li>
						@foreach ($characters as $character)
							<li>
								<a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}">
									<img src='//image.eveonline.com/Character/{{ $character->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
								</a>
								{{ $character->characterName }}
								<a href="{{ action('ApiKeyController@getNewGroup', array('characterID' => $character->characterID )) }}" class="pull-right">Use as Main for new Group</a>
							</li>
						@endforeach
						<hr>
					@endforeach
				</ul>
		    </div><!-- /.box-body -->
		</div>

	</dov>
</div>

<!-- add to existing modal -->
<div class="modal fade" id="existing-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Add to an existing Person Group</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-12">
						{{ Form::open(array('class' => 'form-horizontal', 'action' => 'ApiKeyController@postAddToGroup')) }}
							<fieldset>

								<!-- Prepended text-->
								<div class="form-group">
								  <label class="col-md-4 control-label" for="searchinput"></label>
								    <div class="input-group">
								      {{ Form::text('personid', null, array('id' => 'searchinput', 'class' => 'form-control'), 'required', 'autofocus') }}
								    </div>
								</div>

								<input id="affected-key" name="affected-key" type="hidden" value="">

								<div class="form-group">
								  <label class="col-md-4 control-label"></label>
								    <div class="input-group">
								      {{ Form::submit('Add to Group', array('class' => 'btn bg-olive')) }}
								    </div>
								</div>

							</fieldset>
						{{ Form::close() }}            			
            		</div>
            	</div>
            
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop

@section('javascript')
	<script type="text/javascript">

		//Update the modals hidden field to have the key added to the correct group
		$("a#existing").click(function() {
			$("input#affected-key").val($(this).attr('a-keyid'));
		})

		// Search for avaialble people groups
		$('#searchinput').select2({
			multiple: false,
			width: "250",
			placeholder: "Select the Groups Main",
			minimumInputLength: 1,
			ajax: {
			  url: "{{ action('HelperController@getAvailablePeople') }}",
			  dataType: 'json',
			  data: function (term, page) {
			    return {
			      q: term
			    };
			  },
			  results: function (data, page) {
			    return { results: data };
			  }
			}
		});

	</script>
@stop