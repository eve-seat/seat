@extends('layouts.masterLayout')

@section('html_title', 'All Corporation Industry')

@section('page_content')

  @foreach (array_chunk($corporations, 2) as $corpSplitted)

    <div class="row">

      @foreach ($corpSplitted as $corp)

        <div class="col-md-4">
          <div class="small-box bg-blue">

            <div class="inner">
              <h3>{{ $corp->corporationName }}</h3>
              <p>From character: {{ $corp->characterName }}</p>
            </div>
            <div class="icon">
              <img src="http://image.eveonline.com/Corporation/{{ $corp->corporationID }}_32.png" class="img-circle" />
            </div>
            <a href="{{ action('CorporationController@getIndustry', array('corporationID' => $corp->corporationID)) }}" class="small-box-footer">
              View Corporation Jobs <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

      @endforeach

    </div>

  @endforeach

@stop
