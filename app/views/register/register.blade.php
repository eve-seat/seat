@extends('layouts.minimalLayout')

@section('page_content')

  <div class="form-box" id="login-box">
    <div class="header">Sorry</div>
    <div class="body bg-gray">
      <h2>Signups are currently not enabled.</h2>
    </div>
    <div class="footer">
      <p>Please come back later when it has been enabled.</p>
      <p> <a href="{{ URL::previous() }}" class="btn btn-block bg-olive">Back to Main Menu</a> </p>
    </div>
  </div>

@stop
