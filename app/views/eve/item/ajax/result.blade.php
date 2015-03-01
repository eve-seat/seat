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
          <td>
              {{ Seat\services\helpers\Img::type($result->typeID, 16, array('class' => 'eveIcon small')) }}
          </td>
          <td>{{ $result->typeID }}</td>
          <td>{{ $result->typeName }}</td>
          <td>{{ $result->description }}</td>
          <td>{{ number_format($result->capacity, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
          <td>{{ number_format($result->volume, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
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
