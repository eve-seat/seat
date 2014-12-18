@extends('layouts.masterLayout')

@section('html_title', 'Starbase Details')

@section('page_content')

  {{-- open a empty form to get a crsf token --}}
  {{ Form::open(array()) }} {{ Form::close() }}

  {{-- starbase summaries in a table. yeah, couldnt avoid this table --}}
  <div class="row">
    <div class="col-md-12">

      <div class="box box-solid box-primary">
        <div class="box-header">
          <h3 class="box-title">Starbase Summaries ({{ count($starbases) }})</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
          </div>
        </div>
        <div class="box-body no-padding">
          <div class="row">

            {{-- split the summaries into 2 tables next to each other --}}
            @foreach (array_chunk($starbases, (count($starbases) / 2) > 1 ? count($starbases) / 2 : 2) as $starbase)

              <div class="col-md-6">

                <table class="table table-condensed table-hover">
                  <thead>
                    <tr>
                      <th>Type</th>
                      <th>Location</th>
                      <th>Sec</th>
                      <th>Name</th>
                      <th>Fuel Blocks</th>
                      <th>State</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($starbase as $details)
                      <tr>

                        {{--
                          Find the starbases name in the $item_locations array.
                          We acheive this by looping over the item_locaions we received, and check
                          if we have a matching item id.

                          If the moonID is 0, we just keep the null value. This could happen if the location was not
                          determined. It appears that this may happen if the state of the tower is not online.
                        --}}
                        {{--*/$posname = null/*--}}
                        @if ($details->moonID != 0 && isset($item_locations[$details->moonID]))
                          @foreach ($item_locations[$details->moonID] as $name_finder)
                            @if ($name_finder['itemID'] == $details->itemID)
                              {{--*/$posname = $name_finder['itemName']/*--}}
                            @endif
                          @endforeach
                        @endif

                        <td>
                          <img src='//image.eveonline.com/Type/{{ $details->typeID }}_32.png' style='width: 18px;height: 18px;'>
                          {{ $details->typeName }}
                        </td>
                        <td>{{ $details->itemName }}</td>
                        <td>{{ App\Services\Helpers\Helpers::format_number($details->security,'1') }}</td>
                        <td><b>{{ $posname }}</b></td>
                        <td>{{ $details->fuelBlocks }}</td>
                        <td>
                          @if ($details->state == 4)
                            <span class="label label-success pull-right">{{ $tower_states[$details->state] }}</span>
                          @else
                            <span class="label label-warning pull-right">{{ $tower_states[$details->state] }}</span>
                          @endif
                        </td>
                        <td><a href="#{{ $details->itemID }}"><i class="fa fa-anchor" data-toggle="tooltip" title="" data-original-title="Details"></i></a></td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div>

            @endforeach

          </div> <!-- ./row -->
        </div><!-- /.box-body -->
      </div>

    </div>
  </div>

  @foreach (array_chunk($starbases, 3) as $starbase)

    <div class="row">

      @foreach ($starbase as $details)

        {{-- find the starbases name in the $item_locations array --}}
        {{--*/$posname = null/*--}}
        @if ($details->moonID != 0 && isset($item_locations[$details->moonID]))
          @foreach ($item_locations[$details->moonID] as $name_finder)
            @if ($name_finder['itemID'] == $details->itemID)
              {{--*/$posname = $name_finder['itemName']/*--}}
            @endif
          @endforeach
        @endif

        {{-- process the rest of the html --}}
        <div class="col-md-4">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1{{ $details->itemID }}" data-toggle="tab" id="{{ $details->itemID }}">Tower Info</a></li>
              <li><a href="#tab_2{{ $details->itemID }}" data-toggle="tab">Tower Configuration</a></li>
              <li><a href="#tab_3{{ $details->itemID }}" data-toggle="tab">Module Details @if (isset($item_locations[$details->moonID])) ({{ count($item_locations[$details->moonID]) }}) @endif</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1{{ $details->itemID }}">
                <h4><b>{{ $details->itemName }}</b></h4>
                @if ($details->state == 4)
                  <span class="label label-success pull-right">{{ $tower_states[$details->state] }}</span>
                @else
                  <span class="label label-warning pull-right">{{ $tower_states[$details->state] }}</span>
                @endif

                {{--
                  so here we need to determine the time left for the tower fuel.
                  This is just my old code copied and pasted, so chances are there is some logic flaw somewhere.

                  An important thing to note here. I am escaping the template comments in order to do these calculations.
                  It will probably be better to move this hax to the controller sometime.

                  Note about the fuel usage calculations.
                  ---

                  There are 3 tower sizes. Small, Medium and Large. ie:

                  - Amarr Control Tower
                  - Amarr Control Tower Medium
                  - Amarr Control Tower Small

                  Fuel usage is calculated by based on wether the tower is anchored in sov or non sov space. [1] ie.

                  == No SOV Usage
                  - 40 Blocks a hour => Amarr Control Tower
                  - 20 Blocks a hour => Amarr Control Tower Medium
                  - 10 Blocks a hour => Amarr Control Tower Small

                  == SOV Usage
                  - 30 Blocks a hour => Amarr Control Tower
                  - 15 Blocks a hour => Amarr Control Tower Medium
                  - 7 Blocks a hour => Amarr Control Tower Small

                  Time2hardcode this shit

                  [1] https://wiki.eveonline.com/en/wiki/Starbase#Fuel_Usage

                  Start escape
                  ------------
                  */

                  // Set some base usage values ...

                  $usage = 0;
                  $stront_usage =0;

                  // ... fuel for non sov towers ...

                  $large_usage = 40;
                  $medium_usage = 20;
                  $small_usage = 10;

                  // ... and sov towers

                  $sov_large_usage = 30;
                  $sov_medium_usage = 15;
                  $sov_small_usage = 8;

                  // Stront usage

                  $stront_large = 400;
                  $stront_medium = 200;
                  $stront_small = 100;

                  // basically, here we check if the names Small/Medium exists in the tower name. Then,
                  // if the tower is in the sov_tower array, set the value for usage

                  if (strpos($details->typeName, 'Small') !== false) {

                    $stront_usage = $stront_small;

                    if (array_key_exists($details->itemID, $sov_towers))
                      $usage = $sov_small_usage;
                    else
                      $usage = $small_usage;

                  } elseif (strpos($details->typeName, 'Medium') !== false) {

                    $stront_usage = $stront_medium;

                    if (array_key_exists($details->itemID, $sov_towers))
                      $usage = $sov_medium_usage;
                    else
                      $usage = $medium_usage;

                  } else {

                    $stront_usage = $stront_large;

                    if (array_key_exists($details->itemID, $sov_towers))
                      $usage = $sov_large_usage;
                    else
                      $usage = $large_usage;
                  }

                  /*
                  End the escape
                  --------------
                --}}

                <ul class="list-unstyled">
                  <li>Name: <b>{{ $posname }}</b></li>
                  <li>Type: <b>{{ $details->typeName }}</b></li>
                  <li>Corp Access: <b> @if($details->allowCorporationMembers==1)Yes @else No @endif</b></li>
                  <li>Alliance Access: <b> @if($details->allowAllianceMembers==1)Yes @else No @endif</b></li>
                  <li>Current State Since: <b> {{ $details->stateTimeStamp }}</b> ({{ Carbon\Carbon::parse($details->stateTimeStamp)->diffForHumans() }})</li>
                  <li>Online Since: <b> {{ $details->onlineTimeStamp }}</b> ({{ Carbon\Carbon::parse($details->onlineTimeStamp)->diffForHumans() }})</li>
                  <br>

                  @if($details->security >= 0.5 )
                    <li>
                      <b>Charters Left: </b>
                      {{ $details->starbaseCharter }} charters
                        (
                          @ 1 charter/h, it will go offline

                          {{-- determine if the time left is less than 3 days --}}
                          @if ( Carbon\Carbon::now()->addHours($details->starbaseCharter / 1)->lte(Carbon\Carbon::now()->addDays(3)))
                            <b><span class="text-red">{{ Carbon\Carbon::now()->addHours($details->starbaseCharter / 1)->diffForHumans() }}</span></b>
                          @else
                            <b>{{ Carbon\Carbon::now()->addHours($details->starbaseCharter / 1)->diffForHumans() }}</b>
                          @endif
                        )
                        <i class="fa fa-clock-o pull-right" data-toggle="tooltip" title="" data-placement="left" data-original-title="Estimated offline at {{ Carbon\Carbon::now()->addHours($details->starbaseCharter / 1)->toDateTimeString() }}"></i>
                    </li>
                  @endif

                  <li>
                    <b>Fuel Left: </b>
                    {{ $details->fuelBlocks }} blocks
                    (
                      @ {{ $usage }} blocks/h, it will go offline

                      {{-- determine if the time left is less than 3 days --}}
                      @if ( Carbon\Carbon::now()->addHours($details->fuelBlocks / $usage)->lte(Carbon\Carbon::now()->addDays(3)))
                        <b><span class="text-red">{{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $usage)->diffForHumans() }}</span></b>
                      @else
                        <b>{{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $usage)->diffForHumans() }}</b>
                      @endif
                    )
                    <i class="fa fa-clock-o pull-right" data-toggle="tooltip" title="" data-placement="left" data-original-title="Estimated offline at {{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $usage)->toDateTimeString() }}"></i>
                  </li>
                  <li>
                    <b>Stront Left:</b>
                    {{ $details->strontium }} units
                    (
                      @ {{ $stront_usage }} units/h, it should stay reinforced for <b>{{ round($details->strontium / $stront_usage) }}</b> hours
                    )
                    <i class="fa fa-clock-o pull-right" data-toggle="tooltip" title="" data-placement="left" data-original-title="Estimated reinforced until {{ Carbon\Carbon::now()->addHours($details->strontium / $stront_usage)->toDateTimeString() }}"></i>
                  </li>
                </ul>

                {{-- tower specific information --}}
                {{-- stront is 3m3 a unit, fuel is 5m3  unit, this is why me multiply the quantities when calculating the bay usage --}}
                <table class="table table-condensed table-hover">
                  <tbody>
                    <tr>
                      <th>
                        {{-- check if the towers id is part of the sov_towers array --}}
                        @if (array_key_exists($details->itemID, $sov_towers))
                          <span class="text-green">Reduced Fuel Usage</span>
                        @else
                          <span class="text-red">Full Fuel Usage</span>
                        @endif
                      </th>
                      <th>Fuel Reserve Details</th>
                      <th></th>
                    </tr>
                    <tr>
                      <td>
                        <img src='//image.eveonline.com/Type/4051_32.png' style='width: 18px;height: 18px;'>
                        <b>Fuel Blocks @if($details->security >= 0.5)+ Charters @endif:</b> {{ ($details->starbaseCharter) + ($details->fuelBlocks * 5) }} m3 / {{ $bay_sizes[$details->typeID]['fuelBay'] }} m3
                      </td>
                      <td>
                        <div class="progress">
                          <div class="progress-bar @if( ($details->starbaseCharter > 24 && $details->security >= 0.5) || ($details->fuelBlocks / $usage > 24 )) progress-bar-primary @else progress-bar-danger @endif" style="width: {{ (($details->fuelBlocks * 5) / $bay_sizes[$details->typeID]['fuelBay']) * 100 }}%"></div>
                        </div>
                      </td>
                      <td><span class="badge @if( ($details->starbaseCharter > 24 && $details->security >= 0.5) || ($details->fuelBlocks / $usage > 24 )) bg-blue @else bg-red @endif pull-right">{{ round((($details->starbaseCharter + ($details->fuelBlocks * 5)) / $bay_sizes[$details->typeID]['fuelBay']) * 100,0) }}%</span></td>
                    </tr>
                    <tr>
                      <td>
                        <img src='//image.eveonline.com/Type/16275_32.png' style='width: 18px;height: 18px;'>
                        <b>Strontium:</b> {{ ($details->strontium * 3) }} m3 / {{ $bay_sizes[$details->typeID]['strontBay'] }} m3

                      </td>
                      <td>
                        <div class="progress">
                          <div class="progress-bar progress-bar-success" style="width: {{ (($details->strontium * 3) / $bay_sizes[$details->typeID]['strontBay']) * 100 }}%"></div>
                        </div>
                      </td>
                      <td><span class="badge bg-green pull-right">{{ round((($details->strontium * 3) / $bay_sizes[$details->typeID]['strontBay']) * 100,0) }}%</span></td>
                    </tr>
                  </tbody>
                </table>
              </div><!-- /.tab-pane -->

              {{-- tower configuration --}}
              <div class="tab-pane" id="tab_2{{ $details->itemID }}">
                <h4>Configuration:</h4>
                <ul class="list-unstyled">
                  <li>Use Standings From: <b><span rel="id-to-name">{{ $details->useStandingsFrom }}</span></b></li>
                  <li>On Aggression: <b> @if ($details->onAggression == 1)Yes @else No @endif</b></li>
                  <li>On Corporation War: <b> @if ($details->onCorporationWar == 1) Yes @else No @endif</b></li>
                  <li>Last Updated: <b> {{ $details->updated_at }}</b> ({{ Carbon\Carbon::parse($details->updated_at)->diffForHumans() }})</li>
                </ul>
              </div><!-- /.tab-pane -->

              {{-- tower module information --}}
              <div class="tab-pane" id="tab_3{{ $details->itemID }}">
                <h4>Modules Detail: <small class="pull-right text-muted">% Full</small></h4>
                <table class="table table-condensed table-hover">
                  <tbody>
                    {{--
                      In the case of us not knowing what the moonID is for whatever reason,
                      make a note about it
                    --}}
                    @if ($details->moonID == 0)
                        <tr>
                          <td colspan="3">Unknown MoonID, unable to find assets</td>
                        </tr>
                    @else

                      @foreach ($item_locations[$details->moonID] as $item)

                        {{--
                          a quick check is done here to ensure that the tower type id and the module type id is
                          not the same. there is no need to show the towers details here too as it is already
                          covered in the Tower Info block, together with the fuel usage information.

                          If we are not at a tower, continue
                        --}}
                        @if ($item['typeID'] <> $details->typeID)
                          <tr>
                            <td>
                              <b>{{ $item['typeName'] }}</b>

                              {{-- if the module has been given a name, show it --}}
                              @if ($item['typeName'] <> $item['itemName'])
                              <small class="text-muted">{{ $item['itemName'] }}</small>
                              @endif
                            </td>
                            <td>
                              {{--
                                Here we will count the total m3 for everything in this module.
                                We will start off by making the total_size 0, and then adding the
                                volume of the item inside the module to it.
                              --}}
                              {{-- */ $total_size = 0 /* --}}
                              @if (isset($item_contents[$item['itemID']]))

                                @if (count($item_contents[$item['itemID']]) > 1)
                                  <ul class="list-unstyled">
                                    <li class="nav-header text-muted pull-right">{{ count($item_contents[$item['itemID']]) }} Items</li>

                                    @foreach ($item_contents[$item['itemID']] as $contents)

                                      <li>
                                        <img src='//image.eveonline.com/Type/{{ $contents['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                        <b>{{ $contents['quantity']}} x</b> {{ $contents['name'] }}
                                        {{-- add the volume --}}
                                        {{-- */ $total_size = $total_size + ($contents['quantity'] * $contents['volume']) /* --}}
                                      </li>

                                    @endforeach

                                  </ul>
                                @else

                                  @foreach ($item_contents[$item['itemID']] as $contents)

                                    <img src='//image.eveonline.com/Type/{{ $contents['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                    <b>{{ $contents['quantity']}} x</b> {{ $contents['name'] }}
                                          {{-- add the volume --}}
                                          {{-- */ $total_size = $total_size + ($contents['quantity'] * $contents['volume']) /* --}}
                                  @endforeach

                                @endif
                              @endif

                              </td>
                              <td>
                                {{-- check if there is a capacity larger that 0. Some modules, like hardners have 0 capacity --}}
                                @if ($item['capacity'] > 0)

                                  {{--
                                    Some Silos have bonusses to silo capacity, so lets take that into account here.
                                    If a silo/coupling array is bonusable, add the bonus to the total capacity
                                  --}}
                                  @if (array_key_exists($details->typeID, $tower_cargo_bonusses) && (in_array($item['typeID'], $cargo_size_bonusable_modules)))
                                    <span class="pull-right">
                                      {{ round(($total_size / ( $item['capacity'] *= (1 + $tower_cargo_bonusses[$details->typeID] / 100))) * 100) }}%
                                    </span>
                                  @else   {{-- We have a non bonused module --}}
                                    <span class="pull-right">
                                      {{ $percentage = round( ($total_size / $item['capacity']) * 100) }}%
                                    </span>
                                  @endif
                                @endif
                              </td>
                            </tr>

                        @endif

                      @endforeach

                    @endif

                  </tbody>
                </table>
              </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
          </div>
        </div><!-- /.col -->

      @endforeach

    </div> <!-- ./row -->

  @endforeach

@stop

@section('javascript')

  <script>
    $( document ).ready(function() {
      var items = [];
      var arrays = [], size = 250;

      $('[rel="id-to-name"]').each( function(){
         //add item to array
         items.push( $(this).text() );
      });

      var items = $.unique( items );

      while (items.length > 0)
        arrays.push(items.splice(0, size));

      $.each(arrays, function( index, value ) {

        $.ajax({
          type: 'POST',
          url: "{{ action('HelperController@postResolveNames') }}",
          data: {
            'ids': value.join(',')
          },
          success: function(result){
            $.each(result, function(id, name) {

              $("span:contains('" + id + "')").html(name);
            })
          },
          error: function(xhr, textStatus, errorThrown){
           console.log(xhr);
           console.log(textStatus);
           console.log(errorThrown);
         }
       });
      });
    });
    </script>

@stop
