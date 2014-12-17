{{-- character skills --}}
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
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#standard" data-toggle="tab">Core</a></li>
                        <li><a href="#tech2" data-toggle="tab">Tech 2</a></li>
                        <li><a href="#tech3" data-toggle="tab">Tech 3</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="standard">
                            <table class="table table-condensed compact table-hover" id="datatable">
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
                                        <td data-order="frigate">Frigate</td>
                                      @foreach( array(3331, 3330, 3328, 3329) as $s)
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
                                        <td data-order="destroyer">Destroyer</td>
                                      @foreach( array(33091, 33092, 33093, 33094) as $s)
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
                                        <td data-order="cruiser">Cruiser</td>
                                      @foreach( array(3335, 3334, 3332, 3333) as $s)
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
                                        <td data-order="battlecruiser">Battlecruiser</td>
                                          @foreach( array(33095, 33096, 33097, 33098) as $s)
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
                                        <td data-order="battleship">Battleship</td>
                                          @foreach( array(3339, 3338, 3336, 3337) as $s)
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
                                        <td data-order="industrial">Industrial</td>
                                          @foreach( array(3343, 3342, 3340, 3341) as $s)
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
                                        <td data-order="freighter">Freighter</td>
                                          @foreach( array(20524, 20526, 20527, 20528) as $s)
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
                                        <td data-order="carrier">Carrier</td>
                                          @foreach( array(24311, 24312, 24313, 24314) as $s)
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
                                        <td data-order="dreadnaught">Dreadnaught</td>
                                          @foreach( array(20525, 20530, 20531, 20532) as $s)
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
                                        <td data-order="titan">Titan</td>
                                          @foreach( array(3347, 3346, 3344, 3345) as $s)
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
                        </div><!-- /. tab-pane -->
                        <div class="tab-pane" id="tech2">
                            <table class="table table-condensed compact table-hover" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Skill</th>
                                        <th>Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-order="assaultfrigate">Assault Frigate</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12095) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12095) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12095) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12095) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12095) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12095) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3331, 3330, 3328, 3329) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="blackops">Black Ops</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28656) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28656) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28656) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28656) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28656) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28656) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3339, 3338, 3336, 3337) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="commandship">Command Ships</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 23950) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 23950) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 23950) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 23950) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 23950) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 23950) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(33095, 33096, 33097, 33098) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="covertops">Covert Ops</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12093) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12093) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12093) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12093) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12093) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12093) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3331, 3330, 3328, 3329) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="electronicattackships">Electronic Attack Ships</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28615) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28615) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28615) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28615) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28615) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28615) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3331, 3330, 3328, 3329) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="interceptors">Interceptors</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12092) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12092) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12092) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12092) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12092) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12092) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3331, 3330, 3328, 3329) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="interdictors">Interdictors</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12098) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12098) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12098) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12098) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12098) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12098) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(33091, 33092, 33093, 33094) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="jumpfreighter">Jump Freighters</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29029) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29029) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29029) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29029) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29029) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29029) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(20524, 20526, 20527, 20528) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="heavyassaultcruiser">Heavy Assault Cruisers</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 16591) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 16591) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 16591) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 16591) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 16591) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 16591) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3335, 3334, 3332, 3333) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="heavyinterdictioncruiser">Heavy Interdiction Cruisers</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29637) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29637) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29637) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29637) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29637) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 29637) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3335, 3334, 3332, 3333) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="logistics">Logistics</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12096) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12096) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12096) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12096) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12096) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 12096) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3335, 3334, 3332, 3333) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="marauders">Marauders</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28667) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28667) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28667) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28667) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28667) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 28667) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3339, 3338, 3336, 3337) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="reconships">Recon Ships</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 22761) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 22761) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 22761) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 22761) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 22761) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 22761) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3335, 3334, 3332, 3333) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-order="transportships">Transport Ships</td>
                                        <td>
                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 19719) == 5)
                                            <span class="label label-success">5</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 19719) == 0)
                                            <span class="label label-default">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 19719) }}</span>
                                          @elseif (App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) <= 2)
                                            <span class="label label-danger">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 19719) }}</span>
                                          @else
                                            <span class="label label-primary">{{ App\Services\Helpers\Helpers::findSkillLevel($character_skills, 19719) }}</span>
                                          @endif

                                          @if (App\Services\Helpers\Helpers::findSkillLevel($character_skills, 19719) > 0)
                                            {{--*/$skill5 = false;/*--}}
                                            @foreach( array(3343, 3342, 3340, 3341) as $s)
                                              @if(App\Services\Helpers\Helpers::findSkillLevel($character_skills, $s) == 5)
                                                {{--*/$skill5 = true;/*--}}
                                              @endif
                                            @endforeach
                                            @if($skill5 == false)
                                            <span class="label label-danger">Ship Skill missing!</span>
                                            @endif
                                          @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- /. tab-pane -->
                        <div class="tab-pane" id="tech3">
                            <table class="table table-condensed compact table-hover" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Subsystems</th>
                                        <th>Amarr</th>
                                        <th>Caldari</th>
                                        <th>Gallente</th>
                                        <th>Minmatar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-order="t3cruiser-defense">Defensive Systems</td>
                                      @foreach( array(30532, 30544, 30540, 30545) as $s)
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
                                        <td data-order="t3cruiser-electronicsystems">Electronic Systems</td>
                                      @foreach( array(30536, 30542, 30541, 30543) as $s)
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
                                        <td data-order="t3cruiser-engineeringsystems">Engineering Systems</td>
                                      @foreach( array(30539, 30548, 30546, 30547) as $s)
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
                                        <td data-order="t3cruiser-offensivesystems">Offensive Systems</td>
                                          @foreach( array(30537, 30549, 30550, 30551) as $s)
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
                                        <td data-order="t3cruiser-propulsionsystems">Propulsion Systems</td>
                                          @foreach( array(30538, 30552, 30553, 30554) as $s)
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
                                        <td data-order="t3cruiser">Strategic Cruiser</td>
                                          @foreach( array(30650, 30651, 30652, 30653) as $s)
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
                                        <td data-order="t3destroyer">Strategic Destroyer</td>
                                          @foreach( array(34390, 0, 0, 0) as $s)
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
                        </div><!-- /. tab-pane -->
                    </div><!-- /. tab-content -->
                </div><!-- ./nav-tabs-custom -->
            </div><!-- /.box-body -->
        </div>
    </div><!-- ./col-md6 -->
</div> <!-- ./row -->
