@extends('layouts.masterLayout')

@section('html_title', 'Read Mail')

@section('page_content')

<style>
  #mail {
      font-size: 1.0em;
      line-height: 100%;
  }
  #mail font {
      font-size: inherit;
      color: #000000 !important;
  }
</style>

{{-- open a empty form to get a crsf token --}}
{{ Form::open(array()) }} {{ Form::close() }}

<div class="row">
	<div class="col-md-12">

		<div class="box">
		    <div class="box-header">
                <h4 class="box-title">
                	<i class="fa fa-envelope-o"></i> {{ $message->title }}
                    	<small>
                    		<b>From:</b> {{ $message->senderName }} sent about {{ Carbon\Carbon::parse($message->sentDate)->diffForHumans() }}
                    			@ {{ $message->sentDate }}
                    		 |

                    		{{-- corporations --}}
                    		@if (strlen($message->toCorpOrAllianceID) > 0 && count(explode(',', $message->toCorpOrAllianceID)) > 0)
	                    		<b>To Corp/Alliance:</b>
	                    			@foreach (explode(',', $message->toCorpOrAllianceID) as $corp_alliance)
	                    				<span rel="id-to-name">{{ $corp_alliance }}</span>
	                    			@endforeach
	                    	@endif

	                    	{{-- characters --}}
                    		@if (strlen($message->toCharacterIDs) > 0 && count(explode(',', $message->toCharacterIDs)) > 0)
	                    		<b>To Characters:</b>
	                    			@foreach (explode(',', $message->toCharacterIDs) as $characterID)
	                    				<span rel="id-to-name">{{ $characterID }}</span>
	                    			@endforeach
	                    	@endif

	                    	{{-- mailing lists --}}
                    		@if (strlen($message->toListID) > 0 && count(explode(',', $message->toListID)) > 0)
	                    		<b>To Mailing List:</b>
	                    			@foreach (explode(',', $message->toListID) as $list)
	                    				{{ $list }}
	                    			@endforeach
	                    	@endif

                    	</small>
               	</h4>
            </div>

		    <div class="box-body">
		    	<div id="mail">
			    	{{ $message->body }}
			    </div>
		    </div><!-- /.box-body -->
		    <div class="box-footer clearfix">
		    	<div class="pull-right">
			    	Receiver Type(s): 
			    	@if (strlen($message->toCorpOrAllianceID) > 0)
				    	<b>{{ count(explode(',', $message->toCorpOrAllianceID)) }}</b> Corporation(s) / Alliance(s)
				    @endif
				    @if (strlen($message->toCharacterIDs) > 0)
				    	<b>{{ count(explode(',', $message->toCharacterIDs)) }}</b> Character(s)
				    @endif
				    @if (strlen($message->toListID) > 0)
				    	<b>{{ count(explode(',', $message->toListID)) }}</b> Mailing List(s)
				    @endif
			    </div>
            </div>
		</div>

	</div>
</div>
	
@stop

@section('javascript')
	<script>
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