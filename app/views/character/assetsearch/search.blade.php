@extends('layouts.masterLayout')

@section('html_title', 'Assets Search')

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

    // Search for asset types to search for... lol
    $('#searchinput').select2({
      multiple: true,
      width: "350",
      placeholder: "Search for assets",
      minimumInputLength: 3,
      ajax: {
        url: "{{ action('HelperController@getAvailableItems') }}",
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

    // Listen for when the select2() emits a change, and perform the search
    $("#searchinput").on("change", function(e) {

      if (e.val.length > 0) { // Don't try and search for nothing

        $("div#result").html("<i class='fa fa-cog fa-spin'></i> Loading...");
        $("div#result-box").fadeIn("slow");

        $.ajax({
          type: 'post',
          url: "{{ action('CharacterController@postSearchAssets') }}",
          data: {
            'items': e.val
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
