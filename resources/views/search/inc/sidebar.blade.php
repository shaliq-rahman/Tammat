
<link href="{{ url('css/custom-search.css') . getPictureVersion() }}" rel="stylesheet">
<style>
.count{display: none !important;}

</style>

<?php
/**
 * Mobile Filter Sidebar
 * 
 * This section (.mobile-filter-sidebar) will be position fixed in the mobile version.
 *
 * @created 2020-06-25
 * @author Abdelhay
 * 
 * Notes:
 * 1. The width for <a href> is effective and will not work if set to full width.
 */

// Abdelhay's comment || 1==1

// Set default value for distance if not provided in the request
$postsCount = 0;
$requestGetDistance = empty(Request::get('distance')) ? 300 : Request::get('distance');

// Get the full URL
$fullUrl = url(\Illuminate\Support\Facades\Request::getRequestUri());

// Extract the tail part of the URL
$tail = "";
$tmpExplode = explode('?', $fullUrl);
if (!empty($tmpExplode[1])) {
    $tmpExplode[1] = str_replace("page=", "", $tmpExplode[1]);
    $tail = "?" . $tmpExplode[1];
}

// Get the full URL without parameters
$fullUrlNoParams = current($tmpExplode);

// Uncomment the line below for debugging purposes
// print_r($tmpExplode);

// Uncomment the lines below if parameters need to be processed individually
// $parms = explode('&', $tmpExplode[1]);
// foreach($parms as $parm) {}
//dd($_get())
//echo $_get['lang'];
//dd($_ssesion)
//dd($cats); 
 ?>  

@if(strtoupper(config('app.locale')) == 'AR')                                  

<style>

    .panel-default>.panel-heading a:after {float: left;}

	b, strong {color: black;}

	.panel-default>.panel-heading a {

    color: black;}

</style>

@endif


<div class="col-sm-3 page-sidebar mobile-filter-sidebar" style="padding-bottom: 20px;">



	<aside>



		<div class="inner-box enable-long-words">



                @if (isset($cat))



                  <?php $style = 'style="display: none;"'; ?>



                @endif



               <!-- Category -->



			<div id="catsList" class="categories-list list-filter" <?php echo (isset($style)) ? $style : ''; ?>>



				<h5 class="list-title">



                    <strong><a href="#">{{ t('All Categories') }}</a></strong>



                </h5>



				<ul class="list-unstyled">



                    @if ($cats->groupBy('parent_id')->has(0))



					@foreach ($cats->groupBy('parent_id')->get(0) as $iCat)





							<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iCat->slug]; ?>



							<!-- for counttig -->

							<?php 

							
 /* abdelhay comment 
							   

							    $childIds = \App\Models\Category::whereIn('parent_id', [$iCat->id,$iCat->translation_of])
                                          //  ->where('translation_lang', '=', $iCat->translation_lang)
                                          ->pluck('id');
                                $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)

                                              // ->where('translation_lang', '=', $iCat->translation_lang)
                                            ->pluck('id');
                                            //dd($subchildIds);
                   
                                $subSubchildIds = \App\Models\Category::whereIn('parent_id', $subchildIds)
                                            //   ->where('translation_lang', '=', $iCat->translation_lang)
                                            ->pluck('id');



                              $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                ->where(function ($query) use ($subchildIds,$childIds,$subSubchildIds) {



                                                                   $query->whereIn('category_id',$childIds)



                                                                         ->orWhereIn('category_id',$subchildIds)



                                                                         ->orWhereIn('category_id',$subSubchildIds);



                                                               })->where('reviewed',1)->where('archived',0)->count();*/



							//echo $postsCount;

							?>

                         <?php if($postsCount > 0   || 1==1){//first level ?>
						<li>









							@if ((isset($uriPathCatSlug) and $uriPathCatSlug == $iCat->slug) or (Request::input('c') == $iCat->tid))



								<strong>



									<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}{{$tail}}" title="{{ $iCat->name }}"
                                        
                                        style="color:grey;font-weight: bold"
                                        >

							

										<span class="title">*{{ $iCat->name }}</span>



										<span class="count">&nbsp;({{ $postsCount or 0 }})</span>



									</a>



								</strong>



							@else

 

                           

                           <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">
                                  <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingfive">
                                        <h4 class="panel-title">
                                           <a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$iCat->slug}}"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>
 
											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iCat->name }}" style="width:85%;">
												{{ str_limit($iCat->name, 100) }}
                                                <span class="count">({{ $postsCount or 0 }}) </span>
                                            </a> 
                                        <!---srart code---> 

                           <?php  
                            $supersubcat = \DB::table('categories')
						   ->whereIn('parent_id', [$iCat->id,$iCat->translation_of])
                           ->where('translation_lang', '=', $iCat->translation_lang)
                            ->get();
                            ?>



<div id="collapse{{$iCat->slug}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($i=0;$i<sizeof($supersubcat);$i++){?>	

                                        

                                        

                                        <?php 
                                      /* abdelhay comment  $childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat[$i]->id,$supersubcat[$i]->translation_of])
                                                     ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)
                                                     ->pluck('id');

                                        $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)
                                                       ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)
                                                       ->pluck('id');

                                        $subSubchildIds = \App\Models\Category::whereIn('parent_id', $subchildIds)
                                                          ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)
                                                          ->pluck('id');
                                 
                                        $superid=$supersubcat[$i]->id;
                                        $superid_ext=$supersubcat[$i]->translation_of;

        

                                        $postsCount = \App\Models\Post::where('country_code',config('country.icode'))

        

                                                                   //     ->where(function ($query) use ($subchildIds,$childIds,$subSubchildIds) {

																	    ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

        

                                                                           $query->whereIn('category_id',$childIds)

        

                                                                                 ->orWhereIn('category_id',$subchildIds)

        

                                                                                // ->orWhereIn('category_id',$subSubchildIds);

																				 ->orWhereIn('category_id',[$superid,$superid_ext]);

        

                                                                       })->where('reviewed',1)->where('archived',0)->count();*/

        

                                    ?>

                 <?php if($postsCount > 0  || 1==1){//second level ?>                   

                                    

       

                             <li>

                              

                               

                           

                           <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	



										

										

            									 

                

                                

                                	<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$supersubcat[$i]->slug}}"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>



<?php /*?>  <a href="https://www.tmmat.com/category/<?php print_r($supersubcat[$i]->slug); ?>" title="{{ $supersubcat[$i]->name }}" style="width:85%;"><?php */?>



<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $supersubcat[$i]->slug]; ?>



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $supersubcat[$i]->name }}" style="width:85%;">



												<span style="color: whitesmoke">**</span>{{ str_limit($supersubcat[$i]->name, 100) }}



												<span class="count">({{ $postsCount or 0 }}) </span>



											</a>

                                           

                                

                                            

                                            

                                

                                    <!---srart code--->

                          

                           <?php  $supersubcat_2 = \DB::table('categories')->whereIn('parent_id', [$supersubcat[$i]->id,$supersubcat[$i]->translation_of])		



                         ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)

						



                        ->get(); 

						

						

						?>



<div id="collapse{{$supersubcat[$i]->slug}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($x=0;$x<sizeof($supersubcat_2);$x++){?>	

                                        

                                         <?php  

