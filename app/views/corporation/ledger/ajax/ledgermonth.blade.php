<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab_summaries" data-toggle="tab">Summaries</a></li>
    <li><a href="#tab_tax_contributors" data-toggle="tab">Tax Contributors</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab_summaries">

      {{-- wallet ledger --}}
      <div class="box box-solid box-success">
        <div class="box-header">
          <h3 class="box-title">Wallet Ledger for {{ Carbon\Carbon::parse($date)->year }}-{{ Carbon\Carbon::parse($date)->month }}</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body no-padding">

          {{-- account ledgers --}}
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <br>

              {{--*/ $active = 1 /* Would LOVE a way to do this more cleanly!--}}
              @foreach ($ledgers as $accountKey => $ledger)
                @if ($active == 1)
                  <li class="active"><a href="#ledger{{ $accountKey }}" data-toggle="tab">{{ $ledger['divisionName'] }}</a></li>
                  {{--*/ $active = 0 /*--}}
                @else
                  <li><a href="#ledger{{ $accountKey }}" data-toggle="tab">{{ $ledger['divisionName'] }}</a></li>
                @endif
              @endforeach

            </ul>
            <div class="tab-content">

              {{--*/ $active = 1 /* Would LOVE a way to do this more cleanly!--}}
              @foreach ($ledgers as $accountKey => $ledger)

                @if ($active == 1)
                  <div class="tab-pane active" id="ledger{{ $accountKey }}">
                  {{--*/ $active = 0 /*--}}
                @else
                  <div class="tab-pane" id="ledger{{ $accountKey }}">
                @endif
                <table class="table table-condensed compact table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Transaction Type</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($ledger['ledger'] as $entry)

                      <tr>
                        <td>{{ $entry->refTypeName }}</td>
                        <td>
                          <b>
                            @if ($entry->total < 0)
                              <span class="text-red">{{ App\Services\Helpers\Helpers::format_number($entry->total, 2) }} ISK</span>
                            @else
                              {{ App\Services\Helpers\Helpers::format_number($entry->total, 2) }} ISK
                            @endif
                          </b>
                        </td>
                      </tr>

                    @endforeach

                    <tr>
                      <td>Net</td>
                      <td>
                        <b>
                          @if ($ledger['total'] < 0)
                            <span class="text-red">{{ App\Services\Helpers\Helpers::format_number($ledger['total'], 2) }} ISK</span>
                          @else
                            {{ App\Services\Helpers\Helpers::format_number($ledger['total'], 2) }} ISK
                          @endif
                        </b>
                      </td>
                    </tr>
                  </tbody>
                </table>

                </div><!-- /.tab-pane -->

              @endforeach

            </div><!-- /.tab-content -->
          </div>

        </div><!-- /.box-body -->
      </div> <!-- ./box -->
    </div><!-- /.tab-pane -->

    <div class="tab-pane" id="tab_tax_contributors">

      {{-- tax breakdowns --}}

      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_tax_bounties" data-toggle="tab">Bounty Prizes</a></li>
          <li><a href="#tab_tax_missions" data-toggle="tab">Mission Rewards</a></li>
          <li><a href="#tab_tax_pi" data-toggle="tab">Planetary Interaction</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_tax_bounties">

            {{-- bounty tax --}}
            @if (count($bounty_tax) > 0)

              <div class="box box-solid box-primary">
                <div class="box-header">
                  <h3 class="box-title">Tax Contributions for Bounty Prizes</h3>
                </div>
                <div class="box-body no-padding">
                  <table class="table table-condensed compact table-hover" id="datatable">
                    <thead>
                      <tr>
                        <th>Contributor</th>
                        <th>Contribution Total</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach ($bounty_tax as $entry)

                        <tr>
                          <td>
                            <a href="{{ action('CharacterController@getView', array('characterID' => $entry->ownerID2 )) }}">
                              <img src='//image.eveonline.com/Character/{{ $entry->ownerID2 }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                              {{ $entry->ownerName2 }}
                            </a>
                          </td>
                          <td> <b> {{ App\Services\Helpers\Helpers::format_number($entry->total, 2) }} ISK </b> </td>
                        </tr>

                      @endforeach

                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div> <!-- ./box -->

            @else
              <p class="lead">No Tax Contributor Information Available</p>
            @endif

          </div><!-- /.tab-pane -->
          <div class="tab-pane active" id="tab_tax_missions">

          {{-- mission tax --}}
          @if (count($mission_tax) > 0)

            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Tax Contributions for Mission Rewards</h3>
              </div>
              <div class="box-body no-padding">
                <table class="table table-condensed compact table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Contributor</th>
                      <th>Contribution Total</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($mission_tax as $entry)
                      <tr>
                        <td>
                          <a href="{{ action('CharacterController@getView', array('characterID' => $entry->ownerID2 )) }}">
                            <img src='//image.eveonline.com/Character/{{ $entry->ownerID2 }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                            {{ $entry->ownerName2 }}
                          </a>
                        </td>
                        <td> <b> {{ App\Services\Helpers\Helpers::format_number($entry->total, 2) }} ISK </b> </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div> <!-- ./box -->

            @else
              <p class="lead">No Tax Contributor Information Available</p>
            @endif

          </div><!-- /.tab-pane -->
          <div class="tab-pane" id="tab_tax_pi">

          {{-- pi tax --}}
          @if (count($pi_tax) > 0)

            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Tax Contributions for Planetary Interaction</h3>
              </div>
              <div class="box-body no-padding">
                <table class="table table-condensed compact table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Contributor</th>
                      <th>Contribution Total</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($pi_tax as $entry)

                      <tr>
                        <td>
                          <a href="{{ action('CharacterController@getView', array('characterID' => $entry->ownerID1 )) }}">
                            <img src='//image.eveonline.com/Character/{{ $entry->ownerID1 }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                            {{ $entry->ownerName1 }}
                          </a>
                        </td>
                        <td> <b> {{ App\Services\Helpers\Helpers::format_number($entry->total, 2) }} ISK </b> </td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div> <!-- ./box -->

            @else
              <p class="lead">No Tax Contributor Information Available</p>
            @endif

          </div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->

      </div> <!-- ./nav-tabs -->
    </div><!-- /.tab-pane -->
  </div><!-- /.tab-content -->
</div>
