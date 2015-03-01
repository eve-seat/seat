@extends('layouts.masterLayout')

@section('html_title', 'All Corporations')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>All Corporations(s) @if (count($corporations) > 0) ({{ count($corporations) }}) @endif</b>
          </h3>
        </div>
        <div class="panel-body">

          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <th>Corp Name</th>
              <th>CEO</th>
              <th>Alliance</th>
              <th>Tax Rate</th>
              <th>Members</th>
              <th>Shares</th>
              <th></th>
            </thead>
            <tbody>

              @foreach ($corporations as $corporation)

                <tr>
                  <td>
                      {{ Seat\services\helpers\Img::corporation($corporation->corporationID, 16, array('class' => 'img-circle eveIcon small')) }}
                      {{ $corporation->corporationName }} [{{ $corporation->ticker }}]
                  </td>
                  <td>
                    <a href="{{ action('CharacterController@getView', array('characterID' => $corporation->ceoID)) }}">
                        {{ Seat\services\helpers\Img::character($corporation->ceoID, 16, array('class' => 'img-circle eveIcon small')) }}
                        {{ $corporation->ceoName }}
                    </a>
                  </td>
                  <td>
                      {{ Seat\services\helpers\Img::alliance($corporation->allianceID, 16, array('class' => 'img-circle eveIcon small')) }}
                      {{ $corporation->allianceName }}
                  </td>
                  <td>
                      {{ number_format($corporation->taxRate, 1, $settings['decimal_seperator'], $settings['thousand_seperator']) }} %
                  </td>
                  <td>
                      {{ number_format($corporation->memberCount, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }} /
                      {{ number_format($corporation->memberLimit, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }}
                  </td>
                  <td>
                      {{ number_format($corporation->shares, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }}
                  </td>

                  {{-- key information --}}
                  <td>
                    @if ($corporation->isOk == 1)
                      <span class="text-green"><i class="fa fa-check"></i> Key Ok</span>
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
