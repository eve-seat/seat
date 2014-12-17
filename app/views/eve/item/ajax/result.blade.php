@if (count($items) > 0)

  <table class="table table-condensed compact table-hover" id="datatable">
    <thead>
      <tr>
        <th>#</th>
        <th>TypeID</th>
        <th>TypeName</th>
        <th>Description</th>
        <th>Capacity</th>
        <th>Volume</th>
      </tr>
    </thead>
    <tbody>

      @foreach ($items as $result)

        <tr>
          <td><img src='//image.eveonline.com/Type/{{ $result->typeID }}_32.png' style='width: 18px;height: 18px;'></td>
          <td>{{ $result->typeID }}</td>
          <td>{{ $result->typeName }}</td>
          <td>{{ $result->description }}</td>
          <td>{{ number_format($result->capacity, 0, '.', ' ') }}</td>
          <td>{{ number_format($result->volume, 0, '.', ' ') }}</td>
        </tr>

      @endforeach
    </tbody>
  </table>

@else

  <div class="callout callout-warning">
    <h4>No Results</h4>
    <p>Your asset specific search yielded no results.</p>
  </div>

@endif
