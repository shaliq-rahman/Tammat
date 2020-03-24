@extends('admin::layout')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
 
<script>

var app = angular.module('myApp1', []);
  app.controller('myCtrl1', function($scope, $http) {
     //alert('test');
  $scope.orgtitle=0;
  $scope.show = false;
 
     $scope.UserCategory1 =function() {
         
        $scope.url="{{ url($xPanel->route) }}";
        
      $scope.bootscatid=(document.getElementById("bootscatid").value);
      //alert($scope.bootscatid);
       $('#bootscatid').attr('value',$scope.bootscatid);
    
     
     var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{ url($xPanel->route) }}'+'?kncategory='+$scope.bootscatid,
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
                  $scope.data2 = response.data.CAT1;
                  $scope.data3 = response.data.CAT2;
                  $scope.data4 = response.data.CAT;
	    
	    }    else {
	        $scope.SUBCAT1 = response.data.CAT4;
                  $scope.data1 = response.data.tab5;
                  $scope.data2 = response.data.CAT3;
                  $scope.data3 = response.data.CAT1;
                  $scope.data4 = response.data.CAT2;
                  $scope.data5 = response.data.CAT;
	    }
					$scope.hastrue=false;
					
				}).error(function (data, status, headers, config) {
				    
					$scope.hastrue=false;
				});
     
		
    };
    $scope.category =function() {
      $scope.cat=(document.getElementById("deecat").value);
    // alert($scope.cat);
    
    $('#bootscatid').attr('value', $scope.cat);
    //document.getElementById('bootscatid2').value=$scope.cat;
      // $("#bootscatid").val($scope.cat);
    
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{ url($xPanel->route) }}'+'?cat='+$scope.cat+'&url='+$scope.url,
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
                          $('#bootscatid').attr('value', $scope.cat);
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
        $('#bootscatid').attr('value', $scope.subcat);
     
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{ url($xPanel->route) }}'+'?subcat='+$scope.subcat+'&url='+$scope.url,
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
      $('#bootscatid').attr('value', $scope.subcat2);
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{ url($xPanel->route) }}'+'?subcat2='+$scope.subcat2+'&url='+$scope.url,
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
      //alert($scope.subcat3);
      $scope.url="{{ url()->current() }}";
       $('#bootscatid').attr('value', $scope.subcat3);
	var limit =$scope.itemPerPage;
	var offset=limit*($scope.currentPage-1);
		
	$scope.details=[];
	$scope.subdetails = [];
	$scope.hastrue=true;
	
	var xsrf = $.param({limit: limit, offset:offset});
	$http({
	url: '{{ url($xPanel->route) }}'+'?subcat3='+$scope.subcat3+'&url='+$scope.url,
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
@section('header')
    <section class="content-header" >
        <h1>
            {{ trans('admin::messages.edit') }} <span class="text-lowercase">{!! $xPanel->entity_name !!}</span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('larapen.admin.route_prefix', 'admin')) }}">{{ trans('admin::messages.dashboard') }}</a></li>
            <li><a href="{{ url($xPanel->route) }}" class="text-capitalize">{!! $xPanel->entity_name_plural !!}</a></li>
            <li class="active">{{ trans('admin::messages.edit') }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row"  ng-app="myApp1" ng-controller="myCtrl1" ng-init="UserCategory1()">
        <div class="col-md-8 col-md-offset-2">
            <!-- Default box -->
            @if ($xPanel->hasAccess('list'))
                <a href="{{ url($xPanel->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('admin::messages.back_to_all') }} <span class="text-lowercase">{{-- $xPanel->entity_name_plural --}}</span></a><br><br>
            @endif


            {!! Form::open(array('url' => $xPanel->route.'/'.$entry->getKey(), 'method' => 'put', 'files'=>$xPanel->hasUploadFields('update', $entry->getKey()))) !!}
            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('admin::messages.edit') }}</h3>
                </div>
                
                <div class="box-body row">
            

                    <!-- load the view from the application if it exists, otherwise load the one in the package -->
                    @if(view()->exists('vendor.admin.panel.' . $xPanel->entity_name . '.form_content'))
                    
                        @include('vendor.admin.panel.' . $xPanel->entity_name . '.form_content', ['fields' => $xPanel->getFields('update', $entry->getKey())])
                       
                    @elseif(view()->exists('vendor.admin.panel.form_content'))
                  <?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
   $cururl=explode('/',$actual_link);
   
   if($cururl[4]=='posts'){
   ?>
<?php if(isset($entry)) { ?>
<input type="hidden" id="postcatid"    value="2" name="post_type_id">

	<input type="hidden" value="{{$entry['category_id']}}" id="bootscatid"   name="category_id"  >
	

	<?php } else {?>
	<input type="hidden" value="" id="bootscatid"   name="category_id"  >
	
	<?php } ?>

          		<label class="col-md-3 control-label">{{ t('Category') }} <sup>*</sup></label>
                    	<select onchange="angular.element(this).scope().category(this)" id="deecat" name="cattest"    style="width:96%;margin-left:2%;"
		class="form-control"> 
		
				<option  ng-repeat="d in data1" value="@{{d.id}}">@{{d.name}}</option>
				<option  ng-repeat="C in SUBCAT1"  value="@{{C.id}}" selected>@{{C.name}}</option>
			
		
	</select>
	<!-----Sub Category---->
<br/>
											<label class="col-md-3 control-label">{{ t('Sub-Category') }} <sup>*</sup></label>
											
											    
	<select onchange="angular.element(this).scope().subcategory(this)" id="deesubcat" name="subcat1test"   style="width:96%;margin-left:2%;"
		class="form-control" disabled> 
		
				<option  ng-repeat="x in data2" value="@{{x.id}}">@{{x.name}}</option>
				
			
		
	</select>
	<br/>
	<!-----Sub Category 2--->
	
											<label class="col-md-3 control-label">{{ t('Sub-Category') }}2 </label>
											
											    
		<select onchange="angular.element(this).scope().post(this)" id="deepost" name="subcat2test"  style="width: 96%;margin-left:2%;"
			class="form-control" disabled> 

				<option  ng-repeat="y in data3" value="@{{y.id}}">@{{y.name}}</option>
				
			
		
	</select>
	<br/>
	<!-----Sub Category--->

											<label class="col-md-3 control-label">{{ t('Sub-Category') }}3 </label>
										
		<select onchange="angular.element(this).scope().subcategory3(this)"  id="subcat3" name="subcat3test" style="width: 96%;margin-left:2%;"
			class="form-control" disabled> 
		<option  ng-repeat="z in data4" value="@{{z.id}}">@{{z.name}}</option>
				<option  ng-repeat="f in SUBCAT4"  value="@{{f.id}}" selected>@{{f.name}}</option>
				
				
			
		
	</select>
	
	<br/>
	
		<!-----Sub Category4--->

											<label class="col-md-3 control-label">{{ t('Sub-Category') }}4 </label>
										
		<select onchange="angular.element(this).scope().subcategory4(this)" name="subcat4test"  id="subcat4" style="width: 96%;margin-left:2%;"
			class="form-control" disabled> 
		<option  ng-repeat="t in data5" value="@{{t.id}}">@{{t.name}}</option>
			
				
				
			
		
	</select>
              
	<?php } else {?>
	@if(!empty($field))
	<select
			name="{{ $field['name'] }}@if (isset($field['allows_multiple']) && $field['allows_multiple']==true)[]@endif"
			style="width: 100%"
			@include('admin::panel.inc.field_attributes', ['default_class' =>  'form-control select2_from_array'])
			@if (isset($field['allows_multiple']) && $field['allows_multiple']==true)multiple @endif
	>
		
		@if (isset($field['allows_null']) && $field['allows_null']==true)
			<option value="">-</option>
		@endif
		
		@if (isset($field['options']) && !empty($field['options']))
			@foreach ($field['options'] as $key => $value)
				<option value="{{ $key }}"
						@if (isset($field['value']) && ($key==$field['value'] || (is_array($field['value']) && in_array($key, $field['value'])))
							|| ( ! is_null( old($field['name']) ) && old($field['name']) == $key))
						selected
						@endif
				>{!! $value !!}</option>
			@endforeach
		@endif
	</select>
	@endif
	<?php } ?>
                        @include('vendor.admin.panel.form_content', ['fields' => $xPanel->getFields('update', $entry->getKey())])
                    @else
                   
                        @include('admin::panel.form_content', ['fields' => $xPanel->getFields('update', $entry->getKey())])
                    @endif
                </div><!-- /.box-body -->
                <div class="box-footer">
	
					@include('admin::panel.inc.form_save_buttons')
                
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
            {!! Form::close() !!}
        </div>
    </div>
  
@endsection
