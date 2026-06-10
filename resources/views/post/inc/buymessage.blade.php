<div class="modal fade" id="buyNow" tabindex="-1" role="dialog">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal">

					<span aria-hidden="true">&times;</span>

					<span class="sr-only">{{ t('Close') }}</span>

				</button>

				<h4 class="modal-title"><i class="icon-mail-2"></i> {{ t('Buy Now') }} </h4>

			</div>

			<form role="form" method="POST" action="{{ lurl('posts/' . $post->id . '/contact') }}" enctype="multipart/form-data">

				{!! csrf_field() !!}

				<div class="modal-body">



					@if (isset($errors) and $errors->any() and old('messageForm')=='1')

						<div class="alert alert-danger">

							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

							<ul class="list list-check">

								@foreach($errors->all() as $error)

									<li>{{ $error }}</li>

								@endforeach

							</ul>

						</div>

					@endif

					

					

						<input type="hidden" name="from_name" value="{{ auth()->user()->username }}">

						

							<div class="form-group required <?php echo (isset($errors) and $errors->has('from_email')) ? 'has-error' : ''; ?>">

								<label for="from_email" class="control-label">{{ t('E-mail') }} ({{t('show/hide your email')}})</label>

								<div style="clear:both"></div>

								<div class='col-md-6' style="padding: 0px;margin-top: 9px;width: 0%;">

								    <!--<input checked type="checkbox" id="buynowEmailCheckbox">-->

								    <input id="buynowfrom_email_checkbox" type="hidden" value="{{ old('from_email', auth()->user()->email) }}">

								</div>

								

								<div class='col-md-6' style="padding: 0px;width: 97%;" id="buynowShowEmailusingCheckbox">

    								<div class="input-group">

    								    

    									<span class="input-group-addon"><i class="icon-mail"></i></span>

                                        <label  class="form-control">{{ old('from_email', auth()->user()->email) }}</label>

    									<input type="hidden"  id="buynowfrom_email" name="from_email" value="{{ old('from_email', auth()->user()->email) }}"placeholder="{{ t('i.e. you@gmail.com') }}"

    										   class="form-control" >

    								</div>

								</div>

								<div style="clear:both"></div>

							</div>

							

						

					<div class="form-group required <?php echo (isset($errors) and $errors->has('from_phone')) ? 'has-error' : ''; ?>">

						<label for="phone" class="control-label">{{ t('Phone Number') }} ({{ t('show/hide your phone') }})</label>

						<div style="clear:both"></div>

						<div class='col-md-6' style="padding: 0px;margin-top: 9px;width: 0%;">

						    <!--<input checked id="buynowPhoneCheckbox" type="checkbox">-->

						    	<input id="buynowfrom_phone_checkbox" type="hidden" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}">

						</div>

						<div class="col-md-6" id="buynowShowPhoneusingCheckbox" style="padding: 0px;width: 97%;">

    						<div class="input-group">

    							<span class="input-group-addon"><i class="icon-phone-1"></i></span>

                                 <label  class="form-control">{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}</label>

    							<input type="hidden" id="buynowfrom_phone" name="from_phone" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}"

    								   placeholder="{{ t('Phone Number') }}"

    								   maxlength="60" class="form-control number_ltr" >

    						</div>

						</div>

						<div style="clear:both"></div>

					</div>

				

					

					<!-- message -->

					<div class="form-group required <?php echo (isset($errors) and $errors->has('message')) ? 'has-error' : ''; ?>">

						<label for="message" class="control-label">{{ t('Message') }} <span class="text-count">({{ t('500 max') }})</span> <sup>*</sup></label>

						<textarea id="message" name="message" minlength="20" class="form-control required" placeholder="{{ t('Your message here...') }}" rows="5">{{ t('I want to purchase your item for the same price you are asking for. Please contact me on the email/phone shown above so we discuss how you want to get paid and how I want to collect the item.') }}</textarea>

					</div>



                    <div class="form-group ">

                        <label for="message" class="control-label">{{ t('Delivery Preference:') }} <sup>*</sup></label>

                        

                        

                        <div style="clear:both"></div>

                        

                        <label class="radio-inline" for="postTypeId-1">

                            <input type="radio" value="1" id="postTypeId-1" class="delivery_preference"  required  name="delivery_preference">

										{{ t('Pick up the item myself') }}

						</label>

						<div style="clear:both"></div>

						 

						<label class="radio-inline" style="margin-top: 5px;" for="postTypeId-2">

                            <input type="radio" value="2" id="postTypeId-2" class="delivery_preference"  required  name="delivery_preference">

							{{ t('Shipped or deliverd to me') }}

						</label>

                        <div style="clear:both"></div>

					</div>

					 

					<div class="form-group required <?php echo (isset($errors) and $errors->has('date_time')) ? 'has-error' : ''; ?>">

						<label for="phone" class="control-label">{{ t('Date & Time Preference') }} <sup>*</sup></label>

						<div style="clear:both"></div>

						<div class="col-md-6" style="padding: 0px;width: 97%;">

    						<div class="input-group">

    							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>

    							<input  name="date_time" value="{{ old('date_time')}}" type="text" required placeholder="{{ t('Date & Time Preference') }}"  class="form-control datetimepicker"  >

    						</div>

						</div>

						<div style="clear:both"></div>

					</div>

					

						<!-- message -->

					<div class="form-group required" id="buyer_address_show" style="display:none">

						<label for="buyer_address" class="control-label">{{ t('Your Address') }} <sup>*</sup></label>

						<textarea id="buyer_address" name="buyer_address"  class="form-control " placeholder="{{ t('Your Address') }}" rows="2">{{ !empty(auth()->user()->address)?auth()->user()->address.', ':'' }}{{ !empty( auth()->user()->city)? auth()->user()->city.', ':'' }}{{ !empty(auth()->user()->zipcode)?auth()->user()->zipcode.', ':'' }}{{ !empty(auth()->user()->state)?auth()->user()->state.', ':'' }}{{ !empty(auth()->user()->country->name)?auth()->user()->country->name:'' }}</textarea>

						

                        <p style="margin-top: 10px;margin-bottom: 3px;font-size: 15px;">

                            {{ t('The delivery service is provided by a third party where Tammat no control over, and assumes no responsibility or liability for, the practices of any third party.') }}

                         

                        </p>

                        <p style="font-size: 15px;">

                            {{ t('Please check the item post for to know who will be charged for the delivery cost.') }}

                            

                        </p>						

						

					</div>

					

					

					

					

					

					

					<input type="hidden" name="country_code" value="{{ config('country.code') }}">

					<input type="hidden" name="post_id" value="{{ $post->id }}">

					<input type="hidden" name="messageForm" value="1">

				</div>

				<div class="modal-footer">

					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>

					<button type="submit" class="btn btn-success">{{ t('Send') }}</button>

				</div>

			</form>

		</div>

	</div>

