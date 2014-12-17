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
                            <td><img src='//image.eveonline.com/Type/{{ $route->sourceTypeID }}_32.png' style='width: 18px;height: 18px;'>{{ $route->sourceTypeName }}</td>
                            <td><img src='//image.eveonline.com/Type/{{ $route->contentTypeID }}_32.png' style='width: 18px;height: 18px;'> {{ $route->contentTypeName }} ({{ $route->quantity }})</td>
                            <td>@if($route->cycleTime != 0){{ $route->cycleTime }} minutes @endif</td>
                            <td>@if($route->quantityPerCycle != 0){{ $route->quantityPerCycle }} @endif</td>
                            <td>@if( date('Y-m-d H:i:s') < ($route->expiryTime)){{ Carbon\Carbon::parse($route->expiryTime)->diffForHumans() }}@else No Active Cycle @endif</td>
                            <td><img src='//image.eveonline.com/Type/{{ $route->destinationTypeID }}_32.png' style='width: 18px;height: 18px;'>{{ $route->destinationTypeName }}</td>
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
                            <td><img src='//image.eveonline.com/Type/{{ $link->sourceTypeID }}_32.png' style='width: 18px;height: 18px;'>{{ $link->sourceTypeName }}</td>
                            <td>{{ $link->linkLevel }}</td>
                            <td><img src='//image.eveonline.com/Type/{{ $link->destinationTypeID }}_32.png' style='width: 18px;height: 18px;'>{{ $link->destinationTypeName }}</td>
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
                            <td><img src='//image.eveonline.com/Type/{{ $installation->typeID }}_32.png' style='width: 18px;height: 18px;'> {{ $installation->typeName }}</td>
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
