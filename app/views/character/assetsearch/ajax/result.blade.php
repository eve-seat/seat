@if (count($assets) > 0)
	<table class="table table-condensed table-hover">
	    <tbody>
	    	<tr>
		        <th>Character</th>
		        <th>Corporation</th>
		        <th>Item Name</th>
		        <th>Location</th>
		        <th>Quantity</th>
		    </tr>
		    @foreach ($assets as $result)
			    <tr>
			        <td>{{ $result->characterName }}</td>
			        <td>{{ $result->corporationName }}</td>
			        <td>{{ $result->typeName }}</td>
			        <td>{{ $result->location }}</td>
			        <td>{{ $result->quantity }}</td>
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