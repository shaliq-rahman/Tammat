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
  <link href="{{ url('datepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet"/>

  
	@include('common.spacer')
	<div class="main-container"  style="margin-top: 50px;">
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
				
    			@if(!empty(session('success')))
    			<div class="col-lg-12">
                        <div class="alert alert-success alert-dismissible fade in" role="alert">
                            <button class="close" aria-label="Close" data-dismiss="alert" type="button">
                              <span aria-hidden="true">×</span>
                            </button>
                             {{ session('success') }}
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
						<h2 class="title-2"  style="color: #fff">
							<i class="icon-mail"></i> {{ t('Messages') }}
						</h2>
                   </div>
						<div id="reloadBtn" class="mb30" style="display: none;">
							<a href="" class="btn btn-primary" class="tooltipHere" title="" data-placement="{{ (config('lang.direction')=='rtl') ? 'left' : 'right' }}"
							   data-toggle="tooltip"
							   data-original-title="{{ t('Reload to see New Messages') }}"><i class="icon-arrows-cw"></i> {{ t('Reload') }}</a>
							<br><br>
						</div>
						
						<div style="clear:both"></div>
						
						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ lurl('account/'.$pagePath.'/delete') }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<label for="checkAll">
										<input type="checkbox" id="checkAll">
										{{ t('Select') }}: {{ t('All') }} |
										<button type="submit" class="btn btn-sm btn-default delete-action">
											<i class="fa fa-trash"></i> {{ t('Delete') }}
										</button>
									</label>
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
										<th style="width:2%" data-type="numeric" data-sort-initial="true">{{ t('Select') }}</th>
										<th style="width:12%" data-sort-ignore="true">{{ t('Subject') }}</th>
										<th style="width:2%" data-type="numeric" data-sort-initial="true">{{ t('From') }}</th>
										<th style="width:2%" data-type="numeric" data-sort-initial="true">{{ t('To') }}</th>
										<th style="width:25%" data-sort-ignore="true">{{ t('Title') }}</th>
										<th style="width:25%" data-sort-ignore="true">{{ t('Photo') }}</th>
										<th style="width:25%">{{ t('Date & Time') }}</th>
										<th style="width:10%">{{ t('Option') }}</th>
									</tr>
									</thead>
									<tbody>
									<?php
									if (isset($conversations) && $conversations->count() > 0):
										foreach($conversations as $key => $conversation):
										    
										    
                                                
                                        $formgetusernamedetail = \DB::table('users')->where('id', '=', $conversation->from_user_id)->first();
                                        $fromusername = !empty($formgetusernamedetail->username)?$formgetusernamedetail->username:'';         
                                        $fromuserphone = !empty($formgetusernamedetail->phone)?$formgetusernamedetail->phone:'';         
                                        
                                        
                                        
                                        $formgetusernamedetail = \DB::table('users')->where('id', '=', $conversation->to_user_id)->first();
                                        $tousername = !empty($formgetusernamedetail->username)?$formgetusernamedetail->username:'';         
                                                                                
                                        $getposttitle = \DB::table('posts')->where('id', '=', $conversation->post_id)->first();
                                        $posttitle = !empty($getposttitle->title)?$getposttitle->title:'';    
                                        
                                        
                                        $getpostpicture = \DB::table('pictures')
                                                        ->where('post_id', '=', $conversation->post_id)
                                                        ->where('position', '=', 1)
                                                        ->where('active', '=', 1)
                                                        ->first();
														
														// Get Post's Pictures
                                        if (!empty($getpostpicture->filename)) {
                                            $postImg = resize($getpostpicture->filename, 'medium');
                                        } else {
                                            $postImg = resize(config('larapen.core.picture.default'));
                                        }
										//echo "xxxxxxxxxxx".$postImg;
										
									?>
							        <?php
								    $selectedquery = '';
								    ?>
								    @if (\App\Models\Message::conversationHasNewMessages($conversation))
									    <?php
									    $selectedquery = 'font-weight: bold;';
									    ?>
						           @endif
									<tr style="<?=$selectedquery?>">
									    
										<td class="add-img-selector">
											<div class="checkbox">
												<label><input type="checkbox" name="entries[]" value="{{ $conversation->id }}"></label>
											</div>
										</td>
										<td>
										    
										    
										     <?php
										    if(auth()->user()->id == $conversation->to_user_id)
                                            {
    											if(!empty($conversation->delivery_info))
    											{ ?>
    									                <button type="button" style="cursor: auto;"  class="btn btn-danger btn-sm">{{ t('Delivery Info') }}</button>
    											<?php 
    											}
    											else
    											{ ?>
    											    @if(!empty($conversation->delivery_preference))
        										      <button type="button" style="cursor: auto;"  class="btn btn-danger btn-sm">{{ t('Buy Now') }}</button>
        										    @else
        										       <button type="button" style="cursor: auto;"  class="btn btn-default btn-sm"> {{ t('Message') }}</button>
        										    @endif       
									    	<?php }
                                            }
                                            else
                                            {
											?>
											    @if(!empty($conversation->delivery_info))
											        <button type="button" style="cursor: auto;"  class="btn btn-danger btn-sm">{{ t('Delivery Info') }}</button>
    											@else
        										    @if(!empty($conversation->delivery_preference))
        										      <button type="button" style="cursor: auto;"  class="btn btn-danger btn-sm">{{ t('Buy Now') }}</button>
        										    @else
        										       <button type="button" style="cursor: auto;"  class="btn btn-default btn-sm"> {{ t('Message') }}</button>
        										    @endif
    										    @endif
										    <?php 
                                            }
										    ?>
										    
									    </td>
										    
										<td>{{ $fromusername }}</td>
										<td>{{ $tousername }}</td>
										<td>
  								            <?php $attr1 = ['slug' => slugify($posttitle), 'id' => $conversation->post_id];
                                            $uri = trans('routes.v-post', ['slug' => slugify($posttitle), 'id' => $conversation->post_id]);
                                            ?>
                                                <a href="{{ lurl($uri, $attr1) }}">
    										    {{ $conversation->subject }}
    										    </a>
                                                <br /># {{$conversation->post_id}}
									    </td>
									    <td>
                                        <img class="thumbnail img-responsive" src="{{ $postImg }}"  style="height: 100px;"  alt="img"></a>
                                        
											@if (!empty($conversation->filename) and \Storage::exists($conversation->filename))
										   <img src="{{url('storage/'.$getpostpicture->filename)}}" style="height: 100px;" alt="&nbsp;">
										   @endif
											<!--<div style="word-break:break-all;">-->
												<!--{{ t('Received at') }}:-->
												<!--{{ $conversation->created_at->formatLocalized(config('settings.app.default_datetime_format')) }}-->
												<!--@if (\App\Models\Message::conversationHasNewMessages($conversation))-->
													<!--<i class="icon-flag text-primary"></i>-->
												<!--@endif-->
												<!--<br>-->
												<!--{{ t('Subject') }}:&nbsp;{{ $conversation->subject }}<br>-->
												<!--{{ t('Started by') }}:&nbsp;{{ \Illuminate\Support\Str::limit($fromusername, 50) }}-->
												
												
												<!--{!! (!empty($conversation->filename) and \Storage::exists($conversation->filename)) ? ' <i class="icon-attach-2"></i> ' : '' !!}&nbsp;|&nbsp;-->
												<!--<a href="{{ lurl('account/conversations/' . $conversation->id . '/messages') }}">-->
												<!--	{{ t('Click here to read the messages') }}-->
												<!--</a>-->
											<!--</div>-->
										</td>
										<td>{{ $conversation->created_at->formatLocalized(config('settings.app.default_datetime_format')) }}</td>
										<td class="action-td">
											<div>
								    	    <?php
    										    if(auth()->user()->id == $conversation->to_user_id)
                                                {
        								// 			if(!empty($conversation->delivery_info))
        								// 			{ 
        											    if($conversation->delivery_preference == 2) 
        											    {
        											?>
    									                <p><a id="{{$conversation->id}}" data-datetime="{{ $conversation->date_time }}" data-phone="{{ $fromuserphone }}" data-title="{{ $conversation->subject }}" data-address="{{ $conversation->buyer_address }}" data-usename="{{$tousername}}" data-val="{{$fromusername}}" class="btn btn-primary btn-sm DeliveryPopup" >{{t('Request a delivery')}}</a></p>
        											<?php 
        											    }
        								// 			}
                                                }
											?>
												<p>
													<a class="btn btn-default btn-sm" href="{{ lurl('account/conversations/' . $conversation->id . '/messages') }}">
														<i class="icon-eye"></i> {{ t('View') }}
													</a>
												</p>
												<p>
													<a class="btn btn-danger btn-sm delete-action" href="{{ lurl('account/conversations/' . $conversation->id . '/delete') }}">
														<i class="fa fa-trash"></i> {{ t('Delete') }}
													</a>
												</p>
											</div>
										</td>
									</tr>
									<?php endforeach; ?>
									<?php endif; ?>
									</tbody>
								</table>
							</form>
						</div>
						
						<div class="pagination-bar text-center">
							{{ (isset($conversations)) ? $conversations->links() : '' }}
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
	
	
	
