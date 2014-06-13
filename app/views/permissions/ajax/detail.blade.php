@foreach(array_chunk($seat_users, 2) as $user_chunk)

	<div class="row">

		@foreach ($user_chunk as $user)

			<div class="col-md-6">
				<div class="panel panel-default">
				  <div class="panel-heading">
				    <h3 class="panel-title">
				    	{{ $user->email }}
				    </h3>
				  </div>
				  <div class="panel-body">

				  	<p class="lead small pull-right">Note, users in the <i>Administrators</i> group automatically have all permissions</p>

				  	<ul class="nav nav-pills nav-stacked">
					  	<li class="header">Corporation Permissions</li>
					  	{{-- loop over the groups that are available --}}
					  	@foreach ($available_groups as $available_group)
					  		<li>

						  		{{-- check if the user has any permissions assigned, and that the permission exists for them --}}
						  		@if (isset($group_memberships[$user->id]) && in_array($available_group, $group_memberships[$user->id]))
							  		<div class="form-group">{{ Form::checkbox($available_group, 'yes', true, array('id' => 'permission' ,'a-group-name' => $available_group, 'a-user-id' => $user->id)) }} {{ $available_group }}</div>
							  	@else
							  		<div class="form-group">{{ Form::checkbox($available_group, 'yes', false, array('id' => 'permission' ,'a-group-name' => $available_group, 'a-user-id' => $user->id)) }} {{ $available_group }}</div>
						  		@endif
						  	</li>
					  	@endforeach
					</ul>

				  </div>
				  <div class="panel-footer">
				  	@if (isset($user_characters[$user->id]))
				  		@foreach($user_characters[$user->id] as $character)
							<a href="{{ action('CharacterController@getView', array('characterID' => $character['characterID'])) }}">
								<img src='//image.eveonline.com/Character/{{ $character["characterID"] }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
					  			{{ $character['characterName'] }}
							</a>
				  		@endforeach
				  	@endif
				  </div>
				</div>
			</div>
		@endforeach

	</div>

@endforeach

