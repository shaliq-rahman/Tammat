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

				<div class="col-md-9 page-content">
					<div class="inner-box category-content">
					    
					    <button onclick = "closeWindow('quit')" id="closeTab" class="btn btn-primary btn-lg"> Close Browser Tab </button>
					</div>
				</div>
			</div>
	    </div>
</div>


<script type="text/javascript">
	
	function closeWindow(cmd) {
	    
	    if (cmd=='quit')
        {
            open(location, '_self').close();
        }   
        return false;   
	}
            
    
    
</script>
