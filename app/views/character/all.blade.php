@extends('layouts.masterLayout')

@section('html_title', 'All Characters')

@section('page_content')


 <div class="box">


	<div class="box-header">
	    <h3 class="box-title">All Characters @if (count($characters) > 0) ({{ count($characters) }}) @endif</h3>
	    <div class="box-tools">
	        <div class="input-group">
	            <input type="text" name="table_search" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search">
	            <div class="input-group-btn">
	                <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
	            </div>
	        </div>
	    </div>
	</div>

    <div class="box-body">

		@foreach(array_chunk($characters, 4) as $character_row)
			<div class="row">
				@foreach($character_row as $character)
					<a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}" style="color:inherit;">
						<div class="col-md-1">
							<img src="//image.eveonline.com/Character/{{ $character->characterID }}_64.jpg" class="img-circle">
						</div>
						<div class="col-md-2">
							<ul class="list-unstyled">
								<li><b>Name: </b>{{ $character->characterName }}</li>
								<li><b>Corp: </b>{{ $character->corporationName }}</li>
								<li>
									@if (strlen($character->trainingEndTime) > 0)
										<b>Training Ends: </b> {{ Carbon\Carbon::parse($character->trainingEndTime)->diffForHumans() }}
									@endif
								</li>
								{{-- key information, if available --}}
								@if (!empty($character_info) && array_key_exists($character->characterID, $character_info))

									{{-- skillpoints --}}
									@if (!empty($character_info[$character->characterID]->skillPoints))
										<li><b>Skillpoints:</b> {{ number_format($character_info[$character->characterID]->skillPoints, 0, '.', ' ') }}</li>
									@endif
									{{-- ship type --}}
									@if (!empty($character_info[$character->characterID]->shipTypeName))
										<li><b>Ship:</b> {{ $character_info[$character->characterID]->shipTypeName }}</li>
									@endif
									{{-- last location --}}
									@if (!empty($character_info[$character->characterID]->lastKnownLocation))
										<li><b>Last Location:</b> {{ $character_info[$character->characterID]->lastKnownLocation }}</li>
									@endif
								@endif
								<li>
									@if ($character->isOk == 1)
										<span class="text-green"><i class="fa fa-check"></i> Key Ok</span>
									@else
										<span class="text-red"><i class="fa fa-times"></i> Key not Ok</span>
									@endif
								</li>
							</ul>
						</div>
					</a>
				@endforeach	
			</div>
			<hr>
		@endforeach

    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop
