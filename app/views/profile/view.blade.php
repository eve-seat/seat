@extends('layouts.masterLayout')

@section('html_title', 'User Profile')

@section('page_content')

	<div class="col-md-6">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    <h3 class="panel-title">
		    	<b>{{ $user->email }}</b>
		    	<span class="pull-right">
		    		Last Login: {{ $user->last_login }} ({{ Carbon\Carbon::parse($user->last_login)->diffForHumans() }})
		    	</span>
		    </h3>
		  </div>
		  <div class="panel-body">

		  	@foreach($groups as $group)
		  		{{ $group->name }}<br>
		  	@endforeach

		  </div>
		  <div class="panel-footer">
		  	{{ $key_count }} Owned API Keys
		  	<span class="pull-right">
		  		@if (Sentry::getUser()->isSuperUser())
		  			<span class="label label-danger">Administrator Account</span>
		  		@endif
		  	</span>
		  </div>
		</div>
	</div>

@stop
