<table class="table table-condensed">
  <tbody>
    <tr>
      <th style="width: 10px">#</th>
      <th>Readable</th>
      <th>Time Stamp</th>
      <th>Login Source</th>
    </tr>

    @foreach ($access_log as $log)

      <tr>
        <td>{{ $log->id }}</td>
        <td>{{ Carbon\Carbon::parse($log->login_date)->diffForHumans() }}</td>
        <td>{{ $log->login_date }}</td>
        <td>{{ $log->login_source }}</td>
      </tr>

    @endforeach

  </tbody>
</table>
