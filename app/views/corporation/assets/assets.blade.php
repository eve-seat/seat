@extends('layouts.masterLayout')

@section('html_title', 'Corporation Assets')

@section('page_content')

<div class="row">
	<div class="col-md-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title">Assets ({{ $assets_count }}) for {{ $corporation_name->corporationName }}</h3>
			</div><!-- /.box-header -->
			<div class="box-body no-padding">
				@foreach ($assets_list as $location => $assets)
					<div class="box box-solid box-primary">
						<div class="box-header">
							<h3 class="box-title">{{ $location }} ({{ count($assets) }})</h3>
							<div class="box-tools pull-right">
								<button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>	
							</div>
						</div>
						<div class="box-body no-padding">	
							<div class="row">
								@foreach (array_chunk($assets, ceil(count($assets) / 2)) as $column)
									<div class="col-md-6">
										<table class="table table-hover table-condensed">
										<tbody>
											<tr>
												<th style="width: 40px">#</th>
												<th style="width: 50%" colspan="2">Type</th>
												<th>Group</th>
												<th style="width: 50px"></th>
											</tr>
										</tbody>
											@foreach ($column as $asset)
												<tbody style="border-top:0px solid #FFF">
													<tr class="item-container">
														<td>{{ App\Services\Helpers\Helpers::formatBigNumber($asset['quantity']) }}</td>
														<td colspan="2">
															<span data-toggle="tooltip" title="" data-original-title="{{ number_format($asset['quantity'], 0, '.', ' ') }} x {{ $asset['typeName'] }}">
																<img src='http://image.eveonline.com/Type/{{ $asset['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
																{{ str_limit($asset['typeName'], 35, $end = '...') }} {{ isset($asset['contents']) ? "(". count($asset['contents']) . ")" : "" }}
															</span>
														</td>
														<td>
															<span data-toggle="tooltip" title="" data-original-title="{{ $asset['groupName'] }}">
																{{ str_limit($asset['groupName'], 40, $end = '...') }}
															</span>
														</td>
														@if(isset($asset['contents']))
															<td style="text-align: right"><i class="fa fa-plus viewcontent" style="cursor: pointer;"></i></td>
														@else
															<td></td>
														@endif
													</tr>
												</tbody>
												@if(isset($asset['contents']))
													<tbody style="border-top:0px solid #FFF" class="tbodycontent">
														@foreach ($asset['contents'] as $content)
															<tr class="hidding">
																<td>{{ App\Services\Helpers\Helpers::formatBigNumber($content['quantity']) }}</td>
																<td style="width: 18px;"></td>
																<td>
																	<span data-toggle="tooltip" title="" data-original-title="{{ number_format($content['quantity'], 0, '.', ' ') }} x {{ $content['typeName'] }}">
																		<img src='http://image.eveonline.com/Type/{{ $content['typeID'] }}_32.png' style='width: 18px;height: 18px;'>
																		{{ str_limit($content['typeName'], 30, $end = '...') }}
																	</span>
																</td>
																<td>
																	<span data-toggle="tooltip" title="" data-original-title="{{ $content['groupName'] }}">
																		{{ str_limit($content['groupName'], 25, $end = '...') }}
																	</span>
																</td>
																<td></td>
															</tr>
														@endforeach
													</tbody>
												@endif
											@endforeach
										</table>
									</div> <!-- /.col-md-6 -->
								@endforeach 
							</div> <!-- /.row -->
						</div><!-- /.box-body -->
					</div> <!-- ./box -->
				@endforeach	
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div> <!-- ./col-md-12 -->
</div> <!-- ./row -->

@stop

@section('javascript')
<script type="text/javascript">

	// First Hide all contents. Not very clean to add a fake class.. TODO: Think another way to do this
	$(".tbodycontent").hide(); 
	// on button click. Not very clean to add a fake class.. TODO: Think another way to do this
	$(".viewcontent").on("click", function( event ){ 
		// get the tbody tag direct after the button
		var contents = $(this).closest( "tbody").next( "tbody" ); 
		// Show or hide
		contents.toggle();

		// some code for stylish
		if (contents.is(":visible")){
			$(this).removeClass('fa-plus').addClass('fa-minus');
			$(this).closest("tr").css( "background-color", "#EBEBEB" ); // change the background color of container (for easy see where we are)
			contents.css( "background-color", "#EBEBEB" ); // change the background color of content (for easy see where we are)
		} else {
			$(this).removeClass('fa-minus').addClass('fa-plus'); 
			$(this).closest("tr").css( "background-color", "#FFFFFF" ); // reset the background color on container when we hide content
		}
	});

</script>
@stop
