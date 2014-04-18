@extends('layouts.masterLayout')

@section('html_title', 'Starbase Details')

@section('page_content')

{{-- starbase summaries in a table. yeah, couldnt avoid this table --}}
<div class="row">
	<div class="col-md-12">

		<div class="box box-solid box-primary">
		    <div class="box-header">
		        <h3 class="box-title">Starbase Summaries ({{ count($starbases) }})</h3>
		        <div class="box-tools pull-right">
		            <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
		            <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
		        </div>
		    </div>
		    <div class="box-body no-padding">
		    	<div class="row">
		    		{{-- split the summaries into 2 tables next to each other --}}
		    		@foreach (array_chunk($starbases, count($starbases) / 2) as $starbase)
			    		<div class="col-md-6">


					        <table class="table table-condensed table-hover">
					            <tbody>
					            	<tr>
						                <th>Location</th>
						                <th>Name</th>
						                <th>Type</th>
						                <th>Fuel Blocks</th>
						                <th>State</th>
						                <th></th>
						            </tr>
					            	@foreach ($starbase as $details)
							            <tr>

											{{-- find the starbases name in the $item_locations array --}}	
											{{--*/$posname = null/*--}}
											@foreach ($item_locations[$details->moonID] as $name_finder)
												@if ($name_finder['itemID'] == $details->itemID)
													{{--*/$posname = $name_finder['itemName']/*--}}
												@endif
											@endforeach

							                <td>{{ $details->itemName }}</td>
							                <td>{{ $posname }}</td>
							                <td>{{ $details->typeName }}</td>
							                <td>{{ $details->fuelBlocks }}</td>
							                <td>{{ $tower_states[$details->state] }}</td>
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
				@foreach ($item_locations[$details->moonID] as $name_finder)
					@if ($name_finder['itemID'] == $details->itemID)
						{{--*/$posname = $name_finder['itemName']/*--}}
					@endif
				@endforeach

				{{-- process the rest of the html --}}
				<div class="col-md-4">
					<div class="nav-tabs-custom">
					    <ul class="nav nav-tabs">
					        <li class="active"><a href="#tab_1{{ $details->itemID }}" data-toggle="tab" id="{{ $details->itemID }}">Tower Info</a></li>
					        <li><a href="#tab_2{{ $details->itemID }}" data-toggle="tab">Module Details @if (isset($item_locations[$details->moonID])) ({{ count($item_locations[$details->moonID]) }}) @endif</a></li>
					    </ul>
					    <div class="tab-content">
					        <div class="tab-pane active" id="tab_1{{ $details->itemID }}">
					            <h4>{{ $details->itemName }}</h4>
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
				                </p>
				               	{{-- tower specific information --}} 
				                {{-- stront is 3m3 a unit, fuel is 5m3  unit, this is why me multiply the quantities when calculating the bay usage --}}
								<table class="table table-condensed">
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
									        <th>Fuel Details</th>
									        <th></th>
									    </tr>
									    <tr>
									        <td>
									        	<b>{{ ($details->fuelBlocks * 5) }} / {{ $bay_sizes[$details->typeID]['fuelBay'] }}</b> m3 |
									        	Fuel Blocks
									        </td>
									        <td>
									            <div class="progress xs">
									                <div class="progress-bar progress-bar-primary" style="width: {{ (($details->fuelBlocks * 5) / $bay_sizes[$details->typeID]['fuelBay']) * 100 }}%"></div>
									            </div>
									        </td>
									        <td><span class="badge bg-blue">{{ round((($details->fuelBlocks * 5) / $bay_sizes[$details->typeID]['fuelBay']) * 100,0) }}%</span></td>
									    </tr>
									    <tr>
									        <td>
									        	<b>{{ ($details->strontium * 3) }} / {{ $bay_sizes[$details->typeID]['strontBay'] }}</b> m3 |
									        	Strontium
									        </td>
									        <td>
									            <div class="progress xs">
									                <div class="progress-bar progress-bar-success" style="width: {{ (($details->strontium * 3) / $bay_sizes[$details->typeID]['strontBay']) * 100 }}%"></div>
									            </div>
									        </td>
									        <td><span class="badge bg-green">{{ round((($details->strontium * 3) / $bay_sizes[$details->typeID]['strontBay']) * 100,0) }}%</span></td>
									    </tr>
									</tbody>
								</table>
					        </div><!-- /.tab-pane -->
					        
					        {{-- tower module information --}}
					        <div class="tab-pane" id="tab_2{{ $details->itemID }}">
								<table class="table table-condensed">
								    <tbody>
								    	<tr>
									        <th>Module Type</th>
									    </tr>
									    @foreach ($item_locations[$details->moonID] as $item)
										    <tr>
										        <td><b>{{ $item['itemName'] }}</b></td>
										        <td>
										        	@if (isset($item_contents[$item['itemID']]))
											        	@foreach ($item_contents[$item['itemID']] as $contents)
											        		<b>{{ $contents['quantity']}} x</b> {{ $contents['name'] }}
											        	@endforeach
											        @endif
										        </td>
										    </tr>
										@endforeach
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

