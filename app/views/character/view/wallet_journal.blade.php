{{-- wallet journal --}}
<div class="row">
  <div class="col-span-12">
    <div id="chart" style="height:200px;"></div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Wallet Journal ({{ count($wallet_journal) }})</h3>
        <div class="box-tools">
          <a href="{{ action('CharacterController@getFullWalletJournal', array('characterID' => $characterID)) }}" class="btn btn-default btn-sm pull-right">
            <i class="fa fa-money"></i> View Full Journal
          </a>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">
        <table class="table table-condensed compact table-hover" id="datatable">
          <thead>
            <tr>
              <th>Date</th>
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
                <td data-sort="{{ $e->amount }}">
                  {{ number_format($e->amount, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }}
                </td>
                <td data-sort="{{ $e->balance }}">
                  {{ number_format($e->balance, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }}
                </td>
              </tr>

            @endforeach

          </tbody>
        </table>
      </div><!-- /.box-body -->
    </div>
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
