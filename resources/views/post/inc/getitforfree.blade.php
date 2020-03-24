<div class="modal fade" id="GetitforFree" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">{{ t('Close') }}</span>
				</button>
				<h4 class="modal-title"><i class="icon-mail-2"></i> {{ t('Get it for Free') }} </h4>
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
					
					
						<input type="hidden" name="from_name" value="{{ auth()->user()->name }}">
						
							<div class="form-group required <?php echo (isset($errors) and $errors->has('from_email')) ? 'has-error' : ''; ?>">
								<label for="from_email" class="control-label">{{ t('E-mail') }} ({{t('show/hide your email')}})</label>
								<div style="clear:both"></div>
								<div class='col-md-6' style="padding: 0px;margin-top: 9px;width: 3%;">
								    <input checked type="checkbox" id="GetitforFreeEmailCheckbox">
								    <input id="GetitforFreefrom_email_checkbox" type="hidden" value="{{ old('from_email', auth()->user()->email) }}">
								</div>
								
								<div class='col-md-6' style="padding: 0px;width: 97%;" id="GetitforFreeShowEmailusingCheckbox">
    								<div class="input-group">
    								    
    									<span class="input-group-addon"><i class="icon-mail"></i></span>
    									<input id="GetitforFreefrom_email" name="from_email" value="{{ old('from_email', auth()->user()->email) }}" type="text" placeholder="{{ t('i.e. you@gmail.com') }}"
    										   class="form-control" >
    								</div>
								</div>
								<div style="clear:both"></div>
							</div>
						
						
					<div class="form-group required <?php echo (isset($errors) and $errors->has('from_phone')) ? 'has-error' : ''; ?>">
						<label for="phone" class="control-label">{{ t('Phone Number') }} ({{ t('show/hide your phone') }})</label>
						<div style="clear:both"></div>
						<div class='col-md-6' style="padding: 0px;margin-top: 9px;width: 3%;">
						    <input checked id="GetitforFreePhoneCheckbox" type="checkbox">
						    	<input id="GetitforFreefrom_phone_checkbox" type="hidden" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}">
						</div>
						<div class="col-md-6" id="GetitforFreeShowPhoneusingCheckbox" style="padding: 0px;width: 97%;">
    						<div class="input-group">
    							<span class="input-group-addon"><i class="icon-phone-1"></i></span>
    							<input id="GetitforFreefrom_phone" name="from_phone" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}" type="text"
    								   placeholder="{{ t('Phone Number') }}"
    								   maxlength="60" class="form-control number_ltr" value="">
    						</div>
						</div>
						<div style="clear:both"></div>
					</div>
					
					
					
					<!-- message -->
					<div class="form-group required <?php echo (isset($errors) and $errors->has('message')) ? 'has-error' : ''; ?>">
						<label for="message" class="control-label">{{ t('Message') }} <span class="text-count">({{ t('500 max') }})</span> <sup>*</sup></label>
						<textarea id="message" name="message" minlength="20" class="form-control required" placeholder="{{ t('Your message here...') }}" rows="5">{{ t('I want to have your item for free. would you please contact me here or on the phone number or the email shown above to discuss to how I can collect it. Thank you for your generosity !!') }}</textarea>
					</div>

					
					<input type="hidden" name="country_code" value="{{ config('country.code') }}">
					<input type="hidden" name="post_id" value="{{ $post->id }}">
					<input type="hidden" name="messageForm" value="1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ t('Cancel') }}</button>
					<button type="submit" class="btn btn-success pull-right">{{ t('Send') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>


@section('after_scripts')
    @parent

	<script>
	
	    $('#GetitforFreeEmailCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var email = $('#GetitforFreefrom_email_checkbox').val();
	            $('#GetitforFreefrom_email').val(email);
	            $('#GetitforFreeShowEmailusingCheckbox').show();
	        }
	        else
	        {
	            $('#GetitforFreefrom_email').val('');
	            $('#GetitforFreeShowEmailusingCheckbox').hide();
	        }
	    });
	    
	    
        $('#GetitforFreePhoneCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var phone = $('#buynowfrom_phone_checkbox').val();
	            $('#GetitforFreefrom_phone').val(phone);
	            
	            $('#GetitforFreeShowPhoneusingCheckbox').show();
	        }
	        else
	        {
	            $('#GetitforFreefrom_phone').val('');
	            $('#GetitforFreeShowPhoneusingCheckbox').hide();
	        }
	        
	    });
	

	</script>
	<script>
		$(document).ready(function () {
			@if ($errors->any())
				@if ($errors->any() and old('messageForm')=='1')
					$('#GetitforFree').modal();
				@endif
			@endif
		});
	</script>
@endsection





