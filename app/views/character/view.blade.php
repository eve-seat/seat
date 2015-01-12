@extends('layouts.masterLayout')

@section('html_title', 'View Character')

@section('page_content')

  {{-- open a empty form to get a crsf token --}}
  {{ Form::open(array()) }} {{ Form::close() }}

  {{-- character information --}}
  <div class="row">
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-body">
          <p class="text-center">
            <img src='//image.eveonline.com/Character/{{ $character->characterID }}_256.jpg' class='img-circle'>
          </p>
          <p class="text-center lead">{{ $character->characterName }}</p>
        </div><!-- /.box-body -->
        <div class="box-footer text-center">
            <a target="_blank" title="View {{ $character->characterName }} on EVEBoard" data-toggle="tooltip" href="http://eveboard.com/pilot/{{ $character->characterName }}" ><img src="{{ URL::asset('assets/img/eveboard.png') }}"/></a>
            <a target="_blank" title="View {{ $character->characterName }} on EVE Gate" data-toggle="tooltip" href="https://gate.eveonline.com/Profile/{{ $character->characterName }}" ><img src="{{ URL::asset('assets/img/evegate.png') }}"/></a>
            <a target="_blank" title="View {{ $character->characterName }} on EVE-Kill" data-toggle="tooltip" href="https://eve-kill.net/?a=pilot_detail&plt_external_id={{ $character->characterID }}" ><img src="{{ URL::asset('assets/img/evekill.png') }}"/></a>
            <a target="_blank" title="View {{ $character->characterName }} on EVE-Search" data-toggle="tooltip" href="http://eve-search.com/search/author/{{ $character->characterName }}" ><img src="{{ URL::asset('assets/img/evesearch.png') }}"/></a>
            <a target="_blank" title="View {{ $character->characterName }} on EVE WHO" data-toggle="tooltip" href="http://evewho.com/pilot/{{ $character->characterName }}" ><img src="{{ URL::asset('assets/img/evewho.png') }}"/></a>
            <a target="_blank" title="View {{ $character->characterName }} on zKillboard" data-toggle="tooltip" href="https://zkillboard.com/character/{{ $character->characterID }}/" ><img src="{{ URL::asset('assets/img/zkillboard.png') }}"/></a>
        </div>
      </div><!-- ./box -->
    </div>
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-body">
          @if (count($other_characters) > 0)

            @foreach ($other_characters as $alt)

              <div class="row">
                <a href="{{ action('CharacterController@getView', array('characterID' => $alt->characterID )) }}" style="color:inherit;">
                  <div class="col-md-4">
                    <img src="//image.eveonline.com/Character/{{ $alt->characterID }}_64.jpg" class="img-circle">
                  </div>
                  <div class="col-md-8">
                    <ul class="list-unstyled">
                      <li><b>Name: </b>{{ $alt->characterName }}</li>
                      <li><b>Corp: </b>{{ $alt->corporationName }}</li>
                      <li>
                        @if (strlen($alt->trainingEndTime) > 0)
                          <b>Training Ends: </b> {{ Carbon\Carbon::parse($alt->trainingEndTime)->diffForHumans() }}
                        @endif
                      </li>
                    </ul>
                  </div>
                </a>
              </div><!-- ./row -->

            @endforeach

          @else
            No other known characters on this key.
          @endif

        </div><!-- /.box-body -->
        <div class="box-footer">
          <p class="text-center lead">{{ count($other_characters) }} other characters on this API Key</p>
        </div><!-- /.box-footer-->
      </div><!-- ./box -->
    </div>
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-body">
          @if (count($people) > 0)
            <div class="row">

              @foreach (array_chunk($people, (count($people) / 2) > 1 ? count($people) / 2 : 2) as $other_alts)

                <div class="col-md-6">
                  <ul class="list-unstyled">
                    @foreach ($other_alts as $person)
                      <li>
                        <a href="{{ action('CharacterController@getView', array('characterID' => $person->characterID)) }}">
                          <img src='//image.eveonline.com/Character/{{ $person->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                          {{ $person->characterName }}
                        </a>
                      </li>
                    @endforeach
                  </ul>
                </div>

              @endforeach

            </div> <!-- ./row -->
          @else
            No characters in a person group
          @endif

        </div><!-- /.box-body -->
        <div class="box-footer">
          <p class="text-center lead">{{ count($people) }} characters on this people group</p>
        </div><!-- /.box-footer-->
      </div><!-- ./box -->
    </div>
  </div>

  {{-- details such as character sheet, skills, mail, wallet etc etc here --}}
  <div class="row">
    <div class="col-md-12">
      <!-- Custom Tabs -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active" id="start"><a href="#character_sheet" data-toggle="tab" id="load-tab" a-tab-id="character_sheet">Character Sheet</a></li>
          <li><a href="#character_skills" data-toggle="tab" id="load-tab" a-tab-id="character_skills">Character Skills</a></li>
          <li id="journal_graph"><a href="#wallet_journal" data-toggle="tab" id="load-tab" a-tab-id="wallet_journal">Wallet Journal</a></li>
          <li><a href="#wallet_transactions" data-toggle="tab" id="load-tab" a-tab-id="wallet_transactions">Wallet Transactions</a></li>
          <li><a href="#mail" data-toggle="tab" id="load-tab" a-tab-id="mail">Mail</a></li>
          <li><a href="#notifications" data-toggle="tab" id="load-tab" a-tab-id="notifications">Notifications</a></li>
          <li><a href="#assets" data-toggle="tab" id="load-tab" a-tab-id="assets">Assets</a></li>
          <li><a href="#industry" data-toggle="tab" id="load-tab" a-tab-id="industry">Industry</a></li>
          <li><a href="#contacts" data-toggle="tab" id="load-tab" a-tab-id="contacts">Contacts</a></li>
          <li><a href="#contracts" data-toggle="tab" id="load-tab" a-tab-id="contracts">Contracts</a></li>
          <li><a href="#market_orders" data-toggle="tab" id="load-tab" a-tab-id="market_orders">Market Orders</a></li>
          <li><a href="#calendar_events" data-toggle="tab" id="load-tab" a-tab-id="calendar_events">Calendar Events</a></li>
          <li><a href="#character_standings" data-toggle="tab" id="load-tab" a-tab-id="character_standings">Standings</a></li>
          <li><a href="#killmails" data-toggle="tab" id="load-tab" a-tab-id="killmails">Kill Mails</a></li>
          <li><a href="#character_research" data-toggle="tab" id="load-tab" a-tab-id="character_research">Research Agents</a></li>
          <li><a href="#character_pi" data-toggle="tab" id="load-tab" a-tab-id="character_pi">Planetary Interaction</a></li>
          <li class="pull-right">
            <a href="{{ action('ApiKeyController@getDetail', array('keyID' => $character->keyID)) }}" class="text-muted" data-toggle="tooltip" title="" data-placement="top" data-original-title="API Key Details">
              <i class="fa fa-gear"></i>
            </a>
          </li>
        </ul>
        <div class="tab-content" id="tab-results">
        </div><!-- /.tab-content -->
      </div><!-- nav-tabs-custom -->
    </div><!-- ./col-md-12 -->
  </div><!-- ./row -->

