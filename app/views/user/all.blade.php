@extends('layouts.masterLayout')

@section('html_title', 'All Users')

@section('page_content')


 <div class="box">

  <div class="box-header">
      <h3 class="box-title">All Users @if (count($users) > 0) ({{ count($users) }}) @endif</h3>
  </div>

    <div class="box-body no-padding">
        <table class="table table-condensed table-hover" id="datatable">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Administrator</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

        @foreach($users as $user)

                  <tr>
                      <td>{{ $user->email }}</td>
                      <td>{{ $user->first_name }}</td>
                      <td>{{ $user->last_name }}</td>
                      <td>{{ $user->isSuperUser() ? "Yes" : "No" }}</td>
                      <td>
                <a href="{{ action('UserController@getDetail', array('userID' => $user->getKey())) }}" class="btn btn-default btn-xs"><i class="fa fa-cog"></i> User Details</a>
                @if (count($users) > 1) <a a-delete-user="{{ action('UserController@getDeleteUser', array('userID' => $user->getKey(), 'delete_all_info'=> true)) }}" a-user-name="{{ $user->email }}" class="btn btn-danger btn-xs delete-key"><i class="fa fa-times"></i> Delete</a> @endif
                      </td>
                  </tr>

        @endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop

@section('javascript')
<script type="text/javascript">
    $(document).on("click", ".delete-key", function(e) {

      // Save the links
      var delete_user = $(this).attr("a-delete-user");

      // Provide the user a option to keep the existing data, or delete everything we know about the key
    bootbox.dialog({
      message: "Please confirm whether you want to delete the user?",
      title: "Delete user " + $(this).attr("a-user-name"),
      buttons: {
        success: {
          label: "No Thanks",
          className: "btn-default"
        },
        danger: {
          label: "Delete User",
          className: "btn-danger",
          callback: function() {
            window.location = delete_user;
          }
        }
      }
    });
    });
</script>
@stop