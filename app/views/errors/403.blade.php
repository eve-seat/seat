@extends('layouts.minimalLayout')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header header-red">403</div>

    <div class="body bg-gray">
      <h1>Sorry! But I can't let you see that.</h1>
    </div>
    <div class="footer">
      If you feel that this may have been an error, please provide your administrator with
      the below string so that they may be able to debug further:

      <div>
        <pre>{{ Cache::get('last_error_ref') }}</pre>
      </div>

      {{-- <p><a href={{ url('/')}} class="btn btn-block">Go home</a></p> --}}
    </div>
  </div>

@stop
