@extends('layouts.masterLayout')

@section('html_title', 'Mail Timeline')

@section('page_content')

  <!-- override the CCP styles -->
  <style>
    #mail {
      font-size: 1.0em;
      line-height: 100%;
    }
    #mail font {
      font-size: inherit;
      color: #000000 !important;
    }
  </style>

  {{-- open a empty form to get a crsf token --}}
  {{ Form::open(array()) }} {{ Form::close() }}

  <div class="row">
    <div class="col-md-12">

        {{-- display day changes from this reference day --}}
        {{-- */$day = Carbon\Carbon::now()->dayOfYear;/* --}}

        @foreach ($mail as $message)

          @if ( Carbon\Carbon::parse($message->sentDate)->dayOfYear < $day)
            {{-- */$day = Carbon\Carbon::parse($message->sentDate)->dayOfYear;/* --}}
            <p>
              <a href="#{{ Carbon\Carbon::parse($message->sentDate)->toDateString() }}" id="{{ Carbon\Carbon::parse($message->sentDate)->toDateString() }}" class="btn bg-navy">
                <i class="fa fa-calendar"></i> Mail from from the day
                {{ Carbon\Carbon::parse($message->sentDate)->toFormattedDateString() }}
                which was about
                {{ Carbon\Carbon::parse($message->sentDate)->diffForHumans() }}
              </a>
            </p>
          @endif

          <div class="box">
            <div class="box-header">
              <h4 class="box-title">
                <i class="fa fa-envelope-o"></i> {{ $message->title }}<br>
                <small>
                  <b>From:</b>
                  <a href="{{ action('CharacterController@getView', array('characterID' => $message->senderID)) }}">
                    <img src='//image.eveonline.com/Character/{{ $message->senderID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                  </a>
                  {{ $message->senderName }} sent about {{ Carbon\Carbon::parse($message->sentDate)->diffForHumans() }}
                  @ {{ $message->sentDate }}

                  |<!-- determine the recipient information to display -->

                  {{-- corporations --}}
                  @if (strlen($message->toCorpOrAllianceID) > 0 && count(explode(',', $message->toCorpOrAllianceID)) > 0)
                    <b>To Corp/Alliance:</b>

                      @foreach (explode(',', $message->toCorpOrAllianceID) as $corp_alliance)

                        <img src='{{ App\Services\Helpers\Helpers::generateEveImage($corp_alliance, 32) }}' class='img-circle' style='width: 18px;height: 18px;'>
                        <span rel="id-to-name">{{ $corp_alliance }}</span>

                      @endforeach

                  @endif

                  {{-- characters --}}
                  @if (strlen($message->toCharacterIDs) > 0 && count(explode(',', $message->toCharacterIDs)) > 0)
                    <b>To Characters:</b>

                      @foreach (explode(',', $message->toCharacterIDs) as $characterID)

                        <a href="{{ action('CharacterController@getView', array('characterID' => $characterID)) }}">
                          <img src='//image.eveonline.com/Character/{{ $characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                        </a>
                        <span rel="id-to-name">{{ $characterID }}</span>

                      @endforeach

                  @endif

                  {{-- mailing lists --}}
                  @if (strlen($message->toListID) > 0 && count(explode(',', $message->toListID)) > 0)
                    <b>To Mailing List:</b>

                      @foreach (explode(',', $message->toListID) as $list)

                        @if(array_key_exists($list, $mailing_list_names))
                            {{ $mailing_list_names[$list] }}
                        @else
                            Unknown Mailing List {{ $list }}
                        @endif

                      @endforeach

                  @endif

                </small>
              </h4>
            </div>

            <div class="box-body">
              <div id="mail">
                {{ $message->body }}
              </div>
            </div><!-- /.box-body -->

            <div class="box-footer clearfix">
              <div class="pull-right">
                Receiver Type(s):
                @if (strlen($message->toCorpOrAllianceID) > 0)
                  <b>{{ count(explode(',', $message->toCorpOrAllianceID)) }}</b> Corporation(s) / Alliance(s)
                @endif
                @if (strlen($message->toCharacterIDs) > 0)
                  <b>{{ count(explode(',', $message->toCharacterIDs)) }}</b> Character(s)
                @endif
                @if (strlen($message->toListID) > 0)
                  <b>{{ count(explode(',', $message->toListID)) }}</b> Mailing List(s)
                @endif
                | {{ HTML::linkAction('MailController@getRead', 'Permalink', array('messageID' => $message->messageID ), array('class' => 'btn btn-primary btn-xs')) }}
              </div>
            </div>
          </div>

        @endforeach

      <div class="pull-right">{{ $mail->links() }}</div>

    </div>
  </div>

@stop

@section('javascript')

  <script>

    $(document).ready(function() {

      var items = [];
      var arrays = [], size = 250;

      $('[rel="id-to-name"]').each( function(){
       //add item to array
       items.push( $(this).text() );
      });

      var items = $.unique( items );

      while (items.length > 0)
        arrays.push(items.splice(0, size));

      $.each(arrays, function( index, value ) {

        $.ajax({
          type: 'POST',
          url: "{{ action('HelperController@postResolveNames') }}",
          data: {
            'ids': value.join(',')
          },
          success: function(result){
            $.each(result, function(id, name) {

              $("span:contains('" + id + "')").html(name);
            })
          },
          error: function(xhr, textStatus, errorThrown){
           console.log(xhr);
           console.log(textStatus);
           console.log(errorThrown);
         }
        });
      });
    });
  </script>

@stop
