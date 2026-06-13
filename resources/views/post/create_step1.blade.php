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
 * Please read the full License from here - http://codecanyon.net/licenses/standardc
--}}
@extends('layouts.master')

@section('wizard')
	@include('post.inc.wizard2')
@endsection
  
@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">
				
				@include('post.inc.notification')

				<div class="col-md-12 page-content">
                
                
               
                
                
                
					<div class="inner-box category-content">
                        <div class="col-lg-12 box-title no-border" style="background-color: #ff5555 ; border-radius: 40px;margin:7px">
                        <h2  style="color: #fff"><i class="icon-docs"></i> 
						{{ t('Select Main Category') }}</h2> 
                        <br />
                    </div>
                
                
                
						<div class="row row-featured row-featured-category">
						
							    
							    
							
				<?php if (isset($categories) and $categories->count() > 0) { ?>
						@foreach($categories as $key => $cat)
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 col-lg-2-new f-category" style="border-radius: 40px">
								<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $cat->slug]; ?>
								
									<?php
								
								$final_cat_id = $cat->id;
								if(config('app.locale')!='en'){
								    
								      $cat_org = DB::table('categories')->where('id', $cat->translation_of)
	                                   
	                                    ->first();
								    
								  $final_cat_id =   $cat_org->id;
								}
								
								
								?>
								
								<a href="create_step2/{{$final_cat_id}}">
									<img src="{{ \Storage::url($cat->picture) . getPictureVersion() }}" class="img-responsive" alt="img">
									<h6> <?php print $cat->name; ?> </h6>
								</a>
							</div>
						@endforeach
					<?php } ?>
							
							
						</div>
						<div style="clear: both;"><br></div>
					</div>
				</div>
				<!-- /.page-content -->

			
			</div>
		</div>
	</div>
	
	

         
@endsection

@section('after_styles')
	@include('layouts.inc.tools.wysiwyg.css')
@endsection

@section('after_scripts')
    @include('layouts.inc.tools.wysiwyg.js')