<div class="modal fade" id="myModalDelivery" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="icon-mail-2"></i> {{ t('Request a delivery') }}</h4>
        </div>
        
    	<form role="form" method="POST" action="{{ url('delivery_post') }}" enctype="multipart/form-data">
    	    {!! csrf_field() !!}
            <div class="modal-body">
			<!-- message -->
			<!--<div class="col-md-6">-->
			
    			<div class="form-group required ">
    				<label for="message" class="control-label">{{ t('Subject') }}</label>
                    <div style="font-size: 14px;" id="getSubject"></div>
                </div>
    			
    			<div class="form-group required ">
    				<label for="message" class="control-label">{{ t('Buyer Username') }} <sup>*</sup></label>
                    <input class="form-control" name="buyername" required readonly id="getBuyerName">				
                    <input class="form-control" name="message_id" type="hidden" id="getMessageId">	
                    <input class="form-control" name="postsubject" type="hidden" id="postsubject">	
    			</div>
    			
    			<div class="form-group required ">
    				<label for="message" class="control-label">{{ t('Buyer Phone') }} <sup>*</sup></label>
                    <input class="form-control" name="phone" required readonly id="getBuyerPhone">				
    			</div>
    			<div class="form-group required ">
    				<label for="getBuyerAddress" class="control-label">{{ t('Buyer Address') }} <sup>*</sup></label>
                    <input class="form-control" name="buyer_address"  readonly id="getBuyerAddress">				
    			</div>
    			
    			
				<div class="form-group required <?php echo (isset($errors) and $errors->has('date_time')) ? 'has-error' : ''; ?>">
		    		<label for="phone" class="control-label">{{ t('Date & Time Preference') }}</label>
					<div style="clear:both"></div>
					<div class="col-md-6" style="padding: 0px;width: 97%;">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input id="getdatetimeBuyer" readonly  type="text"  class="form-control"  >
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
    			
    			
    			<div class="form-group required ">
    				<label for="message" class="control-label">{{ t('Seller Username') }} <sup>*</sup></label>
                    <input class="form-control" name="sellerusername" required readonly id="sellerusername">				
    			</div>
    			

    			<div class="form-group required ">
					<label for="phone" class="control-label">{{t('Date of pick up the item from the seller')}} <sup>*</sup></label>
					<div style="clear:both"></div>
					<div class="col-md-6" style="padding: 0px;width: 97%;">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<input  name="dateofpick" value="{{ old('dateofpick')}}" type="text" id="datepicker" placeholder="{{t('Date of pick up the item from the seller')}}"  class="form-control "  >
						</div>
					</div>
					<div style="clear:both"></div>
				</div>
				
    			
    			
				
				<div class="form-group required"  >
					<label for="seller_address" class="control-label">{{ t('Your Address') }} <sup>*</sup></label>
					<textarea id="seller_address" required name="seller_address"  class="form-control " placeholder="{{ t('Your Address') }}" rows="2">{{ !empty(auth()->user()->address)?auth()->user()->address.', ':'' }}{{ !empty( auth()->user()->city)? auth()->user()->city.', ':'' }}{{ !empty(auth()->user()->zipcode)?auth()->user()->zipcode.', ':'' }}{{ !empty(auth()->user()->state)?auth()->user()->state.', ':'' }}{{ !empty(auth()->user()->country->name)?auth()->user()->country->name:'' }}</textarea>
				</div>
				
				<div class="form-group required ">
						<label for="message" class="control-label">{{t('Message')}} </label>
						<textarea id="message" name="message" class="form-control" placeholder="{{ t('Your message here...') }}" rows="3"></textarea>
				</div>
				
					
			
		</div>
		<div style="clear:both;"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
		  <button type="submit" class="btn btn-success">{{ t('Send') }}</button>
        </div>
        
        </form>
      </div>
    </div>
