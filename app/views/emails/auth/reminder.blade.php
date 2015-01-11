@extends('emails.layouts.masterLayout')

@section('email_title', 'SeAT Password Reset')

@section('email_content')
  <p style="Margin-top: 0;font-weight: 400;color: #fff;font-family: sans-serif;font-size: 14px;line-height: 22px;Margin-bottom: 20px">
    Hello,
  </p>
  <p style="Margin-top: 0;font-weight: 400;color: #fff;font-family: sans-serif;font-size: 14px;line-height: 22px;Margin-bottom: 20px">
    A password reset for your SeAT account has been requested. Please click the below link to reset your password. If this was not requested by you then you may safely disregard this email.
  </p>
@stop

@section('button_action')
<div class="btn" style="Margin-bottom: 20px;text-align: left">
  <![if !mso]>
    <a
      style="padding-top: 15px;padding-bottom: 15px;font-weight: 500;display: inline-block;font-size: 16px;line-height: 20px;text-align: center;text-decoration: none;color: #fff;transition: background-color 0.2s;background-color: #3d88fd;border-bottom: 3px solid #1d2227;font-family: sans-serif;width: 480px;padding-left: 20px;padding-right: 20px"
      href="{{ URL::action('RemindersController@getReset', array($token)) }}"
    >
      Reset My SeAT Password
    </a>
  <![endif]>
  <!--[if mso]>
    <v:rect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ URL::action('RemindersController@getReset', array($token)) }}" style="width:520px" fillcolor="#3D88FD" stroke="f"><v:shadow on="t" color="#1D2227" on="t" offset="0,3px"></v:shadow><v:textbox style="mso-fit-shape-to-text:t" inset="0px,15px,0px,15px"><center style="font-size:16px;line-height:20px;color:#FFFFFF;font-family:sans-serif;font-weight:500;mso-line-height-rule:exactly;mso-text-raise:1px">
      Reset My SeAT Password
    </center></v:textbox></v:rect>
  <![endif]-->
</div>

@stop
