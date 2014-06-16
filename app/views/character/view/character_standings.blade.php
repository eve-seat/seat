 {{-- character standings --}}
<div class="tab-pane" id="standings">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
      			<div class="box-header">
          			<h3 class="box-title">Standings</h3>                             
      			</div><!-- /.box-header -->
      			<div class="box-body no-padding">
			        <ul class="nav nav-tabs">
				        <li class="active"><a href="#agents" data-toggle="tab">Agents ({{ count($agent_standings) }})</a></li>
				        <li><a href="#npcs" data-toggle="tab">NPC Corporations ({{ count($npc_standings) }})</a></li>
				        <li><a href="#factions" data-toggle="tab">Factions ({{ count($faction_standings) }})</a></li>
	    			</ul>
			   		<div class="tab-content">

			   			{{-- Agent Standings --}}
						<div class="tab-pane active" id="agents">
					        <table class="table table-hover table-condensed" id="datatable">
						        <thead>
						          <tr>
						          	<td>#</td>
						          	<td>Name</td>
						          	<td>Standing</td>
						          </tr>
						        </thead>
						        <tbody>
						        	@foreach($agent_standings as $standing)
						        		<tr>
						        			<td>
						        				<img src="{{ App\Services\Helpers\Helpers::generateEveImage( $standing->fromID, 32) }}">
						        			</td>
						        			<td>{{ $standing->fromName }}</td>
						        			<td>{{ $standing->standing }}</td>
						        		</tr>
						        	@endforeach
						        </tbody>
					      	</table>
					    </div> <!-- ./ tab-pane -->

					    {{-- NPC Standings --}}
					    <div class="tab-pane" id="npcs">
					        <table class="table table-hover table-condensed" id="datatable">
					        <thead>
					          <tr>
					          	<td>#</td>
					          	<td>Name</td>
					          	<td>Standing</td>
					          </tr>
					        </thead>
					        <tbody>
					        	@foreach($npc_standings as $standing)
					        		<tr>
					        			<td>
					        				<img src="{{ App\Services\Helpers\Helpers::generateEveImage( $standing->fromID, 32) }}">
					        			</td>
					        			<td>{{ $standing->fromName }}</td>
					        			<td>{{ $standing->standing }}</td>
					        		</tr>
					        	@endforeach
					        </tbody>
					      </table>
					    </div> <!-- ./ tab-pane -->

					    {{-- Faction Standings --}}
					    <div class="tab-pane" id="factions">
					        <table class="table table-hover table-condensed" id="datatable">
					        <thead>
					          <tr>
					          	<td>#</td>
					          	<td>Name</td>
					          	<td>Standing</td>
					          </tr>
					        </thead>
					        <tbody>
					        	@foreach($faction_standings as $standing)
					        		<tr>
					        			<td>
					        				<img src="{{ App\Services\Helpers\Helpers::generateEveImage( $standing->fromID, 32) }}">
					        			</td>
					        			<td>{{ $standing->fromName }}</td>
					        			<td>{{ $standing->standing }}</td>
					        		</tr>
					        	@endforeach
					        </tbody>
					      </table>
					    </div> <!-- ./ tab-pane -->
						    
      			</div><!-- /.box-body -->
    		</div>
    	</div> <!-- ./col-md-12 -->
    </div> <!-- ./row -->
</div><!-- /.tab-pane -->