@extends('admin::layout')

@section('content')

<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	
<section class="content-header" style='margin-bottom: 20px;'>
        <h1>
            <span class="text-capitalize">Side Bar Category Banner</span>
            <small></small>
        </h1>
        <br />
          @if(!empty(session('success')))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button class="close" aria-label="Close" data-dismiss="alert" type="button">
                  <span aria-hidden="true">×</span>
                </button>
                  <strong>Well done!</strong>
                 {{ session('success') }}
            </div>
            @endif
            
        
                 
  
    
    <a href="{{url('admin/add_sidebar_banner')}}" class="btn btn-primary ladda-button" data-style="zoom-in">
		<span class="ladda-label">
            <i class="fa fa-plus"></i> Add Banner
        </span>
    </a>
        
        
  
</section>
<div class="col-md-12">
	<div class="box box-primary">
<table class="table" id="crudTable">
    <thead>
      <tr>
        <th>S#</th>
        <th>Category Name</th>
        <th>Country Code</th>
        <th>Created Date</th>
        <th>Updated Date</th>
        <th>Action</th>
     </tr>
    </thead>
    <tbody>
<?php if(count($advertisings_banner)>0){ 
    $i=0;
    foreach($advertisings_banner as $nlData) {
        $i++;
     ?>
      <tr>
        <td>{{$i}}</td>
        <td>{{$nlData->name}}</td>
        <td>{{$nlData->country_code}}</td>
        <td><?=date('d-m-Y h:i A', strtotime($nlData->created_date));?></td>
        <td><?=date('d-m-Y h:i A', strtotime($nlData->updated_date));?></td>
        <td>
            <a href="{{url('admin/edit_sidebar_category_banner/'.$nlData->id)}}" class="btn btn-primary">Edit</a>
            <a href="{{url('admin/delete_sidebar_category_banner/'.$nlData->id)}}" class="btn btn-danger">Delete</a>
        </td>
      </tr>
<?php } } ?>      
    </tbody>
  </table>
 </div>
</div>

@section('after_styles')
    <link href="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('vendor/adminlte/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('after_scripts')
    <!-- DATA TABLES SCRIPT -->
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables/dataTables.bootstrap.js') }}" type="text/javascript"></script>
	<script src="{{ asset('vendor/adminlte/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var table = $("#crudTable").DataTable({
                    "order": [[ 0, "desc" ]],
                    "pageLength": 250,
			        "lengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
    				responsive: true,
            });
        });
    </script>
  

    
    @stack('crud_list_scripts')
@endsection

@endsection
