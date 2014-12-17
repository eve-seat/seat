<!-- header logo: style can be found in header.less -->
<header class="header">
  <a href="{{ URL::to('/') }}" class="logo">
    <!-- Add the class icon to your logo image or logo icon to add the margining -->
    <i class="fa fa-terminal"></i> {{ \App\Services\Settings\SettingHelper::getSetting('app_name') }}
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>
    <div class="navbar-right">
      <ul class="nav navbar-nav">
        <!-- Tasks: style can be found in dropdown.less -->
        @if(\Auth::hasAccess('queue_manager'))
          <li class="dropdown tasks-menu">
            <a href="{{ action('QueueController@getStatus') }}" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Queued Jobs">
              <i class="fa fa-truck"></i>
              <span class="label label-success" id="queue_count">0</span>
            </a>
          </li>
          <li class="dropdown tasks-menu">
            <a href="{{ action('QueueController@getStatus') }}" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Working Jobs">
              <i class="fa fa-exchange"></i>
              <span class="label label-warning" id="working_count">0</span>
            </a>
          </li>
          <li class="dropdown tasks-menu">
            <a href="{{ action('QueueController@getStatus') }}" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Errored Jobs">
              <i class="fa fa-exclamation"></i>
              <span class="label label-danger" id="error_count">0</span>
            </a>
          </li>
        @endif
        <li class="dropdown tasks-menu">
          <a href="{{ action('NotificationController@getAll') }}" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Notifications">
            <i class="fa fa-bolt"></i>
            <span class="label label-info" id="notification_count">0</span>
          </a>
        </li>

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="glyphicon glyphicon-user"></i>
            <span>{{ \Auth::User()->email }} <i class="caret"></i></span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            @if(\App\Services\Settings\SettingHelper::getSetting('color_scheme') == "blue")
              <li class="user-header bg-light-blue">
            @else
              <li class="user-header bg-black">
            @endif
              {{-- See SettingHelper why this has to be more than 1 --}}
              @if(App\Services\Settings\SettingHelper::getSetting('main_character_id') > 1)
                <img src="{{ App\Services\Helpers\Helpers::generateEveImage( App\Services\Settings\SettingHelper::getSetting('main_character_id'), 32) }}" class="img-circle" alt="User Image" />
              @else
                <img src="//image.eveonline.com/Character/1_32.jpg" class="img-circle" alt="User Image" />
              @endif
                <p>
                  {{ \Auth::User()->email }}
                  <small>Joined: {{ \Auth::User()->created_at }}</small>
                </p>
              </li>

            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                {{ HTML::linkAction('ProfileController@getView', 'Profile', array(), array('class' => 'btn btn-default btn-flat')) }}
              </div>
              <div class="pull-right">
                {{ HTML::linkAction('SessionController@getSignOut', 'Sign out', array(), array('class' => 'btn btn-default btn-flat')) }}
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
