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

@section('wizard')
	@include('post.inc.wizard')
@endsection

<?php

  $is_regular = '';
// Category

if ($post->category) {
	
	
    if ($post->category->parent_id == 0) {
        $postCatParentId = $post->category->id;
        $categoryname = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $postCatParentId)
                        ->first(); 
    } else {
	    $postCatParentId = $post->category->parent_id;
	    $categoryname = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $postCatParentId)
                        ->first(); 
                        
	}



$id=$post->category->id;

	     $p_id = \DB::table('categories')->where('id', $id)
	        
			//abdelhay hash this command becouse make problem when i try to change langaug in same page 
	      //  ->where('translation_lang', config('app.locale'))
	        
	        ->first();
			
			if($p_id->parent_id > 0){
				
				 $p_id2 = \DB::table('categories')->where('id', $p_id->parent_id)->first();	        
			    if($p_id2->parent_id > 0){
					
					 $p_id3 = \DB::table('categories')->where('id', $p_id2->parent_id)->first();	     
					
					     if($p_id3->parent_id > 0){
							 $cat1=$p_id->parent_id;$cat2=$p_id2->parent_id;$cat3=$p_id3->parent_id;$cat4=$id;
							 }else{
						 
						  $cat1=$p_id->parent_id;$cat2=$p_id2->parent_id;$cat3=$id;$cat4=0;
						 }
						 
					}else{
					 
					$cat1=$p_id->parent_id;$cat2=$id;$cat3=0;$cat4=0;
					}
	        
				
				}else{$cat1=$id;$cat2=0;$cat3=0;$cat4=0;}
			
			
		//	echo "a".$cat1."b".$cat2.'c'.$cat3.'d'.$cat4; 
			
			$data['cat1']=$cat1;
			$data['cat2']=$cat2;
			$data['cat3']=$cat3;
			$data['cat4']=$cat4;



$postCatParentId=$cat2;


//echo "qqqqq";


} else {
	$postCatParentId = 0;
	$categoryname = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $postCatParentId)
                        ->first(); 
						//echo "xxx";
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<script>

var app = angular.module('myApp', []);
  app.controller('myCtrl', function($scope, $http) {
  $scope.orgtitle=0;
  $scope.show = false;
 
     $scope.UserCategory =function() {
         $scope.url="{{ url()->current() }}";
         //alert(url);
      $scope.bootscatid=(document.getElementById("bootscatid").value);
      $("#postcatid").val($scope.bootscatid);
     var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{url('')}}/ajax/category/sub-categories'+'?kncategory='+$scope.bootscatid+'&url='+$scope.url,
	method: "POST",
			data: xsrf,
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
	}).then(function(response) {
	    $scope.SIZE = response.data.size;
                  //alert(JSON.stringify($scope.SIZE));
                  
            
	    if($scope.SIZE==2){
	        //alert('I am in');
	               
                  $scope.SUBCAT1 = response.data.CAT1;
                  $scope.data1 = response.data.tab1;
                  $scope.data2 = response.data.CAT;
                  
	    } else if($scope.SIZE==3){
			       
	              $scope.SUBCAT1 = response.data.CAT2;
                  $scope.data1 = response.data.tab3;
                  $scope.data2 = response.data.CAT1;
                  $scope.data3 = response.data.CAT;
	    } else if($scope.SIZE==1){
	            
                  $scope.SUBCAT1 = response.data.CAT;
                  $scope.data1 = response.data.tab2;
	    }
	    else if($scope.SIZE==4) {
			 
                 $scope.SUBCAT1 = response.data.CAT3;
                  $scope.data1 = response.data.tab4;
                  $scope.data2 = response.data.CAT2;
                  $scope.data3 = response.data.CAT1;
                  $scope.data4 = response.data.CAT;
	    
	    }    else {
			
			      $scope.SUBCAT1 = response.data.CAT4;
                  $scope.data1 = response.data.tab5;
                  $scope.data2 = response.data.CAT3;
                  $scope.data3 = response.data.CAT2;
                  $scope.data4 = response.data.CAT1;
                  $scope.data5 = response.data.CAT;
			  
	           /*   $scope.SUBCAT1 = response.data.CAT4;
                  $scope.data1 = response.data.tab5;
                  $scope.data2 = response.data.CAT3;
                  $scope.data3 = response.data.CAT1;
                  $scope.data4 = response.data.CAT2;
                  $scope.data5 = response.data.CAT;*/
	    }
					$scope.hastrue=false;
					
				}).error(function (data, status, headers, config) {
				    
					$scope.hastrue=false;
				});
     
		
    };
    $scope.category =function() {
      $scope.cat=(document.getElementById("deecat").value);
      $scope.url="{{ url()->current() }}";
       $("#postcatid").val($scope.cat);
    
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{url('')}}/ajax/category/sub-categories'+'?cat='+$scope.cat+'&url='+$scope.url,
	method: "POST",
			data: xsrf,
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
	}).then(function(response) {
                  $scope.data2 = response.data.tab1;
                  
                  if($scope.data2!=undefined){
                     if($("#deesubcat option[value='']").length > 0){} else {
                      $("#deesubcat").append(new Option("---Please Select SubCategory", "")); }
                       $("#deesubcat").prop("disabled", false);
                        if($("#deepost option[value='']").length > 0){} else {
                        $('#deepost').find('option').remove().end().append('<option>---Please Select SubCategory2</option>');
                       $("#deepost").prop("disabled", false);
                        }
                       if($("#subcat3 option[value='']").length > 0){} else {
                        $('#subcat3').find('option').remove().end().append('<option>---Please Select SubCategory3</option>');
                       $("#subcat3").prop("disabled", false);
                       }
                       if($("#subcat4 option[value='']").length > 0){} else {
                       $('#subcat4').find('option').remove().end().append('<option>---Please Select SubCategory4</option>');
                       $("#subcat4").prop("disabled", false);
                       }
                        $("#postcatid").val($scope.cat);
                  } 
                  //alert(JSON.stringify($scope.data));
					$scope.hastrue=false;
			
				}).error(function (data, status, headers, config) {
				    $scope.data='undefined';
					$scope.hastrue=false;
				});
		
		
    };
   $scope.subcategory =function() {
      $scope.subcat=(document.getElementById("deesubcat").value);
       $scope.url="{{ url()->current() }}";
      $("#postcatid").val($scope.subcat);
     
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{url('')}}/ajax/category/sub-categories'+'?subcat='+$scope.subcat+'&url='+$scope.url,
	method: "POST",
			data: xsrf,
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
	}).then(function(response) {
                  $scope.data3 = response.data.tab2;
                  //alert(JSON.stringify($scope.data2));
                   $('#subcat3').find('option').remove().end().append('<option>---Please Select SubCategory3</option>');
                   $('#subcat4').find('option').remove().end().append('<option>---Please Select SubCategory4</option>');
					$scope.hastrue=false;
					
				}).error(function (data, status, headers, config) {
				    $scope.data2='undefined';
					$scope.hastrue=false;
				});
		
    };
   $scope.post =function() {
      $scope.subcat2=(document.getElementById("deepost").value);
      $scope.url="{{ url()->current() }}";
       $("#postcatid").val($scope.subcat2);
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{url('')}}/ajax/category/sub-categories'+'?subcat2='+$scope.subcat2+'&url='+$scope.url,
	method: "POST",
			data: xsrf,
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
	}).then(function(response) {
                  $scope.data4 = response.data.tab3;
                  //alert(JSON.stringify($scope.data3));
                  $('#subcat4').find('option').remove().end().append('<option>---Please Select SubCategory4</option>');
					$scope.hastrue=false;
					
				}).error(function (data, status, headers, config) {
				    
					$scope.hastrue=false;
				});
		
    };
    $scope.subcategory3 =function() {
      $scope.subcat3=(document.getElementById("subcat3").value);
      $scope.url="{{ url()->current() }}";
       $("#postcatid").val($scope.subcat3);
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{url('')}}/ajax/category/sub-categories'+'?subcat3='+$scope.subcat3+'&url='+$scope.url,
	method: "POST",
			data: xsrf,
			headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}
	}).then(function(response) {
                  $scope.data5 = response.data.tab4;
                   //alert(JSON.stringify($scope.data5));
                  
					$scope.hastrue=false;
					
				}).error(function (data, status, headers, config) {
				    
					$scope.hastrue=false;
				});
		
    };
	
	    $scope.subcategory4 =function() {
      $scope.subcat4=(document.getElementById("subcat4").value);
      //alert($scope.subcat3);
     
       $('#bootscatid').attr('value', $scope.subcat4);

		
    };
	
  });