/*abdelhay comment

        									$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat_2[$x]->id,$supersubcat_2[$x]->translation_of])



                                               //->where('translation_lang', '=', $supersubcat_2[$x]->translation_lang)

                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                      //   ->where('translation_lang', '=', $supersubcat_2[$x]->translation_lang)



                                                        ->pluck('id');

														

$superid=$supersubcat_2[$x]->id;

$superid_ext=$supersubcat_2[$x]->translation_of;

                                            $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>

                                    

                                    

        <!-- icommented this becouse redirect in same place but disign not good abdelhay xxxxxx-->

                             <?php /*?>     <li><a href="https://www.tmmat.com/category/{{$iCat->slug}}/<?php print_r($supersubcat[$i]->slug); ?>" title="Watches"><?php */?>

                           

                          <?php if($postsCount > 0   || 1==1){//third level ?>                    

                             <li>

                             

        

                                

                                

                                    <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	



										

										

            								<?php /*?>     <li><a href="https://www.tmmat.com/category/{{$iCat->slug}}/<?php print_r($supersubcat[$i]->slug); ?>" title="Watches"><?php */?>	 

                                                         

                                

                                

                                

                                

                                	<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$supersubcat_2[$x]->slug}}"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>





 <?php /*?> <a href="https://www.tmmat.com/category/<?php print_r($supersubcat[$i]->slug); ?>/<?php print_r($supersubcat_2[$x]->slug); ?>" title="{{ $supersubcat_2[$x]->name }}" style="width:85%;"><?php */?>

  

  

  <?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $supersubcat[$i]->slug]; ?>



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}/<?php print_r($supersubcat_2[$x]->slug); ?>{{$tail}}" title="{{ $supersubcat_2[$x]->name }}" style="width:85%;">

                                                <span style="color: whitesmoke">***</span>{{ str_limit($supersubcat_2[$x]->name, 100) }}

                                            <span class="count">({{ $postsCount or 0 }}) </span>

                                            </a>

                                      

                                 

                                

                                    <!---srart code--->

                          

                           <?php  $supersubcat_3 = \DB::table('categories')->whereIn('parent_id', [$supersubcat_2[$x]->id,$supersubcat_2[$x]->translation_of])

                        ->where('translation_lang', '=', $supersubcat_2[$x]->translation_lang)



                        ->get(); 

						

					//print_r($supersubcat_3);	

						?>



<div id="collapse{{$supersubcat_2[$x]->slug}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($w=0;$w<sizeof($supersubcat_3);$w++){

									
 /*  abdelhay comment


        									$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat_3[$w]->id,$supersubcat_3[$w]->translation_of])

					                                       // ->where('translation_lang', '=', $supersubcat_3[$w]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                       //  ->where('translation_lang', '=', $supersubcat_3[$w]->translation_lang)



                                                        ->pluck('id');

														

$superid=$supersubcat_3[$w]->id;



$superid_ext=$supersubcat_3[$w]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);



                                          $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                   

																					->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                       

                       

                       

                                    

                                            <!-- icommented this becouse redirect in same place but disign not good abdelhay yyyyyy-->

                             <?php /*?>     <li><a href="https://www.tmmat.com/category/{{$supersubcat_2[$x]->slug}}/<?php print_r($supersubcat_2[$x]->slug); ?>" title="Watches"><?php */?>

                            

                             <?php if($postsCount > 0   || 1==1){//fourth level ?>   

                            

                             <li>

                             

        

                          

                      

                      

                       

                                    <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	



										

										

            								<?php /*?>     <li><a href="https://www.tmmat.com/category/{{$iCat->slug}}/<?php print_r($supersubcat[$i]->slug); ?>" title="Watches"><?php */?>	 

                                                         

                                

                                

                                

                                

                                	<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$supersubcat_3[$w]->slug}}"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>







<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $supersubcat_2[$x]->slug]; ?>



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}/<?php print_r($supersubcat_3[$w]->slug); ?>{{$tail}}" title="{{ $supersubcat_3[$w]->name }}" style="width:85%;">

                                            

                                            

<?php /*?>  <a href="https://www.tmmat.com/category/<?php print_r($supersubcat_2[$x]->slug); ?>/<?php print_r($supersubcat_3[$w]->slug); ?>" title="{{ $supersubcat_3[$w]->name }}" style="width:85%;">

											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}" title="{{ $supersubcat[$i]->name }}" style="width:85%;"><?php */?>



                                           <span style="color: whitesmoke">****</span>{{ str_limit($supersubcat_3[$w]->name, 100) }}



												<span class="count">({{ $postsCount or 0 }}) </span>



											</a>

                                      

                                            

                                            

                                

                                    <!---srart code--->

                          

                           <?php  $supersubcat_4 = \DB::table('categories')



                        ->whereIn('parent_id', [$supersubcat_3[$w]->id,$supersubcat_3[$w]->translation_of])

                        ->where('translation_lang', '=', $supersubcat_3[$w]->translation_lang)

                        ->get(); 

						

						

						?>



<div id="collapse{{$supersubcat_3[$w]->slug}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($z=0;$z<sizeof($supersubcat_4);$z++){?>	

                                        

                                        

                                   

                                    

                                    

                                    

										 

 

                                                <?php  

  /*abdelhay comment

        									$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat_4[$z]->id,$supersubcat_4[$z]->translation_of])



					                                      // ->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)

											               



                                                            // ->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');

														

$superid=$supersubcat_4[$z]->id;

$superid_ext=$supersubcat_4[$z]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);



                                          $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                       

                       

                       

                                    

                                            <!-- icommented this becouse redirect in same place but disign not good abdelhay wwwwww-->

                             <?php /*?>     <li><a href="https://www.tmmat.com/category/{{$supersubcat_2[$x]->slug}}/<?php print_r($supersubcat_2[$x]->slug); ?>" title="Watches"><?php */?>

                             

                                <?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $supersubcat_3[$w]->slug]; ?>

                       

                         <?php if($postsCount > 0   || 1==1){//5th level ?> 
                            <li>
                                <a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}/<?php print_r($supersubcat_4[$z]->slug); ?>{{$tail}}" 
                                    title="{{ $supersubcat_4[$z]->name }}" style="width:85%;">
                                    <?php /*?>    <a href="https://www.tmmat.com/category/<?php print_r($supersubcat_3[$w]->slug); ?>/<?php print_r($supersubcat_4[$z]->slug); ?>"
                                     title="{{$supersubcat_4[$z]->name}}">
                                     <?php */?>
                                      <span style="color: whitesmoke">*****</span><?php print_r($supersubcat_4[$z]->name);?> <span class="count">({{ $postsCount or 0 }})</span></a></li>
                                  <?php } //end 5th level ?> 
                                
                                  <?php } ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                      

                      

                      

                      

                      

                      

                      

                      

                      

                      

                      

                      

                      

                      

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                </li>

                                

                             <?php } //end fourth level?>  

                                

                                

                                

        

                                <?php }//endforeach ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                </li>

                               

                           <?php } //end third level ?>

                                <?php }//endforeach ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                

                                </li>

                                

             <?php } //end second level?>                  

                                

                                

                                

        

                                <?php } ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      



							@endif



						</li>

<?php } //end first level ?>

					@endforeach



                    @endif



				</ul>



                        <!-- Date -->



            <div class="list-filter">



                <h5 class="list-title"><strong><a href="#"> {{ t('Date Posted') }} </a></strong></h5>



                <div class="filter-date filter-content">



                    <ul>



                        @if (isset($dates) and !empty($dates))



                            @foreach($dates as $key => $value)



                                <li>



                                    <input type="radio" name="postedDate" value="{{ $key }}" id="postedDate_{{ $key }}" {{ (Request::get('postedDate')==$key) ? 'checked="checked"' : '' }}>



                                    <label for="postedDate_{{ $key }}">{{ $value }}</label>



                                </li>



                            @endforeach



                        @endif



                        <input type="hidden" id="postedQueryString" value="{{ httpBuildQuery(Request::except(['postedDate'])) }}">



                    </ul>



                </div>



            </div>



			</div>



            @if (isset($cat))



        		<?php $parentId = ($cat->parent_id == 0) ? $cat->tid : $cat->parent_id; ?>



                <!-- SubCategory -->



				<div id="subCatsList" class="categories-list list-filter">



					<h5 class="list-title">



                        <strong><a href="#"><i class="fa fa-angle-left"></i> {{ t('Others Categories') }}</a></strong>



                    </h5>



					<ul class="list-unstyled">



					     @if ($cats->groupBy('parent_id')->has($parentId))



								@foreach ($cats->groupBy('parent_id')->get($parentId) as $iSubCat)



                                    @continue(!$cats->has($iSubCat->parent_id))



                                    @if ($cats->groupBy('parent_id')->has($iSubCat->translation_of) )



                                        @php($total = 0)



                    					@foreach ($cats->groupBy('parent_id')->get($iSubCat->translation_of) as $iSubCat2)



                        					@if(isset($countSubCatPosts->get($iSubCat2->tid)->total))



                        					    @php($total +=  $countSubCatPosts->get($iSubCat2->tid)->total )



                        				    @endif



                    					@endforeach



                    		    <?php $sai[]= "$total,"; ?>



                    			<?php $average = collect($sai)->sum();  ?>



                    				@endif



                    				@endforeach



                    	@endif

                        

                           <?php 


/* abdelhay comment 
							    $childIds = \App\Models\Category::whereIn('parent_id', [$parentId,$iSubCat->translation_of])

								                                // ->where('translation_lang', '=', $iSubCat->translation_lang)

                                                                 ->pluck('id');



                                $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                             // ->where('translation_lang', '=', $iSubCat->translation_lang)



                                            ->pluck('id');



                                $subSubchildIds = \App\Models\Category::whereIn('parent_id', $subchildIds)



                                           //   ->where('translation_lang', '=', $iSubCat->translation_lang)



                                            ->pluck('id');



                               $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                ->where(function ($query) use ($subchildIds,$childIds,$subSubchildIds) {



                                                                   $query->whereIn('category_id',$childIds)



                                                                         ->orWhereIn('category_id',$subchildIds)



                                                                         ->orWhereIn('category_id',$subSubchildIds);



                                                               })->where('reviewed',1)->where('archived',0)->count();*/



							?>

                            

                            

                           <?php if($postsCount > 0   || 1==1){//single search 1 level ?>  



						<li >

                        

                        

                              <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	

                                           

                                            



                            @if ($cats->has($parentId))



								<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $cats->get($parentId)->slug]; ?>



<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_acc_main"  class="panel-collapse"  aria-expanded="true" aria-controls="collapseOne" title="{{ $cats->get($parentId)->name }}" ></a>

								<a href="{{ lurl(trans('routes.v-search-cat', $attr), $attr) }}{{$tail}}" title="{{ $cats->get($parentId)->name }}" style="width:85%">



									<span class="title"><strong>6*{{ $cats->get($parentId)->name }}</strong>



									<!-- <span class="count">&nbsp;({{$average or 0}}) </span>  -->

                                    <span class="count">&nbsp;({{$postsCount or 0}}) </span>

                                    

                                     </span>



								</a>



                            @endif

                            

                            <div id="collapse_acc_main" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   



							<ul class="list-unstyled">



							    <!--long-list-->



                                @if ($cats->groupBy('parent_id')->has($parentId))



								@foreach ($cats->groupBy('parent_id')->get($parentId) as $iSubCat)



                                    @continue(!$cats->has($iSubCat->parent_id))



                                    @if ($cats->groupBy('parent_id')->has($iSubCat->translation_of) )



                                        @php($total = 0)



                    					@foreach ($cats->groupBy('parent_id')->get($iSubCat->translation_of) as $iSubCat2)



                        					@if(isset($countSubCatPosts->get($iSubCat2->tid)->total))



                        					    @php($total +=  $countSubCatPosts->get($iSubCat2->tid)->total )



                        				    @endif



                    					@endforeach



                    				@endif



     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

     

                                    <!-----disha (Collapse)------>

 

<li style="clear: both;">



<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">



<div class="panel panel-default">



<div class="panel-heading" role="tab" id="headingOne">



<h4 class="panel-title">







                                         	





<!--<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$iSubCat->slug}}xxx" class="collapsed" aria-expanded="false" aria-controls="collapseOne" style="width:22%;float:right;">-->



<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat->parent_id)->slug,



											'subCatSlug'  => $iSubCat->slug



										]; 



										?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat->slug) or (Request::input('sc') == $iSubCat->tid))



											<strong>

												<!--<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}" title="{{ $iSubCat->name }}" style="width:85%;">-->

													7*{{ str_limit($iSubCat->name, 100) }}

													<span class="count">({{ $countSubCatPosts->get($iSubCat->tid)->total or 0 }}) </span>



											</strong>



										@else



									<?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

									

									$slugarr2=explode("?",$actual_link);

									$actual_link=$slugarr2[0];



$slug=explode("/",$actual_link);



/*if(!empty($slug[5])){	

	$slugarr2=explode("?",$slug[5]);

	if(!empty($slugarr2[0])){$slug[5]=$slugarr2[0];}

}

*/

//dd($slugarr2);



$font_w="";

if($slug[3]=='category'){



if($slug[4]==$iSubCat->slug){

	

	

	

 









?>	



										<!--	<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}" title="{{ $iSubCat->name }}" style="width:85%;">-->



<a  data-toggle="collapse" data-parent="#accordion" href="#collapse{{$iSubCat->slug}}_new1"  aria-expanded="true" aria-controls="collapseOne" title="{{ $iSubCat->name }}" style="width:100%;"></a> 



											<?php } else {?>



<a  data-toggle="collapse" data-parent="#accordion" href="#collapse{{$iSubCat->slug}}_new1" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="{{ $iSubCat->name }}" style="width:100%;"></a>



											<?php }



											} else {



											if($slug[5]==$iSubCat->slug){?>



<a  data-toggle="collapse" data-parent="#accordion" href="#collapse{{$iSubCat->slug}}_new1"  aria-expanded="true" aria-controls="collapseOne" title="{{ $iSubCat->name }}" style="width:100%;"></a>	  

<?php $font_w="Bold";?>

												

												<?php } else {?>



<?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iSubCat->slug]; ?>



<a  data-toggle="collapse" data-parent="#accordion" href="#collapse{{$iSubCat->slug}}_new1" aria-expanded="false" aria-controls="collapseOne" title="{{ $iSubCat->name }}" class="collapsed" style="width:100%;"></a>



											<?php }



											}?> 

                                            

                                            

                                            

										

										

            									 

                                                         

                                 

                                            

                                            



									<?php  $supersubcatname = \DB::table('categories')



                                                        ->where('parent_id', '=', $iSubCat->id)



                                                        ->where('translation_lang', '=', $iSubCat->translation_lang)



                                                        ->pluck('slug'); 



                                                        // if(isset($supersubcatname->slug) && $supersubcatname->slug == "bags-1")



                                                        //     dd($supersubcatname);



                        ?>		



						<?php 
/* abdelhay comment


								$childIds = \App\Models\Category::whereIn('parent_id', [$iSubCat->id,$iSubCat->translation_of])

								

					                                     //  ->where('translation_lang', '=', $iSubCat->translation_lang)



                                                        ->pluck('id');



                                $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                        //   ->where('translation_lang', '=', $iSubCat->translation_lang)



                                                        ->pluck('id');



                                                        

															$superid=$iSubCat->id;

                                                    $superid_ext=$iSubCat->translation_of;

        

                                         $postsCount = \App\Models\Post::where('country_code',config('country.icode'))

        

                                                                   //     ->where(function ($query) use ($subchildIds,$childIds,$subSubchildIds) {

																	    ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

        

                                                                           $query->whereIn('category_id',$childIds)

        

                                                                                 ->orWhereIn('category_id',$subchildIds)

        

                                                                                // ->orWhereIn('category_id',$subSubchildIds);

																				 ->orWhereIn('category_id',[$superid,$superid_ext]);

        

                                                                       })->where('reviewed',1)->where('archived',0)->count();*/

																	   

    



						for($i=0;$i<1;$i++){

							

							?>	<p style="font-weight:{{$font_w}};margin: 0px;">



						<?php if(is_null($supersubcatname)){	?>	



				



				



											{{ str_limit($iSubCat->name, 100) }}



										<span class="count"> ({{$postsCount or 0}}) 111111111</span>



										<?php } else {?>

 

                                      

										<!-- icommented this becouse redirect in same place but disign not good abdelhayfffff-->

                                      <a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat->name }}" style="width:85%">



                                      <?php /*?>  <a href="https://www.tmmat.com/category/<?php print_r($iSubCat->slug); ?>" title="{{ $iSubCat->name }}" style="width:85%"> <?php */?>

										8*{{ str_limit($iSubCat->name, 100) }}



										<span class="count"> ({{$postsCount or 0}})

                                        <!-- we need this xxxxssss--></span>



										</a>

                                        

                                        

                                        



										<?php }?>

										

										</p>

										<?php 



								// 		dd($postsCount);



										} ?>

                                                

											</a> {{-- revferv --}}



											</a> {{-- revferv --}}



										@endif



