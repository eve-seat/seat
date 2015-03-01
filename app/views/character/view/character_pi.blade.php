{{-- character pi --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Planetary Interaction</h3>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">

        @foreach($colonies as $colony)

          <div class="box box-solid box-primary">
            <div class="box-header">
              <h3 class="box-title">{{ $colony['planetName'] }} {{ $colony['planetTypeName'] }}</h3>
              <div class="box-tools pull-right">
                Upgrade Level: {{ $colony['upgradeLevel']}} Installations: {{ $colony['numberOfPins']}}
                <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
              </div>
            </div>
            <div class="box-body no-padding">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-hover table-condensed compact" id="datatable">
                    <tbody>
                      <tr>
                        <th colspan="100%" style="text-align:center; font-size:120%">
                          Routes
                        </th>
                      </tr>
                      <tr>
                        <th>Installation Name</th>
                        <th>Product</th>
                        <th>Cycle Time</th>
                        <th>Quantity Per Cycle</th>
                        <th>Cycle End Time</th>
                        <th>Destination</th>
                      </tr>
                    </tbody>

                    @foreach($routes as $route)

                      @if($route->planetID == $colony['planetID'])
                        <tbody>
                          <tr>
                            <td>
                                {{ Seat\services\helpers\Img::type($route->sourceTypeID, 16, array('class' => 'eveIcon small')) }}
                                {{ $route->sourceTypeName }}
                            </td>
                            <td>
                                {{ Seat\services\helpers\Img::type($route->contentTypeID, 16, array('class' => 'eveIcon small')) }}
                                {{ $route->contentTypeName }} ({{ $route->quantity }})
                            </td>
                            <td>@if($route->cycleTime != 0){{ $route->cycleTime }} minutes @endif</td>
                            <td>@if($route->quantityPerCycle != 0){{ $route->quantityPerCycle }} @endif</td>
                            <td>@if( date('Y-m-d H:i:s') < ($route->expiryTime)){{ Carbon\Carbon::parse($route->expiryTime)->diffForHumans() }}@else No Active Cycle @endif</td>
                            <td>
                                {{ Seat\services\helpers\Img::type($route->destinationTypeID, 16, array('class' => 'eveIcon small')) }}
                                {{ $route->destinationTypeName }}
                            </td>
                          </tr>
                        </tbody>
                      @endif

                    @endforeach

                  </table>
                  <div class="box-footer"></div>
                </div><!-- /.col-md-12 -->

                <div class="col-md-6">
                  <table class="table table-hover table-condensed compact" id="datatable">
                    <tbody>
                      <tr>
                        <th colspan="100%" style="text-align:center; font-size:120%">
                          Links
                        </th>
                      </tr>
                      <tr>
                        <th>Source Installation</th>
                        <th>Level</th>
                        <th>Destination Installation</th>
                      </tr>
                    </tbody>

                    @foreach($links as $link)

                      @if($link->planetID == $colony['planetID'])
                        <tbody>
                          <tr>
                            <td>
                                {{ Seat\services\helpers\Img::type($link->sourceTypeID, 16, array('class' => 'eveIcon small')) }}
                                {{ $link->sourceTypeName }}
                            </td>
                            <td>{{ $link->linkLevel }}</td>
                            <td>
                                {{ Seat\services\helpers\Img::type($link->destinationTypeID, 16, array('class' => 'eveIcon small')) }}
                                {{ $link->destinationTypeName }}
                            </td>
                          </tr>
                        </tbody>
                      @endif

                    @endforeach

                  </table>
                </div> <!-- /.col-md-6 -->

                <div class="col-md-6">
                  <table class="table table-hover table-condensed compact" id="datatable">
                    <tbody>
                      <tr>
                        <th colspan="100%" style="text-align:center; font-size:120%">
                          Other Installations
                        </th>
                      </tr>
                      <tr>
                        <th>Installation Name</th>
                      </tr>
                    </tbody>

                    @foreach($installations as $installation)

                      @if($installation->planetID == $colony['planetID'])
                        <tbody>
                          <tr>
                            <td>
                                {{ Seat\services\helpers\Img::type($installation->typeID, 16, array('class' => 'eveIcon small')) }}
                                {{ $installation->typeName }}
                            </td>
                          </tr>
                        </tbody>
                      @endif

                    @endforeach

                  </table>
                </div> <!-- /.col-md-6 -->

              </div> <!-- /.row -->
            </div><!-- /.box-body -->
          </div> <!-- ./box -->

        @endforeach

      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