</div>
	
	

@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	
	<script src="{{ url('datepicker/moment-with-locales.js') }}"></script>
    <script src="{{ url('datepicker/bootstrap-datetimepicker.js') }}"></script>
	
	<script type="text/javascript">
	    $(function () {
                $('#datepicker').datetimepicker();
            });
            
            
            
	    $('table').delegate('.DeliveryPopup','click',function(){
	         $('#myModalDelivery').modal({
                    backdrop: 'static',
                    keyboard: false
            });
            
            var buyername = $(this).attr('data-val');
            var buyerphone = $(this).attr('data-phone');
            
            var messageid = $(this).attr('id');
            var subject = $(this).attr('data-title');
            var sellerusername = $(this).attr('data-usename');
            var buyer_address = $(this).attr('data-address');
            var buyer_datetime = $(this).attr('data-datetime');
            
            
            
            
            $('#getdatetimeBuyer').val(buyer_datetime);
            
            $('#getBuyerPhone').val(buyerphone);
            $('#getBuyerAddress').val(buyer_address);
            
            
            $('#getBuyerName').val(buyername);
            $('#getMessageId').val(messageid);
            $('#getSubject').html(subject);
            $('#postsubject').val(subject);
            $('#sellerusername').val(sellerusername);
	    });
	    

	 
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