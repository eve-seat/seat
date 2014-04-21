@extends('layouts.masterLayout')

@section('html_title', 'All Corporations Assets')

@section('page_content')

@foreach ($corporations as $corp)
	<div class="small-box bg-blue col-md-4">
	    <div class="inner">
	        <h3>
	            {{ $corp->corporationName }}
	        </h3>
	        <p>
	            From character: {{ $corp->characterName }}
	        </p>
	    </div>
        <div class="icon">
            <i class="fa fa-money"></i>
        </div>
	    <a href="{{ action('CorporationController@getAssets', array('corporationID' => $corp->corporationID)) }}" class="small-box-footer">
	        View Assets <i class="fa fa-arrow-circle-right"></i>
	    </a>
	</div>
@endforeach

@stop
