@extends('layouts.masterLayout')

@section('html_title', 'All Groups')

@section('page_content')

  <div class="row">

    <div class="col-md-8">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Groups</h3>
        </div>

        <div class="box-body">
          <table class="table table-condensed compact table-hover" id="datatable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Group Name</th>
                <th>Number of Users</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>

              @foreach($groups as $group)
              <tr>
                <td>{{ $group->id }}</td>
                <td>{{ $group->name }}</td>
                <td>{{ $counter[$group->name] }}</td>
                <td><a href="{{ action('GroupsController@getDetail', array('groupID' => $group->id)) }}" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Edit</a></td>
                <td><a a-delete-group="{{ action('GroupsController@getDeleteGroup', array('groupID' => $group->id)) }}" a-group-name="{{ $group->name }}" class="btn btn-danger btn-xs delete-group"><i class="fa fa-times"></i> Delete</a></td>
              </tr>
              @endforeach

            </tbody>
          </table>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>

    <div class="col-md-4">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Add New Group</h3>
        </div>

        <div class="box-body table-responsive">

          {{ Form::open(array('action' => 'GroupsController@postNewGroup', 'class' => 'form-horizontal')) }}

            <fieldset>

              <div class="form-group">
                <label class="col-md-4 control-label" for="email">Group Name</label>
                <div class="col-md-6">
                  <div class="input-group">
                    {{ Form::text('groupName', null, array('id' => 'groupName', 'class' => 'form-control', 'placeholder' => 'Group Name'), 'required', 'autofocus') }}
                  </div>
                </div>
              </div>

              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="singlebutton"></label>
                <div class="col-md-6">
                  {{ Form::submit('Add Group', array('class' => 'btn bg-olive btn-block')) }}
                </div>
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
    $(document).on("click", ".delete-group", function(e) {

      // Save the links
      var delete_group = $(this).attr("a-delete-group");

      // Provide the user a option to keep the existing data, or delete everything we know about the key
      bootbox.dialog({
        message: "Please confirm whether you want to delete this group?",
        title: "Delete group " + $(this).attr("a-group-name"),
        buttons: {
          success: {
            label: "No Thanks",
            className: "btn-default"
          },
          danger: {
            label: "Delete Group",
            className: "btn-danger",
            callback: function() {
              window.location = delete_group;
            }
          }
        }
      });
    });
  </script>

@stop
