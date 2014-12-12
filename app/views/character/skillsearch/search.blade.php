@extends('layouts.masterLayout')

@section('html_title', 'Skills Search')

@section('page_content')
  <div class="row">

    <div class="col-md-12 text-center">

      {{ Form::open(array('class' => 'form-inline')) }}
        <fieldset>

          <!-- Prepended text-->
          <div class="form-group">
            <label class="sr-only" for="searchinput"></label>
            <div class="input-group">
              {{ Form::text('searchinput', null, array('id' => 'searchinput', 'class' => 'form-control'), 'required', 'autofocus') }}
            </div>
          </div>
          <div class="form-group">
            <label class="sr-only" for="level"></label>
            <div class="input-group">
              {{ Form::select('level', array('A' => 'Any Level', '0' => 'Injected Only', '1' => 'Level 1', '2' => 'Level 2', '3' => 'Level 3', '4' => 'Level 4', '5' => 'Level 5'), 'A', array('id' => 'level', 'class' => 'form-control'))}}
            </div>
          </div>

        </fieldset>
      {{ Form::close() }}
    </div>

  </div>
  <br>
  <div class="row">

    <div class="col-md-12">
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
    </div>
  </div>

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
    $("#level").select2({ width: "150" });

    // Listen for when the select2() emits a change, and perform the search
    $("#searchinput, #level").on("change", function(e) {

      if (e.val.length > 0) { // Don't try and search for nothing

        $("div#result").html("<i class='fa fa-cog fa-spin'></i> Loading...");
        $("div#result-box").fadeIn("slow");

        $.ajax({
          type: 'post',
          url: "{{ action('CharacterController@postSearchSkills') }}",
          data: {
            'skills': $("#searchinput").val(),
            'level': $("#level").val()
          },
            success: function(result){
            $("div#result").html(result);
            $("table#datatable").dataTable({ paging:false });
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
