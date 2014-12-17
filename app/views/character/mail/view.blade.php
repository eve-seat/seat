@extends('layouts.masterLayout')

@section('html_title', 'Character Mail Subjects')

@section('page_content')

  <div class="row">
    <div class="col-md-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">
            All Mail for:
            <a href="{{ action('CharacterController@getView', array('characterID' => $characterID)) }}">
              <img src='//image.eveonline.com/Character/{{ $characterID }}_32.jpg' class='img-circle'>
            </a>
            {{ $character_name }}
          </h3>
          <div class="box-tools">
            <ul class="pagination pagination-sm no-margin pull-right">
              {{ $mail->links() }}
            </ul>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body">
          <table class="table table-hover table-condensed compact" id="datatable">
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Subject</th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              @foreach ($mail as $message)

                <tr>
                  <td>{{ $message->messageID }}</td>
                  <td data-order="{{ $message->sentDate }}">
                    <span data-toggle="tooltip" title="" data-original-title="{{ $message->sentDate }}">
                      {{ Carbon\Carbon::parse($message->sentDate)->diffForHumans() }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ action('CharacterController@getView', array('characterID' => $message->senderID)) }}">
                      <img src='//image.eveonline.com/Character/{{ $message->senderID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                    </a>
                    {{ $message->senderName }}
                  </td>
                  <td>
                    @if (strlen($message->toCorpOrAllianceID) > 0)
                      <b>{{ count(explode(',', $message->toCorpOrAllianceID)) }}</b> Corporation(s) / Alliance(s)
                    @endif
                    @if (strlen($message->toCharacterIDs) > 0)
                      <b>{{ count(explode(',', $message->toCharacterIDs)) }}</b> Character(s)
                    @endif
                    @if (strlen($message->toListID) > 0)
                      <b>{{ count(explode(',', $message->toListID)) }}</b> Mailing List(s)
                    @endif
                  </td>
                  <td><b>{{ $message->title }}</b></td>
                  <td>
                    {{ HTML::linkAction('MailController@getRead', 'Permalink', array('messageID' => $message->messageID ), array('class' => 'btn btn-primary btn-xs pull-right')) }}
                 </td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
        <div class="pull-right">{{ $mail->links() }}</div>
      </div>
    </div>
  </div>

@stop
