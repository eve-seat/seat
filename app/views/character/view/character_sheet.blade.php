{{-- character sheet --}}
<div class="row">

	<div class="col-md-6">

    	{{-- character information --}}
        <div class="box box-solid box-primary">
            <div class="box-header">
                <h3 class="box-title">Character Information</h3>
                <div class="box-tools pull-right">
                </div>
            </div>
            <div class="box-body">
            	<dl class="dl-horizontal">
                    <dt>Name</dt>
                    <dd>{{ $character->characterName }}</dd>

                    <dt>Corporation</dt>
                    <dd>{{ $character->corporationName }}</dd>

                    <dt>Race, Bloodline, Sex</dt>
                    <dd>{{ $character->race }}, {{ $character->bloodLine }}, {{ $character->gender }}</dd>

                    <dt>Date of Birth</dt>
                    <dd>{{ $character->DoB }} ({{ Carbon\Carbon::parse($character->DoB)->diffForHumans() }})</dd>
                    <dt>Skillpoints</dt>
                    <dd>{{ number_format($skillpoints, 0, '.', ' ') }}</dd>

                    <dt>Clone Grade</dt>
                    <dd>
                    	{{ $character->cloneName }} covering {{ number_format($character->cloneSkillPoints, 0, '.', ' ') }} skillpoints
                    	@if ($skillpoints > $character->cloneSkillPoints)
                    		<span class="text-red"><i class="fa fa-exclamation"></i> Clone out of date</span>
                    	@else
                    		<span class="text-green"><i class="fa fa-check"></i> Clone OK</span>
                    	@endif
                    </dd>

                    <dt>Wallet Balance</dt>
                    <dd>{{ number_format($character->balance, 2, '.', ' ') }} ISK</dd>

					{{-- ship type --}}
					@if (!empty($character_info->shipTypeName))
                        <dt>Ship:</dt>
                        <dd>{{ $character_info->shipTypeName }} called <i>{{ $character_info->shipName }}</i></dd>						
					@endif

					{{-- last location --}}
					@if (!empty($character_info->lastKnownLocation))
                        <dt>Last Location:</dt>
                        <dd>{{ $character_info->lastKnownLocation }}</i></dd>							
					@endif

					@if (!empty($character_info->securityStatus))
                        <dt>Security Status:</dt>
                    	<dd>
							@if ($character_info->securityStatus < -5)
								<span class="text-red">{{ $character_info->securityStatus }}</span>
							@elseif ($character_info->securityStatus < -2)
								<span class="text-yellow">{{ $character_info->securityStatus }}</span>
							@else
								<span class="text-green">{{ $character_info->securityStatus }}</span>
							@endif
						</dd>
					@endif					
                </dl>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

    	{{-- skill information --}}
        <div class="box box-solid box-primary">
            <div class="box-header">
                <h3 class="box-title">Skills Information</h3>
                <div class="box-tools pull-right">
                </div>
            </div>
            <div class="box-body">
            	<dl class="dl-horizontal">
                    <dt>Currently Training</dt>
                    <dd>
                    	@if ( strlen($character->typeName) > 0)
                        	{{ $character->typeName }} {{ $character->trainingToLevel }}
                        @else
                        	<span class="text-yellow"><i class="fa fa-exclamation"></i> This character is not currently training</span>
                        @endif
                    </dd>

                    <dt>Training Ends</dt>
                    <dd>
                    	{{ $character->trainingEndTime }} ({{ Carbon\Carbon::parse($character->trainingEndTime)->diffForHumans() }})
                    	@if (Carbon\Carbon::parse($character->trainingEndTime)->lte(Carbon\Carbon::now()->addDay()))
                    		<span class="text-yellow"><i class="fa fa-exclamation"></i> Less than 24hrs worth of training left</span>
                    	@else
                    		<span class="text-green"><i class="fa fa-check"></i> More than 24hrs worth of training left</span>
                    	@endif
                    </dd>
                    <dt>Skill Queue Ends</dt>
                    <dd>
                    	@if ($skill_queue)
                        	{{ end($skill_queue)->endTime }} ({{ Carbon\Carbon::parse(end($skill_queue)->endTime)->diffForHumans() }})
                        	@if (Carbon\Carbon::parse(end($skill_queue)->endTime)->lte(Carbon\Carbon::now()->addDay()))
                        		<span class="text-yellow"><i class="fa fa-exclamation"></i> Less than 24hrs worth of skill queue left</span>
                        	@else
                        		<span class="text-green"><i class="fa fa-check"></i> More than 24hrs worth of skill queue left</span>
                        	@endif
                        @endif
                    </dd>
                </dl>
                 <dl>

                    <dt>Skill Queue</dt>
                    <dd>
                    	<ol>
                    		@foreach($skill_queue as $skill)
                    			<li>{{ $skill->typeName }} {{ $skill->level }}</li>
                    		@endforeach
                    	</ol>
                    </dd>
                 </dl>
            </div><!-- /.box-body -->
        </div><!-- /.box -->



    	{{-- key/account information --}}
        <div class="box box-solid box-primary">
            <div class="box-header">
                <h3 class="box-title">Key/Account Information</h3>
                <div class="box-tools pull-right">
                </div>
            </div>
            <div class="box-body">
            	<dl class="dl-horizontal">
                    <dt>Key ID</dt>
                    <dd>{{ $character->keyID }}</dd>

                    <dt>Key Type</dt>
                    <dd>{{ $character->type }}</dd>

                    <dt>Access Mask</dt>
                    <dd>{{ $character->accessMask }}</dd>

                    <dt>Paid Until</dt>
                    <dd>{{ $character->paidUntil }} (Payment due {{ Carbon\Carbon::parse($character->paidUntil)->diffForHumans() }})</dd>

                    <dt>Logon Count</dt>
                    <dd>{{ $character->logonCount }} logins to eve related services</dd>

                    <dt>Online Time</dt>
                    <dd>{{ $character->logonMinutes }} minutes, {{ round(((int)$character->logonMinutes/60),0) }} hours or {{ round(((int)$character->logonMinutes/60)/24,0) }} days</dd>
                </dl>
            </div><!-- /.box-body -->
        </div><!-- /.box -->

    	{{-- augmentation information --}}
        <div class="box box-solid box-primary">
            <div class="box-header">
                <h3 class="box-title">Augmentations Information</h3>
                <div class="box-tools pull-right">
                </div>
            </div>
            	<div class="box-body">
					<dl class="dl-horizontal">
						<dt>Intelligence</dt>
						<dd>@if(!$character->intelligenceAugmentatorValue)
								<span class="text-orange"><i class="fa fa-exclamation"></i> {{ $character->intelligenceAugmentatorValue + $character->intelligence }}</span>
							@else
								<span class="text-green"><i class="fa fa-check"></i> {{ $character->intelligenceAugmentatorValue + $character->intelligence }}</span>
							@endif
							{{ $character->intelligenceAugmentatorName }} (17 Base + {{ $character->intelligenceAugmentatorValue ? $character->intelligenceAugmentatorValue : 0}} implant + {{ $character->intelligence -17 }} remap)</dd>

						<dt>Memory</dt>
						<dd>@if(!$character->memoryAugmentatorValue)
								<span class="text-orange"><i class="fa fa-exclamation"></i> {{ $character->memoryAugmentatorValue + $character->memory }}</span>
							@else
								<span class="text-green"><i class="fa fa-check"></i> {{ $character->memoryAugmentatorValue + $character->memory }}</span>
							@endif
							{{ $character->memoryAugmentatorName }} (17 Base + {{ $character->memoryAugmentatorValue ? $character->memoryAugmentatorValue : 0}} implant + {{ $character->memory -17 }} remap)</dd>

						<dt>Perception</dt>
						<dd>@if(!$character->perceptionAugmentatorValue)
								<span class="text-orange"><i class="fa fa-exclamation"></i> {{ $character->perceptionAugmentatorValue + $character->perception }}</span>
							@else
								<span class="text-green"><i class="fa fa-check"></i> {{ $character->perceptionAugmentatorValue + $character->perception }}</span>
							@endif
							{{ $character->perceptionAugmentatorName }} (17 Base + {{ $character->perceptionAugmentatorValue ? $character->perceptionAugmentatorValue : 0}} implant + {{ $character->perception -17 }} remap)</dd>

						<dt>Willpower</dt>
						<dd>@if(!$character->willpowerAugmentatorValue)
								<span class="text-orange"><i class="fa fa-exclamation"></i> {{ $character->willpowerAugmentatorValue + $character->willpower }}</span>
							@else
								<span class="text-green"><i class="fa fa-check"></i> {{ $character->willpowerAugmentatorValue + $character->willpower }}</span>
							@endif
							{{ $character->willpowerAugmentatorName }} (17 Base + {{ $character->willpowerAugmentatorValue ? $character->willpowerAugmentatorValue : 0}} implant + {{ $character->willpower -17 }} remap)</dd>

						<dt>Charisma</dt>
						<dd>@if(!$character->charismaAugmentatorValue)
								<span class="text-orange"><i class="fa fa-exclamation"></i> {{ $character->charismaAugmentatorValue + $character->charisma }}</span>
							@else
								<span class="text-green"><i class="fa fa-check"></i> {{ $character->charismaAugmentatorValue + $character->charisma }}</span>
							@endif
							{{ $character->charismaAugmentatorName }} (17 Base + {{ $character->charismaAugmentatorValue ? $character->charismaAugmentatorValue : 0 }} implant + {{ $character->charisma -17 }} remap)</dd>
					</dl>
				</div><!-- /.box-body -->
        </div><!-- /.box -->
    </div> <!-- ./col-md-6 -->

	<div class="col-md-6">

		{{-- if we have the character info from eve_characterinfo, dispaly that --}}
		@if (!empty($character_info))

        	{{-- employment information --}}
            <div class="box box-solid box-primary">
                <div class="box-header">
                    <h3 class="box-title">Employment History ({{ count($employment_history) }})</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <div class="box-body">
                	<ul class="list-unstyled">
                		@foreach($employment_history as $employment)
                			<li>
                				<img src='https://image.eveonline.com/Corporation/{{ $employment->corporationID }}_32.png' class='img-circle'>
                				Joined <b><span rel="id-to-name">{{ $employment->corporationID }}</span></b> on {{ $employment->startDate }} ({{ Carbon\Carbon::parse($employment->startDate)->diffForHumans() }})
                			</li>
                		@endforeach
                	</ul>
                </div><!-- /.box-body -->
            </div><!-- /.box -->

        @endif

    </div> <!-- ./col-md-6 -->
</div> <!-- ./row -->
