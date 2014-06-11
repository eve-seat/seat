{{-- character skills --}}
	            <div class="tab-pane" id="character_skills">
	            	<div class="row">
	            		<div class="col-md-6">
			            	{{--
			            		Lets try and document this for a change.
			            		
			            		We start by looping over the available groups, found in $skill_groups
			            		passed by the controller. Every pass of a new group will count the amount
			            		of skills the character has in that particulat group as $character_skills
			            		array has the groupID as a key.

			            		If a group has more than 0 skills, we prepare a 'box' and loop over the actual
			            		skills for that group, displaying the level etc.
			            	--}}
			            	@foreach ($skill_groups as $skill_group)

			            		@if ( isset($character_skills[$skill_group->groupID]) && count($character_skills[$skill_group->groupID]) > 0)
				                    <div class="box box-solid">
				                        <div class="box-header">
				                            <h3 class="box-title">{{ $skill_group->groupName }}</h3>
				                            <div class="box-tools pull-right">
				                            </div>
				                        </div>
				                        <div class="box-body">
				                        	<ul class="list-unstyled">
				                        		{{--*/$group_sp = 0;/*--}}
				                        		@foreach ($character_skills[$skill_group->groupID] as $skill)
			                                        <li>
			                                        	<i class="fa fa-book"></i> {{ $skill['typeName'] }}
			                                        	<span class="pull-right">
			                                        		{{--
			                                        			Here we check if the skills level is 0. If so, just display
			                                        			a empty star. Else, check if its fully trained (level 5) and display
			                                        			5 green stars.
			                                        			Lastly, if neither of the above are the case, display stars equal to the
			                                        			level of the skill.
			                                        		--}}
			                                        		@if ($skill['level'] == 0)
				                                        		<i class="fa fa-star-o"></i>
				                                        	@elseif ($skill['level'] == 5)
				                                        		<span class="text-green">
				                                        			<i class="fa fa-star"></i>
				                                        			<i class="fa fa-star"></i>
				                                        			<i class="fa fa-star"></i>
				                                        			<i class="fa fa-star"></i>
				                                        			<i class="fa fa-star"></i>
				                                        		</span>
			                                        		@else
			                                        			@for ($i=0; $i < $skill['level'] ; $i++)
			                                        				<i class="fa fa-star"></i>
			                                        			@endfor	
			                                        		@endif
			                                        		| {{ $skill['level'] }}
			                                        	</span>
			                                        </li>
			                                        {{--*/$group_sp += $skill['skillpoints'];/*--}}
		                                        @endforeach
		                                    </ul>
				                        </div><!-- /.box-body -->
				                        <div class="box-footer">
				                        	{{-- $group_sp comes from the comment hack above ;D --}}
				                        	<b>{{ number_format($group_sp) }}</b> Total Skillpoints
				                        </div>
				                    </div><!-- /.box -->
			                   @endif
			            	@endforeach
		                </div> <!-- ./col-md-6 -->
		                <div class="col-md-6">


							<div class="box box-solid">
							    <div class="box-header">
							        <h3 class="box-title">Spaceship Command Skills</h3>
							    </div>

							    <div class="box-body">
									<table class="table table-condensed table-hover" id="datatable">
									    <thead>
									    	<tr>
									        <th>Skill</th>
									        <th>Amarr</th>
									        <th>Caldari</th>
									        <th>Gallente</th>
									        <th>Minmatar</th>
										    </tr>
										</thead>
										<tbody>
										    <tr>
										        <td>Frigate</td>

										        	@foreach( array(3331, 3330, 3328 ,3329) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Destroyer</td>
										        	@foreach( array(33091, 33092, 33093 ,33094) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Cruiser</td>
										        	@foreach( array(3335, 3334, 3332 ,3333) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Battlecruiser</td>
										        	@foreach( array(33095, 33096, 33097 ,33098) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Battleship</td>
										        	@foreach( array(3339, 3338, 3336 ,3337) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Strategic Cruiser</td>
										        	@foreach( array(30650, 30651, 30652 ,30653) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Industrial</td>
										        	@foreach( array(3343, 3342, 3340 ,3341) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>									    
										    <tr>
										        <td>Freighter</td>
										        	@foreach( array(20524, 20526, 20527 ,20528) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>									    
										    <tr>
										        <td>Carrier</td>
										        	@foreach( array(24311, 24312, 24313 ,24314) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>
										    <tr>
										        <td>Dreadnaught</td>
										        	@foreach( array(20525, 20530, 20531 ,20532) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>	
										        <td>Titan</td>
										        	@foreach( array(3347, 3346, 3344 ,3345) as $s)
												        <td>
												        	@if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
											        			<span class="label label-success">5</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 0)
													        	<span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
												        	@elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
													        	<span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @else
														        <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) }}</span>
													        @endif
													    </td>
													@endforeach
										    </tr>										    
										</tbody>
									</table>
							    </div><!-- /.box-body -->
							</div>

		                </div>
		            </div> <!-- ./row -->
	            </div><!-- /.tab-pane -->