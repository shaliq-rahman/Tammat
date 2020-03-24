@extends('admin::layout')
@section('content')
<section class="content-header" style='margin-bottom: 20px;'>
   <h1>
      <span class="text-capitalize">Edit Sidebar Category Banner</span>
      <small></small>
   </h1>
</section>
<section class="content">
   <div class="row">
      <div class="col-md-8 col-md-offset-2">
         <!-- Default box -->
         <a href="{{url('admin/side_bar_post_banner')}}"><i class="fa fa-angle-double-left"></i> Back to all sidebar category <span class="text-lowercase">banner</span></a><br><br>
         <form enctype="multipart/form-data"   method="POST" action="{{url('admin/update_sidebar_category_banner')}}" accept-charset="UTF-8">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{ $category_sidebar_banner->id }}">
            <div class="box box-primary">
               <div class="box-header with-border">
                  <h3 class="box-title">Edit a new Side Bar Category Banner</h3>
               </div>
               <div class="box-body row">
                  
                <div class="form-group col-md-6">
                    <label>Country</label>
                     
                      <select class="form-control select2_field" name="country_code" required>
                            <option value="">Select Country</option>    
                            @foreach($countrydata as $value)
                                <?php
                                $slected = '';
                                if($value->code == $category_sidebar_banner->country_code)
                                {
                                    $slected = 'selected';
                                }
                                ?>
                                <option <?=$slected?> value="{{$value->code}}">{{$value->name}}</option>    
                            @endforeach
                     </select>
                </div>
                
                <div class="form-group col-md-6">
                    <label>Category</label>
                     
                      <select class="form-control select2_field" name="category_id" required >
                            <option value="">Select Category</option>    
                            @foreach($categorydata as $value)
                            <?php
                                $slected = '';
                                    if($value->id == $category_sidebar_banner->category_id)
                                    {
                                        $slected = 'selected';
                                    }
                            ?>
                                <option <?=$slected?> value="{{$value->id}}">{{$value->name}}</option>    
                            @endforeach
                     </select>
                </div>
                  <!-- text input -->
                     <div class="form-group col-md-12">
                        <label>Tracking Code (Large Format)</label>
                        <input type="file" name="tracking_code_large">
                        @if(!empty($category_sidebar_banner->tracking_code_large))
                        <img style="width: 100px;height: 25px;" src="{{url('banner/'.$category_sidebar_banner->tracking_code_large)}}">
                        @endif
                        <input type="hidden" name="tracking_code_large_old" value="{{$category_sidebar_banner->tracking_code_large}}">
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
                     <a href="{{url('admin/side_bar_post_banner')}}" class="btn btn-default"><span class="fa fa-ban"></span> &nbsp;Cancel</a>
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

