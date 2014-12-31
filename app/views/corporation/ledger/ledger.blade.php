@extends('layouts.masterLayout')

@section('html_title', 'Corporation Ledger')

@section('page_content')

  <div class="row">
    <div class="col-md-3">

      <div style="margin-top: 15px;">
        <ul class="nav nav-pills nav-stacked" id="available-ledgers">
          <li class="header">Summarized Ledger</li>
          <li class="active"><a href="{{ action('CorporationController@getLedgerSummary', array('corporationID' => $corporationID)) }}"><i class="fa fa-calendar-o"></i> Today ( {{ Carbon\Carbon::now()->toDateString() }} )</a></li>
          <li class="header">Available Ledgers</li>

          @foreach ($ledger_dates as $ledger_date)

            <li>
              <a href="#" id="ledger" a-date="{{ $ledger_date }}">
                <i class="fa fa-calendar-o"></i> {{ Carbon\Carbon::parse($ledger_date)->year }}-{{ Carbon\Carbon::parse($ledger_date)->month }}
              </a>
            </li>

          @endforeach

        </ul>
      </div>

    </div>

    <div class="col-md-9">

      <!-- ajax responses will get pushed into this span -->
      <span id="ledger-result">

        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_summaries" data-toggle="tab">Summaries</a></li>
            <li><a href="#tab_tax_contributors" data-toggle="tab">Tax Contributors</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_summaries">

              {{-- wallet division balances --}}
              <div class="box box-solid box-primary">
                <div class="box-header">
                  <h3 class="box-title">Corporation Account Balances</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
                    <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body no-padding">
                  <table class="table table-condensed compact table-hover" id="datatable">
                    <thead>
                      <tr>
                        <th>Account ID</th>
                        <th>Wallet Division Name</th>
                        <th>Balance</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach ($wallet_balances as $wallet_division)

                        <tr>
                          <td>{{ $wallet_division->accountID }}</td>
                          <td>{{ $wallet_division->description }}</td>
                          <td><b>{{ number_format($wallet_division->balance, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</b> ISK</td>
                        </tr>

                      @endforeach

                    </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div>

              {{-- wallet ledger --}}
              <div class="box box-solid box-success">
                <div class="box-header">
                  <h3 class="box-title">Global Wallet Ledger</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
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
                                      <span class="text-red">{{ number_format($entry->total, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK</span>
                                    @else
                                      {{ number_format($entry->total, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK
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
                                    <span class="text-red">{{ number_format($ledger['total'], 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK</span>
                                  @else
                                    {{ number_format($ledger['total'], 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK
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
                                <td data-sort="{{ $entry->total }}"> <b> {{ number_format($entry->total, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK </b> </td>
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
                <div class="tab-pane" id="tab_tax_missions">

                {{-- mission reward tax --}}
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
                              <td data-sort="{{ $entry->total }}"> <b> {{ number_format($entry->total, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK </b> </td>
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
                              <td data-sort="{{ $entry->total }}"> <b> {{ number_format($entry->total, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }} ISK </b> </td>
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

    </span> <!-- ./result-span -->

  </div> <!-- ./col-md-9 -->
</div> <!-- ./row -->
@stop

@section('javascript')

  <script type="text/javascript">

    // Call the monthly ledger
    $('a#ledger').click(function() {

        $('ul#available-ledgers li.active').removeClass('active');
        $(this).closest('li').addClass('active');

        $('#ledger-result')
          .html('<br><i class="fa fa-cog fa fa-spin"></i> Loading Ledger...')
          .load("{{ action('CorporationController@getLedgerMonth', array('corporationID' => $corporationID )) }}" + "/" + $(this).attr('a-date'));
        $("table#datatable").dataTable({ paging:false });

    });

  </script>
@stop
