@extends('layouts.masterLayout')

@section('html_title', 'Starbase Details')

@section('page_content')

  {{-- open a empty form to get a crsf token --}}
  {{ Form::token() }}

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

          <table class="table table-condensed table-hover">
            <thead>
              <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Location</th>
                <th>Onlined</th>
                <th>Sec</th>
                <th>Offline Estimate</th>
                <th><img src='//image.eveonline.com/Type/4051_32.png' style='width: 18px;height: 18px;'> Fuel Level</th>
                <th>State</th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              @foreach ($starbases as $details)

                <tr>
                  <td>
                    <img src='//image.eveonline.com/Type/{{ $details->typeID }}_32.png' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $details->typeName }}
                  </td>
                  <td><b>{{ $starbase_names[$details->itemID] }}</b></td>
                  <td>{{ $details->itemName }}</td>
                  <td>
                    {{ $details->onlineTimeStamp }}</b> ({{ Carbon\Carbon::parse($details->onlineTimeStamp)->diffForHumans() }})
                  </td>
                  <td>{{ number_format($details->security, 1, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
                  <td>
                    {{-- determine if the time left is less than 3 days --}}
                    @if ( Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->lte(Carbon\Carbon::now()->addDays(3)))
                      <span class="text-red">{{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->diffForHumans() }}</span>
                    @else
                      {{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->diffForHumans() }}
                    @endif
                  </td>
                  <td>
                    <div class="progress">
                      <div class="progress-bar @if( ($details->starbaseCharter > 24 && $details->security >= 0.5) || ($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'] > 24 )) progress-bar-primary @else progress-bar-danger @endif" style="width: {{ (($details->fuelBlocks * 5) / $bay_sizes[$details->typeID]['fuelBay']) * 100 }}%"></div>
                    </div>
                  </td>
                  <td>
                    @if ($details->state == 4)
                      <span class="label label-success">{{ $tower_states[$details->state] }}</span>
                    @else
                      <span class="label label-warning">{{ $tower_states[$details->state] }}</span>
                    @endif
                  </td>
                  <td><a href="#{{ $details->itemID }}"><i class="fa fa-anchor" data-toggle="tooltip" title="" data-original-title="Details"></i></a></td>
                </tr>

              @endforeach

            </tbody>
          </table>

        </div><!-- /.box-body -->
      </div>

    </div>
  </div>

  @foreach (array_chunk($starbases, 3) as $starbase)

    <div class="row">

      @foreach ($starbase as $details)

        <div class="col-md-4">
          <div class="nav-tabs-custom">
          <div class="tab-content">
            <h4><b>{{ $details->itemName }}</b></h4>
          </div>
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1{{ $details->itemID }}" data-toggle="tab" id="{{ $details->itemID }}">Tower Info</a></li>
              <li><a href="#tab_2{{ $details->itemID }}" data-toggle="tab">Tower Configuration</a></li>
              <li><a href="#tab_3{{ $details->itemID }}" data-toggle="tab">Module Details</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1{{ $details->itemID }}">
                @if ($details->state == 4)
                  <span class="label label-success pull-right">{{ $tower_states[$details->state] }}</span>
                @else
                  <span class="label label-warning pull-right">{{ $tower_states[$details->state] }}</span>
                @endif

                <ul class="list-unstyled">
                  <li>Name: <b>{{ $starbase_names[$details->itemID] }}</b></li>
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
                      @ {{ $starbase_fuel_usage[$details->itemID]['fuel_usage'] }} blocks/h, it will go offline

                      {{-- determine if the time left is less than 3 days --}}
                      @if ( Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->lte(Carbon\Carbon::now()->addDays(3)))
                        <b><span class="text-red">{{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->diffForHumans() }}</span></b>
                      @else
                        <b>{{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->diffForHumans() }}</b>
                      @endif
                    )
                    <i class="fa fa-clock-o pull-right" data-toggle="tooltip" title="" data-placement="left" data-original-title="Estimated offline at {{ Carbon\Carbon::now()->addHours($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'])->toDateTimeString() }}"></i>
                  </li>
                  <li>
                    <b>Stront Left:</b>
                    {{ $details->strontium }} units
                    (
                      @ {{ $starbase_fuel_usage[$details->itemID]['stront_usage'] }} units/h, it should stay reinforced for <b>{{ round($details->strontium / $starbase_fuel_usage[$details->itemID]['stront_usage']) }}</b> hours
                    )
                    <i class="fa fa-clock-o pull-right" data-toggle="tooltip" title="" data-placement="left" data-original-title="Estimated reinforced until {{ Carbon\Carbon::now()->addHours($details->strontium / $starbase_fuel_usage[$details->itemID]['stront_usage'])->toDateTimeString() }}"></i>
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
                      <th>Fuel Reserve Levels</th>
                      <th></th>
                    </tr>
                    <tr>
                      <td>
                        <img src='//image.eveonline.com/Type/4051_32.png' style='width: 18px;height: 18px;'>
                        <b>Fuel Blocks @if($details->security >= 0.5)+ Charters @endif:</b> {{ ($details->starbaseCharter) + ($details->fuelBlocks * 5) }} m3 / {{ $bay_sizes[$details->typeID]['fuelBay'] }} m3
                      </td>
                      <td>
                        <div class="progress">
                          <div class="progress-bar @if( ($details->starbaseCharter > 24 && $details->security >= 0.5) || ($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'] > 24 )) progress-bar-primary @else progress-bar-danger @endif" style="width: {{ (($details->fuelBlocks * 5) / $bay_sizes[$details->typeID]['fuelBay']) * 100 }}%"></div>
                        </div>
                      </td>
                      <td><span class="badge @if( ($details->starbaseCharter > 24 && $details->security >= 0.5) || ($details->fuelBlocks / $starbase_fuel_usage[$details->itemID]['fuel_usage'] > 24 )) bg-blue @else bg-red @endif pull-right">{{ round((($details->starbaseCharter + ($details->fuelBlocks * 5)) / $bay_sizes[$details->typeID]['fuelBay']) * 100,0) }}%</span></td>
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

              <div class="tab-pane" id="tab_3{{ $details->itemID }}">

                {{-- check that we have known starbase_modules for this tower --}}
                @if (!array_key_exists($details->itemID, $starbase_modules) || count($starbase_modules[$details->itemID]) <= 0)
                  No modules could be shown for this tower.
                @else

                  <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1_modules_industry{{ $details->itemID }}" data-toggle="tab">Industry ({{ count($starbase_modules[$details->itemID]['industry']) }})</a></li>
                        <li><a href="#tab_2_modules_storage{{ $details->itemID }}" data-toggle="tab">Storage ({{ count($starbase_modules[$details->itemID]['storage']) }})</a></li>
                        <li><a href="#tab_3_modules_other{{ $details->itemID }}" data-toggle="tab">Other ({{ count($starbase_modules[$details->itemID]['other']) }})</a></li>
                      </ul>
                      <div class="tab-content">

                        {{-- tab to display modules in the industry group --}}
                        <div class="tab-pane active" id="tab_1_modules_industry{{ $details->itemID }}">

                          @if(count($starbase_modules[$details->itemID]['industry']) <= 0)
                            No modules in this category.
                          @else

                            @foreach( $starbase_modules[$details->itemID]['industry'] as $module_group_name => $modules)

                              <div class="panel-group" id="accordion_modules_{{ $details->itemID }}_{{ studly_case($module_group_name) }}" role="tablist" aria-multiselectable="true">

                                <div class="panel panel-default">
                                  <div class="panel-heading" role="tab" id="headingOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                    <h4 class="panel-title">
                                      <a data-toggle="collapse" data-parent="#accordion_modules_{{ $details->itemID }}_{{ studly_case($module_group_name) }}" href="#collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}" aria-expanded="true" aria-controls="collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                        {{ str_plural($module_group_name) }} <span class="pull-right"><small>{{ count($modules) }} module(s) in group</small></span>
                                      </a>
                                    </h4>
                                  </div>
                                  <div id="collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                    <div class="panel-body">

                                      {{-- Now we will loop over the modules in this group --}}
                                      @foreach($modules as $module_id => $module_content)

                                        <ul class="list-unstyled">
                                          <li>
                                            <img src='//image.eveonline.com/Type/{{ $module_content['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                            <b>{{ $module_group_name }}</b>
                                             @if(!is_null($module_content['module_name']))
                                              <span class="text-muted">(called {{ $module_content['module_name'] }})</span>
                                             @endif
                                            @if($module_content['capacity'] > 0)
                                              is currently <b>{{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%</b> full
                                              with {{ count($module_content['contents']) }} item(s).
                                            @endif
                                          </li>
                                        </ul>

                                        @if(count($module_content['contents']) > 0 && $module_content['capacity'] > 0)

                                          {{-- items inside of the module --}}
                                          <table class="table table-condensed table-hover">
                                            <tbody>
                                              <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>% of Use</th>
                                              </tr>

                                              {{-- the final loop is to show the items inside of the module --}}
                                              @foreach($module_content['contents'] as $content_item)

                                                <tr>
                                                  <td>{{ $content_item['quantity'] }}</td>
                                                  <td>
                                                    <img src='//image.eveonline.com/Type/{{ $content_item['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                                    {{ $content_item['name'] }}
                                                  </td>
                                                  <td>{{ round(( ($content_item['quantity'] * $content_item['volume']) / $module_content['used_volume']) * 100, 0) }}%</td>
                                                </tr>

                                              @endforeach

                                            </tbody>
                                          </table>

                                          <ul class="list-unstyled">
                                            <li>
                                              Storage capacity is <b>{{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%</b> full
                                              with {{ $module_content['used_volume'] }}m3 / {{ $module_content['capacity'] }}m3 in use by {{ count($module_content['contents']) }} item(s).<br>
                                              @if($module_content['cargo_size_bonus']) <span class="text-green">This module is receiving a cargo size bonus from the tower type.</span> @endif
                                            </li>
                                            <li>
                                              <div class="progress">
                                                <div class="progress-bar progress-bar-default" style="width: {{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%"></div>
                                              </div>
                                            </li>
                                          </ul>

                                        @endif  {{-- contents count --}}

                                        @if(!next($modules) === false)
                                            <hr> <!-- divide modules -->
                                        @endif

                                      @endforeach {{-- group modules --}}

                                    </div>
                                  </div>
                                </div>
                                <br>

                              </div> <!-- ./panel-group -->

                            @endforeach {{-- starbase idustry modules --}}

                          @endif

                        </div><!-- /.tab-pane -->

                        {{-- tab to display modules in the storage group --}}
                        <div class="tab-pane" id="tab_2_modules_storage{{ $details->itemID }}">

                          @if(count($starbase_modules[$details->itemID]['storage']) <= 0)
                            No modules in this category.
                          @else

                            @foreach( $starbase_modules[$details->itemID]['storage'] as $module_group_name => $modules)

                              <div class="panel-group" id="accordion_modules_{{ $details->itemID }}_{{ studly_case($module_group_name) }}" role="tablist" aria-multiselectable="true">

                                <div class="panel panel-default">
                                  <div class="panel-heading" role="tab" id="headingOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                    <h4 class="panel-title">
                                      <a data-toggle="collapse" data-parent="#accordion_modules_{{ $details->itemID }}_{{ studly_case($module_group_name) }}" href="#collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}" aria-expanded="true" aria-controls="collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                        {{ str_plural($module_group_name) }} <span class="pull-right"><small>{{ count($modules) }} module(s) in group</small></span>
                                      </a>
                                    </h4>
                                  </div>
                                  <div id="collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                    <div class="panel-body">

                                      {{-- Now we will loop over the modules in this group --}}
                                      @foreach($modules as $module_id => $module_content)

                                        <ul class="list-unstyled">
                                          <li>
                                            <img src='//image.eveonline.com/Type/{{ $module_content['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                            <b>{{ $module_group_name }}</b>
                                             @if(!is_null($module_content['module_name']))
                                              <span class="text-muted">(called {{ $module_content['module_name'] }})</span>
                                             @endif
                                            @if($module_content['capacity'] > 0)
                                              is currently <b>{{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%</b> full
                                              with {{ count($module_content['contents']) }} item(s).
                                            @endif
                                          </li>
                                        </ul>

                                        @if(count($module_content['contents']) > 0 && $module_content['capacity'] > 0)

                                          {{-- items inside of the module --}}
                                          <table class="table table-condensed table-hover">
                                            <tbody>
                                              <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>% of Use</th>
                                              </tr>

                                              {{-- the final loop is to show the items inside of the module --}}
                                              @foreach($module_content['contents'] as $content_item)

                                                <tr>
                                                  <td>{{ $content_item['quantity'] }}</td>
                                                  <td>
                                                    <img src='//image.eveonline.com/Type/{{ $content_item['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                                    {{ $content_item['name'] }}
                                                  </td>
                                                  <td>{{ round(( ($content_item['quantity'] * $content_item['volume']) / $module_content['used_volume']) * 100, 0) }}%</td>
                                                </tr>

                                              @endforeach

                                            </tbody>
                                          </table>

                                          <ul class="list-unstyled">
                                            <li>
                                              Storage capacity is <b>{{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%</b> full
                                              with {{ $module_content['used_volume'] }}m3 / {{ $module_content['capacity'] }}m3 in use by {{ count($module_content['contents']) }} item(s).<br>
                                              @if($module_content['cargo_size_bonus']) <span class="text-green">This module is receiving a cargo size bonus from the tower type.</span> @endif
                                            </li>
                                            <li>
                                              <div class="progress">
                                                <div class="progress-bar progress-bar-default" style="width: {{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%"></div>
                                              </div>
                                            </li>
                                          </ul>

                                        @endif  {{-- contents count --}}

                                        @if(!next($modules) === false)
                                            <hr> <!-- divide modules -->
                                        @endif

                                      @endforeach {{-- group modules --}}

                                    </div>
                                  </div>
                                </div>  <!-- ./panel -->
                                <br>

                              </div> <!-- ./panel-group -->

                            @endforeach {{-- starbase storage modules --}}

                          @endif

                        </div><!-- /.tab-pane -->

                        {{-- tab to display uncatagorized modules --}}
                        <div class="tab-pane" id="tab_3_modules_other{{ $details->itemID }}">

                          @if(count($starbase_modules[$details->itemID]['other']) <= 0)
                            No modules in this category.
                          @else

                            @foreach( $starbase_modules[$details->itemID]['other'] as $module_group_name => $modules)

                              <div class="panel-group" id="accordion_modules_{{ $details->itemID }}_{{ studly_case($module_group_name) }}" role="tablist" aria-multiselectable="true">

                                <div class="panel panel-default">
                                  <div class="panel-heading" role="tab" id="headingOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                    <h4 class="panel-title">
                                      <a data-toggle="collapse" data-parent="#accordion_modules_{{ $details->itemID }}_{{ studly_case($module_group_name) }}" href="#collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}" aria-expanded="true" aria-controls="collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                        {{ str_plural($module_group_name) }} <span class="pull-right"><small>{{ count($modules) }} module(s) in group</small></span>
                                      </a>
                                    </h4>
                                  </div>
                                  <div id="collapseOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne{{ $details->itemID }}_{{ studly_case($module_group_name) }}">
                                    <div class="panel-body">

                                      {{-- Now we will loop over the modules in this group --}}
                                      @foreach($modules as $module_id => $module_content)

                                        <ul class="list-unstyled">
                                          <li>
                                            <img src='//image.eveonline.com/Type/{{ $module_content['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                            <b>{{ $module_group_name }}</b>
                                             @if(!is_null($module_content['module_name']))
                                              <span class="text-muted">(called {{ $module_content['module_name'] }})</span>
                                             @endif
                                            @if($module_content['capacity'] > 0)
                                              is currently <b>{{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%</b> full
                                              with {{ count($module_content['contents']) }} item(s).
                                            @endif
                                          </li>
                                        </ul>

                                        @if(count($module_content['contents']) > 0 && $module_content['capacity'] > 0)

                                          {{-- items inside of the module --}}
                                          <table class="table table-condensed table-hover">
                                            <tbody>
                                              <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>% of Use</th>
                                              </tr>

                                              {{-- the final loop is to show the items inside of the module --}}
                                              @foreach($module_content['contents'] as $content_item)

                                                <tr>
                                                  <td>{{ $content_item['quantity'] }}</td>
                                                  <td>
                                                    <img src='//image.eveonline.com/Type/{{ $content_item['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
                                                    {{ $content_item['name'] }}
                                                  </td>
                                                  <td>{{ round(( ($content_item['quantity'] * $content_item['volume']) / $module_content['used_volume']) * 100, 0) }}%</td>
                                                </tr>

                                              @endforeach

                                            </tbody>
                                          </table>

                                          <ul class="list-unstyled">
                                            <li>
                                              Storage capacity is <b>{{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%</b> full
                                              with {{ $module_content['used_volume'] }}m3 / {{ $module_content['capacity'] }}m3 in use by {{ count($module_content['contents']) }} item(s).<br>
                                              @if($module_content['cargo_size_bonus']) <span class="text-green">This module is receiving a cargo size bonus from the tower type.</span> @endif
                                            </li>
                                            <li>
                                              <div class="progress">
                                                <div class="progress-bar progress-bar-default" style="width: {{ round(($module_content['used_volume'] / $module_content['capacity']) * 100, 0) }}%"></div>
                                              </div>
                                            </li>
                                          </ul>

                                        @endif  {{-- contents count --}}

                                        @if(!next($modules) === false)
                                            <hr> <!-- divide modules -->
                                        @endif

                                      @endforeach {{-- group modules --}}

                                    </div>
                                  </div>
                                </div>  <!-- ./panel -->
                                <br>

                              </div> <!-- ./panel-group -->

                            @endforeach {{-- starbase other modules --}}

                          @endif

                        </div><!-- /.tab-pane -->
                      </div><!-- /.tab-content -->

                  </div> <!-- ./tabs for module groups -->

                @endif
              </div>  <!-- ./tab-pane -->

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
