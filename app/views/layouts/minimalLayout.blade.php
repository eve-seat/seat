<!DOCTYPE html>
<html class="bg-black">
  <head>
    <meta charset="UTF-8">
    <title>
      @if (trim($__env->yieldContent('html_title')))
        @yield('html_title') |
      @endif
      {{ \App\Services\Settings\SettingHelper::getSetting('app_name') }}
    </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- bootstrap 3.0.2 -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- font Awesome -->
    <link href="{{ URL::asset('assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Theme style -->
    <link href="{{ URL::asset('assets/css/app.css') }}" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="bg-black">

    <section class="content">

      @if ($errors->has())
          @foreach ($errors->all() as $error)
              <div class="alert alert-danger alert-dismissable">
                  <i class="fa fa-ban"></i>
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <b>Error!</b> {{ $error }}
              </div>
          @endforeach
      @endif

      @if(Session::has('success'))
          <div class="alert alert-success alert-dismissable">
              <i class="fa fa-check"></i>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <b>Success!</b> {{ Session::get('success') }}
          </div>
      @endif

      @if(Session::has('warning'))
          <div class="alert alert-warning alert-dismissable">
              <i class="fa fa-warning"></i>
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <b>Warning!</b> {{ Session::get('warning') }}
          </div>
      @endif

      <!-- site content -->
      @yield('page_content')

    </section>

    <!-- jQuery 2.0.2 -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>

    </body>

</html>
