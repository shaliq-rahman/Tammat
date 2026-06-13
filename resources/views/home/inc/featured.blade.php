<?php

if (!isset($cacheExpiration)) {

    $cacheExpiration = (int)config('settings.other.cache_expiration');

}

?>



@if (isset($featured) and !empty($featured) and !empty($featured->posts))

	@include('home.inc.spacer')

	<div class="container">

    

    

  

    

		<div class="col-lg-12 content-box layout-section">

			<div class="row row-featured row-featured-category">

				<div class="col-lg-12 box-title">

					<div class="inner" style="text-align: center;">

						<h2> 

							<span class="title-3">

							    @if(Request::segment(1) != "")

							        {!! t('Similar Ads') !!}

							    @else

							        {!! t('Premium ad') !!}

							    @endif

							    <!--{!! $featured->title !!}-->

							    </span>

							<!--<a href="{{ $featured->link }}" class="sell-your-item">-->

							<!--	{{ t('View more') }} <i class="icon-th-list"></i>-->

							<!--</a>-->

						</h2>

					</div>

				</div>

		

				<div style="clear: both"></div>

					

                 

                    

                    

                    <div class="relative content featured-list-row clearfix">

                        <div class="large-12 columns">



                            <?php

							

							 

							foreach($featured->posts as $key => $value_post):

								if (empty($countries) or !$countries->has($post->country_code)) continue; 

                               

							 

                                // Get Pack Info

                                $package = null;

                                // if ($value_post->featured == 1) {

                                //     $cacheId = 'package.' . $value_post->package_id . '.' . config('app.locale');

                                //     $package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($value_post) {

                                //     $package = \App\Models\Package::findTrans($value_post->package_id);

                                //         return $package;

                                //     });

                                // }

                                $pictures = \App\Models\Picture::where('post_id', $value_post->id)->orderBy('position')->orderBy('id');

                                if ($pictures->count() > 0) {

                                    $postImg = resize($pictures->first()->filename, 'medium');

                                } else {

                                    $postImg = resize(config('larapen.core.picture.default'));

                                }

                                $cacheId = 'postType.' . $value_post->post_type_id . '.' . config('app.locale');

                                $postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($value_post) {

                                    $postType = \App\Models\PostType::findTrans($value_post->post_type_id);

                                    return $postType;

                                });

                                if (empty($postType)) continue;

                                // Get the Post's City

                                $cacheId = config('country.code') . '.city.' . $value_post->city_id;

                                $city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($value_post) {

                                    $city = \App\Models\City::find($value_post->city_id);

                                    return $city;

                                });

                                if (empty($city)) continue;

                                $value_post->created_at = \Date::parse($value_post->created_at)->timezone(config('timezone.id'));

                                $value_post->created_at = $value_post->created_at->ago();

                                $getcategorydata = \DB::table('categories')

                                    ->select('*')

                                    ->where('id', '=', $value_post->category_id)

                                    ->first();

                                if (!empty($getcategorydata->parent_id)) {

                                    $getcategorydataparent = \DB::table('categories')

                                        ->select('*')

                                        ->where('id', '=', $getcategorydata->parent_id)

                                        ->first();

                                if(!empty($getcategorydataparent->id)){

                                    $liveCatParentId = $getcategorydataparent->id;

                                    $liveCatName = $getcategorydataparent->name;
                                }

                                } else {

                                    $liveCatParentId = $getcategorydata->id;

                                    $liveCatName = $getcategorydata->name;

                                }?>

                                <div class="item-list make-grid" style="height: 319px;">

                                    <div class="col-sm-2 no-padding photobox">

                                        <div class="add-image">

                                            <span class="photo-count"><i

                                                        class="fa fa-camera"></i> {{ $pictures->count() }} </span>

                                            <?php $attr1 = ['slug' => slugify($value_post->title), 'id' => $value_post->id];

                                            $uri = trans('routes.v-post', ['slug' => slugify($value_post->title), 'id' => $value_post->id]);

                                            ?>

                                            @if(isset($_GET['cat']))

                                            <a href="{{ lurl($uri, $attr1) }}?cat={{$_GET['cat']}}">

                                            @else

                                            <a href="{{ lurl($uri, $attr1) }}">

                                             @endif   

                                                <img class="thumbnail no-margin" src="{{ $postImg }}" alt="img">

                                            </a>

                                        </div>

                                    </div>



                                    <div class="add-desc-box col-sm-7">

                                        <div class="add-details">

                                            <h5 class="add-title">



                                                <?php

                                                $uri = trans('routes.v-post', ['slug' => slugify($value_post->title), 'id' => $value_post->id]);

                                                $attr11 = ['slug' => slugify($value_post->title), 'id' => $value_post->id]; ?>

                                                @if(isset($_GET['cat']))

                                                <a href="{{ lurl($uri, $attr11) }}?cat={{$_GET['cat']}}">{{ \Illuminate\Support\Str::limit($value_post->title, 70) }} </a>

                                                @else

                                                <a href="{{ lurl($uri, $attr11) }}">{{ \Illuminate\Support\Str::limit($value_post->title, 70) }} </a>

                                                @endif

                                            </h5>



      <!--                                      <span class="info-row">-->

      <!--                  <span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right"-->

      <!--                        title="{{ $postType->name }}">-->

						<!--{{ strtoupper(mb_substr($postType->name, 0, 1)) }}-->

					 <!--   </span>&nbsp; -->

					      <?php $attr_user = ['countryCode' => config('country.icode'), 'id' =>  $post->user_id]; ?>

					    {{-- <div class="date" style="display: inline;"><i class="icon-user"> </i> 

					    @if(isset($_GET['cat']))

					    <a style="font-weight: normal;" href="{{ lurl(trans('routes.v-search-user', $attr_user), $attr_user) }}?cat={{$_GET['cat']}}">{{ $username }}</a>

					    @else

					    <a style="font-weight: normal;" href="{{ lurl(trans('routes.v-search-user', $attr_user), $attr_user) }}">{{ $username }}</a>

            		    @endif

            		    </div> --}}

            		    <div class="data_color date" style="margin-left: 0px;margin-top: -1px;"><i class="icon-clock"> </i> {{ $value_post->created_at }} </div>

                                                {{-- @if (isset($liveCatParentId) and isset($liveCatName))

                                                    <div class="category" style="margin-left: 3px;"> <i

                                           	                     class="fa fa-list-alt"></i>&nbsp;

							                        <a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c'=>$liveCatParentId])) !!}"

                                                       class="info-link">{{ $liveCatName }}</a>

						                            </div>

                                                @endif --}}

                                                <div  style="margin-left: 0px;" class="item-location">

													     <img style="margin-right: 1px;" src="{{ url('images/blank.gif') . getPictureVersion() }}"  class="flag flag-<?=strtolower($value_post->country_code)?>" >

						<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$value_post->city_id])) !!}"

                           class="info-link" style="color:#201c1c">{{ $city->name }}</a> {{ (isset($value_post->distance)) ? '- ' . round(lengthPrecision($value_post->distance), 2) . unitOfLength() : '' }}

					  </div>

                    </span>

                                        </div>

                                    </div>



                                    <div class="col-sm-3 text-right price-box">

                                        <h4 class="item-price">

                                            @if (isset($liveCatType) and !in_array($liveCatType, ['non-salable']))

                                                @if ($value_post->price > 0)

                                                    {!! \App\Helpers\Number::money($value_post->price) !!}

                                                @else

                                                    <!--{!! \App\Helpers\Number::money('--') !!}-->

                                                    {{t('0')}}

                                                @endif

                                            @else

                                                @if ($value_post->price > 0)

                                                    {!! \App\Helpers\Number::money($value_post->price) !!}

                                                @else

                                                    {{t('0')}}

                                                @endif

                                            @endif

                                        </h4>

                                        @if (isset($package) and !empty($package))

                                            @if ($package->has_badge == 1)

                                                <a class="btn btn-danger btn-sm make-favorite"><i

                                                            class="fa fa-certificate"></i><span> {{ $package->short_name }} </span></a>

                                                &nbsp;

                                            @endif

                                        @endif

                                        @if (auth()->check())

                                            <a class="btn btn-{{ (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $value_post->id)->count() > 0) ? 'success' : 'default' }} btn-sm make-favorite"

                                               id="{{ $post->id }}">

                                                <i class="fa fa-heart"></i><span> {{ t('Save') }} </span>

                                            </a>

                                        @else

                                            <a class="btn btn-default btn-sm make-favorite" id="{{ $value_post->id }}"><i

                                                        class="fa fa-heart"></i><span> {{ t('Save') }} </span></a>

                                        @endif

                                    </div>





                                </div>











                            @endforeach





                        </div>

                    </div>

                    

                    

                    

                      

                        

				

					</div>

		

				</div>

			</div>

		</div>

	</div>

@endif



@section('after_style')

	@parent

@endsection



@section('before_scripts')

	@parent

	<script>

		/* Carousel Parameters */

		var carouselItems = {{ (isset($featured) and isset($featured->posts)) ? collect($featured->posts)->count() : 0 }};

		var carouselAutoplay = {{ (isset($featuredOptions) && isset($featuredOptions['autoplay'])) ? $featuredOptions['autoplay'] : 'false' }};

		var carouselAutoplayTimeout = {{ (isset($featuredOptions) && isset($featuredOptions['autoplay_timeout'])) ? $featuredOptions['autoplay_timeout'] : 1500 }};

		var carouselLang = {

			'navText': {

				'prev': "{{ t('prev') }}",

				'next': "{{ t('next') }}"

			}

		};

	</script>

@endsection