@extends('layouts.minimalLayout')
@section('page_content')
    <div class="form-box" id="login-box">
        <div class="header">Register</div>

            <div class="body bg-gray">
                <h2>Signups are enabled.</h2>
            </div>
            <div class="footer">
                <p>
                    Please fill out the form below
                </p>
                <p>
                    <a href={{ url('/')}} class="btn btn-block bg-olive">Go home</a>
                </p>
            </div>
    </div>
@stop
