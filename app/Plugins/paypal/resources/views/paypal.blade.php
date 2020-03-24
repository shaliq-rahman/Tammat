<div class="row payment-plugin" id="paypalPayment" style="display: none;">
    <div class="col-xs-12 col-md-8 box-center center">
        
        <img class="img-responsive box-center center" src="{{ url('images/paypal/payment.png') }}" title="{{ trans('paypal::messages.Payment with Paypal') }}" style="margin-bottom: 20px;">
        
    </div>
</div>

@section('after_scripts')
    @parent
    <script>
        $(document).ready(function ()
        {
            var selectedPackage = $('input[name=package_id]:checked').val();
            var packagePrice = getPackagePrice(selectedPackage);
            
            // var paymentMethod = $('#paymentMethodId').find('option:selected').data('name');
    
            var paymentMethod = '';
				
				$('.paymentMethodId').each(function(){
				        if(this.checked)
				        {
				             paymentMethod = $(this).attr('data-name');
				        }
				});
    
    
            /* Check Payment Method */
            checkPaymentMethodForPaypal(paymentMethod, packagePrice);
            
            $('.paymentMethodId').on('change', function () {
                // paymentMethod = $(this).find('option:selected').data('name');
                paymentMethod = $(this).attr('data-name');
                checkPaymentMethodForPaypal(paymentMethod, packagePrice);
            });
            $('.package-selection').on('click', function () {
                selectedPackage = $(this).val();
                packagePrice = getPackagePrice(selectedPackage);
                
                $('.paymentMethodId').each(function(){
				        if(this.checked)
				        {
				             paymentMethod = $(this).attr('data-name');
				        }
				});
                // paymentMethod = $('#paymentMethodId').find('option:selected').data('name');
                checkPaymentMethodForPaypal(paymentMethod, packagePrice);
            });
    
            /* Send Payment Request */
            $('#submitPostForm').on('click', function (e)
            {
                e.preventDefault();
        
        
                // paymentMethod = $('#paymentMethodId').find('option:selected').data('name');
                $('.paymentMethodId').each(function(){
				        if(this.checked)
				        {
				             paymentMethod = $(this).attr('data-name');
				        }
				});
                
                if (paymentMethod != 'paypal' || packagePrice <= 0) {
                    return false;
                }
    
                $('#postForm').submit();
        
                /* Prevent form from submitting */
                return false;
            });
        });

        function checkPaymentMethodForPaypal(paymentMethod, packagePrice)
        {
            if (paymentMethod == 'paypal' && packagePrice > 0) {
                $('#paypalPaymentKnet').hide();
                $('#paypalPayment').show();
                
            } 
            else if (paymentMethod == 'KNET' && packagePrice > 0) {
                $('#paypalPayment').hide();
                $('#paypalPaymentKnet').show();
            } 
            else {
                $('#paypalPayment').hide();
                $('#paypalPaymentKnet').hide();
            }
        }
    </script>
@endsection
