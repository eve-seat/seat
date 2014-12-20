<div class="row">
  <div class="col-md-12">
      <!-- Custom Tabs -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#res_characters" data-toggle="tab">Characters ({{ count($characters) }})</a></li>
            <li><a href="#res_character_assets" data-toggle="tab">Character Assets ({{ count($character_assets) }})</a></li>
            <li><a href="#res_character_contacts" data-toggle="tab">Character Contacts ({{ count($character_contactlist) }})</a></li>
            <li><a href="#res_character_mail" data-toggle="tab">Character Mail ({{ count($character_mail) }})</a></li>
            <li><a href="#res_character_standings" data-toggle="tab">Character Standings ({{ count($character_standings) }})</a></li>
            <li><a href="#res_corporation_assets" data-toggle="tab">Corporation Assets ({{ count($corporation_assets) }})</a></li>
            <li><a href="#res_corporation_standings" data-toggle="tab">Corporation Standings ({{ count($corporation_standings) }})</a></li>
            <li class="pull-right header">
              <span onclick="window.location.reload();" class="btn btn-default">Clear Search</span>
            </li>
        </ul>
        <div class="tab-content">

          {{-- Characters --}}
          <div class="tab-pane active" id="res_characters">
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
          </div><!-- /.tab-pane -->

          {{-- Character Assets --}}
          <div class="tab-pane" id="res_character_assets">
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
          </div><!-- /.tab-pane -->

          {{-- Character Contact List --}}
          <div class="tab-pane" id="res_character_contacts">
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
          </div><!-- /.tab-pane -->

          {{-- Character Mail --}}
          <div class="tab-pane" id="res_character_mail">
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
          </div><!-- /.tab-pane -->

          {{-- Character Standings --}}
          <div class="tab-pane" id="res_character_standings">
            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Found {{ count($character_standings) }} character(s) with standings:</small></h3>
              </div>

              <div class="box-body">
                <table class="table table-condensed compact table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Corporation</th>
                      <th>Faction Name</th>
                      <th>Standing</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($character_standings as $standing)

                      <tr>
                        <td>
                          <a href="{{ action('CharacterController@getView', array('characterID' => $standing->characterID)) }}">
                            <img src='//image.eveonline.com/Character/{{ $standing->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                            {{ App\Services\Helpers\Helpers::highlightKeyword($standing->characterName, $keyword) }}
                          </a>
                        </td>
                        <td>{{ $standing->corporationName }}</td>
                        <td>{{ App\Services\Helpers\Helpers::highlightKeyword($standing->fromName, $keyword) }}</td>
                        <td>{{ number_format($standing->standing, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div>
            </div>
          </div><!-- /.tab-pane -->

          {{-- Corporation Assets --}}
          <div class="tab-pane" id="res_corporation_assets">
            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Found {{ count($corporation_assets) }} asset(s):</h3>
              </div>

              <div class="box-body">
                <table class="table table-condensed compact table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Corporation</th>
                      <th>Assets</th>
                      <th>Quantity</th>
                      <th>Location</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($corporation_assets as $asset)

                      <tr>
                        <td>
                          @if(array_key_exists($asset->corporationID, $corporation_names))
                            {{ $corporation_names[$asset->corporationID] }}
                          @else
                            Unknown
                          @endif
                        </td>
                        <td>{{ App\Services\Helpers\Helpers::highlightKeyword($asset->typeName, $keyword) }}</td>
                        <td>{{ $asset->quantity }}</td>
                        <td>{{ $asset->location }}</td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div>
            </div>
          </div><!-- /.tab-pane -->

          {{-- Corporation Standings --}}
          <div class="tab-pane" id="res_corporation_standings">
            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Found {{ count($corporation_standings) }} corporations(s) with standings:</small></h3>
              </div>

              <div class="box-body">
                <table class="table table-condensed compact table-hover" id="datatable">
                  <thead>
                    <tr>
                      <th>Corporation</th>
                      <th>Faction Name</th>
                      <th>Standing</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($corporation_standings as $standing)

                      <tr>
                        <td>
                          @if(array_key_exists($standing->corporationID, $corporation_names))
                            {{ $corporation_names[$standing->corporationID] }}
                          @else
                            Unknown
                          @endif
                        </td>
                        <td>{{ App\Services\Helpers\Helpers::highlightKeyword($standing->fromName, $keyword) }}</td>
                        <td>{{ number_format($standing->standing, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
                      </tr>

                    @endforeach

                  </tbody>
                </table>
              </div>
            </div>
          </div><!-- /.tab-pane -->

      </div><!-- /.tab-content -->
    </div><!-- nav-tabs-custom -->
  </div>
</div>
