@extends('layouts.masterLayout')

@section('html_title', 'Home')

@section('page_content')
  <div class="row">
    <div class="col-md-8">
      <a href="{{ action('ApiKeyController@getNewKey') }}" type="button" class="btn btn-primary btn-lg"><i class="fa fa-plus"></i> Add a new API Key</a>
      <a href="{{ action('ApiKeyController@getAll') }}" type="button" class="btn btn-success btn-lg"><i class="fa fa-list"></i> List All Keys</a>
    </div>

    {{-- check if the server information is available and display iy --}}
    @if (isset($server))
      <div class="col-md-4">
        <dl class="dl-horizontal">
          <dt>Server Online</dt>
          <dd>{{ $server->serverOpen }}</dd>
          <dt>Online Players</dt>
          <dd>{{ $server->onlinePlayers }}</dd>
          <dt>Last Checked</dt>
          <dd>{{ Carbon\Carbon::parse($server->currentTime)->diffForHumans() }}</dd>
        </dl>
      </div>
    @endif
  </div>

  <hr>

  <div class="row">

    <div class="col-lg-3 col-xs-12">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $total_keys }}</h3>
          <p>Total Recorded API Keys</p>
        </div>
        <div class="icon">
          <i class="fa fa-key"></i>
        </div>
        <a href="{{ action('ApiKeyController@getAll') }}" class="small-box-footer">
          All Keys <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-3 col-xs-12">
      <!-- small box -->
      <div class="small-box bg-purple">
        <div class="inner">
          <h3>{{ $total_characters }}</h3>
          <p>Total Character Sheets</p>
        </div>
        <div class="icon">
          <i class="fa fa-users"></i>
        </div>
        <a href="{{ action('CharacterController@getAll') }}" class="small-box-footer">
          All Characters <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div><!-- ./col -->

    <div class="col-lg-3 col-xs-12">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ number_format($total_isk , 2, '.', ' ') }}</h3>
          <p>Total Recorded ISK</p>
        </div>
        <div class="icon">
          <i class="fa fa-money"></i>
        </div>
        <a href="{{ action('CharacterController@getAll') }}" class="small-box-footer">
          All Characters <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-12">
      <!-- small box -->
      <div class="small-box bg-maroon">
        <div class="inner">
          <h3>{{ number_format($total_skillpoints , 0, '.', ' ') }}</h3>
          <p>Total Recorded Skillpoints</p>
        </div>
        <div class="icon">
          <i class="fa fa-align-justify"></i>
        </div>
        <a href="{{ action('CharacterController@getAll') }}" class="small-box-footer">
          All Characters <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div><!-- ./col -->

  </div>
@stop
