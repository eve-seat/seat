@if ($errors->has())
  @foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-dismissable">
      <i class="fa fa-ban"></i>
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <b>Error!</b> {{ $error }}
    </div>
  @endforeach
@endif

@if(Session::has('success'))
  <div class="alert alert-success alert-dismissable">
    <i class="fa fa-check"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <b>Success!</b> {{ Session::get('success') }}
  </div>
@endif

@if(Session::has('warning'))
  <div class="alert alert-warning alert-dismissable">
    <i class="fa fa-warning"></i>
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <b>Warning!</b> {{ Session::get('warning') }}
  </div>
@endif
