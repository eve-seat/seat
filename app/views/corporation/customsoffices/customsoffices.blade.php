@extends('layouts.masterLayout')

@section('html_title', 'Customs Offices')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Customs Offices of {{ $corporation->corporationName }}</h3>
        </div>
        <div class="box-body no-padding">
          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <tr>
                <th>Planet</th>
                <th>Reinforce</th>
                <th>Corporation</th>
                <th>Alliance</th>
                <th>Standing</th>
                <th>+10</th>
                <th>+5</th>
                <th>Neutral</th>
                <th>-5</th>
                <th>-10</th>
              </tr>
            </thead>
            <tbody>

              @foreach ($customsoffices as $poco)

                <tr>
                  <td>{{ $poco->mapName }}</td>
                  <td>
                    {{ Carbon\Carbon::createFromTime($poco->reinforceHour, 0, 0)->subHour()->format('H:i') }}
                    - {{ Carbon\Carbon::createFromTime($poco->reinforceHour, 0, 0)->addHour()->format('H:i') }}
                  </td>
                  <td>{{ ($poco->taxRateCorp * 100) }} %</td>

                  {{-- Check for Alliance based access --}}

                  <td>
                    @if($poco->allowAlliance)
                      <span>{{ ( $poco->taxRateAlliance*100) }} %</span>
                      <span class="label label-success">Enabled</span>
                    @else
                      <span class="label label-danger">Access Denied</span>
                    @endif
                  </td>

                  {{-- Check for Standing based access --}}
                  <td>
                    @if($poco->allowStandings)
                      <span class="label label-success">Enabled</span>
                    @else
                      <span class="label label-danger">Access Denied</span>
                    @endif
                  </td>

                  <td data-sort="{{ $poco->taxRateStandingHigh }}"> {{ ($poco->taxRateStandingHigh*100) }}%</td>
                  <td data-sort="{{ $poco->taxRateStandingGood }}"> {{ ($poco->taxRateStandingGood*100) }}%</td>
                  <td data-sort="{{ $poco->taxRateStandingNeutral }}"> {{ ($poco->taxRateStandingNeutral*100) }}%</td>
                  <td data-sort="{{ $poco->taxRateStandingBad }}"> {{ ($poco->taxRateStandingBad*100) }}%</td>
                  <td data-sort="{{ $poco->taxRateStandingHorrible }}"> {{ ($poco->taxRateStandingHorrible*100) }}%</td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- ./box-body no-padding -->
      </div>
    </div>
  </div>

@stop
