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
							<i class="icon-mail"></i> Favorite Users
						</h2>
                   </div>
						
						
						<div style="clear:both"></div>
						
						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ lurl('account/'.$pagePath.'/delete') }}">
								{!! csrf_field() !!}
								 
								
								<table id="addManageTable" class="table table-striped table-bordered add-manage-table table demo" data-filter="#filter" data-filter-text-only="true">
									<thead>
									<tr>
										 
										<th style="width:12%" data-sort-ignore="true">Image</th>
                                        <th style="width:12%" data-sort-ignore="true">UserName</th>									
										<th style="width:10%">{{ t('Option') }}</th>
									</tr>
									</thead>
									<tbody>
									<?php
									if (isset($conversations) && $conversations->count() > 0):
										foreach($conversations as $key => $conversation):
										    
										    
                                                
                                        $user = \DB::table('users')->where('id', '=', $conversation->fav_user_id)->first();
                                        $username = !empty($user->username)?$user->username:'';         
                                        $userphone = !empty($user->phone)?$user->phone:'';         
                                        
                                       
								 
								    $selectedquery = '';
								    ?>
								   
									<tr >
									    
										 
										<td>
										    @if (!empty($user->profile_image))

											<img class="userImg" src="<?= url('ProfilePictures/'.$user->profile_image.'') ?>" alt="user" style="width: 100px;">                                  

										@else
											<img class="userImg" src="{{ url('images/user.jpg') }}" alt="user" style="width: 100px;">
										@endif
										    
										   
										    
									    </td>
										    
										<td>{{ $username }}</td>
										 										 
									    
									 
										<td class="action-td">
											<div>
												<p>
													 
                                                    
                                               @if ($pagePath=='favourite-user')
												   <p>
                                                        <a  class="newbtn btn btn-danger btn-sm delete-action" href="{{ lurl('account/'.$pagePath.'/'.$conversation->fav_user_id.'/deleteuserfavourite') }}">
                                                            <i class="fa fa-trash"></i> {{ t('Remove favorite') }}
                                                        </a>
                                                    </p>
												@endif
                                                
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