@extends('layouts.masterLayout')

@section('html_title', 'All Keys')

@section('page_content')


 <div class="box">


	<div class="box-header">
	    <h3 class="box-title">All API Keys @if (count($key_info) > 0) ({{ count($key_info) }}) @endif</h3>
	</div>

    <div class="box-body no-padding">
        <table class="table table-condensed table-hover" id="datatable">
            <thead>
                <tr>
                    <th>KeyID</th>
                    <th>Type</th>
                    <th>Access Mask</th>
                    <th>Expires</th>
                    <th>Characters On Key</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

				@foreach($key_info as $key)

	                <tr @if ($key['isOk'] <> 1) class="danger" @endif>
	                    <td>{{ $key['keyID'] }}</td>
	                    <td>{{ $key['type'] }}</td>
	                    <td>{{ $key['accessMask'] }}</td>
	                    <td>{{ $key['expires_human'] }}</td>
	                    <td>
	                    	@if (isset($key['characters']))
		                    	@foreach($key['characters'] as $char)
		                    		<a href="{{ action('CharacterController@getView', array('characterID' => $char['characterID'])) }}">
		                    			<img src='//image.eveonline.com/Character/{{ $char['characterID'] }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
		                    			{{ $char['characterName'] }}
		                    		</a>
		                    	@endforeach
		                    @else
		                    	No known characters for this keyID, maybe its still updating or entirely invalid/expired.
		                    @endif
		                    <span class="pull-right">
                                @if ($key['ban_count'] > 0)
                                    <span class="text-muted" data-toggle="tooltip" title="" data-original-title="{{ $key['ban_count'] }} banned API Call(s)">{{ $key['ban_count'] }}</span>
                                @endif
					        	@if (strlen($key['lastError']) > 0)
						        	<i class="fa fa-bullhorn pull-right" data-container="body" data-toggle="popover" data-placement="top" data-content="{{ $key['lastError'] }}" data-trigger="hover"></i>
						        @endif
		                    </span>
	                    </td>
	                    <td>
							<div class="btn-group">
								<a href="{{ action('ApiKeyController@getDetail', array('keyID' => $key['keyID'])) }}" class="btn btn-default btn-xs"><i class="fa fa-cog"></i> Key Details</a>
								<a href="{{ action('ApiKeyController@getDeleteKey', array('keyID' => $key['keyID'])) }}" class="btn btn-danger btn-xs confirmlink"><i class="fa fa-times"></i> Delete</a>
							</div>
	                    </td>
	                </tr>

				@endforeach

            </tbody>
        </table>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop
