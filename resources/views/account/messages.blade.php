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
@if(strtoupper(config('app.locale')) == 'AR')
<style>
.number_ltr {
        direction: ltr!important;
        text-align: right;
}
</style>
@endif

	<div class="main-container"   style="margin-top: 50px;">
		<div class="container">
			<div class="row">
				
				@if (Session::has('flash_notification'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
				
				<div class="col-sm-3 page-sidebar">
					@include('account.inc.sidebar')
				</div>
				<!--/.page-sidebar-->
				
				<div class="col-sm-9 page-content">
					<div class="inner-box">
						<div  style="background-color: #ff5555 ; border-radius: 40px;margin:7px;padding:12px 0px 0px 14px;">
						<h2 class="title-2"  style="color: #fff"><i class="icon-mail"></i> {{ t('Messages') }} </h2>
                        </div>
						
						<div style="clear:both"></div>
						
						<?php
						if (isset($conversation) && !empty($conversation) > 0):
						
							// Conversation URLs
							$consUrl = lurl('account/conversations');
							$conDelAllUrl = lurl('account/conversations/' . $conversation->id . '/messages/delete');
						?>
						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ $conDelAllUrl }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<div class="table-search pull-right col-xs-7">
										<div class="form-group">
											<label class="col-xs-5 control-label text-right">{{ t('Search') }} <br>
												<a title="clear filter" class="clear-filter" href="#clear">[{{ t('clear') }}]</a>
											</label>
											<div class="col-xs-7 searchpan">
												<input type="text" class="form-control" id="filter">
											</div>
										</div>
									</div>
								</div>
								
								<table id="addManageTable" class="table table-striped table-bordered add-manage-table table demo" data-filter="#filter" data-filter-text-only="true">
									<thead>
									<tr>
										<th data-sort-ignore="true" colspan="3">
											<a href="{{ $consUrl }}"><i class="icon-level-up"></i> {{ t('Back') }}</a>&nbsp;|&nbsp;
											{{ t("Conversation") }} #{{ $conversation->id }}&nbsp;|&nbsp;
											{{ $conversation->subject }}
										</th>
									</tr>
									</thead>
									<tbody>
									<!-- Main Conversation -->
									<tr>
										<td colspan="3">
										    <?php
                                                // if(auth()->user()->id == $conversation->from_user_id)
                                                // {
                                                //     $formgetusernamedetail = \DB::table('users')->where('id', '=', $conversation->to_user_id)->first();
                                                //     $fromusername = !empty($formgetusernamedetail->username)?$formgetusernamedetail->username:'';             
                                                // }
                                                // else
                                                // {
                                               
                                                // }
                                                $formgetusernamedetail = \DB::table('users')->where('id', '=', $conversation->from_user_id)->first();
                                                $fromusername = !empty($formgetusernamedetail->username)?$formgetusernamedetail->username:'';                 
                                                
                                             ?>
											<strong>{{ t("Sender's Name") }}:</strong> {{ $fromusername or '--' }}<br>
											<strong>{{ t("Sender's Email") }}:</strong> {{ $conversation->from_email or '--' }}<br>
											<strong>{{ t("Sender's Phone") }}:</strong><div style="display: inline;" class="number_ltr">{{ $conversation->from_phone or '--' }}</div><br>
											<hr>
											{!! nl2br($conversation->message) !!}
											<div style="clear:both;"></div>
											
											<?php
											if(!empty($conversation->delivery_preference))
											{ ?>
											    <br />
											    <div class="col-md-3" style="padding: 0px;">
											        {{ t('Delivery Preference:') }}
											    </div>
											    <div class="col-md-9" style="padding: 0px;">
										            @if($conversation->delivery_preference == 1)   
										                {{ t('Pick up the item myself') }}
										            @elseif($conversation->delivery_preference == 2)   
										                {{ t('Shipped or deliverd to me') }}
										            @endif
									            </div>
										<?php	}
											?>
											
											
												
											<?php
											if(!empty($conversation->date_time))
											{ ?>
											    <br />
											    <div class="col-md-3" style="padding: 0px;">
											        {{ t('Date & Time Preference') }}:  
											    </div>
											    <div class="col-md-9" style="padding: 0px;">
											        {{ $conversation->date_time }}
									            </div>
										<?php	}
											?>
											
											<?php
											if(!empty($conversation->delivery_preference))
											{ ?>
											    <br />
											    <div class="col-md-3" style="padding: 0px;">
											        {{ t('Buyer Address') }}:
											    </div>
											    <div class="col-md-9" style="padding: 0px;">
										            @if($conversation->delivery_preference == 2)   
										               {{ $conversation->buyer_address }}
										            @endif
									            </div>
										    <?php	
											}
											?>
											
											
										    <div style="clear:both;"></div>
											@if (!empty($conversation->filename) and \Storage::exists($conversation->filename))
												<br><br><a class="btn btn-info" href="{{ \Storage::url($conversation->filename) }}">{{ t('Download') }}</a>
											@endif
											<hr>
											<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#replyTo{{ $conversation->id }}">
												<i class="icon-reply"></i> {{ t('Reply') }}
											</a>
										</td>
									</tr>
									<!-- All Conversation's Messages -->
									<?php
									if (isset($messages) && $messages->count() > 0):
										foreach($messages as $key => $message):
									?>
									
					    			 <?php
                                        $formgetusernamedetailmessage = \DB::table('users')->where('id', '=', $message->from_user_id)->first();
                                        $fromusernamemessage = $formgetusernamedetailmessage->username;             
                                     ?>
                                         
									<tr>
										@if ($message->from_user_id == auth()->user()->id)
											<td class="add-img-selector">
												<div class="checkbox" style="width:2%">
													<label><input type="checkbox" name="entries[]" value="{{ $message->id }}"></label>
												</div>
											</td>
											<td style="width:88%;">
												<div style="word-break:break-all;">
													<strong>
		                                            <i class="icon-reply"></i> {{ $fromusernamemessage }}: <span style="font-weight: normal;margin-left: 10px;">({{ $message->created_at->formatLocalized(config('settings.app.default_datetime_format')) }})</span>
													</strong><br>
													{!! nl2br($message->message) !!}
													@if (!empty($message->filename) and \Storage::exists($message->filename))
														<br><br><a class="btn btn-info" href="{{ \Storage::url($message->filename) }}">{{ t('Download') }}</a>
													@endif
												</div>
											</td>
											<td class="action-td" style="width:10%">
												<div>
													<p>
														<?php $conDelUrl = lurl('account/conversations/' . $conversation->id . '/messages/' . $message->id . '/delete'); ?>
														<a class="btn btn-danger btn-sm delete-action" href="{{ $conDelUrl }}">
															<i class="fa fa-trash"></i> {{ t('Delete') }}
														</a>
													</p>
												</div>
											</td>
										@else
											<td colspan="3">
												<div style="word-break:break-all;">
													<strong>{{ $fromusernamemessage }}: <span style="font-weight: normal;margin-left: 10px;">({{ $message->created_at->formatLocalized(config('settings.app.default_datetime_format')) }})</span></strong><br>
													{!! nl2br($message->message) !!}
													@if (!empty($message->filename) and \Storage::exists($message->filename))
														<br><br><a class="btn btn-info" href="{{ \Storage::url($message->filename) }}">{{ t('Download') }}</a>
													@endif
												</div>
											</td>
										@endif
									</tr>
									<?php endforeach; ?>
									<?php endif; ?>
									</tbody>
								</table>
								
								<div class="table-action">
									<label for="checkAll">
										<input type="checkbox" id="checkAll">
										{{ t('Select') }}: {{ t('All') }} |
										<button type="submit" class="btn btn-sm btn-default delete-action">
											<i class="fa fa-trash"></i> {{ t('Delete') }}
										</button>
									</label>
								</div>
								
							</form>
						</div>
						
						<div class="pagination-bar text-center">
							{{ (isset($messages)) ? $messages->links() : '' }}
						</div>
						<?php endif; ?>
						
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
	
	@if (isset($conversation) && $conversation->count() > 0)
		@include('account.inc.reply-message')
	@endif

@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function () {
			$('#addManageTable').footable().bind('footable_filtering', function (e) {
				var selected = $('.filter-status').find(':selected').text();
				if (selected && selected.length > 0) {
					e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
					e.clear = !e.filter;
				}
			});
			
			$('.clear-filter').click(function (e) {
				e.preventDefault();
				$('.filter-status').val('');
				$('table.demo').trigger('footable_clear_filter');
			});
			
			$('#checkAll').click(function () {
				checkAll(this);
			});
			
			$('a.delete-action, button.delete-action').click(function(e)
			{
				e.preventDefault(); /* prevents the submit or reload */
				var confirmation = confirm("{{ t('Are you sure you want to perform this action?') }}");
				
				if (confirmation) {
					if( $(this).is('a') ){
						var url = $(this).attr('href');
						if (url !== 'undefined') {
							redirect(url);
						}
					} else {
						$('form[name=listForm]').submit();
					}
				}
				
				return false;
			});
		});
	</script>
	<!-- include custom script for ads table [select all checkbox]  -->
	<script>
		function checkAll(bx) {
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type == 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection