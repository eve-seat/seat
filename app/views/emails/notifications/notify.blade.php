@extends('emails.layouts.masterLayout')

@section('email_title', $title . ' notification')

@section('email_content')
  <p style="Margin-top: 0;font-weight: 400;color: #fff;font-family: sans-serif;font-size: 14px;line-height: 22px;Margin-bottom: 20px">
    Hello,
  </p>
  <p style="Margin-top: 0;font-weight: 400;color: #fff;font-family: sans-serif;font-size: 14px;line-height: 22px;Margin-bottom: 20px">
    You have received a new SeAT notification:
  </p>

  <ul style="Margin-top: 0;padding-left: 0;font-weight: 400;color: #fff;font-family: sans-serif;font-size: 14px;Margin-left: 48px;line-height: 22px;Margin-bottom: 20px">
    <li style="Margin-top: 0;padding-left: 13px;list-style-type: disc;list-style-position: outside;Margin-bottom: 10px">
      Notification type : {{ $type }}
    </li>
    <li style="Margin-top: 0;padding-left: 13px;list-style-type: disc;list-style-position: outside;Margin-bottom: 10px">
      Notification title: {{ $title }}
    </li>
  </ul>

  <p style="Margin-top: 0;font-weight: 400;color: #fff;font-family: sans-serif;font-size: 14px;line-height: 22px;Margin-bottom: 20px">
    The notification text is:
  </p>

  <h3 style="Margin-top: 0;font-weight: normal;color: #3d88fd;font-family: sans-serif;font-size: 16px;Margin-bottom: 12px;line-height: 28px">
    <strong style="font-weight: bold">
      {{ $text }}
    </strong>
  </h3>
@stop

@section('button_action')
<div class="btn" style="Margin-bottom: 20px;text-align: left">
  <![if !mso]>
    <a
      style="padding-top: 15px;padding-bottom: 15px;font-weight: 500;display: inline-block;font-size: 16px;line-height: 20px;text-align: center;text-decoration: none;color: #fff;transition: background-color 0.2s;background-color: #3d88fd;border-bottom: 3px solid #1d2227;font-family: sans-serif;width: 480px;padding-left: 20px;padding-right: 20px"
      href="{{ URL::action('NotificationController@getDetail', array('notificationID' => $notification_id)) }}"
    >
      View Notification Online
    </a>
  <![endif]>
  <!--[if mso]>
    <v:rect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ URL::action('NotificationController@getDetail', array('notificationID' => $notification_id)) }}" style="width:520px" fillcolor="#3D88FD" stroke="f"><v:shadow on="t" color="#1D2227" on="t" offset="0,3px"></v:shadow><v:textbox style="mso-fit-shape-to-text:t" inset="0px,15px,0px,15px"><center style="font-size:16px;line-height:20px;color:#FFFFFF;font-family:sans-serif;font-weight:500;mso-line-height-rule:exactly;mso-text-raise:1px">
      View Notification Online
    </center></v:textbox></v:rect>
  <![endif]-->
</div>

@stop
