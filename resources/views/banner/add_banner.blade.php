@extends('admin::layout')
@section('content')
<section class="content-header" style='margin-bottom: 20px;'>
   <h1>
      <span class="text-capitalize">Add Banner</span>
      <small></small>
   </h1>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-8 col-md-offset-2">
         <!-- Default box -->
         <a href="{{url('admin/banner')}}"><i class="fa fa-angle-double-left"></i> Back to all  <span class="text-lowercase">banner</span></a><br><br>
         <form enctype="multipart/form-data" method="POST" action="{{url('admin/post_banner')}}" accept-charset="UTF-8">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box box-primary">
               <div class="box-header with-border">
                  <h3 class="box-title">Add a new  Banner</h3>
               </div>
               <div class="box-body row">
                  <!-- load the view from the application if it exists, otherwise load the one in the package -->
                  <!-- text input -->
                  <div class="form-group col-md-6" id="first_name">
                     <label>Banner Type</label>
                     <select class="form-control" required name="banner_type">
                            <option value="">Select Banner Type</option>    
                            <option value="top">top</option>    
                            <option value="bottom">bottom</option>    
                     </select>
                  </div>
                <div class="form-group col-md-6">
                    <label>Country</label>
                     
                      <select class="form-control select2_field" name="country_code" required >
                            <option value="">Select Country</option>    
                            @foreach($countrydata as $value)
                                <option value="{{$value->code}}">{{$value->name}}</option>    
                            @endforeach
                     </select>
                  </div>
                  <!-- text input -->
                  
                  
                    <div class="form-group col-md-12">
                        <label>Image</label>
                        <input type="file" required  name="tracking_code_large">
                        
                    </div>
                  <div class="form-group col-md-12">
                        <label>Link</label>
                        <input type="url" class="form-control"   name="link">
                        
                    </div>
                    <!--<div class="form-group col-md-12">-->
                    <!--    <label>Tracking Code (Tablet Format)</label>-->
                    <!--     <input type="file" name="tracking_code_medium">-->
                    <!--</div>  -->
                    
                    <!--<div class="form-group col-md-12">-->
                    <!--    <label>Tracking Code (Phone Format)</label>-->
                    <!--    <input type="file" name="tracking_code_small">-->
                        
                    <!--</div>-->
               </div>
               <!-- /.box-body -->
               <div class="box-footer">
                  <div id="saveActions" class="form-group">
                     <input name="save_action" value="save_and_back" type="hidden">
                     <div class="btn-group">
                        <button type="submit" class="btn btn-success">
                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span> &nbsp;
                        <span data-value="save_and_back">Save and back</span>
                        </button>
                      
                     </div>
                     <a href="{{url('admin/banner')}}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;Cancel</a>
                  </div>
               </div>
               <!-- /.box-footer-->
            </div>
         </form>
         <!-- /.box -->
      </div>
   </div>
</section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<link href="{{ asset('vendor/adminlte/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('vendor/adminlte/plugins/select2/select2.min.js') }}"></script>
	<script>
		jQuery(document).ready(function($) {
			// trigger select2 for each untriggered select2 box
			$('.select2_field').each(function (i, obj) {
				if (!$(obj).hasClass("select2-hidden-accessible"))
				{
					$(obj).select2({
						theme: "bootstrap"
					});
				}
			});
		});
	</script>
	

       

@endsection

