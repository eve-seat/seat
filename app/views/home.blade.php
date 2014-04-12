@extends('layouts.masterLayout')

@section('html_title', 'Home')

@section('page_content')

<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>
                	{{ Carbon\Carbon::parse($server->currentTime)->diffForHumans() }}
                </h3>
                <p>
                    Last Server Status Check
                </p>
            </div>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>
                    {{ $server->onlinePlayers }}
                </h3>
                <p>
                    Online Players
                </p>
            </div>
        </div>
    </div><!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>
                    {{ $server->serverOpen }}
                </h3>
                <p>
                	Server Online?  
                </p>
            </div>
        </div>
    </div><!-- ./col -->
</div>
@stop
