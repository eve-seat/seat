@extends('layouts.masterLayout')

@section('html_title', 'Corporation Member Tracking')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>All Corporation Member(s) @if (count($members) > 0) ({{ count($members) }}) @endif</b>
          </h3>
        </div>
        <div class="panel-body">

          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <th>Name</th>
              <th>Joined</th>
              <th>Last Logon</th>
              <th>Last Logoff</th>
              <th>Location</th>
              <th>Ship</th>
              <th></th>
            </thead>
            <tbody>

              @foreach ($members as $character)

                <tr>
                  <td>
                    <a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID)) }}">
                      <img src='//image.eveonline.com/Character/{{ $character->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $character->name }}
                    </a>
                  </td>
                  <td>
                    {{ Carbon\Carbon::parse($character->startDateTime)->diffForHumans() }}
                  </td>
                  <td>
                    {{ Carbon\Carbon::parse($character->logonDateTime)->diffForHumans() }}
                    @if(Carbon\Carbon::parse($character->logonDateTime)->lt(Carbon\Carbon::now()->subMonth()))
                      <span class="text-red pull-right"><i class="fa fa-exclamation"></i></span>
                    @endif
                  </td>
                  <td>
                    {{ Carbon\Carbon::parse($character->logoffDateTime)->diffForHumans() }}
                  </td>
                  <td>
                    {{ $character->location }}
                  </td>
                  <td>
                    {{ $character->shipType }}
                  </td>

                  {{-- key information --}}
                  <td>
                    @if ($character->isOk == 1)
                      <span class="text-green"><i class="fa fa-check"></i> Key Ok</span>
                      @if (strlen($character->keyID) > 0)
                        <a href="{{ action('ApiKeyController@getDetail', array('keyID' => $character->keyID)) }}" data-toggle="tooltip" title="" data-original-title="Key Details"><i class="fa fa-cog"></i></a>
                      @endif
                    @else
                      <span class="text-red"><i class="fa fa-exclamation"></i> Key not Ok</span>
                    @endif
                  </td>

                </tr>

              @endforeach

            </tbody>
          </table>
        </div>
      </div>
    </div> <!-- col-md-12 -->
  </div> <!-- row -->

@stop
