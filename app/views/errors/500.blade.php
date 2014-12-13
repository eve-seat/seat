@extends('layouts.minimalLayout')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header header-red">500</div>

    <div class="body bg-gray">
      <h1>Sorry! Something went wrong.</h1>
    </div>
    <div class="footer">
      <p>
        Chances are the site administrator will be super happy if you told him this, along with what you last did to get here.
        That way they could have a look at log files and try and fix it.
      </p>
      <!-- <p><a href={{ url('/')}} class="btn btn-block bg-olive">Go home</a></p> -->
    </div>
  </div>

@stop
