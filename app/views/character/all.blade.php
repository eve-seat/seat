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
					<div class="col-md-1">
						<a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}">
							<img src="http://image.eveonline.com/Character/{{ $character->characterID }}_64.jpg" class="img-circle">
						</a>
					</div>
					<div class="col-md-2">
						<ul class="list-unstyled">
							<li><b>Name: </b>{{ $character->characterName }}</li>
							<li><b>Corp: </b>{{ $character->corporationName }}</li>
							<li>
								@if ($character->isOk == 1)
									<span class="text-green"><i class="fa fa-check"></i> Key Ok</span>
								@else
									<span class="text-red"><i class="fa fa-times"></i> Key not Ok</span>
								@endif
							</li>
							<li>
								@if (strlen($character->trainingEndTime) > 0)
									<b>Training Ends: </b> {{ Carbon\Carbon::parse($character->trainingEndTime)->diffForHumans() }}
								@endif
							</li>
						</ul>
					</div>
				@endforeach	
			</div>
			<hr>
		@endforeach

    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop
