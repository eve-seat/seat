@extends('layouts.masterLayout')

@section('html_title', 'Help & About')

@section('page_content')

  <div class="row">
    <div class="col-md-6">
      <div class="box box-solid box-primary">
        <div class="box-header">
          <h3 class="box-title">Getting Help <small>with SeAT only sorry >:)</small></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
            <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <h4>If you are totally stuck, have feature ideas etc, use the following channels to get into contact!</h4>
          <ul>
            <li><a href="http://eve-seat.github.io/seat/">SeAT</a> the SeAT Project</li>
            <li><a href="https://kiwiirc.com/client/irc.coldfront.net/?nick=seat_user%7C?#wcs-pub">IRC</a> <b>(preferred method)</b> for general information and help</li>
            <li><a href="https://github.com/eve-seat/seat/tree/master/docs">Documentation</a> for install &amp; upgrade guides</li>
            <li><a href="https://forums.eveonline.com/default.aspx?g=posts&t=336800&find=unread">EVE-O Forum Post</a> for general information and help</li>
            <li><a href="https://github.com/eve-seat/seat/issues">Github Issues</a> for SeAT specific errors</li>
          </ul>
          <ul>
            <li><a href="https://twitter.com/qu1ckkkk">@qu1ckkkk</a> on twitter for general crap, oh and SeAT announcements</li>
            <li><a href="https://gate.eveonline.com/Profile/qu1ckkkk">qu1ckkkk</a> ingame Character</li>
          </ul>
        </div><!-- /.box-body -->
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-solid box-primary">
        <div class="box-header">
          <h3 class="box-title">Version Information</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-primary btn-sm"><i class="fa fa-minus" id="collapse-box"></i></button>
            <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">

          @if ($versions_behind > 0)

            <br>
            <div class="alert alert-warning alert-dismissable">
              <i class="fa fa-warning"></i>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <b>Hmm...</b> Your install of SeAT is <b>{{ $versions_behind }}</b> version(s) behind! You should consider upgrading :)<br>
              You are running version <b>{{ \Config::get('seat.version') }}</b> and version <b>{{ $release_data[0]->tag_name }}</b> is available.
            </div>

            @if (is_array($release_data))

              @foreach (array_slice($release_data, 0, $versions_behind) as $release)

                <div class="box box-solid">
                  <div class="box-header">
                    <h3 class="box-title"> {{ $release->name }} </h3>
                  </div><!-- /.box-header -->
                  <div class="box-body">
                    <p>
                      Released: {{ Carbon\Carbon::parse($release->published_at)->diffForHumans() }} by {{ $release->author->login }}
                      <span class="pull-right"><a href="https://github.com/eve-seat/seat/blob/master/docs/UPGRADING.md">Upgrade Guide</a></span>
                    </p>
                    <pre>{{ $release->body }}</pre>
                  </div><!-- /.box-body -->
                </div>

              @endforeach

            @else
              Unable to get realease information. Github could not be successfully reached.
            @endif

          @else
            <br>
            <div class="alert alert-success alert-dismissable">
              <i class="fa fa-check"></i>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              You are running version <b>{{ \Config::get('seat.version') }}</b> of SeAT which is the latest.
            </div>
          @endif

        </div><!-- /.box-body -->
      </div>
    </div>
  </div>

@stop
