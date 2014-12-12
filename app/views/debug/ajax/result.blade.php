@include('layouts.components.flash')

@if (isset($exception))

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid box-danger">
        <div class="box-header">
          <h3 class="box-title">API Call Exception Raised</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-danger btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-danger btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          The API call failed with error code <code>{{ $exception->getCode() }}</code>
          <p>The Error was: <code>{{ $exception->getMessage() }}</code></p>
        </div><!-- /.box-body -->
      </div>
    </div>
  </div>

@endif

@if (isset($call_sample))

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid box-info">
        <div class="box-header">
          <h3 class="box-title">API Call Structure</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-info btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-info btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          The <code>pheal</code> instance was probably called with:
          <p>
            KeyID: <b>{{ $call_sample['keyID'] }}</b><br>
            vCode: <b>{{ $call_sample['vCode'] }}</b></p>
          </p>
          <p>
            PHP Function:<br>
            <pre>$pheal->{{ $call_sample['scope'] }}scope->{{ $call_sample['call'] }}({{ print_r($call_sample['args'], true) }})</pre>
          </p>
        </div><!-- /.box-body -->
      </div>
    </div>
  </div>

@endif

@if (isset($response))

  <div class="row">
    <div class="col-md-12">
      <div class="box box-solid">
        <div class="box-header">
          <h3 class="box-title">
            Raw Response as PHP Array

            @if (isset($response['cachedUntil']))
              <small>
                Cached result expires: {{ Carbon\Carbon::parse($response['cachedUntil'])->diffForHumans() }}
              </small>
            @endif

          </h3>
        </div>
        <div class="box-body">
          <pre>{{ print_r($response, true) }}</pre>
        </div><!-- /.box-body -->
      </div>
    </div>
  </div>

@endif
