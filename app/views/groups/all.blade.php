@extends('layouts.masterLayout')

@section('html_title', 'All Users')

@section('page_content')

<div class="row">

	<div class="col-md-12">
		<div class="box">

			<div class="box-header">
				<h3 class="box-title">Groups</h3>
			</div>

			<div class="box-body">
				<table class="table table-condensed table-hover" id="datatable">
					<thead>
						<tr>
							<th>ID</th>
							<th>Group Name</th>
							<th>Number of Users</th>
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
							</tr> 
						@endforeach

					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->		
	</div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
    $(document).on("click", ".delete-user", function(e) {

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