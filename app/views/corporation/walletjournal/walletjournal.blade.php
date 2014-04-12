@extends('layouts.masterLayout')

@section('html_title', 'Corporation Wallet Journal')

@section('page_content')

<div class="row">
	<div class="col-md-12">

		<div class="box">
		    <div class="box-header">
		        <h3 class="box-title">Wallet Journal for: {{ $corporation_name->corporationName }}</h3>
		        <div class="box-tools">
		            <ul class="pagination pagination-sm no-margin pull-right">
						{{ $wallet_journal->links() }}
		            </ul>
		        </div>
		    </div><!-- /.box-header -->
		    <div class="box-body no-padding">
                <table class="table table-condensed table-hover">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Wallet Division</th>
                            <th>Type</th>
                            <th>Owner1 Name</th>
                            <th>Owner2 Name</th>
                            <th>Amount</th>
                            <th>Balance</th>
                        </tr>
                        @foreach ($wallet_journal as $e)
                            <tr @if ($e->amount < 0)class="danger" @endif>
                                <td>
                                	<spanp data-toggle="tooltip" title="" data-original-title="{{ $e->date }}">
                                		{{ Carbon\Carbon::parse($e->date)->diffForHumans() }}
                                	</span>
                                </td>
                                <td>{{ $e->description }}</td>
                                <td>{{ $e->refTypeName }}</td>
                                <td>{{ $e->ownerName1 }}</td>
                                <td>{{ $e->ownerName2 }}</td>
                                <td>
                                	@if ($e->amount < 0)
                                    	<span class="text-red">{{ number_format($e->amount, 2, '.', ' ') }}</span>
                                    @else
                                    	{{ number_format($e->amount, 2, '.', ' ') }}
                                    @endif
                                </td>
                                <td>{{ number_format($e->balance, 2, '.', ' ') }}</td>
                            </tr>
                        @endforeach

                	</tbody>
               	</table>
		    </div><!-- /.box-body -->
		    <div class="pull-right">{{ $wallet_journal->links() }}</div>
		</div>
	</div>
</div>
	
@stop