<!--</a> 754-->



</h4>



</div>



<?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

	 

	 

									

									$slugarr2=explode("?",$actual_link);

									$actual_link=$slugarr2[0];



$slug=explode("/",$actual_link);



if($slug[3]=='category'){

	

	



if($slug[4]==$iSubCat->slug){



?>



<div id="collapse{{$iSubCat->slug}}_new1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne" aria-expanded="true">



<div class="panel-body">



 @if ($cats->groupBy('parent_id')->has($iSubCat->id) and config('country.icode')=="en" )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->id) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



										<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										]; 



										?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



											<strong>



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



													9*{{ str_limit($iSubCat2->name, 100) }}wwwwww



													<span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})44444444444444444 </span>



												</a>



											</strong>



										@else



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



												10*{{ str_limit($iSubCat2->name, 100) }}rrrrrr



                                                <span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }}) 55555555555555555</span>



											</a>



										@endif



									</li>



									</ul>



								@endforeach



                                @endif



							    @if ($cats->groupBy('parent_id')->has($iSubCat->translation_of) )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->translation_of) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



									    <div class="panel-group" id="accordion3" role="tablist" aria-multiselectable="true">



<div class="panel panel-default">



<div class="panel-heading" role="tab" id="headingthree">



<h4 class="panel-title">	



										<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										];



										?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



											<strong>



											   	<a data-toggle="collapse" data-parent="#accordion3" href="#collapse{{$iSubCat2->slug}}" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="Accessories" >	 



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}" style="width:85%;">



													11*{{ str_limit($iSubCat2->name, 100) }}



													<span class="count">({{ $countSubCatPosts->get($iSubCat2->id)->total or 0 }}) 777777777777777777</span>



												</a>



											</strong>



										@else



									<?php  $supersubcatname = \DB::table('categories')



                        ->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



                         ->where('translation_lang', '=', $iSubCat2->translation_lang)



                        ->first(); ?>		



						<?php 



						for($i=0;$i<1;$i++){



						if($supersubcatname!=""){



					?>		



										<a data-toggle="collapse" data-parent="#accordion3" href="#collapse{{$iSubCat2->slug}}{{$tail}}" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="Accessories" >	



												12*{{ str_limit($iSubCat2->name, 100) }}



                                                <span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})88888888888888</span>



											</a>



											<?php } else {?>



											<a data-toggle="collapse" data-parent="#accordion3" href="#collapse{{$iSubCat2->slug}}" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>	



										<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}" style="width:85%;">



												13*{{ str_limit($iSubCat2->name, 100) }}



                                                <span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})99999999999999 </span>



											</a>



											<?php } 



											}?>



										@endif



								</div>



										<?php  $supersubcat = \DB::table('categories')

										->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



                     

                          ->where('translation_lang', '=', $iSubCat2->translation_lang)



                        ->get(); ?>



									<div id="collapse{{$iSubCat2->slug}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



<div class="panel-body">



    <ul class="list-unstyled">



						<?php for($i=0;$i<sizeof($supersubcat);$i++){?>			



                           <li><a href="https://www.tmmat.com/category/{{$iSubCat2->slug}}/<?php print_r($supersubcat[$i]->slug); ?>{{$tail}}" title="Watches">



						<?php print_r($supersubcat[$i]->name);?></a></li>



                        <?php } ?>



                        </ul>



                        </div>



                        </div>	



							</div>



							</div>



									</li>



									</ul>



								@endforeach



                                @endif



</div>



</div>



<?php } else { ?>



000000000000000000



<div id="collapse{{$iSubCat->slug}}_new1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



<div class="panel-body">



 @if ($cats->groupBy('parent_id')->has($iSubCat->id) and config('country.icode')=="en" )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->id) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



										<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										]; ?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



											<strong>



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



													14*{{ str_limit($iSubCat2->name, 100) }}



													<span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})* </span>



												</a>



											</strong>



										@else



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



												15*{{ str_limit($iSubCat2->name, 100) }}



                                                <span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }}) **</span>



											</a>



										@endif



									</li>



									</ul>



								@endforeach



                                @endif



							    @if ($cats->groupBy('parent_id')->has($iSubCat->translation_of) )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->translation_of) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



									    <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



<div class="panel panel-default">



<div class="panel-heading" role="tab" id="headingtwo">



<h4 class="panel-title">	



										<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										]; ?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



											<strong>



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



													<span class="count">({{ $countSubCatPosts->get($iSubCat2->id)->total or 0 }})*** </span>



												</a>



											</strong>



										@else



									<?php  $supersubcatname = \DB::table('categories')



                        ->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])

						



                        ->where('translation_lang', '=', $iSubCat2->translation_lang)



                        ->first(); ?>		



						<?php 



						for($i=0;$i<1;$i++){



						if($supersubcatname!=""){



					?>	



						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$iSubCat2->slug}}" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="Accessories" >	



											16*{{ str_limit($iSubCat2->name, 100) }}



												<span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }}) ****</span>



											</a>



							<?php } else {?>



							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$iSubCat2->slug}}" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="Accessories" >	 </a>



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}" style="width:85%;">



											   17*{{ str_limit($iSubCat2->name, 100) }}



												<span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})***** </span>



											</a>



							<?php }



							} ?>



							<?php  $supersubcat = \DB::table('categories')



                      ->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])

					 

                             ->where('translation_lang', '=', $iSubCat2->translation_lang)



                        ->get(); ?>			



                      <div id="collapse{{$iSubCat2->slug}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" >



<div class="panel-body">



    <ul class="list-unstyled">



						<?php for($i=0;$i<sizeof($supersubcat);$i++){?>			



                           <li><a href="https://www.tmmat.com/category/{{$iSubCat2->slug}}/<?php print_r($supersubcat[$i]->slug); ?>{{$tail}}" title="Watches">



						<?php print_r($supersubcat[$i]->name);?></a>yyyyyyyyy</li>



                        <?php } ?>



                        </ul>



                        </div>



                        </div>



										@endif



						</div>



						</div>



									</li>



									</ul>



								@endforeach



                                @endif



</div>



</div>