<script type="text/javascript">
	       // $('document').ready(function(){
                
        //         $('body').delegate('#city_name','keypress',function(){
        //                 var input = document.getElementById('city_name');
        //                 initialize(input);
        //         });
                
	       // });    
                function initialize() {
                    //   new google.maps.places.Autocomplete(input);
                    var input = document.getElementById('city_name');
                    var options = {
                    types: ['(cities)'],
                        componentRestrictions: {country: "{{config('country.icode')}}"}
                    };
                    var autocomplete = new google.maps.places.Autocomplete(input, options);
                    
                }
	        
                google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.GoogleMaps.key') }}&libraries=places&callback=initialize"
         async defer></script>
         

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	





 <script>
 	


	    $('#EmailCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var email = $('#from_email_checkbox').val();
	            $('#from_email').val(email);
	            $('#ShowEmailusingCheckbox').show();
	        }
	        else
	        {
	            $('#from_email').val('');
	            $('#ShowEmailusingCheckbox').hide();
	        }
	    });
	    
	    
        $('#PhoneCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var phone = $('#from_phone_checkbox').val();
	            $('#from_phone').val(phone);
	            
	            $('#ShowPhoneusingCheckbox').show();
	        }
	        else
	        {
	            $('#from_phone').val('');
	            $('#ShowPhoneusingCheckbox').hide();
	        }
	        
	    });
	    
	</script>    
  







	<?php if (file_exists(public_path() . '/assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js')) { ?>
		<script src="{{ url('assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js') }}" type="text/javascript"></script>
	<?php } ?>
	
		  
         
         
	<script>
	
	$('document').ready(function(){
	
	
	   // $('#customFields').delegate('.ChangeCheckboxMoving','change',function(){
    //             var value = $(this).attr('data-val');
    //             $('.claralltext').val('');
    //             if(value == '0')
    //             {
    //                 $('#DisplayRecord').show();
    //             }
    //             else
    //             {
    //                 $('#DisplayRecord').hide();
    //             }
    //     }); 
	
       
	
	
                  var getvalue =  $( "#parentId option:selected" ).text();
        	       if($.trim(getvalue) == "Free")
        	       {
    	                $('#price').val('0');
    	                $('#price').prop('readonly',true);
        	       }
        	       
            	$('#parentId').change(function(){
            	        
            	       var getvalue =  $( "#parentId option:selected" ).text();
            	       if($.trim(getvalue) == "Free")
            	       {
        	                $('#price').val('0');
        	                $('#price').prop('readonly',true);
            	       }
            	       else
            	       {
        	                $('#price').val('');
        	                $('#price').prop('readonly',false);
            	       }
            	        
            	});
            	
	});
	
		/* Translation */
		var lang = {
			'select': {
				'category': "{{ t('Select a category') }}",
				'subCategory': "{{ t('Select a subcategory') }}",
				'country': "{{ t('Select a country') }}",
				'admin': "{{ t('Select a location') }}",
				'city': "{{ t('Select a city') }}"
			},
			'price': "{{ t('Price') }}",
			'salary': "{{ t('Salary') }}",
			'nextStepBtnLabel': {
			    'next': "{{ t('Next') }}",
                'submit': "{{ t('Submit') }}"
			}
		};
		
		/* Categories */
		var category = {{ old('parent_id', 0) }};
		var categoryType = '{{ old('parent_type') }}';
		if (categoryType=='') {
			var selectedCat = $('select[name=parent_id]').find('option:selected');
			categoryType = selectedCat.data('type');
		}
		var subCategory = {{ old('category_id', 0) }};
		
		/* Custom Fields */
		var errors = '{!! addslashes($errors->toJson()) !!}';
		var oldInput = '{!! addslashes(collect(session()->getOldInput('cf'))->toJson()) !!}';
		var postId = '';
		
		/* Locations */
        var countryCode = '{{ old('country_code', config('country.code', 0)) }}';
        var adminType = '{{ config('country.admin_type', 0) }}';
        var selectedAdminCode = '{{ old('admin_code', (isset($admin) ? $admin->code : 0)) }}';
        var cityId = '{{ old('city_id', (isset($post) ? $post->city_id : 0)) }}';
		
		/* Packages */
		var packageIsEnabled = false;
        <?php if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0) { ?>
            packageIsEnabled = true;
        <?php } ?>
		
		//Auto load city
		var input = document.getElementById('city_id');
		var opts = {
		  types: ['(cities)']
		};
		var autocomplete = new google.maps.places.Autocomplete(input,opts);
		
		// Begin of the code made by MonTech Team
		var currgeocoder;
		function get_location(){
			 navigator.geolocation.getCurrentPosition(function(position, html5Error) {
				 geo_loc = processGeolocationResult(position);
				 currLatLong = geo_loc.split(",");
				 initializeCurrent(currLatLong[0], currLatLong[1]);
			});
		}
		//Get geo location result
       function processGeolocationResult(position) {
             html5Lat = position.coords.latitude; //Get latitude
             html5Lon = position.coords.longitude; //Get longitude
             html5TimeStamp = position.timestamp; //Get timestamp
             html5Accuracy = position.coords.accuracy; //Get accuracy in meters
             return (html5Lat).toFixed(8) + ", " + (html5Lon).toFixed(8);
       }

        //Check value is present or not & call google api function
        function initializeCurrent(latcurr, longcurr) {
             currgeocoder = new google.maps.Geocoder();
             //console.log(latcurr + "-- ######## --" + longcurr);

             if (latcurr != '' && longcurr != '') {
                 var myLatlng = new google.maps.LatLng(latcurr, longcurr);
                 return getCurrentAddress(myLatlng);
             }
       }

        //Get current address
         function getCurrentAddress(location) {
			 currgeocoder.geocode({'location': location}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    //console.log(results[1]);
					if (results[1]) {
						result = results[1];
						var country = null, countryCode = null, city = null, cityAlt = null;
						var c, lc, component;
						for (c = 0, lc = result.address_components.length; c < lc; c += 1) {
								component = result.address_components[c];
									if (component.types[0] === 'locality') {
										city = component.long_name;
										break;
							}
						}
						if(city){
							$("input[name='city_id']").val(city);
						}
					}
                } else {
                    console.log('Geocode was not successful for the following reason: ' + status);
                }
            });
         }
		// End of the code made by MonTech Team
	</script>
	<script>
		$(document).ready(function() {
			$('#tags').tagit({
				fieldName: 'tags',
				placeholderText: '{{ t('add a tag') }}',
				caseSensitive: false,
				allowDuplicates: false,
				allowSpaces: false,
				tagLimit: {{ (int)config('settings.single.tags_limit', 15) }},
				singleFieldDelimiter: ','
			});
			
			
			
		});
	</script>
	
	<script src="{{ url('assets/js/app/d.select.category.js') . vTime() }}"></script>
	<script src="{{ url('assets/js/app/d.select.location.js') . vTime() }}"></script>
	<style>
	#location_btn_div{padding:0;}
	</style>
@endsection
