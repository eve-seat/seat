@extends('layouts.masterLayout')

@section('html_title', 'All Corporation Industry')

@section('page_content')

  @foreach ($corporations as $corp)

    <div class="col-md-4">
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $corp->corporationName }}</h3>
          <p>From character: {{ $corp->characterName }}</p>
        </div>
        <div class="icon">
          <img src="{{ App\Services\Helpers\Helpers::generateEveImage($corp->corporationID, 32) }}" class="img-circle" />
        </div>
        <a href="{{ action('CorporationController@getIndustry', array('corporationID' => $corp->corporationID)) }}" class="small-box-footer">
          View Corporation Jobs <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>

  @endforeach

@stop
