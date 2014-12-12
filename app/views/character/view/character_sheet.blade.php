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
            @endif
          </dd>
          <dt>Skill Queue Ends</dt>
          <dd>
            @if ($skill_queue)
              {{ end($skill_queue)->endTime }} ({{ Carbon\Carbon::parse(end($skill_queue)->endTime)->diffForHumans() }})
              @if (Carbon\Carbon::parse(end($skill_queue)->endTime)->lte(Carbon\Carbon::now()->addDay()))
                <span class="text-yellow"><i class="fa fa-exclamation"></i> Less than 24hrs worth of skill queue left</span>
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

    {{-- jump clone & fatigue information --}}
    <div class="box box-solid box-primary">
      <div class="box-header">
        <h3 class="box-title">Jump Clones &amp; Fatigue</h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">
        <dl class="dl-horizontal">
          <dt>Jump Fatigue</dt>
          <dd>
            @if(Carbon\Carbon::parse($character->jumpFatigue)->gt(Carbon\Carbon::now()))
              {{ $character->jumpFatigue }} (ends in aprox. {{ Carbon\Carbon::parse($character->jumpFatigue)->diffForHumans() }})
            @else
              None
            @endif
          </dd>
          {{-- only show the difference in time if there is jump fatigue --}}
          @if(Carbon\Carbon::parse($character->jumpFatigue)->gt(Carbon\Carbon::now()))
          <dt>Jump Activation</dt>
          <dd>
            Approx {{ Carbon\Carbon::parse($character->jumpActivation)->diffInHours(Carbon\Carbon::parse($character->jumpFatigue)) }} hour
          </dd>
          @endif
        </dl>

        @if(count($jump_clones) > 0)
          <b>{{ count($jump_clones) }} jump clones:</b>
          <ul class="list-unstyled">

            @foreach($jump_clones as $jump_clone)

              <li>
                {{ $jump_clone->typeName }}
                @if(strlen($jump_clone->cloneName))
                  <i>(called '{{ $jump_clone->cloneName }}')</i>
                @endif
                located at <b>{{ $jump_clone->location }}</b>
              </li>

            @endforeach

          </ul>
        @else
          This character has no jump clones
        @endif
      </div><!-- /.box-body -->
    </div><!-- /.box -->

    {{-- augmentation information --}}
    <div class="box box-solid box-primary">
      <div class="box-header">
        <h3 class="box-title">Implant Information</h3>
        <div class="box-tools pull-right">
        </div>
      </div>
      <div class="box-body">
        <ol>
          @if(count($implants) > 0)
            @foreach($implants as $implant)
              <li>{{ $implant->typeName }}</li>
            @endforeach
          @else
            This character currently has no implants installed.
          @endif
        </ol>
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