<?php }



} else { 



if($slug[5]==$iSubCat->slug){ ?>



<div id="collapse{{$iSubCat->slug}}_new1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne" aria-expanded="true">



<div class="panel-body">



                                @if ($cats->groupBy('parent_id')->has($iSubCat->id) and config('country.icode')=="en" )



    								@foreach ($cats->groupBy('parent_id')->get($iSubCat->id) as $iSubCat2)



                                        <ul class="list-unstyled">



    									<li>



    										<?php $attr = [



    											'countryCode' => config('country.icode'),



    											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



    											'subCatSlug'  => $iSubCat2->slug



    										]; ?>



    										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



    											<strong>



    												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



    													18*{{ str_limit($iSubCat2->name, 100) }}



    													<span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})******</span>



    												</a>



    											</strong>



    										@else



    											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



    												19*{{ str_limit($iSubCat2->name, 100) }}



                                                    <span class="count">({{ $countSubCatPosts->get($iSubCat2->tid)->total or 0 }})********</span>



    											</a>



    										@endif



    									</li>



    									</ul>



    								@endforeach



                                @endif



							    @if ($cats->groupBy('parent_id')->has($iSubCat->translation_of) )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->translation_of) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



									    <div class="panel-group" id="accordion5" role="tablist" aria-multiselectable="true">

                                            <div class="panel panel-default">

                                            <div class="panel-heading" role="tab" id="headingfive">

                                            <h4 class="panel-title">	



								  		<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										]; 



										?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



											<strong>  



											<a data-toggle="collapse" data-parent="#accordion4" href="#collapse{{$iSubCat2->slug}}_new4" class="collapsed" aria-expanded="true" aria-controls="collapseOne" title="Accessories" ></a>	



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}" style="width:85%;">



													20*{{ str_limit($iSubCat2->name, 100) }}<!-- we need this 0000002222-->



																					<?php  



            							 /* abdelhay comment 	$childIds = \App\Models\Category::whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])

					                                      //  ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          //->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');

														

$superid=$iSubCat2->id; 

$superid_ext=$iSubCat2->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);

	

	



                                          $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                   // ->orWhere('category_id',$iSubCat2->id);

																					->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                                                            <span class="count">({{ $postsCount or 0 }}) </span>



												</a>



											</strong>



										    <?php  $supersubcat = \DB::table('categories')

                                                                         ->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])

                                                                        

                                                                        ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                                        ->get(); ?>



                                               <div id="collapse{{$iSubCat2->slug}}_new4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingfive" >



                                                <div class="panel-body">



                                                    <ul class="list-unstyled">

                                                    

                                                   <?php //print_r($supersubcat);?>



                                                						<?php for($i=0;$i<sizeof($supersubcat);$i++){?>	



                                                						<?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

									

									$slugarr2=explode("?",$actual_link);

									$actual_link=$slugarr2[0];



                                                $slug=explode("/",$actual_link);



                                                ?>	



                                                <?php  


/* abdelhay comment
            								$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat[$i]->id,$supersubcat[$i]->translation_of])



					                                      ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)



                                                        ->pluck('id');



                                                        $superid=$supersubcat[$i]->id;

														$superid_ext=$supersubcat[$i]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);



                                            $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                  ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>













  <li>

  

  

  

  

  

  

  

  

  

  

  

  

 <!-- we need this eeeeeeeeeeee-->  

                                                                        

                                                                                       

                      

                       

                                    <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	



										

                                

                                

                                	<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$supersubcat[$i]->slug}}_x"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>



 <a href="https://www.tmmat.com/<?php print_r($slug[3]);?>/category/{{$iSubCat2->slug}}/<?php print_r($supersubcat[$i]->slug); ?>{{$tail}}" title="{{ $supersubcat[$i]->name }}" style="width:85%;">

 

									 

											18*	{{ str_limit($supersubcat[$i]->name, 100) }}



												<span class="count">({{ $postsCount or 0 }}) </span>



											</a>

                                      

                                            

                                            

                                

                                    <!---srart code--->

                          

                           <?php  $supersubcat_4 = \DB::table('categories')						   



                        ->whereIn('parent_id', [$supersubcat[$i]->id,$supersubcat[$i]->translation_of])



                             ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)



                        ->get(); 

						

						

						?>



<div id="collapse{{$supersubcat[$i]->slug}}_x" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($z=0;$z<sizeof($supersubcat_4);$z++){?>	

                                        

                                        

                                   

                                    

                                    

                                    

										 

 

                                                <?php  

 /* abdelhay comment

        									$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat_4[$z]->id,$supersubcat_4[$z]->translation_of])



					                                      //  ->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          //->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');

														

    $superid=$supersubcat_4[$z]->id;

	$superid_ext=$supersubcat_4[$z]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);

	

                                           $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhere('category_id',$superid);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/

                                                      ?>



                       

                       

                       

                                    

                                            <!-- icommented this becouse redirect in same place but disign not good abdelhayooooooooooo-->

                             <?php /*?>     <li><a href="https://www.tmmat.com/category/{{$supersubcat_2[$x]->slug}}/<?php print_r($supersubcat_2[$x]->slug); ?>" title="Watches"><?php */?>

                             <li>

                             

        

                             <a href="https://www.tmmat.com/category/<?php print_r($supersubcat[$i]->slug); ?>/<?php print_r($supersubcat_4[$z]->slug); ?>{{$tail}}" title="{{$supersubcat_4[$z]->name}}">

        

                                <?php print_r($supersubcat_4[$z]->name);?> <span class="count">({{ $postsCount or 0 }})</span></a></li>

                                

                               

                                

                                

                                

        

                                <?php } ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      

                                

                                

                                

                                

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        

                                                                        </li>



                                                                        <?php } ?>



                                                                        </ul>



                                                                        </div>



                                                                        </div>			



        								@else

                                        

                                        	<?php  

       /* abdelhay comment

        									$childIds = \App\Models\Category::whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



					                                      // ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                            //->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');

														

														

$superid=$iSubCat2->id; 

$superid_ext=$iSubCat2->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);



                                     $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                   // ->orWhere('category_id',$iSubCat2->id);

																				   ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>

                                        

            										<a data-toggle="collapse" data-parent="#accordion5" href="#collapse{{$iSubCat2->slug}}_new" class="panel-collapse collapsed" aria-expanded="false" aria-controls="collapseOne" title="{{ $iSubCat2->name }}" ></a>	



            										<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}" style="width:85%;">



            											19*	{{ str_limit($iSubCat2->name, 100) }}<!-- we need this ###########-->



                                                            <span class="count">({{ $postsCount or 0 }}) </span>



            											</a>



            									<?php  $supersubcat_3 = \DB::table('categories')



                                                        ->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



                                                           ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->get(); ?>





                                                      <div id="collapse{{$iSubCat2->slug}}_new" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingfive" >



                                                            <div class="panel-body">



                                                                <ul class="list-unstyled">



                                                            						<?php for($x=0;$x<sizeof($supersubcat_3);$x++){?>	



                                                            						<?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

									

									$slugarr2=explode("?",$actual_link);

									$actual_link=$slugarr2[0];



                                                            $slug=explode("/",$actual_link);



                                                            ?>	





  <?php  
                              /* abdelhay comment 


            								$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat_3[$x]->id,$supersubcat_3[$x]->translation_of])

													     // ->where('translation_lang', '=', $supersubcat_3[$x]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          //  ->where('translation_lang', '=', $supersubcat_3[$x]->translation_lang)



                                                        ->pluck('id');



                                                        $superid=$supersubcat_3[$x]->id;

															$superid_ext=$supersubcat_3[$x]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);



                                          $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)

                                                                                   

																					->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>

                                                            

                                                            

                                                                   <li>

                                                                   

                                                                   

                                                                   

                                                                   

                                                <?php /*?>                   

                                                                   <a href="https://www.tmmat.com/<?php print_r($slug[3]);?>/category/{{$iSubCat2->slug}}/<?php print_r($supersubcat_3[$x]->slug); ?>" title="Watches">



                                        						<?php print_r($supersubcat_3[$x]->name);?><!-- we need this ppppppppppp--> <span class="count">({{ $postsCount or 0 }}) </span></a>

                                                                

                                                                

                                                                <?php */?>

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                            

                                                            

                                                            

                                                            

                                                                                  

                      

                       

                                    <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	



										

										

            								<?php /*?>     <li><a href="https://www.tmmat.com/category/{{$iCat->slug}}/<?php print_r($supersubcat[$i]->slug); ?>" title="Watches"><?php */?>	 

                                                         

                                

                                

                                

                                

                                	<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$supersubcat_3[$x]->slug}}_z"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>



  <a href="https://www.tmmat.com/category/{{$iSubCat2->slug}}/<?php print_r($supersubcat_3[$x]->slug); ?>{{$tail}}" title="{{ $supersubcat_3[$x]->name }}" style="width:85%;">

										<?php /*?>	<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}" title="{{ $supersubcat[$i]->name }}" style="width:85%;"><?php */?>



												20*{{ str_limit($supersubcat_3[$x]->name, 100) }}



												<span class="count">({{ $postsCount or 0 }}) </span>



											</a>

                                      

                                            

                                            

                                

                                    <!---srart code--->

                          

                           <?php  $supersubcat_4 = \DB::table('categories')



                        ->whereIn('parent_id', [$supersubcat_3[$x]->id,$supersubcat_3[$x]->translation_of])



                           ->where('translation_lang', '=', $supersubcat_3[$x]->translation_lang)



                        ->get(); 

						

						

						?>



