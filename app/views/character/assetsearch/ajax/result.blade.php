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
              <img src='//image.eveonline.com/Character/{{ $result->characterID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
              {{ $result->characterName }}
            </a>
          </td>
          <td>{{ $result->corporationName }}</td>
          <td>{{ $result->typeName }}</td>
          <td>{{ $result->location }}</td>
          <td>{{ App\Services\Helpers\Helpers::format_number($result->quantity) }}</td>
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
