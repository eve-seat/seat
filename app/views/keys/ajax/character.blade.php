@include('layouts.components.flash')

{{-- process errors --}}
@if (!is_object($key_info))
	<div class="callout callout-danger">
    <h4><i class="fa fa-warning"></i> <b>Error:</b> Unable to get API Key Info <small>/account/APIKeyInfo.xml.aspx</small></h4>
    <p>The error was: <b>{{ $key_info['error'] }}</b></p>
  </div>
@endif

{{-- process db existance check --}}
@if ($existance > 0)
	<div class="callout callout-warning">
    <h4><i class="fa fa-warning"></i> Key already exists in the database</h4>
    <p>Adding it here will simply reactive it if it is disabled and make you the owner.</p>
  </div>
@else
  <div class="callout callout-info">
   <h4><i class="fa fa-thumbs-up"></i> Key does not exist in the database</h4>
   <p>Adding it here will make it part of the scheduled updates via the backend.</p>
 </div>
@endif

{{-- process the ACTUAL info --}}
<div class="row">
	<div class="col-md-6">

		{{-- key time accounts --}}
    <ul class="list-unstyled">
      <li><i class="fa fa-clock-o fa-fw"></i>This account has logged into EVE related services <b>{{ $status_info->logonCount }}</b> times</li>
      <li>
        <i class="fa fa-clock-o fa-fw"></i>
        This account has been online a total of
        <b>{{ $status_info->logonMinutes }} minutes, {{ round(((int)$status_info->logonMinutes/60),0) }} hours or {{ round(((int)$status_info->logonMinutes/60)/24,0) }} days</b>
      </li>
    </ul>

    {{-- key activity and accessmask details --}}
    <ul class="list-unstyled">
      @if (Carbon\Carbon::parse($status_info->paidUntil)->lte(\Carbon\Carbon::now()))
        <li><i class="fa fa-times fa-fw"></i>This account is not active and expired <b>{{ Carbon\Carbon::parse($status_info->paidUntil)->diffForHumans() }}</b></li>
      @else
        <li><i class="fa fa-check fa-fw"></i>This account is active and will expire on {{ $status_info->paidUntil }} which is <b>{{ Carbon\Carbon::parse($status_info->paidUntil)->diffForHumans() }}</b></li>
      @endif

      @if( strlen($key_info->key->expires) == 0)
        <li><i class="fa fa-check fa-fw"></i>This key will <b>never</b> expire</li>
      @else
        <li><i class="fa fa-times fa-fw"></i>This key will expire on {{ $key_info->key->expires }} which is <b>{{ Carbon\Carbon::parse($key_info->key->expires)->diffForHumans() }}</b></li>
      @endif

      <li><i class="fa fa-check fa-fw"></i>This key type is <b>{{ $key_info->key->type }}</b></li>

      @if ($key_info->key->type == 'Corporation')
        @if ($key_info->key->accessMask == 67108863) {{-- full corporation api key? --}}
          <li><i class="fa fa-check fa-fw"></i>This key has a full corporation Access Mask of: <b><span id='access-mask'>{{ $key_info->key->accessMask }}</span></b></li>
				@else
					<li><i class="fa fa-check fa-fw"></i>This key has a partial corporation Access Mask of: <b><span id='access-mask'>{{ $key_info->key->accessMask }}</span></b></li>
				@endif
      @else
        @if ($key_info->key->accessMask == 268435455) {{-- full character/account api key? --}}
          <li><i class="fa fa-check fa-fw"></i>This key has a full character/account Access Mask of: <b><span id='access-mask'>{{ $key_info->key->accessMask }}</span></b></li>
				@else
					<li><i class="fa fa-check fa-fw"></i>This key has a partial character/account Access Mask of: <b><span id='access-mask'>{{ $key_info->key->accessMask }}</span></b></li>
				@endif
      @endif
    </ul>

  </div> <!-- ./col-6 -->
  <div class="col-md-6">
    <div class="callout callout-info">
      <h4><i class="fa fa-info"></i> A Job will be queued to update all key information</h4>
      <p>Adding this key will queue a job to update its information immediately</p>
      {{ HTML::linkAction('ApiKeyController@getAdd', 'Add this Key', array('keyID'=> $keyID, 'vCode' => $vCode), array('class' => 'btn btn-primary')) }}
    </div>
  </div>
</div> <!-- ./row -->

<hr>

<div class="row">
	<div class="col-md-6">
    {{-- key characters --}}
    <p class="lead">Characters on key {{ $keyID }}</p>

    @foreach ($key_info->key->characters as $character)

      <div class="row">
        <div class="col-md-2">
          <img src='//image.eveonline.com/Character/{{ $character->characterID }}_128.jpg' class='img-thumbnail'>
        </div>
        <div class="col-md-6">
          <blockquote>
            <p>{{ $character->characterName }}</p>
            <small>{{ $character->corporationName }}</small>
          </blockquote>
        </div>
      </div>

    @endforeach

  </div>
  <div class="col-md-6">
    <p class="lead">Access Mask Details:</p>
    Todo™
  </div>
</div>
