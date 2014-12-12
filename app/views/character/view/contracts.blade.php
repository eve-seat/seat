{{-- character contracts --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Contract List ({{ count($contracts_courier) +  count($contracts_other)}})</h3>
      </div><!-- /.box-header -->

      <div class="box-body no-padding">
        <div class="row">

          {{-- Building box for contracts like Itemexchange and auction --}}
          <div class="col-md-6">
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Item Exchange &amp; Auction ({{ count($contracts_other) }})</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
                </div>
              </div>
              <div class="box-body no-padding">
                <div class="row">
                  <div class="col-md-12">
                    <table class="table table-hover table-condensed">
                      <tbody>
                        <tr>
                          <th style="width: 200px">Issuer</th>
                          <th style="width: 200px">Assignee</th>
                          <th>type</th>
                          <th style="width: 30px">Status</th>
                          <th style="width: 30px"></th>
                        </tr>
                      </tbody>
                    </table>

                    {{-- Loop over other contracts and display --}}
                    @foreach ($contracts_other as $contract)

                      <table class="table table-hover table-condensed">
                        <tbody style="border-top:0px solid #FFF">
                          <tr class="item-container">
                            <td style="width: 200px">
                              <img src='{{ App\Services\Helpers\Helpers::generateEveImage($contract['issuerID'], 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                              <span rel="id-to-name">{{ $contract['issuerID'] }}</span>
                            </td>
                            <td style="width: 200px">
                              @if ($contract['assigneeID'] <> 0)
                                <img src='{{ App\Services\Helpers\Helpers::generateEveImage($contract['assigneeID'], 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                                <span rel="id-to-name">{{ $contract['assigneeID'] }}</span>
                              @else
                                Unknown Assignee
                              @endif
                            </td>
                            <td>{{ $contract['type'] }}</td>

                            {{-- Check the status and display icon for this status --}}
                            <td style="width: 30px">
                              @if($contract['status'] == 'Completed')
                                <span class="text-green" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-check"></i></span>
                              @elseif($contract['status'] == 'Outstanding')
                                <span class="text-orange" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-clock-o"></i></span>
                              @else
                                <span class="text-red" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-times"></i></span>
                              @endif
                            </td>
                            <td style="text-align: right; width: 30px"><i class="fa fa-plus viewcontent contracts" style="cursor: pointer;"></i></td>
                          </tr>
                        </tbody>
                      </table>

                      {{-- Add additionnal information for the contracts and give a specific class (tbodycontent) for toggle it --}}
                      <div class="col-md-12 tbodycontent" style="display: none;">
                        <ul class="list-unstyled">
                          <li>
                            <i class="fa fa-map-marker"></i>
                            <span data-toggle="tooltip" title="" data-original-title="{{ $contract['startlocation'] }}">
                              <b>{{ str_limit($contract['startlocation'], 100, $end = '...') }}</b>
                            </span>
                          </li>
                          @if(isset($contract['title']) && strlen($contract['title']) > 0)
                            <li>
                              <i class="fa fa-bullhorn" data-original-title=" {{ $contract['title'] }}" title="" data-toggle="tooltip"></i>
                              Title: <b>{{ str_limit($contract['title'], 100, $end = '...') }}</b>
                            </li>
                          @endif
                          <li>
                            <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateIssued'] }}" title="" data-toggle="tooltip"></i>
                            Issued: <b>{{ Carbon\Carbon::parse($contract['dateIssued'])->diffForHumans() }}</b>
                          </li>

                          {{-- If the contract is not completed, we display the expiration date else nothing --}}
                          @if(!isset($contract['dateCompleted']))
                            <li>
                              <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateExpired'] }}" title="" data-toggle="tooltip"></i>
                              Expires: <b>{{ Carbon\Carbon::parse($contract['dateExpired'])->diffForHumans() }}</b>
                            </li>
                          @endif

                          {{-- If the contract is completed we display the date --}}
                          @if(isset($contract['dateCompleted']))
                            <li>
                              <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateCompleted'] }}" title="" data-toggle="tooltip"></i>
                              Completed: <b>{{ Carbon\Carbon::parse($contract['dateCompleted'])->diffForHumans() }}</b>
                              by <img src='{{ App\Services\Helpers\Helpers::generateEveImage($contract['acceptorID'], 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                              <b><span rel="id-to-name">{{ $contract['acceptorID'] }}</span></b>
                            </li>
                          @endif
                          <li>
                            <i class="fa fa-money"></i>
                            Buyer will get <b><span class="text-green">{{ number_format($contract['reward'], 2, '.', ' ') }}</span></b> ISK
                          </li>
                          <li>
                            <i class="fa fa-money"></i>
                            Buyer will pay <b><span class="text-red">{{ number_format($contract['price'], 2, '.', ' ') }}</span></b> ISK
                          </li>

                          {{-- If the contract is an auction we display the buyout price --}}
                          @if($contract['type'] == 'Auction')
                            <li>
                              <i class="fa fa-money"></i>
                              <b>{{ number_format($contract['buyout'], 2, '.', ' ') }}</b> ISK buyout
                            </li>
                          @endif

                          {{-- Check if contract has contents --}}
                          @if(isset($contract['contents']) && count($contract['contents']) > 0)
                            <li> <i class="fa fa-paperclip"></i> Contents</li>
                            <li>
                              <div class="col-md-6">
                                <ul>
                                  <li style="list-style:none;">
                                    <span class="text-green"><b>Buyer will get</b></span>
                                  </li>

                                  {{-- Loop over contents and display item in contract --}}
                                  @foreach($contract['contents'] as $content)

                                    <li style="list-style:none;">
                                      {{-- Check if it's a item request or not --}}
                                      @if($content['included'] == 1)
                                        <img src='//image.eveonline.com/Type/{{ $content['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                        <span>{{  number_format($content['quantity'], 0, '.', ' ') }} x {{ $content['typeName'] }}</span>
                                      @endif
                                    </li>

                                  @endforeach
                                </ul>
                              </div>
                              <div class="col-md-6">
                                <ul>
                                  <li style="list-style:none;">
                                    <span class="text-red"><b>Buyer will pay</b></span>
                                  </li>

                                  {{-- Loop over contents and display item requested in contract --}}
                                  @foreach($contract['contents'] as $content)

                                    <li style="list-style:none;">
                                      {{-- Check if it's a item request or not --}}
                                      @if($content['included'] == 0)
                                      <img src='//image.eveonline.com/Type/{{ $content['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                      <span>{{  number_format($content['quantity'], 0, '.', ' ') }} x {{ $content['typeName'] }}</span>
                                      @endif
                                    </li>

                                  @endforeach

                                </ul>
                              </div>
                            </li>
                          @endif

                        </ul>
                      </div><!-- ./col-md-12 -->

                    @endforeach

                  </div><!-- ./col-md-12 -->
                </div><!-- ./row -->
              </div><!-- ./box-body -->
            </div><!-- ./box -->
          </div> <!-- ./col-md-6 -->

          {{-- Building box for courier contracts --}}
          <div class="col-md-6">
            <div class="box box-solid box-primary">
              <div class="box-header">
                <h3 class="box-title">Courier ({{ count($contracts_courier) }})</h3>
                <div class="box-tools pull-right">
                  <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
                </div>
              </div>
              <div class="box-body no-padding">
                <div class="row">
                  <div class="col-md-12">
                    <table class="table table-condensed">
                      <tbody>
                        <tr>
                          <th style="width: 200px">Issuer</th>
                          <th style="width: 200px">Assignee</th>
                          <th>type</th>
                          <th style="width: 30px">Status</th>
                          <th style="width: 30px"></th>
                        </tr>
                      </tbody>
                    </table>

                    {{-- Loop over contracts courier and display --}}
                    @foreach ($contracts_courier as $contract)

                      <table class="table table-hover table-condensed">
                        <tbody style="border-top:0px solid #FFF">
                          <tr class="item-container">
                            <td style="width: 200px">
                              <img src='{{ App\Services\Helpers\Helpers::generateEveImage($contract['issuerID'], 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                              <span rel="id-to-name">{{ $contract['issuerID'] }}</span>
                            </td>
                            <td style="width: 200px">
                              @if ($contract['assigneeID'] <> 0)
                                <img src='{{ App\Services\Helpers\Helpers::generateEveImage($contract['assigneeID'], 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                                <span rel="id-to-name">{{ $contract['assigneeID'] }}</span>
                              @else
                                Unknown Assignee
                              @endif
                            </td>
                            <td>{{ $contract['type'] }}</td>
                            <td style="width: 30px">
                              @if($contract['status'] == 'Completed')
                                <span class="text-green" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-check"></i></span>
                              @elseif($contract['status'] == 'inProgress')
                                <span class="text-blue" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-truck"></i></span>
                              @elseif($contract['status'] == 'Outstanding')
                                <span class="text-orange" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-clock-o"></i></span>
                              @else
                                <span class="text-red" data-toggle="tooltip" title="" data-original-title="{{ $contract['status'] }}"><i style="cursor: pointer" class="fa fa-times"></i></span>
                              @endif
                            </td>
                            <td style="text-align: right; width: 30px"><i class="fa fa-plus viewcontent contracts" style="cursor: pointer;"></i></td>
                          </tr>
                        </tbody>
                      </table>
                      {{-- Add additionnal information for the contracts and give a specific class (tbodycontent) for toggle it --}}
                      <div class="col-md-12 tbodycontent" style="display: none;">
                        <ul class="list-unstyled">
                          <li>
                            <i class="fa fa-flag-checkered"></i>
                            <span data-toggle="tooltip" title="" data-original-title="{{ $contract['startlocation'] }}">
                              <b>{{ str_limit($contract['startlocation'], 50, $end = '...') }}</b>
                            </span> >>
                            <span data-toggle="tooltip" title="" data-original-title="{{ $contract['endlocation'] }}">
                              <b>{{ str_limit($contract['endlocation'], 50, $end = '...') }}</b>
                            </span>
                            <span>
                             ({{ number_format($contract['volume'], 2, '.', ' ') }} m<sup>3</sup>)
                            </span>
                          </li>
                          @if(isset($contract['title']) && strlen($contract['title']) > 0)
                            <li>
                              <i class="fa fa-bullhorn" data-original-title=" {{ $contract['title'] }}" title="" data-toggle="tooltip"></i>
                              Title: <b>{{ str_limit($contract['title'], 100, $end = '...') }}</b>
                            </li>
                          @endif
                          <li>
                            <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateIssued'] }}" title="" data-toggle="tooltip"></i>
                            Issued: <b>{{ Carbon\Carbon::parse($contract['dateIssued'])->diffForHumans() }}</b>
                          </li>

                          {{-- Add a conditionnal check. If a contract is not completed we show the expiration date else nothing --}}
                          @if(!isset($contract['dateCompleted']))
                            <li>
                              <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateExpired'] }}" title="" data-toggle="tooltip"></i>
                              Expires: <b>{{ Carbon\Carbon::parse($contract['dateExpired'])->diffForHumans() }}</b>
                            </li>
                          @endif

                          {{-- If a contract is accepted we show the date else nothing --}}
                          @if(isset($contract['dateAccepted']))
                            <li>
                              <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateAccepted'] }}" title="" data-toggle="tooltip"></i>
                              Accepted: <b>{{ Carbon\Carbon::parse($contract['dateAccepted'])->diffForHumans() }}</b>
                              by <img src='{{ App\Services\Helpers\Helpers::generateEveImage($contract['acceptorID'], 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                              <b><span rel="id-to-name">{{ $contract['acceptorID'] }}</span></b>
                            </li>
                          @endif

                          {{-- If a contract is completed we show the date else nothing --}}
                          @if(isset($contract['dateCompleted']))
                            <li>
                              <i class="fa fa-clock-o" data-original-title=" {{ $contract['dateCompleted'] }}" title="" data-toggle="tooltip"></i>
                              Completed: <b>{{ Carbon\Carbon::parse($contract['dateCompleted'])->diffForHumans() }}</b>
                            </li>
                          @endif
                          <li>
                            <i class="fa fa-money"></i>
                            <b>{{ number_format($contract['reward'], 2, '.', ' ') }}</b> ISK in reward
                          </li>
                          <li>
                            <i class="fa fa-money"></i>
                            <b>{{ number_format($contract['collateral'], 2, '.', ' ') }}</b> ISK in collateral
                          </li>
                        </ul>
                      </div><!-- ./col-md-12 -->

                    @endforeach

                  </div><!-- ./col-md-12 -->
                </div><!-- ./row -->
              </div><!-- ./box-body -->
            </div><!-- ./box -->
          </div> <!-- ./col-md-6 -->

        </div><!-- ./row -->
      </div><!-- /.box-body -->
    </div><!-- ./box -->
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
