<?php
/**
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
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Search;

use Illuminate\Http\Request;
use App\Helpers\Search;
use App\Http\Controllers\Search\Traits\PreSearchTrait;
use App\Models\CategoryField;
use Torann\LaravelMetaTags\Facades\MetaTag;

class SearchappController extends BaseController
{
	use PreSearchTrait;
	
	public $isIndexSearch = true;
	
	protected $cat = null;
	protected $subCat = null;
	protected $city = null;
	protected $admin = null;
	
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
	$title = request()->get('q');
	
	if(strlen($title)>2)
	{
	$var = '%'.$title.'%';
	}
	else
	{
	$var = '%'.$title;
	}
	
	$posts = \DB::table('posts')
           		->select('*')
				->where([
					['title', 'like', '%'.$title.'%'],
					['country_code', '=', request()->get('country')],
				])
                ->get();
				
				$i=0;
		foreach($posts as $key => $post){
				
	// Get Post's Pictures
										$pictures = \App\Models\Picture::where('post_id', $post->id)->orderBy('position')->orderBy('id');
										if ($pictures->count() > 0) {
											$postImg = resize($pictures->first()->filename, 'medium');
										} else {
											$postImg = resize(config('larapen.core.picture.default'));
										}
										$post->postImg = $postImg;			
				
	$i++;
								}			
				
				
	
	return response()->json(['getdetail'=>$posts]);
	
	}
	
	
	public function index_app()
	{
	     
	     
	     $getdetail = \DB::table('countries')
           ->select('*')
           ->where('code','=', config('country.icode'))
           ->first();
	    
	    $capital = !empty($getdetail->capital)?$getdetail->capital:'';
	    
    	$city_query = \DB::table('cities')
	            ->select('*')
    	        ->where('country_code','=',config('country.icode'))
                ->where('name', 'like', '%'.$capital.'%')
                ->first();
	    
	    $locationid = !empty($city_query->id)?$city_query->id:'';
	    
	   // if(!empty($capital))
	   // {
    // 	    $capital = explode(' ',$capital);
    // 	    $capital = $capital[0];    
	   // }
	    
	    
	   // print_r(request()->all()); die;
	  
		//view()->share('isIndexSearch', $this->isIndexSearch);
		
		// Pre-Search
		if (request()->filled('c')) {
			if (request()->filled('sc')) {
				$this->getCategory(request()->get('c'), request()->get('sc'));
				
				// Get Category nested IDs
				$catNestedIds = (object)[
					'parentId' => request()->get('c'),
					'id'       => request()->get('sc'),
				];
			} else {
				$this->getCategory(request()->get('c'));
				
				// Get Category nested IDs
				$catNestedIds = (object)[
					'parentId' => 0,
					'id'       => request()->get('c'),
				];
			}
			
			// Get Custom Fields
			$customFields = CategoryField::getFields($catNestedIds);
			//view()->share('customFields', $customFields);
		}
		if (request()->filled('l') || request()->filled('location')) {
			$city = $this->getCity(request()->get('l'), request()->get('location'));
		}
		else
		{
		    $city = $this->getCity($locationid,$capital);
		}
		//Code made by MonTech Team
		if (request()->filled('location')) {
			$city = $this->getCityObj(request()->get('location'));
		}
		else
		{
	    	$city = $this->getCityObj($capital);
		}
		
		
		
		if (request()->filled('r') && !request()->filled('l')) {
			$admin = $this->getAdmin(request()->get('r'));
		}
		if (request()->filled('distance')) {
			$distance = request()->get('distance');
		}
		
		
		// Pre-Search values
		$preSearch = [
			'city'  => (isset($city) && !empty($city)) ? $city : null,
			'distance'  => (isset($distance) && !empty($distance)) ? $distance : null,
			'admin' => (isset($admin) && !empty($admin)) ? $admin : null,
		];
		//echo "<pre>";
		//print_r($preSearch); die;
		// Search
		$search = new Search($preSearch);
		$data = $search->fechAll();
		
		// Export Search Result
		//view()->share('count', $data['count']);
		//view()->share('paginator', $data['paginator']);
		
		// Get Titles
		/*$title = $this->getTitle();
		$this->getBreadcrumb();
		$this->getHtmlTitle();
		
		// Meta Tags
		MetaTag::set('title', $title);
		MetaTag::set('description', $title);
		
		return view('search.serp');*/
		return response()->json(['results'=>$preSearch,'customFields'=>$customFields]);
	}
	
}
