{{--
	* LaraClassified - Geo Classified Ads CMS
	* Copyright (c) BedigitCom. All Rights Reserved
	*
	* Website: http://www.bedigit.com
	*
	* LICENSE
	* -------
	* This software is furnished under a license and may be used and copied
	* only in accordance with the terms of such license and with the inclusion
	* of the above copyright notice. If you Purchased from Codecanyon,
	* Please read the full License from here - http://codecanyon.net/licenses/standard
	--}}
	@extends('layouts.master')

	@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">
				<div class="col-sm-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->
				
				<div class="col-sm-9 page-content">
					<div class="inner-box">
						<h2 class="title-2"><i class="glyphicon glyphicon-hand-left"></i> {{ t('Offers Maker') }} </h2>
						
						<div style="clear:both"></div>
						
						
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><span>{{ t('ID') }}</span></th>
										<th>{{ t('From') }}</th>
										<th>{{ t('To') }}</th>
										<th>{{ t('Date & Time') }}</th>
										<!--<th>{{ t('Description') }}</th>-->
										<!--<th>{{ t('Original Price') }}</th>-->
										<!--<th>{{ t('Offer Price') }}</th>-->
										<th>{{ t('Status') }}</th>
										<th>{{ t('Photo') }}</th>
										<!--<th>{{ t('Add Information') }}</th>-->
										
										<th>{{ t('Option') }}</th>
									</tr>
								</thead>
								<tbody>
									<?php

									if (isset($makeanoffers) && $makeanoffers->count() > 0):
										foreach($makeanoffers as $key => $makeanoffer):
										    $status_counter_offer = 0;
											$picture = \App\Models\Picture::where(['post_id' => $makeanoffer->post_id, 'position'=> 1])->get();
											if (!empty($picture[0]->filename)) {
												$postImg = resize($picture[0]->filename, 'medium');
											} else {
												$postImg = resize(config('larapen.core.picture.default'));
											}
											$countryFlagPath = 'images/flags/16/' . strtolower($makeanoffer->country_code) . '.png';
											
											$getpostprice = \DB::table('posts')->where('id', '=', $makeanoffer->post_id)->first();
											$postprice = $getpostprice->price;    
											$statuscounter = '';
											$rejected = '';
											
											
											
											$getcurrencycountry = \DB::table('countries')
											->join('currencies', 'currencies.code', '=', 'countries.currency_code')
											->select('currencies.*')
											->where('countries.code', '=', $getpostprice->country_code)
											->first();
											
											$SendDeliveryInfo = '';
											?>
											@if($makeanoffer->close_offer == 1)
											<?php
											$statuscounter = '<button style="cursor: auto;" class="btn btn-danger btn-sm">'.t('Closed').'</button>';
											?>
											
											@else
											@if(auth()->user()->id != $makeanoffer->offer_maker_id || $makeanoffer->approve_seller == 1 || $makeanoffer->approve_seller == 2)
											@if($makeanoffer->approve_seller == 1)
											@if(auth()->user()->id != $makeanoffer->offer_maker_id)
											<?php
											$statuscounter = '<button style="cursor: auto;" class="btn btn-success btn-sm">'.t('Deal').'</button>';
											?>
											@else
											<?php
											$SendDeliveryInfo = '<a data-offerid="'.$makeanoffer->id.'" data-postid="'.$makeanoffer->post_id.'"  data-val="'.lurl('posts/' . $makeanoffer->post_id . '/contact').'" id="proceedDeliveryPopup" style="margin-top: 10px;"  class="btn btn-primary btn-sm">'.t('Send Delivery Info').'</a>';
											$statuscounter = '<button style="cursor: auto;" class="btn btn-success btn-sm">'.t('Accepted').'</button>';
											?>
											@endif	
											@else
											@if($makeanoffer->approve_seller == 2)
											@if(auth()->user()->id != $makeanoffer->offer_maker_id)
											<?php
											    $status_counter_offer = 1;
									            $statuscounter = '<button style="cursor: auto;" class="btn btn-danger btn-sm">'.t('Counter-Offer').'</button>';
										            
											?>
											@else
											<?php
											$rejected = 1;
											$statuscounter = '<button style="cursor: auto;background: lightgray;border: none;color: gray;" class="btn btn-danger btn-sm">'.t('Rejected').'</button>';
											?>
											@endif	    
											@else
											<?php
											$statuscounter = '<button style="cursor: auto;" class="btn btn-danger btn-sm">'.t('New Offer').'</button>';
											?>
											@endif	        
											@endif	
											@else
											<?php
											$statuscounter = '<button style="cursor: auto;" class="btn btn-danger btn-sm">'.t('Awaiting Response').'</button>';
											?>
											
											@endif
											@endif
											
												<?php
													$sellername = \DB::table('users')
    													->where('id', '=', $makeanoffer->seller_id)
    													->select('username')
    													->first();
    													$sellername_name =  !empty($sellername->username)?$sellername->username:'';	

													$buyername = \DB::table('users')
    													->where('id', '=', $makeanoffer->buyer_id)
    													->select('username')
    													->first();
    													$buyername_name = !empty($buyername->username)?$buyername->username:'';		
													?>
													
													
													
											<tr style="<?=!empty($rejected)?'color:lightgray;':''?>">
												<td>#{{ $makeanoffer->id }}</td>
												<td>
												    @if(!empty($status_counter_offer))
												        {{ $sellername_name }}
												    @else
												       {{ $buyername_name }}
												    @endif
												</td>
												<td>
												    @if(!empty($status_counter_offer))
											            {{ $buyername_name }}
												    @else
												       {{ $sellername_name }}
												    @endif
												</td>
												<td>
													<!--<strong><i class="icon-clock" title="{{ t('Posted On') }}"></i></strong>&nbsp;-->
													{{ $makeanoffer->created_at}}
												</td>
												<!--<td>-->
													<!--	{{ $makeanoffer->description_text }}-->
													<!--</td>-->
													<!--<td>-->
														<!--	{{ $makeanoffer->original_price }}-->
														<!--</td>-->
														<!--<td>-->
															<!--	{{ $makeanoffer->offer_price }}-->
															<!--</td>-->
															
															<td>
																<?=$statuscounter?> 
																<br />
																<?=$SendDeliveryInfo?> 
																
															</td>
															
															
															
															<td style="width:25%" class="add-img-td">

																<a  target="_blank" href="{{ lurl('/').'/'.slugify($makeanoffer->title).'/'.$makeanoffer->post_id }}"><img style="<?=!empty($rejected)?'opacity: 0.4;':''?>" class="thumbnail img-responsive" src="{{ $postImg }}" alt="img"></a>
																<strong>
																	<a target="_blank" href="{{ lurl('/').'/'.slugify($makeanoffer->title).'/'.$makeanoffer->post_id }}" title="{{ $makeanoffer->title }}">{{ str_limit($makeanoffer->title, 40) }}</a>
																</strong>
																<p>	
																	
																	@if ($postprice > 0)
																	{!! \App\Helpers\Number::money_price_latest($postprice,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator) !!}
																	@else
																	{{t('Free')}}
																	@endif
																	
																</p>
															</td>
															
															
															<!--<td style="width:58%" class="ads-details-td">-->
																<!--		<div>-->
																	<!--			<p>-->
																		<!--@if (in_array($pagePath, ['my-posts', 'archived', 'pending-approval']))-->
																		<!--	@if (isset($makeanoffer->latestPayment) and !empty($makeanoffer->latestPayment))-->
																		<!--		@if (isset($makeanoffer->latestPayment->package) and !empty($makeanoffer->latestPayment->package))-->
																		<?php
																// if ($makeanoffer->featured == 1) {
																// 	$color = $makeanoffer->latestPayment->package->ribbon;
																// 	$packageInfo = '';
																// } else {
																// 	$color = '#ddd';
																// 	$packageInfo = ' (' . t('Expired') . ')';
																// }
																		?>
																		<!--			<i class="fa fa-check-circle tooltipHere" style="color: {{ $color }};" title="" data-placement="bottom"-->
																			<!--			   data-toggle="tooltip" data-original-title="{{ $makeanoffer->latestPayment->package->short_name . $packageInfo }}"></i>-->
																			<!--		@endif-->
																			<!--	@endif-->
																			<!--@endif -->
																			<!--                                   </p>-->
																			
																			<!--<p>-->
																				<!--	<strong><i class="icon-eye" title="{{ t('Visitors') }}"></i></strong> {{ $makeanoffer->visits or 0 }}-->
																				<!--	<strong><i class="fa fa-map-marker" title="{{ t('Located In') }}"></i></strong> {{ !empty($makeanoffer->city) ? $makeanoffer->city->name : '-' }}-->
																				<!--@if (file_exists(public_path($countryFlagPath)))-->
																				<!--<img src="{{ url($countryFlagPath) }}" data-toggle="tooltip" title="{{ $makeanoffer->country_code }}">-->
																				<!--			@endif-->
																				<!--		</p>-->
																				<!--	</div>-->
																				<!--</td>-->
																				
																				<td>
																					@if ($makeanoffer->status == 1)
																					<p>
																						<a class="btn btn-info btn-sm" href="{{ lurl('account/makeanoffers/'.$makeanoffer->post_id.'/edit/'. $makeanoffer->id)}}">
																							<i class="fa fa-eye"></i> {{ t('View') }}
																						</a>
																					</p>
																					<p>
																						<a class="btn btn-danger btn-sm" href="{{ lurl('account/makeanoffers/'.$makeanoffer->id.'/delete' ) }}">
																							<i class="fa fa-trash"></i> {{ t('Delete') }}
																						</a>
																					</p>
																					@else
																					{{ t('Not Active') }}
																					@endif
																				</td>
																			</tr>
																		<?php endforeach; ?>
																	<?php endif; ?>
																</tbody>
															</table>
														</div>
														
														<div class="pagination-bar text-center">
															
														</div>
														
														<div style="clear:both"></div>
														
													</div>
												</div>
												<!--/.page-content-->
												
											</div>
											<!--/.row-->
										</div>
										<!--/.container-->
									</div>
									<!-- /.main-container -->
									@endsection
                                    @section('modal_message')
												@if (auth()->check())
										        	@include('post.inc.proceeddelivery') 
												@endif
									@section('after_scripts')
										<script>
                                    		$(document).ready(function () {
                                    		    $('#proceedDeliveryPopup').click(function(){
                                    		        var action = $(this).attr('data-val');
                                    		        var offerid = $(this).attr('data-offerid');
                                    		        var postid = $(this).attr('data-postid');
                                    		        
                                    		        $('#proceedDeliveryForm').attr('action',action);
                                    		        $('#get_offer_id').val(offerid);
                                    		        $('#get_post_id').val(postid);
                                    		        
                                    		        $('#proceedDelivery').modal('show');
                                    		        
                                    		        
                                    		    });
                                    		});
                                		</script>
									@endsection