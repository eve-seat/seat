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
                  <td><img src="//image.eveonline.com/Character/{{ $agent->agentID }}_32.jpg" class="img-circle" style="width: 18px;height: 18px;"> {{ $agent->itemName }}</td>
                  <td>{{ $agent->typeName }}</td>
                  <td>{{ $agent->researchStartDate }}</td>
                  <td>{{ $agent->pointsPerDay }}</td>
                  <td>{{ number_format($agent->remainderPoints,2,'.',' ') }}</td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div>
    </div> <!-- ./col-md-12 -->
  </div> <!-- ./row -->
