@if (count($assets) > 0)

  <table class="table table-condensed compact table-hover" id="datatable">
    <thead>
      <tr>
        <th>Character</th>
        <th>Corporation</th>
        <th>Item Name</th>
        <th>Location</th>
        <th>Quantity</th>
      </tr>
    </thead>
    <tbody>

      @foreach ($assets as $result)

        <tr>
          <td>
            <a href="{{ action('CharacterController@getView', array('characterID' => $result->characterID)) }}">
                {{ Seat\services\helpers\Img::character($result->characterID, 16, array('class' => 'img-circle eveIcon small')) }}
                {{ $result->characterName }}
            </a>
          </td>
          <td>{{ $result->corporationName }}</td>
          <td>{{ $result->typeName }}</td>
          <td>{{ $result->location }}</td>
          <td>{{ number_format($result->quantity, 0, $settings['decimal_seperator'], $settings['thousand_seperator']) }}</td>
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
