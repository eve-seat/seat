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
					@if($character->isOk == 1)<a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID )) }}" style="color:inherit">@endif
						<div class="col-md-1">
								<img src="//image.eveonline.com/Character/{{ $character->characterID }}_64.jpg" class="img-circle pull-right">
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
								@if(Carbon\Carbon::parse($character->logonDateTime)->lt(Carbon\Carbon::now()->subMonth()))
									<span class="text-red">Over a month since last login</span>
								@endif

								{{-- key information, if available --}}
								@if (!empty($member_info) && array_key_exists($character->characterID, $member_info))

									{{-- skillpoints --}}
									@if (!empty($member_info[$character->characterID]->skillPoints))
										<li><b>Skillpoints:</b> {{ number_format($member_info[$character->characterID]->skillPoints, 0, '.', ' ') }}</li>
									@endif
									{{-- ship type --}}
									@if (!empty($member_info[$character->characterID]->shipTypeName))
										<li><b>Ship:</b> {{ $member_info[$character->characterID]->shipTypeName }}</li>
									@endif
									{{-- last location --}}
									@if (!empty($member_info[$character->characterID]->lastKnownLocation))
										<li><b>Last Location:</b> {{ $member_info[$character->characterID]->lastKnownLocation }}</li>
									@endif
								@else
									{{-- use the information from the Corporation API --}}
									<li><b>Location: </b>{{ $character->location }}</li>
									<li><b>Ship: </b>{{ $character->shipType }}</li>
								@endif
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
					@if($character->isOk == 1)</a>@endif
				@endforeach
			</div>
			<hr>
		@endforeach

    </div><!-- /.box-body -->
</div><!-- /.box -->
@stop