@stop

@section('javascript')

  <script type="text/javascript">

    // Click the first tab on page load. We could probably add some logic here
    // to have the location hash read and that tab clicked
    $(document).ready(function() {
      $("li#start a").click();
    })

    // Bind a listener to the tabs which should load the required ajax for the
    // tab that is selected
    $("a#load-tab").click(function() {

      // Tab Ajax Locations are defined here
      var locations = {
        "character_sheet" : "{{ action('CharacterController@getAjaxCharacterSheet', array('characterID' => $character->characterID)) }}",
        "character_skills" : "{{ action('CharacterController@getAjaxSkills', array('characterID' => $character->characterID)) }}",
        "wallet_journal" : "{{ action('CharacterController@getAjaxWalletJournal', array('characterID' => $character->characterID)) }}",
        "wallet_transactions" : "{{ action('CharacterController@getAjaxWalletTransactions', array('characterID' => $character->characterID)) }}",
        "mail" : "{{ action('CharacterController@getAjaxMail', array('characterID' => $character->characterID)) }}",
        "notifications" : "{{ action('CharacterController@getAjaxNotifications', array('characterID' => $character->characterID)) }}",
        "assets" : "{{ action('CharacterController@getAjaxAssets', array('characterID' => $character->characterID)) }}",
        "industry" : "{{ action('CharacterController@getAjaxIndustry', array('characterID' => $character->characterID)) }}",
        "contacts" : "{{ action('CharacterController@getAjaxContacts', array('characterID' => $character->characterID)) }}",
        "contracts" : "{{ action('CharacterController@getAjaxContracts', array('characterID' => $character->characterID)) }}",
        "market_orders" : "{{ action('CharacterController@getAjaxMarketOrders', array('characterID' => $character->characterID)) }}",
        "calendar_events" : "{{ action('CharacterController@getAjaxCalendarEvents', array('characterID' => $character->characterID)) }}",
        "character_standings" : "{{ action('CharacterController@getAjaxStandings', array('characterID' => $character->characterID)) }}",
        "killmails" : "{{ action('CharacterController@getAjaxKillMails', array('characterID' => $character->characterID)) }}",
        "character_research" : "{{ action('CharacterController@getAjaxResearchAgents', array('characterID' => $character->characterID)) }}",
        "character_pi" : "{{ action('CharacterController@getAjaxPlanetaryInteraction', array('characterID' => $character->characterID)) }}"
      }

      // Populate the tab based on the url in locations
      $('div#tab-results')
        .html('<br><p class="lead text-center"><i class="fa fa-cog fa fa-spin"></i> Loading the request...</p>')
        .load(locations[$(this).attr("a-tab-id")], function() {

        if ($.fn.dataTable.isDataTable('table#datatable')) {
          $('table#datatable').DataTable();
        }
        else {
          table = $('table#datatable').DataTable( {
            paging: false
          });
        }
      });
    });

    // Events to be triggered when the ajax calls have compelted.
    $( document ).ajaxComplete(function() {

      // Update any outstanding id-to-name fields
      var items = [];
      var arrays = [], size = 250;

      $('[rel="id-to-name"]').each( function(){
        //add item to array
        if ($.isNumeric($(this).text())) {
          items.push( $(this).text() );
        }
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

    // Bind events to the HTML that we will be getting from the AJAX response
    $("div#tab-results").delegate('.viewcontent', 'click', function() {

      // Expandable assets & contracts views

      // get the tag direct after the button
      if($(this).hasClass('contracts')){
        // if we are in Contracts view, we check the next Div Tag
        var contents = $(this).closest( "table").next( "div" );
      } else {
        // if we are in Asset view, we check the next Tbody tag
        var contents = $(this).closest( "tbody").next( "tbody" );
      }

      // Show or hide
      contents.toggle();

      // some code for stylish
      if (contents.is(":visible")){
        $(this).removeClass('fa-plus').addClass('fa-minus');
        $(this).closest("tr").css( "background-color", "#EBEBEB" ); // change the background color of container (for easy see where we are)
        contents.css( "background-color", "#EBEBEB" ); // change the background color of content (for easy see where we are)
      } else {
        $(this).removeClass('fa-minus').addClass('fa-plus');
        $(this).closest("tr").css( "background-color", "#FFFFFF" ); // reset the background color on container when we hide content
      }
    });

    // Wallet delta graph
    $("li#journal_graph").click(function() {
      var options = { chart: {
        renderTo: 'chart',
        type: 'line',
        zoomType: 'x',
        },
        title: {
          text: 'Daily ISK Delta',
        },
        xAxis: {
          title: {
            text: 'Time'
          },
          labels: {
            enabled: false
          },
        },
        yAxis: {
          title: {
            text: 'Amount'
          },
          labels: {
            enabled: false
          },
        },
        series: [{}]
      };

      var data;
      $.getJSON("{{ action('CharacterController@getWalletDelta', array('characterID' => $character->characterID)) }}",function(json){

        var deltas = [];
        for (i in json) {
          deltas.push([json[i]['day'], parseInt(json[i]['daily_delta'])]);
        }

        options.series[0].name = "Delta";
        options.series[0].data = deltas;

        var chart = new Highcharts.Chart(options);

        // Trigger a fake resize to get the chart to calculate the width
        // correctly
        $(window).trigger('resize');
      });
    });

  </script>

@stop
