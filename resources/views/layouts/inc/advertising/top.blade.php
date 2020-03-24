<?php
$advertising = \DB::table('banner')->where('banner_type', 'top')->where('country_code', config('country.icode'))->select('*')->get();
// $advertising = \App\Models\Advertising::where('slug', 'top')->first();
?>
@if (!empty(count($advertising)))
	@include('home.inc.spacer')
	<div class="container">
 
<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->


    
    <div class="carousel-inner">
        <?php
             $ad_i = 0;
        ?>
    @foreach($advertising as $ad_value)
    <?php
            $ad_active = '';
            if($ad_i == 0)
            {
                $ad_active = 'active';
                $ad_i++;
            }
    ?>
      <div class="item <?=$ad_active?>">
        <img src="{{ url('banner/'.$ad_value->tracking_code_large) }}"  style="width:100%;" class="banner-size">
      </div>
    @endforeach
      
    </div>

    
    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
      <span class="sr-only">Previous</span>
    </a>
    
    <a class="right carousel-control" href="#myCarousel" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
      <span class="sr-only">Next</span>
    </a>
    
  </div>
  

	</div>
@endif