<div id="collapse{{$supersubcat_3[$x]->slug}}_z" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($z=0;$z<sizeof($supersubcat_4);$z++){?>	

                                        

                                        

                                   

                                    

                                    

                                    

										 

 

                                                <?php  
 /* abdelhay comment


        									$childIds = \App\Models\Category::where('parent_id', '=', $supersubcat_4[$z]->id)



					                                     //  ->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                       //  ->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');

														

$superid=$supersubcat_4[$z]->id;

	$superid_ext=$supersubcat_4[$z]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);

	

                                           $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                       

                       

                       

                                    

                                            <!-- icommented this becouse redirect in same place but disign not good abdelhaymmmmmmm-->

                             <?php /*?>     <li><a href="https://www.tmmat.com/category/{{$supersubcat_2[$x]->slug}}/<?php print_r($supersubcat_2[$x]->slug); ?>" title="Watches"><?php */?>

                             <li>

                             

        

                             <a href="https://www.tmmat.com/category/<?php print_r($supersubcat_3[$x]->slug); ?>/<?php print_r($supersubcat_4[$z]->slug); ?>{{$tail}}" title="{{$supersubcat_4[$z]->name}}">

        

                                <?php print_r($supersubcat_4[$z]->name);?> <span class="count">({{ $postsCount or 0 }})</span></a></li>

                                

                               

                                

                                

                                

        

                                <?php } ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      

                                

                                

                                

                                

                                                            

                                                            

                                                            

                                                            

                                                            

                                                            

                                                            

                                                            

                                                            

                                                            

                                                            

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                

                                                                </li>



                                                                <?php } ?>



                                                                </ul>



                                                                </div>



                                                                </div>		



                                @endif



                                            </h4>

                                            </div>

                                        	</div>

                                        </div>



                                     </li>



                                    </ul>



                                    @endforeach



                                @endif



</div>



</div>



<?php } else {?>



<div id="collapse{{$iSubCat->slug}}_new1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" >



<div class="panel-body">



 @if ($cats->groupBy('parent_id')->has($iSubCat->id) and config('country.icode')=="en" )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->id) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



										<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										]; ?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))

									

											<strong>



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



													21*{{ str_limit($iSubCat2->name, 100) }}wwwwwwwwwwwwww



													<?php  

/* abdelhay comment

            								$childIds = \App\Models\Category::whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



					                                    //  ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          //  ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$iSubCat2) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhere('category_id',$iSubCat2->id);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                                                            <span class="count">({{ $postsCount or 0 }}) </span>



												</a>



											</strong>



										@else



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



												22* {{ str_limit($iSubCat2->name, 100) }}



                                                <?php  

 /* abdelhay comment

            								$childIds = \App\Models\Category::where('parent_id', '=', $iSubCat2->id)



					                                     //  ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                           //->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                           $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$iSubCat2) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhere('category_id',$iSubCat2->id);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                                                            <span class="count">({{ $postsCount or 0 }})uuuuuuuuuuuuuu </span>



											</a>



										@endif



									</li>



									</ul>



								@endforeach



                                @endif



							    @if ($cats->groupBy('parent_id')->has($iSubCat->translation_of) )



								@foreach ($cats->groupBy('parent_id')->get($iSubCat->translation_of) as $iSubCat2)



                                    <ul class="list-unstyled">



									<li>



									    <div class="panel-group" id="accordion4" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                                <div class="panel-heading" role="tab" id="headingfour">



<h4 class="panel-title">	



										<?php $attr = [



											'countryCode' => config('country.icode'),



											'catSlug'     => $cats->get($iSubCat2->parent_id)->slug,



											'subCatSlug'  => $iSubCat2->slug



										]; ?>



										@if ((isset($uriPathSubCatSlug) and $uriPathSubCatSlug == $iSubCat2->slug) or (Request::input('sc') == $iSubCat2->tid))



											<strong>



												<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}">



													23*{{ str_limit($iSubCat2->name, 100) }} aaaaaaaaaaaaaaaa



													<?php  



            								$childIds = \App\Models\Category::whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



					                                      // ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                           //->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                                        



                                            /* abdelhay comment$postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$iSubCat2) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhere('category_id',$iSubCat2->id);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                                                            <span class="count">({{ $postsCount or 0 }}) </span>



												</a>



											</strong>



										@else



										<a data-toggle="collapse" data-parent="#accordion4" href="#collapse{{$iSubCat2->slug}}_new3" class="collapsed" aria-expanded="false" aria-controls="collapseOne" title="Accessories" >	 



										</a>	



										<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}{{$tail}}" title="{{ $iSubCat2->name }}" style="width:85%;">



												24*{{ str_limit($iSubCat2->name, 100) }}<!-- we need this ccccccccccccccccc-->

<?php



$superid=$iSubCat2->id; 

$superid_ext=$iSubCat2->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);





                                                
/*abdelhay comment 


        									$childIds = \App\Models\Category::whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



					                                     //  ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          // ->where('translation_lang', '=', $iSubCat2->translation_lang)



                                                        ->pluck('id');



                                           $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)

																					 ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                                 //   ->orWhere('category_id',$iSubCat2->id);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                         <span class="count">({{ $postsCount or 0 }}) </span>



											</a>



											<?php  $supersubcat = \DB::table('categories')



                       // ->where('parent_id', '=', $iSubCat2->tid)

					   ->whereIn('parent_id', [$iSubCat2->id,$iSubCat2->translation_of])



                           ->where('translation_lang', '=', $iSubCat2->translation_lang)



                        ->get(); ?>



                      <div id="collapse{{$iSubCat2->slug}}_new3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne" >



                          <div class="panel-body">



                            <ul class="list-unstyled">



						<?php for($i=0;$i<sizeof($supersubcat);$i++){?>	



					   	<?php $actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

									

									$slugarr2=explode("?",$actual_link);

									$actual_link=$slugarr2[0];



                        $slug=explode("/",$actual_link);?>	



                           <li>

                           

                        

                        

										 

 

                                                <?php  

 /* abdelhay comment

        									$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat[$i]->id,$supersubcat[$i]->translation_of])



					                                     //  ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                          // ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)



                                                        ->pluck('id');

														

$superid=$supersubcat[$i]->id;



	$superid_ext=$supersubcat[$i]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);

                                           $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhereIn('category_id',[$superid,$superid_ext]);

																					



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                       



										 



                        

                         

                        

                        

                        

                        

                                              

                      

                       

                                    <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="true">



                                            <div class="panel panel-default">



                                            <div class="panel-heading" role="tab" id="headingfive">



                                            <h4 class="panel-title">	



										

										

            								<?php /*?>     <li><a href="https://www.tmmat.com/category/{{$iCat->slug}}/<?php print_r($supersubcat[$i]->slug); ?>" title="Watches"><?php */?>	 

                                                         

                                

                                

                                

                                

                                	<a data-toggle="collapse" data-parent="#accordion2" href="#collapse{{$supersubcat[$i]->slug}}_t"  class="panel-collapse collapsed"  aria-expanded="false" aria-controls="collapseOne" title="Accessories" ></a>



    <?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $iSubCat2->slug]; ?>



											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}/<?php print_r($supersubcat[$i]->slug); ?>{{$tail}}" title="{{ $supersubcat[$i]->name }}" style="width:85%;">

                                            

