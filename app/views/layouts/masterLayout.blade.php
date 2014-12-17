<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ URL::to('/') }}/favicon.ico" />
    <title>
      @if (trim($__env->yieldContent('html_title')))
          @yield('html_title') |
      @endif
      {{ \App\Services\Settings\SettingHelper::getSetting('app_name') }}
    </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Pace Loader - Load this guy asap to start indicating 'loading' -->
    <script src="{{ URL::asset('assets/js/pace.min.js') }}" type="text/javascript"></script>
    <!-- Pace style -->
    <link href="{{ URL::asset('assets/css/pace.css') }}" rel="stylesheet" type="text/css">

    <!-- bootstrap 3.0.2 -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- font Awesome -->
    <link href="{{ URL::asset('assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <!-- select2 -->
    <link href="{{ URL::asset('assets/css/select2.css') }}" rel="stylesheet" type="text/css">
    <!-- select2 bootstrap hax -->
    <link href="{{ URL::asset('assets/css/select2-bootstrap.css') }}" rel="stylesheet" type="text/css">
    <!-- datatables bootstrap  -->
    <link href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
    <!-- Theme style -->
    <link href="{{ URL::asset('assets/css/app.css') }}" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="skin-{{ \App\Services\Settings\SettingHelper::getSetting('color_scheme') }} fixed">

    @include('layouts.components.header')

    <div class="wrapper row-offcanvas row-offcanvas-left">

      @include('layouts.components.sidebar')

      <!-- Right side column. Contains the navbar and content of the page -->
      <aside class="right-side">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            @if (trim($__env->yieldContent('html_title')))
              @yield('html_title')
            @endif
            <small class="pull-right">SeAT v{{ Config::get('seat.version') }}</small>
          </h1>

          <!-- Laater ™
          <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Examples</a></li>
              <li class="active">Blank page</li>
          </ol>
          -->
        </section>

        <!-- Main content -->
        <section class="content" id="main-content">

          <!-- flash messages -->
          @include('layouts.components.flash')

          <!-- sub view contect -->
          @yield('page_content')

        </section><!-- /.content -->
      </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

    <!-- jQuery 2.1.0 -->
    <!-- // <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script> -->
    <script src="{{ URL::asset('assets/js/jquery-2.1.0.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <!-- Bootbox -->
    <script src="{{ URL::asset('assets/js/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- highcharts -->
    <script src="//code.highcharts.com/highcharts.js"></script>
    <!-- datatables -->
    <script src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
    <!-- select2 -->
    <script src="{{ URL::asset('assets/js/select2.min.js') }}" type="text/javascript"></script>
    <!--slimscroll -->
    <script src="{{ URL::asset('assets/js/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <!-- app -->
    <script src="{{ URL::asset('assets/js/app.js') }}" type="text/javascript"></script>

    <!-- cant put this in app.js as we need the proper URL where this application lives :< -->
    <script type="text/javascript">

      // Periodically check the top status bar for queue related
      // information
      (function worker() {
        $.ajax({
          type: "get",
          url: "{{ URL::to('queue/short-status') }}",
          success: function(data) {
            $("span#queue_count").text(data.queue_count);
            $("span#working_count").text(data.working_count);
            $("span#error_count").text(data.error_count);
          },
          complete: function() {
            // Schedule the next request when the current one's complete
            setTimeout(worker, 10000); // 10 Seconds
          }
        });
      })();

      // Periodically check the top status bar for notication
      // related information
      (function worker() {
        $.ajax({
          type: "get",
          url: "{{ URL::to('notification/short-status') }}",
          success: function(data) {
            $("span#notification_count").text(data.notification_count);
          },
          complete: function() {
            // Schedule the next request when the current one's complete
            setTimeout(worker, 10000); // 10 Seconds
          }
        });
      })();

      // Specify the location of the search controller
      var search_location = "{{ action('DashboardController@getSearch') }}"

    </script>

    <!-- view specific js -->
    @yield('javascript')

  </body>
</html>