</div>





@section('after_scripts')

    @parent



	<script>

	    

	    $('.delivery_preference').change(function(){

	        var value = $(this).val();

	        if(value == 2)

	        {

	            $('#buyer_address').prop('required',true);

	            $('#buyer_address_show').show(); 

            	 

	        }

	        else 

	        {

	            $('#buyer_address').prop('required',false);

	            $('#buyer_address_show').hide();

	        }

	    });

	    

	    

	    

	    

	    $('#buynowEmailCheckbox').change(function(){

            if($(this).is(":checked")) 

	        {

	            var email = $('#buynowfrom_email_checkbox').val();

	            $('#buynowfrom_email').val(email);

	            $('#buynowShowEmailusingCheckbox').show();

	        }

	        else

	        {

	            $('#buynowfrom_email').val('');

	            $('#buynowShowEmailusingCheckbox').hide();

	        }

	    });

	    

	    

        $('#buynowPhoneCheckbox').change(function(){

            if($(this).is(":checked")) 

	        {

	            var phone = $('#buynowfrom_phone_checkbox').val();

	            $('#buynowfrom_phone').val(phone);

	            

	            $('#buynowShowPhoneusingCheckbox').show();

	        }

	        else

	        {

	            $('#buynowfrom_phone').val('');

	            $('#buynowShowPhoneusingCheckbox').hide();

	        }

	        

	    });

	



	</script>

	<script>

		$(document).ready(function () {

			@if ($errors->any())

				@if ($errors->any() and old('messageForm')=='1')

					$('#buyNow').modal();

				@endif

			@endif

		});

	</script>

@endsection











