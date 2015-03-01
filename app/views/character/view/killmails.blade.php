{{-- character killmails --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Kill Mails ({{ count($killmails) }})</h3>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">
        <div class="row">

          @foreach (array_chunk($killmails, (count($killmails) / 2) > 1 ? count($killmails) / 2 : 2) as $killmail_list)

            <div class="col-md-6">
              <table class="table table-hover table-condensed">
                <thead>
                  <tr>
                    <th>Victim</th>
                    <th>Ship (zKB Link)</th>
                    <th>Location</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>

                  @foreach ($killmail_list as $killmail)

                    <tr>
                      <td>
                        <a href="{{ action('CharacterController@getView', array('characterID' => $killmail->characterID)) }}">
                          {{ Seat\services\helpers\Img::character($killmail->characterID, 16, array('class' => 'img-circle eveIcon small')) }}
                          {{ $killmail->characterName }}
                          {{-- if the characterID == victim characterID, then this is a loss --}}
                          @if($killmail->characterID == $characterID)
                          <span class="text-danger"><i>(loss!)</i></span>
                          @endif
                        </a>
                      </td>
                      <td>
                        <a href="https://zkillboard.com/kill/{{ $killmail->killID }}/" target="_blank">
                          {{ Seat\services\helpers\Img::type($killmail->shipTypeID, 16, array('class' => 'eveIcon small')) }}
                          {{ $killmail->typeName }}
                        </a>
                      </td>
                      <td>
                        {{ $killmail->solarSystemName }}
                      </td>
                      <td>
                        <span class="text-muted" data-toggle="tooltip" title="" data-placement="top" data-original-title="API Key Details">
                          ({{ Carbon\Carbon::parse($killmail->killTime)->diffForHumans() }})
                        </span>
                      </td>
                    </tr>

                  @endforeach

                </tbody>
              </table>
            </div> <!-- ./col-md-2 -->

          @endforeach

        </div><!-- ./row -->
      </div><!-- /.box-body -->
    </div>
  </div> <!-- ./col-md-12 -->
</div> <!-- ./row -->
