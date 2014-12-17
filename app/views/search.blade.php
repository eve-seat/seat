<div class="row">
  <div class="col-md-12">
    <h4>Search Results
      <span class="small pull-right">
        <span onclick="window.location.reload();" class="btn brn-default">Clear Search</span>
      </span>
    </h4>
  </div>
</div>

{{-- Characters --}}
@if(count($characters))
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Found {{ count($characters) }} character(s):</h3>
    </div>

    <div class="box-body">
      <table class="table table-condensed compact table-hover" id="datatable">
        <thead>
          <tr>
            <th>Character</th>
            <th>Corporation</th>
            <th>Wallet Balance</th>
            <th>Born</th>
          </tr>
        </thead>
        <tbody>

          @foreach ($characters as $character)

            <tr>
              <td>
                <a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID)) }}">
                  <img src='//image.eveonline.com/Character/{{ $character->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                  {{ App\Services\Helpers\Helpers::highlightKeyword($character->characterName, $keyword) }}
                </a>
              </td>
              <td>{{ $character->corporationName }}</td>
              <td>{{ $character->balance }}</td>
              <td>{{ Carbon\Carbon::parse($character->DoB)->diffForHumans() }}</td>
            </tr>

          @endforeach

        </tbody>
      </table>
    </div>
  </div>
@endif

{{-- Character Assets --}}
@if(count($character_assets))
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Found {{ count($character_assets) }} asset(s):</h3>
    </div>

    <div class="box-body">
      <table class="table table-condensed compact table-hover" id="datatable">
        <thead>
          <tr>
            <th>Character</th>
            <th>Corporation</th>
            <th>Assets</th>
            <th>Quantity</th>
            <th>Location</th>
          </tr>
        </thead>
        <tbody>

          @foreach ($character_assets as $character_asset)

            <tr>
              <td>
                <a href="{{ action('CharacterController@getView', array('characterID' => $character_asset->characterID)) }}">
                  <img src='//image.eveonline.com/Character/{{ $character_asset->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                  {{ $character_asset->characterName }}
                </a>
              </td>
              <td>{{ $character_asset->corporationName }}</td>
              <td>{{ App\Services\Helpers\Helpers::highlightKeyword($character_asset->typeName, $keyword) }}</td>
              <td>{{ $character_asset->quantity }}</td>
              <td>{{ $character_asset->location }}</td>
            </tr>

          @endforeach

        </tbody>
      </table>
    </div>
  </div>
@endif

{{-- Character Contact Lists --}}
@if(count($character_contactlist))
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Found {{ count($character_contactlist) }} character(s) with a contact:</h3>
    </div>

    <div class="box-body">
      <table class="table table-condensed compact table-hover" id="datatable">
        <thead>
          <tr>
            <th>Character</th>
            <th>Corporation</th>
            <th>Contact Name</th>
            <th>Watchlisted</th>
          </tr>
        </thead>
        <tbody>

          @foreach ($character_contactlist as $character_contact)

            <tr>
              <td>
                <a href="{{ action('CharacterController@getView', array('characterID' => $character_contact->characterID)) }}">
                  <img src='//image.eveonline.com/Character/{{ $character_contact->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                  {{ $character_contact->characterName }}
                </a>
              </td>
              <td>{{ $character_contact->corporationName }}</td>
              <td>{{ App\Services\Helpers\Helpers::highlightKeyword($character_contact->contactName, $keyword) }}</td>
              <td>
                @if($character_contact->inWatchlist == 1)
                  Yes
                @else
                  No
                @endif
              </td>
            </tr>

          @endforeach

        </tbody>
      </table>
    </div>
  </div>
@endif

{{-- Character Mail --}}
@if(count($character_mail))
  <div class="box">
    <div class="box-header">
      <h3 class="box-title">Found {{ count($character_mail) }} mail message(s) matching the keyword: <small>(mail bodies searched too)</small></h3>
    </div>

    <div class="box-body">
      <table class="table table-condensed compact table-hover" id="datatable">
        <thead>
          <tr>
            <th>Sender Name</th>
            <th>Sender Corporation</th>
            <th>Title</th>
            <th>Sent</th>
            <th>Body</th>
          </tr>
        </thead>
        <tbody>

          @foreach ($character_mail as $message)

            <tr>
              <td>
                <a href="{{ action('CharacterController@getView', array('characterID' => $message->senderID)) }}">
                  <img src='//image.eveonline.com/Character/{{ $message->senderID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                  {{ App\Services\Helpers\Helpers::highlightKeyword($message->senderName, $keyword) }}
                </a>
              </td>
              <td>{{ $message->corporationName }}</td>
              <td>{{ App\Services\Helpers\Helpers::highlightKeyword($message->title, $keyword) }}</td>
              <td>{{ $message->sentDate }}</td>
              <td>
                <a href="{{ action('MailController@getRead', array('messageID' => $message->messageID)) }}">Read Full Message</a>
              </td>
            </tr>

          @endforeach

        </tbody>
      </table>
    </div>
  </div>
@endif
