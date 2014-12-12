@extends('layouts.minimalLayout')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header">404</div>

    <div class="body bg-gray">
      <h1>Page not found...</h1>
    </div>
    <div class="footer">
      <p>You may have clicked an invalid or expired link, or are trying to find something that simply does not exist.</p>
      <p><a href={{ url('/')}} class="btn btn-block bg-olive">Go home</a></p>
    </div>
  </div>

@stop
