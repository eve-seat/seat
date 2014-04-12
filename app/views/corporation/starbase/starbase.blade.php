@extends('layouts.masterLayout')

@section('html_title', 'Startbase Details')

@section('page_content')

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
					        <li class="active"><a href="#tab_1{{ $details->itemID }}" data-toggle="tab">Tower Info</a></li>
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

								<ul class="list-unstyled">
								    <li>Name: <b>{{ $posname }}</b></li>
								    <li>Type: <b>{{ $details->typeName }}</b></li>
								    <li>Corp Access: <b> @if($details->allowCorporationMembers==1)Yes @else No @endif</b></li>
								    <li>Alliance Access: <b> @if($details->allowAllianceMembers==1)Yes @else No @endif</b></li>
								    <li>Current State Since: <b> {{ $details->stateTimeStamp }}</b> ({{ Carbon\Carbon::parse($details->stateTimeStamp)->diffForHumans() }})</li>
								    <li>Online Since: <b> {{ $details->onlineTimeStamp }}</b> ({{ Carbon\Carbon::parse($details->onlineTimeStamp)->diffForHumans() }})</li>
								</ul>
				                </p>
				               	{{-- tower specific information --}} 
				                {{-- stront is 3m3 a unit, fuel is 5m3  unit, this is why me multiply the quantities when calculating the bay usage --}}
								<table class="table table-condensed">
								    <tbody>
								    	<tr>
									        <th></th>
									        <th>Fuel Information</th>
									        <th style="width: 40px"></th>
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

