<div id="stepWizard" class="container">
    <div class="row">
        <div class="col-lg-12">
            <section>
                <div class="wizard"  style="margin-top: 47px;">
                    
                    <ul class="nav nav-wizard">
                        <?php $uriPath = getSegment(2); ?>
                        	    <li class="@if($uriPath =='create_step1') active  @else disabled   @endif disabled">
								
								
										<a href="#">{{ t('Select Main Category') }}</a>
								
								
								</li>
								
								
								 <li class="@if($uriPath =='create_step2') active  @else disabled   @endif disabled">
								
								
										<a href="#">{{ t('Select Sub Category') }}</a>
								
								
								</li>
								
								
								 <li class="@if($uriPath =='create_step3') active  @else disabled   @endif disabled">
								
								
										<a href="#">{{ t('Ad\'s Details') }}</a>
								
								
								</li>
								
								
								 <li class="@if($uriPath =='create_step4') active  @else disabled   @endif disabled">
								
								
										<a href="#">{{ t('Photos') }}</a>
								
								
								</li>
								
								 <li class="@if($uriPath =='create_step5') active  @else disabled   @endif disabled">
								
								
										<a href="#">{{ t('Finish') }}</a>
								
								
								</li>
								
	
                    </ul>
                    
                </div>
            </section>
        </div>
    </div>
</div>

@section('after_styles')
    @parent
	@if (config('lang.direction') == 'rtl')
    	<link href="{{ url('assets/css/rtl/wizard.css') }}" rel="stylesheet">
	@else
		<link href="{{ url('assets/css/wizard.css') }}" rel="stylesheet">
	@endif
@endsection
@section('after_scripts')
    @parent
@endsection