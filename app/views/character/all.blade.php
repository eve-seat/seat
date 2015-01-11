@extends('layouts.masterLayout')

@section('html_title', 'All Characters')

@section('page_content')

  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>All Known Character(s) @if (count($characters) > 0) ({{ count($characters) }}) @endif</b>
          </h3>
        </div>
        <div class="panel-body">

          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <th>Name</th>
              <th>Corp</th>
              <th>Skill Queue End</th>
              <th>SkillPoints</th>
              <th>Ship Type</th>
              <th>Last Known Location</th>
              <th></th>
            </thead>
            <tbody>

              @foreach ($characters as $character)

                <tr>
                  <td>
                    <a href="{{ action('CharacterController@getView', array('characterID' => $character->characterID)) }}">
                      <img src='//image.eveonline.com/Character/{{ $character->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
                    {{ $character->characterName }}
                    </a>
                  </td>
                  <td>{{ $character->corporationName }}</td>
                  <td>
                    @if (isset($last_skills_end[$character->characterID]) && strlen($last_skills_end[$character->characterID]->endTime) > 0)
                      {{ Carbon\Carbon::parse($last_skills_end[$character->characterID]->endTime)->diffForHumans() }}
                    @else
                      Unknown
                    @endif
                  </td>

                  {{-- If we have some key information, then we have more to display. Lets do that --}}
                  @if (!empty($character_info) && array_key_exists($character->characterID, $character_info))
                    {{-- skillpoints --}}
                    @if (!empty($character_info[$character->characterID]->skillPoints))
                      <td data-sort="{{ $character_info[$character->characterID]->skillPoints }}">
                        {{ number_format($character_info[$character->characterID]->skillPoints, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }}
                      @else
                      <td data-sort="0">
                        Unknown
                      @endif
                    </td>
                    <td>
                      {{-- ship type --}}
                      @if (!empty($character_info[$character->characterID]->shipTypeName))
                        {{ $character_info[$character->characterID]->shipTypeName }}
                      @else
                        Unknown
                      @endif
                    </td>
                    <td>
                      {{-- last location --}}
                      @if (!empty($character_info[$character->characterID]->lastKnownLocation))
                        {{ $character_info[$character->characterID]->lastKnownLocation }}
                      @else
                        Unknown
                      @endif
                    </td>
                  @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  @endif

                  {{-- key information --}}
                  <td>
                    @if ($character->isOk == 1)
                      <span class="text-green"><i class="fa fa-check"></i> Key Ok</span>
                    @else
                      <span class="text-red"><i class="fa fa-exclamation"></i> Key not Ok</span>
                    @endif
                  </td>

                </tr>

              @endforeach

            </tbody>
          </table>

        </div>

      </div>

    </div> <!-- col-md-12 -->
  </div> <!-- row -->

@stop
