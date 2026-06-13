<?php
if (!isset($cacheExpiration)) {
    $cacheExpiration = (int)config('settings.other.cache_expiration');
}


?>


@if (isset($paginator) and $paginator->getCollection()->count() > 0)
	<?php
		if (!isset($cats)) {
			$cats = collect([]);
		}


//dd($paginator);

		foreach($paginator->getCollection() as $key => $post):
		
		
		 if (empty($countries) or !$countries->has($post->country_code)) continue;
	
	
		
		// Get Pack Info
        $package = null;
        if ($post->featured==1) {
            $cacheId = 'package.' . $post->py_package_id . '.' . config('app.locale');
            $package = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
                $package = \App\Models\Package::findTrans($post->py_package_id);
                return $package;
            });
		}
	
		// Get PostType Info
		$cacheId = 'postType.' . $post->post_type_id . '.' . config('app.locale');
    	$postType = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
            $postType = \App\Models\PostType::findTrans($post->post_type_id);
			return $postType;
		});
		if (empty($postType)) continue;
		
		
		
		
  
		// Get Post's Pictures
		$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');
		if ($pictures->count() > 0) {
			$postImg = resize($pictures->first()->filename, 'medium');
		} else {
			$postImg = resize(config('larapen.core.picture.default'));
		}
  
		// Get the Post's City
		/*$cacheId = config('country.code') . '.city.' . $post->city_id;
    	$city = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
            $city = \App\Models\City::find($post->city_id);
			return $city;
		});
		if (empty($city)) continue;*/
		
	//	 print_r($post);
	
		// Convert the created_at date to Carbon object
		$post->created_at = \Date::parse($post->created_at)->timezone(config('timezone.id'));
		$post->created_at = $post->created_at->ago();
		
		// Category
		$cacheId = 'category.' . $post->category_id . '.' . config('app.locale');
		
		$qedama_cat = \App\Models\Category::find($post->category_id);
		
		
		$liveCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($post) {
			$liveCat = \App\Models\Category::find($post->category_id);
			return $liveCat;
		});
		
		// Check parent
		if (empty($liveCat->parent_id)) {
			$liveCatParentId = $liveCat->id;
			$liveCatType = $liveCat->type;
		} else {
			$liveCatParentId = $liveCat->parent_id;
			
			$cacheId = 'category.' . $liveCat->parent_id . '.' . config('app.locale');
			$liveParentCat = \Illuminate\Support\Facades\Cache::remember($cacheId, $cacheExpiration, function () use ($liveCat) {
				$liveParentCat = \App\Models\Category::find($liveCat->parent_id);
				return $liveParentCat;
			});
			$liveCatType = (!empty($liveParentCat)) ? $liveParentCat->type : 'classified';
		}
		
		// Check translation
		if ($cats->has($liveCatParentId)) {
			$liveCatName = $cats->get($liveCatParentId)->name;
		} else {
			$liveCatName = $liveCat->name;
		}
		
        $getusernamedetail = \DB::table('users')->where('id', '=', $post->user_id)->first();
        $username = $getusernamedetail->username;      
        
        
        $getcurrencycountry = \DB::table('countries')
                            ->join('currencies', 'currencies.code', '=', 'countries.currency_code')
                            ->select('currencies.*')
                            ->where('countries.code', '=', $post->country_code)
                            ->first();
           
        
       
        
        
	?>
	<div class="item-list">
        @if (isset($package) and !empty($package))
            @if ($package->ribbon != '')
                <div class="cornerRibbons {{ $package->ribbon }}">
					<a href="#"> {{ $package->short_name }}</a>
				</div>
            @endif
        @endif
		
		<div class="col-sm-2 no-padding photobox">
			<div class="add-image">
				<span class="photo-count"><i class="fa fa-camera"></i> {{ $pictures->count() }} </span>
				<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
				@if(isset($_GET['cat']))
				<a href="{{ lurl($post->uri, $attr) }}?cat={{$_GET['cat']}}">
				@else
				<a href="{{ lurl($post->uri, $attr) }}">
				@endif
					<img class="thumbnail no-margin" src="{{ $postImg }}" alt="img">
				</a>
			</div>
		</div>

		<div class="col-sm-9 add-desc-box">
			<div class="add-details">
				<h5 class="add-title">
					<?php $attr = ['slug' => slugify($post->title), 'id' => $post->id]; ?>
					@if(isset($_GET['cat']))
					    <a style="color:#ff5555" href="{{ lurl($post->uri, $attr) }}?cat={{$_GET['cat']}}">{{ \Illuminate\Support\Str::limit($post->title, 70) }} </a>
					@else
						<a style="color:#ff5555" href="{{ lurl($post->uri, $attr) }}">{{ \Illuminate\Support\Str::limit($post->title, 70) }} </a>
					@endif
				</h5>
				
				<span class="info-row">
					<!--<span class="add-type business-ads tooltipHere" data-toggle="tooltip" data-placement="right" title="{{ $postType->name }}">-->
					<!--	{{ strtoupper(mb_substr($postType->name, 0, 1)) }}-->
					<!--</span>&nbsp;-->
					
					 <?php $attr_user = ['countryCode' => config('country.icode'), 'id' => $post->user_id]; ?>
					 
                    {{-- <div class="date" style="display: inline;"><i class="icon-user"> </i> 
                    @if(isset($_GET['cat']))
                    <a style="font-weight: normal;" href="{{ lurl(trans('routes.v-search-user', $attr_user), $attr_user) }}?cat={{$_GET['cat']}}">{{ $username }}</a>
                    @else
                    <a style="font-weight: normal;" href="{{ lurl(trans('routes.v-search-user', $attr_user), $attr_user) }}">{{ $username }}</a>
                    @endif
                    </div> --}}
					<div class="date" style="margin-left: 0px;margin-top: -1px;"><i class="icon-clock"> </i> {{ $post->created_at }} </div>
					{{-- @if (isset($liveCatParentId) and isset($liveCatName))
						<div  style="margin-left: 3px;" class="category">
							<i class="fa fa-list-alt" ></i> &nbsp;<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c'=>$liveCatParentId])) !!}" class="info-link">{{ $liveCatName }}</a>
							
							<!--<i class="fa fa-list-alt" ></i> &nbsp;<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except('c'), ['c'=>$liveCatParentId])) !!}" class="info-link">{{ $qedama_cat->name }}</a>-->
						</div>
					@endif --}}
					 <div style="margin-left: 0px;"  class="item-location">
					     <img src="{{ url('images/blank.gif') . getPictureVersion() }}" style="margin-right: 2px;" class="flag flag-<?=strtolower($post->country_code)?>" >
						<a href="{!! qsurl(config('app.locale').'/'.trans('routes.v-search', ['countryCode' => config('country.icode')]), array_merge(Request::except(['l', 'location']), ['l'=>$post->city_id])) !!}" class="info-link" style="color:#201c1c;">{{ $post->city_name }}</a> {{ (isset($post->distance)) ? '- ' . round(lengthPrecision($post->distance), 2) . unitOfLength() : '' }}
					  </div>
				</span>
			</div>

			@if (isset($reviewsPlugin) and !empty($reviewsPlugin))
				@if (view()->exists('reviews::ratings-list'))
					@include('reviews::ratings-list')
				@endif
			@endif
			
		</div>

		<div class="col-sm-3 text-right price-box">
			<h4 class="item-price">
				@if (isset($liveCatType) and !in_array($liveCatType, ['non-salable']))
					@if ($post->price > 0)
						{!! \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator) !!}
					@else
                    {{t('Free')}}
					@endif
				@else
						{!! \App\Helpers\Number::money_price_latest($post->price,$getcurrencycountry->html_entity,$getcurrencycountry->in_left,$getcurrencycountry->decimal_places,$getcurrencycountry->decimal_separator) !!}
				@endif
			</h4>
            @if (isset($package) and !empty($package))
                @if ($package->has_badge == 1)
                    <a class="btn btn-danger btn-sm make-favorite"><i class="fa fa-certificate"></i><span> {{ $package->short_name }} </span></a>&nbsp;
                @endif
            @endif
			@if (auth()->check())
				<a class="btn btn-{{ (\App\Models\SavedPost::where('user_id', auth()->user()->id)->where('post_id', $post->id)->count() > 0) ? 'success' : 'default' }} btn-sm make-favorite"
				   id="{{ $post->id }}">
					<i class="fa fa-heart"></i><span> {{ t('Save') }} </span>
				</a>
			@else
				<a class="btn btn-default btn-sm make-favorite" id="{{ $post->id }}"><i class="fa fa-heart"></i><span> {{ t('Save') }} </span></a>
			@endif
		</div>
	</div>
	<?php endforeach; ?>
@else
	<div class="item-list" style="width: 100%; border-right: 0;">
		{{ t('No result. Refine your search using other criteria.') }}
	</div>
@endif

@section('after_scripts')
	@parent
	<script>
		/* Default view (See in /js/script.js) */
		@if($count->get('all') > 0)
			@if(config('settings.listing.display_mode') == '.grid-view')
				gridView('.grid-view');
			@elseif(config('settings.listing.display_mode') == '.list-view')
				listView('.list-view');
			@elseif(config('settings.listing.display_mode') == '.compact-view')
				compactView('.compact-view');
			@else
				gridView('.grid-view');
			@endif
		@else
			listView('.list-view');
		@endif
		/* Save the Search page display mode */
		var listingDisplayMode = readCookie('listing_display_mode');
		if (!listingDisplayMode) {
			createCookie('listing_display_mode', '{{ config('settings.listing.display_mode', '.grid-view') }}', 7);
		}
		
		/* Favorites Translation */
		var lang = {
			labelSavePostSave: "{!! t('Save ad') !!}",
			labelSavePostRemove: "{!! t('Remove favorite') !!}",
			loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
			loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
			confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
			confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
			confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
			confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
		};
	</script>
@endsection
