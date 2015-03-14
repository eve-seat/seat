@extends('layouts.masterLayout')

@section('html_title', 'Group Details: '.$group->name)

@section('page_content')

  <div class="row">

    <div class="col-md-6">

      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">
                <b>Current Members</b>
              </h3>
            </div>
            <div class="panel-body">

              <table class="table table-condensed compact table-hover" id="datatable">
                <thead>
                <th>User</th>
                <th></th>
                </thead>
                <tbody>

                  @foreach($users as $user)

                    <tr>
                      <td>{{ $user->username }} ({{ $user->email }})</td>
                      <td>
                        <a a-remove-user="{{ action('GroupsController@getRemoveUser', array('userID' => $user->id, 'groupID' => $group->id)) }}" a-user-name="{{ $user->username }} ({{ $user->email }})" a-remove-user-from="{{ $group->name }}" class="btn btn-danger btn-xs pull-right remove-user">
                          <i class="fa fa-times"></i> Remove from group</a>
                        </td>
                    </tr>

                  @endforeach

                </tbody>
              </table>

            </div>

          </div>

        </div> <!-- col-md-12 -->
      </div> <!-- row -->

    </div> <!-- global col-md-8 -->

    <div class="col-md-6">

      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <b>Update Group Permissions</b>
          </h3>
        </div>
        <div class="panel-body">

          {{ Form::open(array('action' => array('GroupsController@postUpdateGroup', $group->id), 'class' => 'form-horizontal')) }}

            <fieldset>

              <!-- Select Multiple -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="permissions">Group Permissions</label>
                <div class="col-md-4">
                  <select id="permissions" name="permissions[]" class="form-control" multiple="multiple">

                    @foreach ($available_permissions as $permission)

                      <option value="{{ $permission->permission }}"{{{ isset($has_permissions[$permission->permission]) ? 'selected' : '' }}} >
                        {{ ucwords(str_replace("_", " ", $permission->permission)) }}
                      </option>

                    @endforeach

                  </select>
                </div>
              </div>

              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="singlebutton"></label>
                <div class="col-md-4">
                  <button id="singlebutton" name="singlebutton" class="btn btn-primary"><i class="fa fa-plus"></i> Update group permissions</button>
                </div>
              </div>

            </fieldset>

          {{ Form::close()}}

        </div>
        <div class="panel-footer">
          The <b>Super User</b> Permissions inherits <b>all</b> permissions!
        </div>
      </div>

    </div>

  </div>

@stop

@section('javascript')

  <script type="text/javascript">

    $("#permissions").select2();

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
