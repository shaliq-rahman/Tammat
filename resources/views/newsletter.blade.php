@extends('admin::layout')

@section('content')
<section class="content-header" style='margin-bottom: 20px;'>
        <h1>
            <span class="text-capitalize">Newsletter</span>
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
            
        
        <a href="http://www.tmmat.com/admin/download-newsletter" id='newsletter_list_download' class="btn btn-primary ladda-button" data-style="zoom-in" style='float: right;margin-bottom: 20px;'>
		<span class="ladda-label">
            <i class="fa fa-download"></i> Download (csv/xls)
        </span>
    </a>
</section>
<div class="col-md-12">
	<div class="box box-primary">
<table class="table">
    <thead>
      <tr>
        <th>S#</th>
        <th>Subscriber (Newsletter)</th>
        <th>Created Date</th>
        <th>Updated Date</th>
        <th>Action</th>
     </tr>
    </thead>
    <tbody>
<?php if(count($newsletterData)>0){ 
    $i=0;
    foreach($newsletterData as $nlData) {
        $i++;
?>
      <tr>
        <td>{{$i}}</td>
        <td>{{$nlData->news_letter_email}}</td>
        <td><?=date('d-m-Y h:i A', strtotime($nlData->created_at));?></td>
        <td><?=date('d-m-Y h:i A', strtotime($nlData->updated_at));?></td>
        <td><a href="{{url('admin/newsletter-delete/'.$nlData->news_letter_id)}}" class="btn btn-danger">Delete</a></td>
      </tr>
<?php } } ?>      
    </tbody>
  </table>
 </div><!-- /.box -->
</div>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
     $("body").delegate("#newsletter_list_download", "click", function() {
         console.log('me hre');
     });
    
</script>-->
@endsection
