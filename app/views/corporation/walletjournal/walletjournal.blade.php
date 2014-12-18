@extends('layouts.masterLayout')

@section('html_title', 'Corporation Wallet Journal')

@section('page_content')

  <div class="row">
    <div class="col-span-12">
      <div id="chart" style="height:200px;"></div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Wallet Journal for: {{ $corporation_name->corporationName }}</h3>
          <div class="box-tools">
            <ul class="pagination pagination-sm no-margin pull-right">
              {{ $wallet_journal->links() }}
            </ul>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body no-padding">
          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Wallet Division</th>
                <th>Type</th>
                <th>Owner1 Name</th>
                <th>Owner2 Name</th>
                <th>ArgName 1</th>
                <th>Amount</th>
                <th>Balance</th>
              </tr>
            </thead>
            <tbody>

              @foreach ($wallet_journal as $e)

                <tr @if ($e->amount < 0)class="danger" @endif>
                  <td data-order="{{ $e->date }}">
                    <span data-toggle="tooltip" title="" data-original-title="{{ $e->date }}">
                      {{ Carbon\Carbon::parse($e->date)->diffForHumans() }}
                    </span>
                  </td>
                  <td>{{ $e->description }}</td>
                  <td>{{ $e->refTypeName }}</td>
                  <td>
                    <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->ownerID1, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $e->ownerName1 }}
                  </td>
                  <td>
                    <img src='{{ App\Services\Helpers\Helpers::generateEveImage($e->ownerID2, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $e->ownerName2 }}
                  </td>
                  <td>{{ $e->argName1 }}</td>
                  <td>
                    @if ($e->amount < 0)
                    <span class="text-red">{{ App\Services\Helpers\Helpers::format_number($e->amount, 2) }}</span>
                    @else
                    {{ App\Services\Helpers\Helpers::format_number($e->amount, 2) }}
                    @endif
                  </td>
                  <td>{{ App\Services\Helpers\Helpers::format_number($e->balance, 2) }}</td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
        <div class="pull-right">{{ $wallet_journal->links() }}</div>
      </div>
    </div>
  </div>

@stop

@section('javascript')

  <script type="text/javascript">

    $(function () {
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
      $.getJSON("{{ action('CorporationController@getWalletDelta', array('corporationID' => $corporationID)) }}",function(json){

        var deltas = [];
        for (i in json) {
          deltas.push([json[i]['day'], parseInt(json[i]['daily_delta'])]);
        }

        options.series[0].name = "Delta";
        options.series[0].data = deltas;

        var chart = new Highcharts.Chart(options);
      });
    });

  </script>

@stop