</script>
@section('content')
	@include('common.spacer')
	
	<div class="main-container" ng-app="myApp" ng-controller="myCtrl" ng-init="UserCategory()">
		<div class="container">
			<div class="row">
				
				@include('post.inc.notification')

				<div class="col-md-9 page-content">
					<div class="inner-box category-content">
						<h2 class="title-2">
							<strong> <i class="icon-docs"></i> {{ t('Update My Ad') }}</strong> -&nbsp;
							<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
							<a href="{{ lurl($post->uri, $attr) }}" class="tooltipHere" title="" data-placement="top"
								data-toggle="tooltip"
								data-original-title="{!! $post->title !!}">
								{!! \Illuminate\Support\Str::limit($post->title, 45) !!}
							</a>
						</h2>
						<div class="row">
							<div class="col-sm-12">
								<form class="form-horizontal" id="postForm" method="POST" action="{{ url()->current() }}" enctype="multipart/form-data">
									{!! csrf_field() !!}
									<input name="_method" type="hidden" value="PUT">
                                    <input type="hidden" id="f_check" name="f_check" value="1">
									<input type="hidden" name="post_id" value="{{ $post->id }}">
									<input type="hidden" id="bootscatid" value="{{$post->category->id}}">
									<!--<input type="hidden" value="{{$post->category_id}}" id="postcatid">-->
									<input type="hidden" value="{{$post->category_id}}" id="postcatid" name="category_id">
									<input name="post_type_id" value="2" type="hidden">
									
									<fieldset>
										<!-- parent_id -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('parent_id')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">
											       @if(strtoupper(config('app.locale')) == 'SQ')
											       {{ t('PostCategory') }}
											       @else
											       {{ t('Category') }}
											       @endif
											     <sup>*</sup></label>
											<div class="col-md-8">
											   
	<select onchange="angular.element(this).scope().category(this)" id="deecat"    style="width:100%;"
		class="form-control selectcat" @if($categoryname->name=='Free') disabled @endif> 
        <option  ng-repeat="d in data1" value="@{{d.id}}">@{{d.name}}</option>
		<option  ng-repeat="C in SUBCAT1"  value="@{{C.id}}" selected>@{{C.name}}</option>		
	</select>

	
												<!--<select name="parent_id" id="parentId" class="form-control selecter">
													<option value="0" data-type=""
															@if (old('parent_id', $postCatParentId)=='' or old('parent_id', $postCatParentId)==0)
																selected="selected"
															@endif
													>
														{{ t('Select a category') }}
													</option>
													@foreach ($categories as $cat)
													
														<option value="{{ $cat->tid }}" data-type="{{ $cat->type }}"
																@if (old('parent_id', $postCatParentId)==$cat->tid)
																	selected="selected"
																@endif
														>
															{{ $cat->name }}
														</option>
													@endforeach
												</select>-->
												<input type="hidden" name="parent_type" id="parentType" value="{{ old('parent_type') }}">
											</div>
										</div>

										<!-- category_id -->
										<div id="subCatBloc" class="form-group required <?php echo (isset($errors) and $errors->has('category_id')) ? 'has-error' : ''; ?>" style="">
											<label class="col-md-3 control-label">{{ t('Sub-Category') }}1 <sup>*</sup></label>
											<div class="col-md-8">
											    
	<select onchange="angular.element(this).scope().subcategory(this)" id="deesubcat"    style="width:100%;"
		class="form-control selectcat" disabled> 
		
				<option  ng-repeat="x in data2" value="@{{x.id}}">@{{x.name}}</option>
				
			
		
	</select>
												<!--<select name="category_id" id="categoryId" class="form-control selecter">
													<option value="0"
															@if (old('category_id', $post->category_id)=='' or old('category_id', $post->category_id)==0)
																selected="selected"
															@endif
													>
														{{ t('Select a sub-category') }}
													</option>
												</select>-->
											</div>
										</div>
										<!----Sub Category2--->
			<div id="subCatBloc" class="form-group required <?php echo (isset($errors) and $errors->has('category_id')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">{{ t('Sub-Category') }}2 </label>
											<div class="col-md-8">
											    
		<select onchange="angular.element(this).scope().post(this)" id="deepost"  style="width: 100%;"
			class="form-control selectcat" disabled> 

				<option  ng-repeat="y in data3" value="@{{y.id}}">@{{y.name}}</option>
				
			
		
	</select>
												
											</div>
										</div>
										
										
										<!----Sub Category 3--->
										<div id="subCatBloc" class="form-group required <?php echo (isset($errors) and $errors->has('category_id')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">{{ t('Sub-Category') }}3 </label>
											<div class="col-md-8">
											    
		<select onchange="angular.element(this).scope().subcategory3(this)"  id="subcat3" style="width: 100%;"
			class="form-control selectcat" disabled> 
		<option  ng-repeat="z in data4" value="@{{z.id}}">@{{z.name}}</option>
				<option  ng-repeat="f in SUBCAT4"  value="@{{f.id}}" selected>@{{f.name}}</option>
				
				
			
		
	</select>
												
											</div>
										</div>
										
										<!-----Sub Category 4--->
										<div id="subCatBloc" class="form-group required <?php echo (isset($errors) and $errors->has('category_id')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label">{{ t('Sub-Category') }}4 </label>
											<div class="col-md-8">
											    
		<select onchange="angular.element(this).scope().subcategory4(this)"  id="subcat4" style="width: 100%;"
			class="form-control selectcat" disabled> 
		<option  ng-repeat="h in data5" value="@{{h.id}}">@{{h.name}}</option>
				<option  ng-repeat="g in SUBCAT5"  value="@{{g.id}}" selected>@{{g.name}}</option>
				
				
			
		
	</select>
												
											</div>
										</div>
										
										
							

										<!-- title -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('title')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="title">{{ t('Title') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="title" name="title" placeholder="{{ t('Ad\'s title') }}" class="form-control input-md"
													   type="text" value="{{ old('title', $post->title) }}">
												<span class="help-block">{{ t('A great title needs at least 60 characters.') }} </span>
											</div>
										</div>

										<!-- description -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('description')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="description">{{ t('Description') }} <sup>*</sup></label>
											<div class="col-md-8">&nbsp;</div>
                                            <div class="col-md-11" style="position: relative; float: right; padding-top: 10px;">
                                                <?php $ckeditorClass = (config('settings.single.ckeditor_wysiwyg')) ? 'ckeditor' : ''; ?>
												<textarea class="form-control {{ $ckeditorClass }}" id="description" name="description" rows="10">{{ old('description', $post->description) }}</textarea>
                                                <p class="help-block">{{ t('Describe what makes your ad unique') }}</p>
                                            </div>
										</div>
										
										<!-- customFields -->
										<div id="customFields"></div>

										<!-- price -->
										
													<?php if($categoryname->name=="Free"){?>
													
											<div id="priceBloc" class="form-group required <?php echo (isset($errors) and $errors->has('price')) ? 'has-error' : ''; ?>">
										    <div class="col-md-3 control-label">
											</div>
											<!--<label class="col-md-3 control-label" for="price">{{ t('Price') }}<sup>*</sup></label>-->
											
											<div class="col-md-8">
												<div class="input-group">
													<input style="z-index: 0;" id="price" name="price" class="form-control" placeholder="{{ t('e.i. 15000') }}" type="text" value="{{ old('price', $post->price) }}" hidden>
													<?php } 
													else{?> 
													
																										<div id="priceBloc" class="form-group required <?php echo (isset($errors) and $errors->has('price')) ? 'has-error' : ''; ?>">
										   
										    <div class="col-md-3 control-label">
    											<label class="control-label" for="price">
    											    {{ t('Price') }}
    											</label><sup> *</sup>
											</div>
											<!--<label class="col-md-3 control-label" for="price">{{ t('Price') }}<sup>*</sup></label>-->
											
											<div class="col-md-8">
												<div class="input-group">
													<span class="input-group-addon">{!! config('currency')['symbol'] !!}</span>
													
													<input style="z-index: 0;" id="price" name="price" class="form-control" type="number" maxlength="10" placeholder="{{ t('e.i. 15000') }}" type="text" value="{{ old('price', $post->price) }}">
													<?php } ?>
													<!--<label class="input-group-addon">
														<input id="negotiable" name="negotiable" type="checkbox"
															   value="1" {{ (old('negotiable', $post->negotiable)=='1') ? 'checked="checked"' : '' }}>
														{{ t('Negotiable') }}
													</label>-->
												</div>
											</div>
										</div>
										
										<!-- country_code -->
										<input id="countryCode" name="country_code" type="hidden" value="{{ !empty($post->country_code) ? $post->country_code : config('country.code') }}">
									
										@if (config('country.admin_field_active') == 1 and in_array(config('country.admin_type'), ['1', '2']))
										<!-- admin_code -->
										<div id="locationBox" class="form-group required <?php echo (isset($errors) and $errors->has('admin_code')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="admin_code">{{ t('Location') }} <sup>*</sup></label>
											<div class="col-md-8">
												<select id="adminCode" name="admin_code" class="form-control sselecter">
													<option value="0" {{ (!old('admin_code') or old('admin_code')==0) ? 'selected="selected"' : '' }}>
														{{ t('Select your Location') }}
													</option>
												</select>
											</div>
										</div>
										@endif
									
										<!-- city_id  -->
										<!--<div id="cityBox" class="form-group required <?php echo (isset($errors) and $errors->has('city_id')) ? 'has-error' : ''; ?>">-->
										<!--	<label class="col-md-3 control-label" for="city_id">{{ t('City') }} <sup>*</sup></label>-->
										<!--	<div class="col-md-8">-->
										<!--		<select id="cityId" name="city_id" class="form-control sselecter">-->
										<!--			<option value="0" {{ (!old('city_id') or old('city_id')==0) ? 'selected="selected"' : '' }}>-->
										<!--				{{ t('Select a city') }}-->
										<!--			</option>-->
										<!--		</select>-->
										<!--	</div>-->
										<!--</div>-->
										
											
										<!-- city_name -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('city_name')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="city_id">{{ t('City') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input   id="city_name" onkeydown="if (event.keyCode == 13) { return false; } " name="city_name" class="form-control" placeholder="{{ t('City Name') }}" type="text" value="{{ old('city_name',$post->city_name) }}">
												<input   name="city_id"  type="hidden" value="{{$post->city_id}}">
											</div>
										</div>
										
										
										
										<!-- <div id="cityBox" class="form-group required <?php echo (isset($errors) and $errors->has('city_id')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="city_id">{{ t('City') }} <sup>*</sup></label>
											<div class="col-md-5">
												<input id="city_id" name="city_id" placeholder="{{ t('Select a city') }}" class="form-control input-md" type="text" 
												value="@if(!empty($post->city_name)) {{$post->city_name}} @endif">
											</div>	
											<div class="col-md-3" id="location_btn_div">
												<button id="user_current_location" class="btn btn-primary btn-md" onclick="get_location();return false;">Use Current Location</button>
											</div>
										</div>
										-->
										
										
										<!-- tags -->
										<!--<div class="form-group <?php echo (isset($errors) and $errors->has('tags')) ? 'has-error' : ''; ?>">-->
										<!--	<label class="col-md-3 control-label" for="title">{{ t('Tags') }}</label>-->
										<!--	<div class="col-md-8">-->
										<!--		<input id="tags" name="tags" placeholder="{{ t('Tags') }}" class="form-control input-md" type="text" value="{{ old('tags', $post->tags) }}">-->
										<!--		<span class="help-block">{{ t('Enter the tags separated by commas.') }}</span>-->
										<!--	</div>-->
										<!--</div>-->


										<div class="content-subheading">
											<i class="icon-user fa"></i>
											<strong>{{ t('Seller information') }}</strong>
										</div>
										
                    						<div class="form-group required ">
                    							 
                    								<div style="clear:both"></div>
                    								<div class="col-md-6" style="padding: 5px;margin-top: 4px;width: 3%;">
                    								    <input id="EmailCheckbox" @if($post->email_hidden==0) checked @endif type="checkbox">
                    								    <input id="from_email_checkbox" type="hidden" value="{{ old('from_email', auth()->user()->email) }}">
                    								</div>
                    								<div class="col-md-6" style="padding: 0px;" id="ShowEmailusingCheckbox">
                        								<div class="input-group">
                        									<span class="input-group-addon"><i class="icon-mail"></i>
                                                            {{t('Show my Email on the Ad')}}
                                                            </span>
                        									<input id="from_email" name="from_email" placeholder="i.e. you@gmail.com" class="form-control" value="@if($post->email_hidden==0) {{ old('from_email', auth()->user()->email) }} @endif" type="hidden" readonly>
                        								
															<input   class="form-control" value="{{ old('from_email', auth()->user()->email) }}" type="hidden" readonly>
														</div>
                    								</div>
                    								<div style="clear:both"></div>
                    						</div>
                                                                                            
                                                                               
                    	                    <div class="form-group required ">
                    						 	<div style="clear:both"></div>
                    							<div class="col-md-6"  style="padding: 5px;margin-top: 4px;width: 3%;">
                    						      <input id="PhoneCheckbox"  @if($post->phone_hidden==0) checked @endif  type="checkbox">
                    						    	<input id="from_phone_checkbox" type="hidden" value="{{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }}">
                    							</div>
                    							<div class="col-md-6" id="ShowPhoneusingCheckbox" style="padding: 0px;">
                    	    						<div class="input-group">
                    	    							<span class="input-group-addon"><i class="icon-phone-1"></i>
                                                        {{t('Show my phone number on the Ad')}}
                                                        </span>
                    	    							<input id="from_phone" name="from_phone" placeholder="{{t('Phone Number')}}" maxlength="60" class="form-control" value="@if($post->phone_hidden==0) {{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }} @endif" type="hidden" readonly>
														<input class="form-control" value="@if($post->phone_hidden==0) {{ old('from_phone', (auth()->check()) ? auth()->user()->phone : '') }} @endif" type="hidden" readonly>
                    	    						
													</div>
                    							</div>
                    							<div style="clear:both"></div>
                    						</div>

										
										<!-- contact_name -->
										<div style="display: none;">
										<div class="form-group required <?php echo (isset($errors) and $errors->has('contact_name')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="contact_name">{{ t('Your name') }} <sup>*</sup></label>
											<div class="col-md-8">
												<input id="contact_name" name="contact_name" placeholder="{{ t('Your name') }}"
													   class="form-control input-md" type="text"
													   value="{{ old('contact_name', $post->contact_name) }}">
											</div>
										</div>

										<!-- email -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('email')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="email"> {{ t('Email') }} <sup>*</sup></label>
											<div class="col-md-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="icon-mail"></i></span>
													<input id="email" name="email" class="form-control"
														   placeholder="{{ t('Email') }}" type="text"
														   value="{{ old('email', $post->email) }}">
												</div>
											</div>
										</div>

										<!-- phone -->
										<div class="form-group required <?php echo (isset($errors) and $errors->has('phone')) ? 'has-error' : ''; ?>">
											<label class="col-md-3 control-label" for="phone">{{ t('Phone Number') }}<sup>*</sup></label>
											<div class="col-md-8">
												<div class="input-group">
                                                    <span id="phoneCountry" class="input-group-addon">{!! getPhoneIcon($post->country_code) !!}</span>
													
													<input id="phone" name="phone"
														   placeholder="{{ t('Phone Number') }}" class="form-control input-md"
														   type="text" value="{{ phoneFormat(old('phone', $post->phone), $post->country_code) }}"
													>
													
													<!--<label class="input-group-addon">
														<input name="phone_hidden" id="phoneHidden" type="checkbox"
															   value="1" {{ (old('phone_hidden', $post->phone_hidden)=='1') ? 'checked="checked"' : '' }}>
														{{ t('Hide') }}
													</label>-->
												</div>
											</div>
										</div>
                                    </div>

										<div class="content-subheading" style="display:none">
											<i class="icon-tag"></i>
											<strong>{{ t('Payment') }}</strong>
										</div>

													 <div class="row" style="display:none">
                    				<div class="col-sm-12">
									   <fieldset>
									         @if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
                                            <div class="well" style="padding-bottom: 0;">
                                                <h3><i class="icon-certificate icon-color-1"></i> 
                                                  <?php
                                		            $chosseplangsting = t('Choose plan'); 
                                		            ?>
                        		              {{ $chosseplangsting }} </h3>

   <div class="form-group <?php echo (isset($errors) and $errors->has('package_id')) ? 'has-error' : ''; ?>" style="margin-bottom: 0;">

   		<table id="packagesTable" class="table table-hover checkboxtable" style="margin-bottom: 0;">
													<?php
															// Get Current Payment data
															$currentPaymentMethodId = 0;
															$currentPaymentActive = 1;
															if (isset($post->latestPayment) and !empty($post->latestPayment)) {
																$currentPaymentMethodId = $post->latestPayment->payment_method_id;
																if ($post->latestPayment->active == 0) {
																	$currentPaymentActive = 0;
																}
															}
                                                        $is_regular = '';
														?>

                                                        @foreach ($packages as $package)
                                                            <?php
                                                            if($package->price == 0){
                                                                $is_regular = '1';
                                                            }
                                                            $currentPackageId = 0;
                                                            $currentPackagePrice = 0;
                                                            $packageStatus = '';
                                                            $badge = '';
                                                    		if (isset($post->latestPayment) and !empty($post->latestPayment)) {
																if (isset($post->latestPayment->package) and !empty($post->latestPayment->package)) {
																	$currentPackageId = $post->latestPayment->package->tid;
																	$currentPackagePrice = $post->latestPayment->package->price;
																}
                                                            }
															
                                                            // Prevent Package's Downgrading
                                                            if ($currentPackagePrice > $package->price) {
                                                                $packageStatus = ' disabled';
                                                                $badge = ' <span class="label label-danger">'. t('Not available') . '</span>';
                                                            } elseif ($currentPackagePrice == $package->price) {
                                                                $badge = '';
                                                            } else {
                                                                $badge = ' <span class="label label-success">'. t('Upgrade') . '</span>';
                                                            }
                                                            if ($currentPackageId == $package->tid) {
                                                                $badge = ' <span class="label label-default">'. t('Current') . '</span>';
																if ($currentPaymentActive == 0) {
																	$badge .= ' <span class="label label-warning">'. t('Payment pending') . '</span>';
																}
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="radio">
                                                                        <label>
                                                        				<input class="package-selection" type="radio" name="package_id"
                                                                                   id="packageId-{{ $package->tid }}"
                                                                                   value="{{ $package->tid }}"
																				   data-name="{{ $package->name }}"
																				   data-currencysymbol="{{ $package->currency->symbol }}"
																				   data-currencyinleft="{{ $package->currency->in_left }}"
                                                                                    {{ (old('package_id', $currentPackageId)==$package->tid) ? ' checked' : (($package->price==0) ? ' checked' : '') }} {{ $packageStatus }}>
                                                                            <strong class="tooltipHere" title="" data-placement="right" data-toggle="tooltip" data-original-title="{!! $package->description !!}">{!! $package->name . $badge !!} </strong>
                                                                        </label>
                                                                    </div>
                                                                    <div class="plan-description">
                                                                        {!!$package->plan_full_description !!}
                                                                        <div class="SelectpremiumShow" style="{{ (old('package_id', $currentPackageId)==$package->tid) ? '' : (($package->price==0) ? '' : 'display:none') }}">
                                                                            @if(!empty($package->more_description))
                                                                               <blink> {!!$package->more_description !!}</blink>
                                                                            @endif
                                                                        </div>
                                                                        
                                                                        <?php
                                                                        if($package->price != 0)
                                                                        { 	?>
                                                                        <br />
                                                                        <div style="clear:both"></div>
                                                                        
                                                                        
                                                                               
                                                                        
                                                                        
                                                <div style="clear:both"></div>
                                                                    <?php    }
                                                                        ?>
                                                                        
                                                                    </div>
                                                                </td>
                                                                <td style="width: 18%;">
                                                                    <p id="price-{{ $package->tid }}">
                                                                        
                                                                          <span class="price-currency">$ <span class="price-int">{{ $package->price }}</span></span>
                                                   
                                                                        
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        @endforeach


                                                        <tr>
                                                            <td>
                                                                <div class="form-group <?php echo (isset($errors) and $errors->has('payment_method_id')) ? 'has-error' : ''; ?>"
                                                                     style="margin-bottom: 0;">
                                                                    <div class="col-md-8">
                                                                        
                                                                        <?php
                                                                        $selectfinalMethod = 1;
                                                                        if(!empty($currentPaymentMethodId))
                                                                        {
                                                                            $selectfinalMethod = $currentPaymentMethodId;
                                                                        }
                                                                        
                                                                        ?>

                                                                   @foreach ($paymentMethods as $paymentMethod)
                                                                    
                                                                            @if($paymentMethod->id == 1)
                                                                                
                                                                                <label>
                                                                    				<input class="paymentMethodId" type="radio"  name="payment_method_id"  value="{{ $paymentMethod->id }}"  data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', $selectfinalMethod)==$paymentMethod->id) ? 'checked' : '' }} >
                                                                                    <strong>{{ $paymentMethod->display_name }} </strong>
                                                                                </label> &nbsp;&nbsp;&nbsp;&nbsp;
                                                                            @else
                                                                                <label>
                                                                    				<input class="paymentMethodId" type="radio"  name="payment_method_id"  value="{{ $paymentMethod->id }}"  data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', $selectfinalMethod)==$paymentMethod->id) ? 'checked' : '' }} >
                                                                                    <strong>{{ $paymentMethod->display_name }} </strong>
                                                                                </label>
                                                                            @endif
                                                                    @endforeach                                                                        
                                                                        
                                                                        
                                                                        
                                                                        <!--<select class="form-control selecter" name="payment_method_id" id="paymentMethodId">-->
                                                                        <!--    @foreach ($paymentMethods as $paymentMethod)-->
                                                                        <!--        {{--    @if (view()->exists('payment::' . $paymentMethod->name)) --}}-->
                                                                        <!--            <option value="{{ $paymentMethod->id }}" data-name="{{ $paymentMethod->name }}" {{ (old('payment_method_id', $currentPaymentMethodId)==$paymentMethod->id) ? 'selected="selected"' : '' }}>-->
                                                                        <!--                @if ($paymentMethod->name == 'offlinepayment')-->
                                                                        <!--                    {{ trans('offlinepayment::messages.Offline Payment') }}-->
                                                                        <!--                @else-->
                                                                        <!--                    {{ $paymentMethod->display_name }}-->
                                                                        <!--                @endif-->
                                                                        <!--            </option>-->
                                                                        <!--        {{--    @endif --}}-->
                                                                        <!--    @endforeach-->
                                                                        <!--</select>-->
                                                                        
                                                                        
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td style="width: 18%;">
                                                                <p style="margin-top: 7px;">
                                                                    <strong>
																		{{ t('Payable Amount') }} :
																		<span class="price-currency amount-currency currency-in-left" style="display: none;"></span>
                                                                        <span class="payable-amount">0</span>
																		<span class="price-currency amount-currency currency-in-right" style="display: none;"></span>
                                                                    </strong>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    
                                                        
   		</table>
	</div>
</div>


 											@if (isset($paymentMethods) and $paymentMethods->count() > 0)
                                                <!-- Payment Plugins -->
                                                <?php $hasCcBox = 0; ?>
                                                @foreach($paymentMethods as $paymentMethod)
                                                    @if (view()->exists('payment::' . $paymentMethod->name))
                                                        @include('payment::' . $paymentMethod->name, [$paymentMethod->name . 'PaymentMethod' => $paymentMethod])
                                                    @endif
                                                    <?php if ($paymentMethod->has_ccbox == 1 && $hasCcBox == 0) $hasCcBox = 1; ?>
                                                @endforeach
                                            @endif
                                                
                                                
                                            <div class="row payment-plugin" id="paypalPaymentKnet" style="display: none;">
                                                    <div class="col-xs-12 col-md-8 box-center center">
                                                        <img class="img-responsive box-center center" title="Payment with Paypal" style="margin-bottom: 20px;" src="http://tmmat.com/images/knet.png">
                                                    </div>
                                            </div>    
                                                

                    		            	@endif
									   </fieldset>
									</div>
								</div>
								




 										 <!-- Button  -->
                                        <div class="form-group">
                                            <div class="col-md-12 mt20" style="text-align: center;">
                                                <!--<a href="{{ lurl('account/my-posts/'.$post->id.'/deletepost') }}" class="btn btn-danger btn-lg"> {{ t('Delete') }} </a>-->
                                                <!--<button class="btn btn-danger btn-lg" id="deletebtn"> {{ t('Delete') }} </button>-->
                                                <a  href="{{ lurl('account/my-posts') }}" class="btn btn-primary btn-lg" > {{ t('Cancel') }} </a>
                                                <?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>

                                                    <button id="submitPostForm" class="btn btn-success btn-lg submitPostForm btn-pay" @if($is_regular) style="display: none;" @endif> {{ t('Pay') }} </button>
                                                    <button class="btn btn-primary btn-lg submitPostForm btn-finish" @if(!$is_regular) style="display: none;" @endif> {{ t('Update') }} </button>
                                                    
                                            </div>
                                        </div>



										<div style="margin-bottom: 30px;"></div>

									</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /.page-content -->

				<div class="col-md-3 reg-sidebar">
					<div class="reg-sidebar-inner text-center">
						
						@if (getSegment(2) != 'create' && auth()->check())
							@if (auth()->user()->id == $post->user_id)
								<div class="panel sidebar-panel panel-contact-seller">
									<div class="panel-heading">{{ t('Author\'s Actions') }}</div>
									<div class="panel-content user-info">
										<div class="panel-body text-center">
											<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
											<a href="{{ lurl($post->uri, $attr) }}" data-toggle="modal" class="btn btn-default btn-block">
												<i class="icon-right-hand"></i> {{ t('Return to the Ad') }}
											</a>
											<a href="{{ lurl('posts/' . $post->id . '/photos') }}" data-toggle="modal" class="btn btn-default btn-block">
												<i class="icon-camera-1"></i> {{ t('Update Photos') }}
											</a>
											
											<!--@if (isset($countPackages) and isset($countPaymentMethods) and $countPackages > 0 and $countPaymentMethods > 0)-->
												<!--<a href="{{ lurl('posts/' . $post->id . '/payment') }}" data-toggle="modal" class="btn btn-success btn-block">-->
												<!--	<i class="icon-ok-circled2"></i> {{ t('Make It Premium') }}-->
												<!--</a>-->
											<!--@endif-->
										</div>
									</div>
								</div>
							@endif
						@endif








						<div class="panel sidebar-panel">
							<div class="panel-heading uppercase">
								<small><strong>{{ t('How to sell quickly?') }}</strong></small>
							</div>
							<div class="panel-content">
								<div class="panel-body text-left">
									<ul class="list-check" style="font-size: 15px;">
										<li> {{ t('Use a brief title and description of the item') }} </li>
										<li> {{ t('Make sure you post in the correct category') }}</li>
										<li> {{ t('Add nice photos to your ad') }}</li>
										<li> {{ t('Put a reasonable price') }}</li>
										<li> {{ t('Check the item before publish') }}</li>
									</ul>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_styles')
	@include('layouts.inc.tools.wysiwyg.css')
@endsection

@section('after_scripts')
    @include('layouts.inc.tools.wysiwyg.js')

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">





 <script type="text/javascript">
 
 setInterval(function(){/*
	 
	  $f_check =$('#f_check').val();
	  //alert($f_check);
	  if($f_check == 2)
	  {return false; }
	 $('#f_check').val(2);
     $catid=$('#deecat').val();
     $catid1=$('#deesubcat').val();
     $catid2=$('#deepost').val();
     $catid3=$('#subcat3').val();
     $catid4=$('#subcat4').val();
     
   $url      = window.location.href;
   //alert($url);
   	var postId = '{{ $post->id }}';
      $.ajaxSetup({'cache':true});
    
   $.ajax({
                    type: "POST",
                    //url: "http://192.168.0.200/disha/Safarkaab/phongap_php/selectreviewtableforhotel.php",
                    url: "{{url('')}}/ajax/category/custom-fields?catid="+$catid+'&catid1='+$catid1+'&catid2='+$catid2+'&catid3='+$catid3+'&catid4='+$catid4+'&postid='+postId+'&url='+$url,
                    
                    crossDomain: true,
                    cache: false,
                    beforeSend: function() {
                      $("#Search_hotel").val('Connecting...');
                    },
                    success: function($data){
                   //alert($data);
                   $("#customFields").html($data);
					//console.log($data);
                    }
                    });
  

 
 
 
 */},5000);  
 
  jQuery(document).ready(function(){
  $(".selectcat").change(function() {
     $catid=$('#deecat').val();
     $catid1=$('#deesubcat').val();
     $catid2=$('#deepost').val();
     $catid3=$('#subcat3').val();
     $catid4=$('#subcat4').val();
	 var languageCode = '<?php echo config('app.locale'); ?>';
     
   $url      = window.location.href;
   //alert($url);
   	var postId = '{{ $post->id }}';
      $.ajaxSetup({'cache':true});
    
   $.ajax({
                    type: "POST",
                    //url: "http://192.168.0.200/disha/Safarkaab/phongap_php/selectreviewtableforhotel.php",
                    url: "{{url('')}}/"+languageCode+"/ajax/category/custom-fields?catid="+$catid+'&catid1='+$catid1+'&catid2='+$catid2+'&catid3='+$catid3+'&catid4='+$catid4+'&postid='+postId+'&posttype=edit&url='+$url,
                    
                    crossDomain: true,
                    cache: false,
                    beforeSend: function() {
                      $("#Search_hotel").val('Connecting...');
                    },
                    success: function($data){
                   //alert($data);
                   $("#customFields").html($data);
					console.log($data);
                    }
                    });
  });
  
   
  
  
});
</script>

	
<script>
    $(function(){
    $('#deletebtn').click(function() {
        var r = confirm("Are you sure you want to delete this post?");
        if (r == true) {
    window.location.href="{{ lurl('account/my-posts/'.$post->id.'/deletepost') }}";
  } else {
    return false;
  }
    });
});
</script>	
	@if (file_exists(public_path() . '/assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js'))
		<script src="{{ url('assets/plugins/forms/validation/localization/messages_'.config('app.locale').'.min.js') }}" type="text/javascript"></script>
	@endif







 <script>
 	


	    $('#EmailCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var email = $('#from_email_checkbox').val();
	            $('#from_email').val(email);
	           // $('#ShowEmailusingCheckbox').show();
	        }
	        else
	        {
	            $('#from_email').val('');
	           // $('#ShowEmailusingCheckbox').hide();
	        }
	    });
	    
	    
        $('#PhoneCheckbox').change(function(){
            if($(this).is(":checked")) 
	        {
	            var phone = $('#from_phone_checkbox').val();
	            $('#from_phone').val(phone);
	            
	          //  $('#ShowPhoneusingCheckbox').show();
	        }
	        else
	        {
	            $('#from_phone').val('');
	           // $('#ShowPhoneusingCheckbox').hide();
	        }
	        
	    });
	    
	</script>    
    <script>
        @if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
				
		 
			var currentPackagePrice = {{ $currentPackagePrice }};
			var currentPaymentActive = {{ $currentPaymentActive }};
			$(document).ready(function ()
			{
				/* Show price & Payment Methods */
				var selectedPackage = $('input[name=package_id]:checked').val();
				var packagePrice = getPackagePrice(selectedPackage);
				var packageCurrencySymbol = $('input[name=package_id]:checked').data('currencysymbol');
				var packageCurrencyInLeft = $('input[name=package_id]:checked').data('currencyinleft');
				// var paymentMethod = $('#paymentMethodId').find('option:selected').data('name');
				var paymentMethod = '';
				
				$('.paymentMethodId').each(function(){
				        if(this.checked)
				        {
				             paymentMethod = $(this).attr('data-name');
				        }
				});
				
				showAmount(packagePrice, packageCurrencySymbol, packageCurrencyInLeft);
				showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				
				/* Select a Package */
				$('.package-selection').click(function () {
					selectedPackage = $(this).val();
					packagePrice = getPackagePrice(selectedPackage);
                    if(packagePrice == 0){
                       $('.SelectpremiumShow').hide();
                        $('.btn-pay').hide();
                        $('.btn-finish').show();
                    }else{
                        $('.SelectpremiumShow').show();
                        $('.btn-pay').show();
                        $('.btn-finish').hide();
                    }
					packageCurrencySymbol = $(this).data('currencysymbol');
					packageCurrencyInLeft = $(this).data('currencyinleft');
					showAmount(packagePrice, packageCurrencySymbol, packageCurrencyInLeft);
					showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				});
				
				/* Select a Payment Method */
				$('.paymentMethodId').on('change', function () {
			       paymentMethod = $(this).attr('data-name');
				// 	paymentMethod = $(this).find('option:selected').data('name');
					showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod);
				});
				
				/* Form Default Submission */
				$('#submitPostForm').on('click', function (e) {
				// 	e.preventDefault();
					
				// 	if (packagePrice <= 0) {
						$('#postForm').submit();
				// 	}
					
				// 	return false;
				});
			});
        
        @endif

		/* Show or Hide the Payment Submit Button */
		/* NOTE: Prevent Package's Downgrading */
		/* Hide the 'Skip' button if Package price > 0 */
		function showPaymentSubmitButton(currentPackagePrice, packagePrice, currentPaymentActive, paymentMethod)
		{
			if (packagePrice > 0) {
				$('#submitPostForm').show();
				$('#skipBtn').hide();
				
				if (currentPackagePrice > packagePrice) {
					$('#submitPostForm').hide();
				}
				if (currentPackagePrice == packagePrice) {
					if (paymentMethod == 'offlinepayment' && currentPaymentActive != 1) {
						$('#submitPostForm').hide();
						$('#skipBtn').show();
					}
				}
			} else {
				$('#skipBtn').show();
			}
		}
    </script>



	
	 <script>
		  	
		  
                
                
                // $('.category-content').delegate('#city_name','keypress',function(){
                //         var input = document.getElementById('city_name');
                //         initialize(input);
                // });
                
                function initialize() {
                    var input = document.getElementById('city_name');
                    var options = {
                    types: ['(regions)'],
                        componentRestrictions: {country: "{{config('country.icode')}}"}
                    };
                    var autocomplete = new google.maps.places.Autocomplete(input, options);
                    
                    //   new google.maps.places.Autocomplete(input);
                }
                google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.GoogleMaps.key') }}&libraries=places&callback=initialize"
         async defer></script>
	
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
    //         }); 
		}); 
	
	
	
		/* Translation */
		var lang = {
			'select': {
				'category': "{{ t('Select a category') }}",
				'subCategory': "{{ t('Select a sub-category') }}",
				'country': "{{ t('Select a country') }}",
				'admin': "{{ t('Select a location') }}",
				'city': "{{ t('Select a city') }}"
			},
			'price': "{{ t('Price') }}",
			'salary': "{{ t('Salary') }}",
            'nextStepBtnLabel': {
                'next': "{{ t('Next') }}",
                'submit': "{{ t('Update') }}"
            }
		};
		
		var stepParam = 0;
		
		/* Categories */
		var category = {{ old('parent_id', (int)$postCatParentId) }};
		var categoryType = '{{ old('parent_type') }}';
		if (categoryType == '') {
			var selectedCat = $('select[name=parent_id]').find('option:selected');
			categoryType = selectedCat.data('type');
		}
		var subCategory = {{ old('category_id', (int)$post->category_id) }};
		
		/* Custom Fields */
		var errors = '{!! addslashes($errors->toJson()) !!}';
		var oldInput = '{!! addslashes(collect(session()->getOldInput('cf'))->toJson()) !!}';
		var postId = '{{ $post->id }}';
		
		/* Locations */
		var countryCode = '{{ old('country_code', !empty($post->country_code) ? $post->country_code : config('country.code')) }}';
        var adminType = '{{ config('country.admin_type', 0) }}';
        var selectedAdminCode = '{{ old('admin_code', ((isset($admin) and !empty($admin)) ? $admin->code : 0)) }}';
        var cityId = '{{ old('city_id', (int)$post->city_id) }}';
		
		/* Packages */
        var packageIsEnabled = false;
		@if (isset($packages) and isset($paymentMethods) and $packages->count() > 0 and $paymentMethods->count() > 0)
            packageIsEnabled = true;
		@endif
		

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


	
@endsection
