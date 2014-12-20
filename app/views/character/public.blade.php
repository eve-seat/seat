@extends('layouts.masterLayout')

@section('html_title', 'View Public Character')

@section('page_content')

  {{-- open a empty form to get a crsf token --}}
  {{ Form::open(array()) }} {{ Form::close() }}

  <div class="row">
    <div class="col-md-12">
      <!-- Default box -->
      <div class="box box-info">
        <div class="box-header">
          <h2 class="box-title"><i class="fa fa-user"></i> Character Details for {{ $character_info->characterName }}</h2>
          <div class="box-tools pull-right">
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-3">
              <img src='//image.eveonline.com/Character/{{ $character_info->characterID }}_256.jpg' class='img-circle pull-right'>
            </div>
            <div class="col-md-4">
              <div class="box box-solid">
                <div class="box-header">
                  <h3 class="box-title">Character Overview</h3>
                  <div class="box-tools pull-right">
                  </div>
                </div>
                <div class="box-body">
                  <dl>
                    <dt>Name</dt>
                    <dd>{{ $character_info->characterName }}</dd>

                    <dt>Corporation</dt>
                    <dd>{{ $character_info->corporation }}</dd>

                    <dt>Race, Booldline, Sex</dt>
                    <dd>{{ $character_info->race }}, {{ $character_info->bloodline }}</dd>
                  </dl>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div>

  {{-- details such as character sheet, skills, mail, wallet etc etc here --}}

  <div class="row">

    <div class="col-md-6">

      {{-- if we have the character info from eve_characterinfo, dispaly that --}}
      @if (!empty($character_info))

        {{-- employment information --}}
        <div class="box box-solid box-primary">
          <div class="box-header">
            <h3 class="box-title">Employment History ({{ count($employment_history) }})</h3>
            <div class="box-tools pull-right">
            </div>
          </div>
          <div class="box-body">
            <ul class="list-unstyled">

              @foreach($employment_history as $employment)

                <li>
                  <img src='https://image.eveonline.com/Corporation/{{ $employment->corporationID }}_32.png' class='img-circle'>
                  Joined <b><span rel="id-to-name">{{ $employment->corporationID }}</span></b> on {{ $employment->startDate }} ({{ Carbon\Carbon::parse($employment->startDate)->diffForHumans() }})
                </li>

              @endforeach

            </ul>
          </div><!-- /.box-body -->
        </div><!-- /.box -->

      @endif

    </div> <!-- ./col-md-6 -->

  <div class="col-md-6">

    {{-- if we have the character info from eve_characterinfo, dispaly that --}}
    @if (!empty($character_info))

      {{-- Ship & Location Information --}}
      <div class="box box-solid box-primary">
        <div class="box-header">
          <h3 class="box-title">Information</h3>
          <div class="box-tools pull-right">
          </div>
        </div>
        <div class="box-body">
          <ul class="list-unstyled">
            {{-- Corporation --}}
            @if (!empty($character_info->corporation))
              <li><b>Current Corporation:</b> {{ $character_info->corporation }}</li>
            @endif
            {{-- Alliance --}}
            @if (!empty($character_info->alliance))
              <li><b>Current Alliance:</b> {{ $character_info->alliance }}</li>
            @endif
            {{-- security status --}}
            @if (!empty($character_info->securityStatus))
              <li>
                <b>Security Status:</b>
                @if ($character_info->securityStatus < -5)
                  <span class="text-red">{{ $character_info->securityStatus }}</span>
                @elseif ($character_info->securityStatus < -2)
                  <span class="text-yellow">{{ $character_info->securityStatus }}</span>
                @else
                  <span class="text-green">{{ $character_info->securityStatus }}</span>
                @endif
              </li>
            @endif
                  </ul>
                </div><!-- /.box-body -->
            </div><!-- /.box -->

        @endif

    </div> <!-- ./col-md-6 -->
  </div> <!-- ./row -->

@stop

@section('javascript')

  <script type="text/javascript">

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