<?php /*?>  <a href="https://www.tmmat.com/category/{{$iSubCat2->slug}}/<?php print_r($supersubcat[$i]->slug); ?>" title="{{ $supersubcat[$i]->name }}" style="width:85%;">

											<a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}" title="{{ $supersubcat[$i]->name }}" style="width:85%;"><?php */?>



												25*{{ str_limit($supersubcat[$i]->name, 100) }}



												<span class="count">({{ $postsCount or 0 }}) </span>



											</a>

                                      

                                            

                                            

                                

                                    <!---srart code--->

                          

                           <?php  $supersubcat_4 = \DB::table('categories')



                        ->whereIn('parent_id', [$supersubcat[$i]->id,$supersubcat[$i]->translation_of])



                           ->where('translation_lang', '=', $supersubcat[$i]->translation_lang)



                        ->get(); 

						

						

						?>



<div id="collapse{{$supersubcat[$i]->slug}}_t" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingthree" >



        <div class="panel-body">

             

                                   <ul class="list-unstyled">

        

                                <?php for($z=0;$z<sizeof($supersubcat_4);$z++){?>	

                                        

                                        

                                   

                                    

                                    

                                    

										 

 

                                                <?php  

/* abdelhay comment

        									$childIds = \App\Models\Category::whereIn('parent_id', [$supersubcat_4[$z]->id,$supersubcat_4[$z]->translation_of])



					                                       //->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');



                                            $subchildIds = \App\Models\Category::whereIn('parent_id', $childIds)



                                                        //   ->where('translation_lang', '=', $supersubcat_4[$z]->translation_lang)



                                                        ->pluck('id');

														

$superid=$supersubcat_4[$z]->id; 

$superid_ext=$supersubcat_4[$z]->translation_of;



//->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {

	//->orWhereIn('category_id',[$superid,$superid_ext]);



                                           $postsCount = \App\Models\Post::where('country_code',config('country.icode'))



                                                                            ->where(function ($query) use ($subchildIds,$childIds,$superid,$superid_ext) {



                                                                               $query->whereIn('category_id',$childIds)



                                                                                     ->orWhereIn('category_id',$subchildIds)



                                                                                    ->orWhereIn('category_id',[$superid,$superid_ext]);



                                                                           })->where('reviewed',1)->where('archived',0)->count();*/



            												?>



                       

                       

                       

                                    

                                            <!-- icommented this becouse redirect in same place but disign not good abdelhaykkkkkkkk-->

                             <?php /*?>     <li><a href="https://www.tmmat.com/category/{{$supersubcat_2[$x]->slug}}/<?php print_r($supersubcat_2[$x]->slug); ?>" title="Watches"><?php */?>

                             <li>

                             

        

        

            <?php $attr = ['countryCode' => config('country.icode'), 'catSlug' => $supersubcat[$i]->slug]; ?>

            <a href="{{ lurl(trans('routes.v-search-subCat', $attr), $attr) }}/<?php print_r($supersubcat_4[$z]->slug); ?>{{$tail}}" title="{{ $supersubcat_4[$z]->name }}" style="width:85%;">

        

        

                          <?php /*?>   <a href="https://www.tmmat.com/category/<?php print_r($supersubcat[$i]->slug); ?>/<?php print_r($supersubcat_4[$z]->slug); ?>" title="{{$supersubcat_4[$z]->name}}">

        <?php */?>

                                <?php print_r($supersubcat_4[$z]->name);?> <span class="count">({{ $postsCount or 0 }})</span></a></li>

                                

                               

                                

                                

                                

        

                                <?php } ?>

        

                                </ul>

        

        </div>



</div>	

                        

                     

                     

                                                        

                      

  <!---end code--->



  

                        </h4>

                        </div>

                       </div>

                      </div>

                      

                                

                                

                                

                                

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        

                        </li>



                        <?php } ?>



                        </ul>



                        </div>



                        </div>



										@endif



								</div>



						</div>



									</li>



									</ul>



								@endforeach



                                @endif



</div>



</div>



<?php }



}?>



</div></div></li>



								@endforeach



                                @endif



							</ul>



						</div>

                        </div>

                        

                        

                        

                        

                        

                         </h4>

                                            </div>

                                            </div>

                                            </div>

                                            

                                            

                        </li>



	 			

                         <?php }//end single search 1 level ?>  

    

    

    

    

    	</ul>

                        <!-- Date -->



            <div class="list-filter">



                <h5 class="list-title"><strong><a href="#"> {{ t('Date Posted') }} </a></strong></h5>



                <div class="filter-date filter-content">



                    <ul>



                        @if (isset($dates) and !empty($dates))



                            @foreach($dates as $key => $value)



                                <li>



                                    <input type="radio" name="postedDate" value="{{ $key }}" id="postedDate_{{ $key }}" {{ (Request::get('postedDate')==$key) ? 'checked="checked"' : '' }}>



                                    <label for="postedDate_{{ $key }}">{{ $value }}</label>



                                </li>



                            @endforeach



                        @endif



                        <input type="hidden" id="postedQueryString" value="{{ httpBuildQuery(Request::except(['postedDate'])) }}">



                    </ul>



                </div>



            </div>

				</div>



                @if (!in_array($cat->type, ['non-salable']))



                <!-- Price -->



                <div class="locations-list list-filter">



                    <h5 class="list-title"><strong><a href="#">{{ (!in_array($cat->type, ['job-offer', 'job-search'])) ? t('Price range') : t('Salary range') }}</a></strong></h5>



                    <form role="form" class="form-inline" action="{{ $fullUrlNoParams }}" method="GET">



						{!! csrf_field() !!}



                        @foreach(Request::except(['minPrice', 'maxPrice', '_token']) as $key => $value)



                            @if (is_array($value))



                                @foreach($value as $k => $v)



									@if (is_array($v))



										@foreach($v as $ik => $iv)



											@continue(is_array($iv))



											<input type="hidden" name="{{ $key.'['.$k.']['.$ik.']' }}" value="{{ $iv }}">



										@endforeach



									@else



                                    	<input type="hidden" name="{{ $key.'['.$k.']' }}" value="{{ $v }}">



									@endif



                                @endforeach



                            @else



                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">



                            @endif



                        @endforeach



                        <div class="form-group col-sm-4 no-padding">



                            <input type="text" placeholder="0" id="minPrice" name="minPrice" class="form-control" value="{{ Request::get('minPrice') }}">



                        </div>



                        <div style="margin-top: 10px;" class="form-group col-sm-1 no-padding text-center hidden-xs to-size-arabic"> - </div>



                        <div  class="form-group col-sm-4 no-padding">



                            <input type="text" placeholder="" id="maxPrice" name="maxPrice" class="form-control" value="{{ Request::get('maxPrice') }}">



                        </div>



                        <div class="form-group col-sm-3 no-padding">



                            <button class="btn btn-default pull-right btn-block-xs" type="submit">{{ t('GO') }}</button>



                        </div>



                    </form>



                    <div style="clear:both"></div>



                </div>



                @endif



				<?php $style = 'style="display: none;"'; ?>



			@else



			<div class="locations-list list-filter">



                    <h5 class="list-title"><strong><a href="#">{{ t('Price range') }}</a></strong></h5>



                    <form role="form" class="form-inline" action="{{ $fullUrlNoParams }}" method="GET">



						<input name="_token" value="{{ csrf_token() }}" type="hidden">



                       <div class="form-group col-sm-4 no-padding">



                            <input placeholder="0" id="minPrice" name="minPrice" class="form-control" value="{{ Request::get('minPrice') }}" type="text">



                        </div>



                        <div style="margin-top: 10px;" class="form-group col-sm-1 no-padding text-center hidden-xs to-size-arabic"> - </div>



                        <div class="form-group col-sm-4 no-padding">



                            <input placeholder="" id="maxPrice" name="maxPrice" class="form-control" value="{{ Request::get('maxPrice') }}" type="text">



                        </div>



                        <div class="form-group col-sm-3 no-padding">



                            <button class="btn btn-default pull-right btn-block-xs" type="submit">{{ t('GO') }}</button>



                        </div>



                    </form>



                    <div style="clear:both"></div>



                </div>



			@endif



            @include('search.inc.fields')



             <div class="locations-list list-filter">



                    <h5 class="list-title"><strong><a href="#">{{t('Distance within')}}</a></strong></h5>



                            	<select id="orderBySideBar" class="selecter" data-style="btn-select" >



								    <?php



								    $distance_array = array("15", "75", "150","300","750");

									$curr_lnk='';

									$curr_lnk_all='';

 

                                  // echo "xxxx".$xxx=Request::get('distance');

								    ?>



									@if (isset($isCitySearch) and $isCitySearch and \App\Helpers\DBTool::checkIfMySQLFunctionExists(config('larapen.core.distanceCalculationFormula')))



										@for($iDist = 0; $iDist <= config('settings.listing.search_distance_max', 500); $iDist += config('settings.listing.search_distance_interval', 50))



										<?php



										if (in_array($iDist, $distance_array))



										{

											

											$curr_lnk= qsurl($fullUrlNoParams, array_merge(Request::except('distance'), ['distance' => $iDist]));

											$curr_lnk=str_replace("page=","",$curr_lnk);



										?>



											<option{{ (Request::get('distance', config('settings.listing.search_distance_default', 50))==$iDist) ? ' selected="selected"' : '' }}



                                            <?php if($requestGetDistance==300 && $iDist==300) {?>  selected="selected"<?php }?>

                                                

                                                	value="{!! $curr_lnk !!}">



												{{ t('Around :distance :unit', ['distance' => $iDist, 'unit' => unitOfLength()]) }}  



                                               



											</option>



										<?php 



										}



										?>	



										@endfor



										<option{{ ((Request::get('distance', config('settings.listing.search_distance_default', 50))+5)==$iDist) ? ' selected="selected"' : '' }}



										<?php if(empty($requestGetDistance)) {?>  selected="selected"<?php }?>

                                        

                                       <?php 

									        $curr_lnk_all= qsurl($fullUrlNoParams, array_merge(Request::except('distance'), ['distance' => 50000]));

											$curr_lnk_all=str_replace("page=","",$curr_lnk_all); ?>

                                         

                                        	value="{!! $curr_lnk_all !!}">



											{{ t('All Ads') }}    

                                            



										</option>



									@else

