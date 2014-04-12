@extends('layouts.masterLayout')

@section('html_title', 'Corporation Member Tracking')

@section('page_content')

 <div class="box">
	<div class="box-header">
	    <h3 class="box-title">All Members @if (count($members) > 0) ({{ count($members) }}) @endif</h3>
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

		@foreach(array_chunk($members, 4) as $character_row)
			<div class="row">
				@foreach($character_row as $character)
					<div class="col-md-1">
						<a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}">
							<img src="http://image.eveonline.com/Character/{{ $character->characterID }}_64.jpg" class="img-circle pull-right">
						</a>
					</div>
					<div class="col-md-2">
						<ul class="list-unstyled">
							<li>
								<b>Name: </b>{{ $character->name }}
								@if (strlen($character->title) > 0) {{-- display the title if its set --}}
									<small><span class="text-muted">{{ $character->title }}</span></small>
								@endif
							</li>
							<li><b>Joined: </b>{{ Carbon\Carbon::parse($character->startDateTime)->diffForHumans() }}</li>
							<li><b>Last Logon: </b>{{ Carbon\Carbon::parse($character->logonDateTime)->diffForHumans() }}</li>
							<li><b>Last Logoff: </b>{{ Carbon\Carbon::parse($character->logoffDateTime)->diffForHumans() }}</li>
							<li><b>Location: </b>{{ $character->location }}</li>
							<li><b>Ship: </b>{{ $character->shipType }}</li>
							<li>
								@if ($character->isOk == 1)
									<span class="text-green"><i class="fa fa-check"></i> Key Ok</span>
								@else
									<span class="text-red"><i class="fa fa-times"></i> Key not Ok</span>
								@endif
								@if (strlen($character->keyID) > 0)
									<a href="{{ action('ApiKeyController@getDetail', array('keyID' => $character->keyID)) }}" data-toggle="tooltip" title="" data-original-title="Key Details"><i class="fa fa-cog"></i></a>
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
