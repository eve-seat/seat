@extends('layouts.masterLayout')

@section('html_title', 'View Job Error')

@section('page_content')

<div class="row">
	<div class="col-md-12">
		<p class="lead"><b>Job Details for Job {{ $detail->id }}:</b></p>
		<p>
			<i>If this is not a error that you understand, or a developer would like to see the stack trace,
			please copy the full content of the below block, paste it in <a href="http://pastebin.com/">Pastebin</a>
			and send the link along.</i>
		</p>
		<p>
			<ul class="list-unstyled">
				<li><b>JobID: </b>{{ $detail->jobID }}</li>
				<li><b>OwnerID: </b>{{ $detail->ownerID }}</li>
				<li><b>API: </b>{{ $detail->api }}</li>
				<li><b>Scope: </b>{{ $detail->scope }}</li>
				<li><b>Status: </b>{{ $detail->scope }}</li>
				<li><b>Created At: </b>{{ $detail->created_at }} ({{ Carbon\Carbon::parse($detail->created_at)->diffForHumans() }})</li>
				<li><b>Last Updated At: </b>{{ $detail->updated_at }} ({{ Carbon\Carbon::parse($detail->updated_at)->diffForHumans() }})</li>
			</ul>
		</p>
		<pre>{{ $detail->output }}</pre>
	</div>
</div>

@stop