<?php /*?>

										@for($iDist = 0; $iDist <= config('settings.listing.search_distance_max', 500); $iDist += config('settings.listing.search_distance_interval', 50))



										<?php



										if (in_array($iDist, $distance_array))



										{



										?>



											<option{{ (Request::get('distance', config('settings.listing.search_distance_default', 50))==$iDist) ? ' selected="selected"' : '' }}



													value="{!! qsurl($fullUrlNoParams, array_merge(Request::except('distance'), ['distance' => $iDist])) !!}">



												{{ t('Around :distance :unit', ['distance' => $iDist, 'unit' => unitOfLength()]) }} 



											</option>



									<?php



									    }



									?>



										@endfor



										<option {{ (Request::get('distance', config('settings.listing.search_distance_default', 50))==$iDist) ? ' selected="selected"' : '' }}



											@if($iDist==50000)  selected="selected" @endif

                                            

                                            

                                            value="{!! qsurl($fullUrlNoParams, array_merge(Request::except('distance'), ['distance' => 50000])) !!}">



											{{ t('All Ads') }}



										</option>

<?php */?>

								

                                

                                	@for($iDist = 0; $iDist <= config('settings.listing.search_distance_max', 500); $iDist += config('settings.listing.search_distance_interval', 50))



										<?php



										if (in_array($iDist, $distance_array))



										{



										?>



											<option{{ (Request::get('distance', config('settings.listing.search_distance_default', 50))==$iDist) ? ' selected="selected"' : '' }}





                                            <?php if($requestGetDistance==300 && $iDist==300) {?>  selected="selected"<?php }?>

                                           



													value="{!! qsurl($fullUrlNoParams, array_merge(Request::except('distance'), ['distance' => $iDist])) !!}">



												{{ t('Around :distance :unit', ['distance' => $iDist, 'unit' => unitOfLength()]) }} 

                                                

                                              



											</option>



										<?php 



										}



										?>	



										@endfor



										<option{{ ((Request::get('distance', config('settings.listing.search_distance_default', 50))+5)==$iDist) ? ' selected="selected"' : '' }}



										<?php if(empty($requestGetDistance)) {?>  selected="selected"<?php }?>

                                        

                                        

                                         

                                         

                                        	value="{!! qsurl($fullUrlNoParams, array_merge(Request::except('distance'), ['distance' => 50000])) !!}">



											{{ t('All Ads') }}   

                                            



										</option>



                                	@endif



								</select>

                                

                                <?php 

								//echo $iDist; 

								//Print_r(Request::get('distance'));

							//	echo (Request::get('distance', config('settings.listing.search_distance_default', 50))==$iDist);

								

								?>



                    <div style="clear:both"></div>



                </div>



            <!-- City -->



			<div class="locations-list list-filter" style="display:none;">



				<h5 class="list-title"><strong><a href="#">{{ t('Locations') }}</a></strong></h5>



				<ul class="browse-list list-unstyled long-list">



                    @if (isset($cities) and $cities->count() > 0)



						@foreach ($cities as $city)



							<?php



								$attr = ['countryCode' => config('country.icode')];



								$fullUrlLocation = lurl(trans('routes.v-search', $attr), $attr);



								$locationParams = [



									'l'  => $city->id,



									'r'  => '',



									'c'  => (isset($cat)) ? $cat->tid : '',



									'sc' => (isset($subCat)) ? $subCat->tid : '',



								];



							?>



							<li>



								@if ((isset($uriPathCityId) and $uriPathCityId == $city->id) or (Request::input('l')==$city->id))



									<strong>



										<a href="{!! qsurl($fullUrlLocation, array_merge(Request::except(array_keys($locationParams)), $locationParams)) !!}" title="{{ $city->name }}">



											{{ $city->name }}



										</a>



									</strong>



								@else



									<a href="{!! qsurl($fullUrlLocation, array_merge(Request::except(array_keys($locationParams)), $locationParams)) !!}" title="{{ $city->name }}">



										{{ $city->name }}



									</a>



								@endif



							</li>



						@endforeach



                    @endif



				</ul>



			</div>



			<div style="clear:both"></div>



		</div>



	</aside>



</div>



<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script>



<script id="rendered-js">



      $(document).ready(function () {



  $(".toggle-accordion").on("click", function () {



    var accordionId = $(this).attr("accordion-id"),



    numPanelOpen = $(accordionId + ' .collapse.in').length;



//alert(numPanelOpen);



    $(this).toggleClass("active");



    if (numPanelOpen == 0) {



      openAllPanels(accordionId);



    } else {



      closeAllPanels(accordionId);



    }



  });



  openAllPanels = function (aId) {



    console.log("setAllPanelOpen");



    $(aId + ' .panel-collapse:not(".in")').collapse('show');



  };



  closeAllPanels = function (aId) {



    console.log("setAllPanelclose");



    $(aId + ' .panel-collapse.in').collapse('hide');



  };



});



      //# sourceURL=pen.js



    </script>



@section('after_scripts')



    @parent



	<script>



        var baseUrl = '{{ $fullUrlNoParams }}';



        $(document).ready(function ()



        {



            $('input[type=radio][name=postedDate]').click(function() {



                var postedQueryString = $('#postedQueryString').val();



                if (postedQueryString != '') {



                    postedQueryString = postedQueryString + '&';



                }



                postedQueryString = postedQueryString + 'postedDate=' + $(this).val();



                var searchUrl = baseUrl + '?' + postedQueryString;



				redirect(searchUrl);



            });



        });



    </script>



@endsection