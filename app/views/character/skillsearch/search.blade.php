@extends('layouts.masterLayout')

@section('html_title', 'Skills Search')

@section('page_content')

	{{ Form::open(array('class' => 'form-horizontal')) }}
		<fieldset>

			<!-- Prepended text-->
			<div class="form-group">
			  <label class="col-md-4 control-label" for="searchinput"></label>
			    <div class="input-group">
			      {{ Form::text('searchinput', null, array('id' => 'searchinput', 'class' => 'form-control'), 'required', 'autofocus') }}
			    </div>
			</div>

		</fieldset>
	{{ Form::close() }}

	<!-- results box -->
	<div class="box" id="result-box" style="display: none;">
		<div class="box-header">
		    <h3 class="box-title">Search Results</h3>
		</div>

	    <div class="box-body">
	    	<div id="result">
	    	</div>
	    </div><!-- /.box-body -->
	</div><!-- /.box -->

@stop

@section('javascript')
	<script type="text/javascript">	
		// We preload the json as we dont want to load this with every user input
		var skillList;
		$(document).ready(function() {
		    $.getJSON("{{ action('HelperController@getAvailableSkills') }}", function(json){
		        skillList = json;
		    });
		});

		$("#searchinput").select2({
		    multiple: true,
		    width: "350",
		    placeholder: "Start typing to filter for skills.",
		    data:function() { return { text:'text', results: skillList }; },
		});

		// Listen for when the select2() emits a change, and perform the search
		$("#searchinput").on("change", function(e) {

			if (e.val.length > 0) { // Don't try and search for nothing

			    $("div#result").html("<i class='fa fa-cog fa-spin'></i> Loading...");
			    $("div#result-box").fadeIn("slow");

				$.ajax({
					type: 'post',
					url: "{{ action('CharacterController@postSearchSkills') }}",
					data: { 
					  'skills': e.val
					},
						success: function(result){
						$("div#result").html(result);
					},
					error: function(xhr, textStatus, errorThrown){
						console.log(xhr);
						console.log(textStatus);
						console.log(errorThrown); 
					}
				});
			}
		})
	</script>
@stop