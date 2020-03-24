@extends('admin::layout')

@section('content')

<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	
<section class="content-header" style='margin-bottom: 20px;'>
        <h1>
            <span class="text-capitalize">Delivery Email</span>
            <small></small>
        </h1>
        <br />
          @if(!empty(session('success')))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button class="close" aria-label="Close" data-dismiss="alert" type="button">
                  <span aria-hidden="true">×</span>
                </button>
                 {{ session('success') }}
            </div>
            @endif
            
        
 <a href="{{url('admin/add_delivery_email')}}" class="btn btn-primary ladda-button" data-style="zoom-in">
		<span class="ladda-label">
            <i class="fa fa-plus"></i> Add Delivery Email
        </span>
    </a>
  
</section>
<div class="col-md-12">
	<div class="box box-primary">
<table class="table" id="crudTable">
    <thead>
      <tr>
        <th>S#</th>
        <th>Country Code</th>
        <th>Email</th>
        <th>Created Date</th>
        <th>Action</th>
     </tr>
    </thead>
    <tbody>
<?php if(count($deliveryemail)>0){ 
    $i=0;
    foreach($deliveryemail  as $nlData) {
        $i++;
     ?>
     <tr>
        <td>{{$i}}</td>
        <td><?=$nlData->country_code?></td>
        <td><?=$nlData->email?></td>
        <td>{{$nlData->created_date}}</td>  
        <td>
            <a href="{{url('admin/edit_delivery_email/'.$nlData->id)}}" class="btn btn-primary">Edit</a>
            <a href="{{url('admin/delete_delivery_email/'.$nlData->id)}}" class="btn btn-danger">Delete</a>
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
