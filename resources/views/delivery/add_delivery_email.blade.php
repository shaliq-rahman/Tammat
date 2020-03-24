@extends('admin::layout')
@section('content')
<section class="content-header" style='margin-bottom: 20px;'>
   <h1>
      <span class="text-capitalize">Add Delivery Email</span>
      <small></small>
   </h1>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-8 col-md-offset-2">
         <!-- Default box -->
         <a href="{{url('admin/deliveryemail')}}"><i class="fa fa-angle-double-left"></i> Back to all  <span class="text-lowercase">Email Delivery</span></a><br><br>
         <form method="POST" action="{{url('admin/post_delivery_email')}}" accept-charset="UTF-8">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="box box-primary">
               <div class="box-header with-border">
                  <h3 class="box-title">Add a new  Delivery Email</h3>
               </div>
               <div class="box-body row">
                  <!-- load the view from the application if it exists, otherwise load the one in the package -->
                  <!-- text input -->
               
                <div class="form-group col-md-6">
                    <label>Country</label>
                     
                      <select class="form-control select2_field" name="country_code" required >
                            <option value="">Select Country</option>    
                            @foreach($countrydata as $value)
                                <option value="{{$value->code}}">{{$value->name}}</option>    
                            @endforeach
                     </select>
                </div>
                
                
                <div class="form-group col-md-6">
                    <label>Email</label>
                    <input class="form-control" required type="email" name="email">
                </div>
                
                

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
                     <a href="{{url('admin/deliveryemail')}}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;Cancel</a>
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

