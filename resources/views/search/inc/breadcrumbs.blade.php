
<?php
if(isset($cat->parent_id)){
if($cat->parent_id!=0){
$categoryname = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $cat->parent_id)
                        ->first();
if($categoryname->parent_id!=0){
   $catnamedee = \DB::table('categories')
                        // ->where('user_id', '=', $post->user_id)
                        ->where('id', '=', $categoryname->parent_id)
                        ->first(); 
                       
}

}
//print_r($categoryname);
}
 
?>

<div class="container">
	<div class="breadcrumbs">
		<ol class="breadcrumb pull-left">
			<li><a href="{{ lurl('/') }}"><i class="icon-home fa"></i></a></li>
			<li>
				<?php $attr = ['countryCode' => config('country.icode')]; ?>
				<a href="{{ lurl(trans('routes.v-search', $attr), $attr) }}">
					{{ config('country.name') }}
				</a>
			</li>
			@if(isset($catnamedee))
			<li><a href="{{ lurl('/') }}/posts/subcats/{{$cat->parent_id}}">{{ t($catnamedee->name) }}</a></li>
			@endif
			@if(isset($categoryname->id))
	        <li>

	            <a href="{{ lurl('/') }}/posts/subcats/{{$cat->parent_id}}">{{ t($categoryname->name) }}</a>
	   
	        </li>
	    @endif
			@if (isset($bcTab) and count($bcTab) > 0)
				@foreach($bcTab as $key => $value)
					<?php $value = collect($value); ?>
					@if ($value->has('position') and $value->get('position') > count($bcTab)+1)
						<li class="active">
							{!! $value->get('name') !!}
							&nbsp;
							{{-- @if (isset($city) or isset($admin))
								<a href="#browseAdminCities" id="dropdownMenu1" data-toggle="modal"> <span class="caret"></span> </a>
							@endif --}}
						</li>
					@else
					@if(isset($_GET['cat']))
						<li><a href="{{ $value->get('url') }}?cat={{$_GET['cat']}}">{!! $value->get('name') !!}</a></li>
					@else
					<li><a href="{{ $value->get('url') }}">{!! $value->get('name') !!}</a></li>
					@endif
					@endif
				@endforeach
			@endif
		</ol>
	</div>
</div>
