{{-- character notification --}}
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Research ({{ count($research) }})</h3>
      </div><!-- /.box-header -->
      <div class="box-body no-padding">
        <table class="table table-hover table-condensed compact" id="datatable">
          <thead>
            <tr>
              <th>Agent</td>
                <th>Skill</th>
                <th>Start Date</th>
                <th>Points Per Day</th>
                <th>Remainder Points</th>
              </tr>
            </thead>
            <tbody>

              @foreach ($research as $agent)

                <tr>
                  <td>
                      {{ Seat\services\helpers\Img::character($agent->agentID, 16, array('class' => 'img-circle eveIcon small')) }}
                      {{ $agent->itemName }}
                  </td>
                  <td>{{ $agent->typeName }}</td>
                  <td>{{ $agent->researchStartDate }}</td>
                  <td>{{ $agent->pointsPerDay }}</td>
                  <td>{{ number_format($agent->remainderPoints, 2, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div>
    </div> <!-- ./col-md-12 -->
  </div> <!-- ./row -->
