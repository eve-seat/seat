@extends('layouts.masterLayout')

@section('html_title', 'Group Details: '.$group->name)

@section('page_content')

  <div class="row">
    <div class="col-md-6">
      <div class="box">

        <div class="box-header">
          <h3 class="box-title">Current Members</h3>
        </div>

        <div class="box-body">
          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <tr>
                <th>User</th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              @foreach($users as $user)

                <tr>
                  <td>{{ $user->username }} ({{ $user->email }})</td>
                  <td><a a-remove-user="{{ action('GroupsController@getRemoveUser', array('userID' => $user->id, 'groupID' => $group->id)) }}" a-user-name="{{ $user->username }} ({{ $user->email }})" a-remove-user-from="{{ $group->name }}" class="btn btn-danger btn-xs remove-user"><i class="fa fa-times"></i> Remove from group</a></td>
                </tr>

              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>

    <div class="col-md-6">
      <div class="box">

        <div class="box-header">
          <h3 class="box-title">Group Permissions</h3>
        </div>

        <div class="box-body table-responsive">

          {{ Form::open(array('action' => array('GroupsController@postUpdateGroup', $group->id), 'class' => 'form-horizontal')) }}

            <fieldset>

              @foreach ($available_permissions as $permission)

                <div class="form-group">
                  <label class="col-md-6 control-label" for="singlebutton">{{ ucwords(str_replace("_", " ", $permission->permission)) }}</label>
                  <div class="form-group">
                    {{ Form::checkbox($permission->permission, '1', (isset($has_permissions[$permission->permission]) ? true : false)) }}
                  </div>
                </div>

              @endforeach

              <div class="form-group">
                <label class="col-md-6 control-label" for="singlebutton">Super User</label>
                <div class="form-group">
                  {{ Form::checkbox('superuser', '1', (isset($has_permissions['superuser']) ? true : false)) }}
                </div>
              </div>

              <!-- Button -->

              <div class="col-md-6">
                {{ Form::submit('Update Group Permissions', array('class' => 'btn bg-olive btn-block')) }}
              </div>

            </fieldset>

          {{ Form::close() }}

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  </div>

@stop

@section('javascript')

  <script type="text/javascript">
    $(document).on("click", ".remove-user", function(e) {

      // Save the links
      var remove_user = $(this).attr("a-remove-user");

      // Provide the user a option to keep the existing data, or delete everything we know about the key
      bootbox.dialog({
        message: "Please confirm whether you want to remove this user?",
        title: "Remove " + $(this).attr("a-user-name") + " from " + $(this).attr("a-remove-user-from"),
        buttons: {
          success: {
            label: "No Thanks",
            className: "btn-default"
          },
          danger: {
            label: "Remove User from Group",
            className: "btn-danger",
            callback: function() {
              window.location = remove_user;
            }
          }
        }
      });
    });
  </script>

@stop
