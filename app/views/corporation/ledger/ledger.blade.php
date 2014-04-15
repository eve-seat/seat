@extends('layouts.masterLayout')

@section('html_title', 'Corporation Ledger')

@section('page_content')

<div class="row">
	<div class="col-md-3">

		<div style="margin-top: 15px;">
		    <ul class="nav nav-pills nav-stacked">
		        <li class="header">Summarized Ledger</li>
			        <li class="active"><a href="#"><i class="fa fa-calendar-o"></i> Today ( {{ Carbon\Carbon::now()->toDateString() }} )</a></li>
		        <li class="header">Available Ledgers</li>
		        @foreach ($ledger_dates as $ledger_date)
			        <li><a href="#"><i class="fa fa-calendar-o"></i> {{ Carbon\Carbon::parse($ledger_date)->year }}-{{ Carbon\Carbon::parse($ledger_date)->month }}</a></li>
			    @endforeach
		    </ul>
		</div>

	</div>

	<div class="col-md-9">
		<h3>Current Ledger Summary</h3>

		<hr>

		{{-- wallet division balances --}}
		<div class="box box-solid box-primary">
		    <div class="box-header">
		        <h3 class="box-title">Corporation Account Balances</h3>
		        <div class="box-tools pull-right">
		            <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
		            <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
		        </div>
		    </div>
		    <div class="box-body no-padding">
				<table class="table table-condensed">
				    <tbody>
				    	<tr>
					        <th>Account ID</th>
					        <th>Wallet Division Name</th>
					        <th>Balance</th>
					        <th>Last Updated</th>
					    </tr>
					    @foreach ($wallet_balances as $wallet_division)
						    <tr>
						        <td>{{ $wallet_division->accountID }}</td>
						        <td>{{ $wallet_division->description }}</td>
						        <td>{{ number_format($wallet_division->balance, 2, '.', ' ') }} ISK</td>
						        <td>{{ Carbon\Carbon::parse($wallet_division->updated_at)->diffForHumans() }}</td>
						    </tr>
					    @endforeach
					</tbody>
				</table>
		    </div><!-- /.box-body -->
		</div>

		{{-- wallet ledger --}}
		<div class="box box-solid box-success">
		    <div class="box-header">
		        <h3 class="box-title">Global Wallet Ledger</h3>
				<div class="box-tools pull-right">
				    <button class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
				    <button class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
		    </div>
		    <div class="box-body no-padding">
				<table class="table table-condensed">
				    <tbody>
				    	<tr>
					        <th>Transaction Type</th>
					        <th>Amount</th>
					    </tr>
					    @foreach ($ledger as $entry)
						    <tr>
						        <td>{{ $entry->refTypeName }}</td>
						        <td>
						        	@if ($entry->total < 0)
							        	<span class="text-red">{{ number_format($entry->total, 2, '.', ' ') }} ISK</span>
							        @else
							        	{{ number_format($entry->total, 2, '.', ' ') }} ISK
							        @endif
						        </td>
						    </tr>
					    @endforeach
					</tbody>
				</table>
		    </div><!-- /.box-body -->
		</div>

	</div>
</div>
@stop
