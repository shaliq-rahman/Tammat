<?php
/**

/home/dealnotd/public_html/app/Http/Controllers/Ajax/CategoryController.php
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

namespace App\Http\Controllers\Ajax;
use App\Http\Controllers\Post\Traits\CustomFieldTrait;
use App\Models\Category;
use App\Http\Controllers\FrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DB;

class CategoryController extends FrontController
{
	use CustomFieldTrait;
	
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getSubCategories(Request $request)
	{
	    
	    if(isset($_GET['kncategory'])){
	        $url=explode("/",$_GET['url']);
	        $lanurl=$url[3];
	        if($lanurl!="posts"){
	            $lang=$lanurl;
	            
	        } else {
    	  	    $lang='en';
	        }
	        
    	  	$entries1 = Category::transIn($lang)->where('id', $_GET['kncategory'])->orderBy('lft')->get();
    	  	
            if (!empty($entries1)) {
                $data2 = [];
				foreach ($entries1 as $Entrie) {
					$data2['CAT'][] =$Entrie;
					$subEntries2 = Category::transIn($lang)->where('parent_id', $Entrie->parent_id)->orderBy('lft')->get();
					
					if (!empty($subEntries2)) {
        			    
        		        foreach ($subEntries2 as $subEntrie2) {
        		            $data2['tab2'][]= $subEntrie2;
        		        
        				}
        				
        			}
        	
        		$entries2 = Category::transIn($lang)->where('translation_of', $Entrie->parent_id)->orderBy('lft')->get();
        		
					if (!empty($entries2)) {
        			    
        		        foreach ($entries2 as $Entrie2) {
        		            $data2['CAT1'][]= $Entrie2;
        		            $subEntries1 = Category::transIn($lang)->where('parent_id', $Entrie2->parent_id)->orderBy('lft')->get();
        					//print_r($subEntries1);exit();
        					if (!empty($subEntries1)) {
                			    
                		        foreach ($subEntries1 as $subEntrie1) {
                		            $data2['tab1'][]= $subEntrie1;
                		        
                				}
                				
                			}
                $entries3 = Category::transIn($lang)->where('translation_of', $Entrie2->parent_id)->orderBy('lft')->get();
					if (!empty($entries3)) {
        			    
        		        foreach ($entries3 as $Entrie3) {
        		            
        		            $data2['CAT2'][]= $Entrie3;
        		            $subEntries = Category::transIn($lang)->where('parent_id', $Entrie3->parent_id)->orderBy('lft')->get();
        					if (!empty($subEntries)) {
                			    
                		        foreach ($subEntries as $subEntrie) {
                		            $data2['tab3'][]= $subEntrie;
                		        
                				}
                				
                			}
                		
                	$entries4 = Category::transIn($lang)->where('translation_of', $Entrie3->parent_id)->orderBy('lft')->get();
                	
					if (!empty($entries4)) {
        			    
        		        foreach ($entries4 as $Entrie4) {
        		            $data2['CAT3'][]= $Entrie4;
        		            $subEntries4 = Category::transIn($lang)->where('parent_id', $Entrie4->parent_id)->orderBy('lft')->get();
        					if (!empty($subEntries4)) {
                			    
                		        foreach ($subEntries4 as $subEntrie4) {
                		            $data2['tab4'][]= $subEntrie4;
                		        
                				}
                				
                			}
                			
                	$entries5 = Category::transIn($lang)->where('translation_of', $Entrie4->parent_id)->orderBy('lft')->get();
                	
					if (!empty($entries5)) {
        			    
        		        foreach ($entries5 as $Entrie5) {
        		            $data2['CAT4'][]= $Entrie5;
        		            $subEntries5 = Category::transIn($lang)->where('parent_id', 0)->orderBy('lft')->get();
        					if (!empty($subEntries5)) {
                			    
                		        foreach ($subEntries5 as $subEntrie5) {
                		            $data2['tab5'][]= $subEntrie5;
                		        
                				}
                				
                			}
        		        }
					}
                		
                			
        		        }
        		        
					} 
                		
        		        }
					} 	
        				}
        				
        			} 
				}
            } 
	        
           
            if(isset($data2['CAT3'])){ $totcat3=count($data2['CAT3']); } else { $totcat3=0;}
            if(isset($data2['CAT2'])){ $totcat2=count($data2['CAT2']); } else { $totcat2=0;}
            if(isset($data2['CAT1'])){ $totcat1=count($data2['CAT1']); } else { $totcat1=0;}
            if(isset($data2['CAT'])){ $totcat=count($data2['CAT']); } else { $totcat=0;}
            if(isset($data2['CAT4'])){ $totcat4=count($data2['CAT4']); } else { $totcat4=0;}
		$data2['size']=$totcat4+$totcat3+$totcat2+$totcat1+$totcat;
	
    		echo json_encode($data2);exit();
	    }
	    
	    
	  $entries = Category::trans()->where('parent_id', 0)->orderBy('lft')->get();
	
		if ($entries->count() <= 0) {
			return [];
		}
		
		$tab = [];
		foreach ($entries as $entry) {
			$tab[$entry->tid] = $entry->name;
		}	
		if(isset($_GET['cat'])){
		    $url=explode("/",$_GET['url']);
	        $lanurl=$url[3];
	        if($lanurl!="posts"){
	            $lang=$lanurl;
	            $trans = Category::transIn($lang)->where('id', $_GET['cat'])->orderBy('lft')->get();
	            foreach ($trans as $tran) {
					$data['lang'][]= $tran;
	            $subEntries = Category::transIn($lang)->where('parent_id', $tran->translation_of)->orderBy('lft')->get();
	            }
	           
	        } else {
    	  	    $lang='en';
		        $subEntries = Category::transIn($lang)->where('parent_id', $_GET['cat'])->orderBy('lft')->get();
		  }
			if (!empty($subEntries)) {
			    $data = [];
				foreach ($subEntries as $subEntrie) {
					$data['tab1'][]= $subEntrie;
				
				}
			}
			
		echo json_encode($data);exit();
		}
		if(isset($_GET['subcat'])){
		    $url=explode("/",$_GET['url']);
	        $lanurl=$url[3];
	        if($lanurl!="posts"){
	            $lang=$lanurl;
	            $trans = Category::transIn($lang)->where('id', $_GET['subcat'])->orderBy('lft')->get();
	            foreach ($trans as $tran) {
					$data['lang'][]= $tran;
	            $subEntries2 = Category::transIn($lang)->where('parent_id', $tran->translation_of)->orderBy('lft')->get();
	            }
	           
	        } else {
    	  	    $lang='en';
		       $subEntries2 = Category::transIn($lang)->where('parent_id', $_GET['subcat'])->orderBy('lft')->get();
		  }
		   
		   
			if (!empty($subEntries2)) {
			    $data2 = [];
		        foreach ($subEntries2 as $subEntrie2) {
		            $data2['tab2'][]= $subEntrie2;
		        
				}
			}
		
		echo json_encode($data2);exit();
		}
		if(isset($_GET['subcat2'])){
		    
		    $url=explode("/",$_GET['url']);
	        $lanurl=$url[3];
	        if($lanurl!="posts"){
	            $lang=$lanurl;
	            $trans = Category::transIn($lang)->where('id', $_GET['subcat2'])->orderBy('lft')->get();
	            foreach ($trans as $tran) {
					$data['lang'][]= $tran;
	            $subEntries3 = Category::transIn($lang)->where('parent_id', $tran->translation_of)->orderBy('lft')->get();
	            }
	           
    	        } else {
        	  	    $lang='en';
    		      $subEntries3 = Category::transIn($lang)->where('parent_id', $_GET['subcat2'])->orderBy('lft')->get();
    		  }
		   
		   
			if (!empty($subEntries3)) {
			    $data3 = [];
		        foreach ($subEntries3 as $subEntrie3) {
		            $data3['tab3'][]= $subEntrie3;
		       
				}
			}
		
		echo json_encode($data3);exit();
		}
		if(isset($_GET['subcat3'])){
		    
			$url=explode("/",$_GET['url']);
	        $lanurl=$url[3];
	        if($lanurl!="posts"){
	            $lang=$lanurl;
	            $trans = Category::transIn($lang)->where('id', $_GET['subcat3'])->orderBy('lft')->get();
				
				
	            foreach ($trans as $tran) {
					$data['lang'][]= $tran;
	            $subEntries4 = Category::transIn($lang)->where('parent_id', $tran->translation_of)->orderBy('lft')->get();
	           
			    }
	           
    	        } else {
        	  	    $lang='en';
    		      $subEntries4 = Category::transIn($lang)->where('parent_id', $_GET['subcat3'])->orderBy('lft')->get();
    		  }
		//  dd($subEntries4);
		   
			if (!empty($subEntries4)) {
			    $data4 = [];
		        foreach ($subEntries4 as $subEntrie4) {
		            $data4['tab4'][]= $subEntrie4;
		       
				}
			}
		
		echo json_encode($data4);exit();
		}
	  
	  
	  
	  
	  
	  
		$languageCode = $request->input('languageCode');
		$parentId = $request->input('catId');
		$selectedSubCatId = $request->input('selectedSubCatId');
		//dd($request);
		// Get SubCategories by Parent Category ID
		$cacheId = 'subCategories.parentId.' . $parentId . '.' . $languageCode;
		$subCats = Cache::remember($cacheId, $this->cacheExpiration, function () use ($languageCode, $parentId) {
			$subCats = Category::transIn($languageCode)->where('parent_id', $parentId)->orderBy('lft')->get();
			
			return $subCats;
		});
		
		// Select the Parent Category if his haven't any SubCategories
		if ($subCats->count() <= 0) {
			$cacheId = 'subCategories.translationOf.' . $parentId . '.' . $languageCode;
			$subCats = Cache::remember($cacheId, $this->cacheExpiration, function () use ($languageCode, $parentId) {
				$subCats = Category::transIn($languageCode)->where('translation_of', $parentId)->orderBy('lft')->get();
				
				return $subCats;
			});
		}
		
		// If SubCategories are not found, Get all the Parent Categories
		if ($subCats->count() <= 0) {
			$cacheId = 'subCategories.parentId.0.' . $languageCode;
			$subCats = Cache::remember($cacheId, $this->cacheExpiration, function () use ($languageCode, $parentId) {
				$subCats = Category::transIn($languageCode)->where('parent_id', 0)->orderBy('lft')->get();
				
				return $subCats;
			});
		}
		
		// If SubCategories are still not found, Show an error message
		if ($subCats->count() <= 0) {
			return response()->json(['error' => ['message' => t("Error. Please select another category.")], 404]);
		}
		
		// Get Result's Data
		$data = [
			'subCats'          => $subCats,
			'countSubCats'     => $subCats->count(),
			'selectedSubCatId' => $selectedSubCatId,
		];
		
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	
	public function getSubCategories_app(Request $request)
	{
	   
		$languageCode = $request->input('languageCode');
		$parentId = $request->input('catId');
		$selectedSubCatId = $request->input('selectedSubCatId');
		//dd($request);
		// Get SubCategories by Parent Category ID
		$cacheId = 'subCategories.parentId.' . $parentId . '.' . $languageCode;
		$subCats = Cache::remember($cacheId, $this->cacheExpiration, function () use ($languageCode, $parentId) {
			$subCats = Category::transIn($languageCode)->where('parent_id', $parentId)->orderBy('lft')->get();
			
			return $subCats;
		});
		
		// Select the Parent Category if his haven't any SubCategories
		if ($subCats->count() <= 0) {
			$cacheId = 'subCategories.translationOf.' . $parentId . '.' . $languageCode;
			$subCats = Cache::remember($cacheId, $this->cacheExpiration, function () use ($languageCode, $parentId) {
				$subCats = Category::transIn($languageCode)->where('translation_of', $parentId)->orderBy('lft')->get();
				
				return $subCats;
			});
		}
		
		// If SubCategories are not found, Get all the Parent Categories
		if ($subCats->count() <= 0) {
			$cacheId = 'subCategories.parentId.0.' . $languageCode;
			$subCats = Cache::remember($cacheId, $this->cacheExpiration, function () use ($languageCode, $parentId) {
				$subCats = Category::transIn($languageCode)->where('parent_id', 0)->orderBy('lft')->get();
				
				return $subCats;
			});
		}
		
		// If SubCategories are still not found, Show an error message
		if ($subCats->count() <= 0) {
			return response()->json(['error' => ['message' => t("Error. Please select another category.")], 404]);
		}
		
		
		
		
		
		
		
		foreach($subCats as $key => $category):
    		
			$categories1 = DB::select('select * from categories where id = :id', ['id' => $category->translation_of]);

		
		$category->slug = $categories1[0]->slug;	
			
		endforeach;
		
		
		
		
		
		// Get Result's Data
		$data = [
			'subCats'          => $subCats,
			'countSubCats'     => $subCats->count(),
			'selectedSubCatId' => $selectedSubCatId,
		];
		
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	 
	
	public function getCustomFields_app(Request $request)
	{
	   
		$languageCode = $request->input('languageCode');
		
		app()->setLocale($languageCode);
		$parentCatId = $request->input('catId');
		$catId = $request->input('subCatId');
		$postId = $request->input('postId');
		$level3 = $request->input('level3');
		$level4 = $request->input('level4');
		
		// Custom Fields vars
		$errors   = stripslashes($request->input('errors'));
		$errors   = collect(json_decode($errors, true));
		$oldInput = stripslashes($request->input('oldInput'));
		$oldInput = json_decode($oldInput, true);
		$arr = array();
		// Get Category nested IDs
		$catNestedIds = (object)[
			'parentId'     => $parentCatId,
			'id'           => $catId,
			'level3'       => $level3,
			'level4'       => $level4,
		];
		
		// Get the Category's Custom Fields buffer
	 $customFields1 = $this->getCategoryFieldsBufferApp($catNestedIds, $languageCode, $errors, $oldInput, $postId);
	   //$fields = CategoryField::getFields($catNestedIds, $postId, $languageCode);
	   
		foreach($customFields1 as $field){
		    $arr[] =  $field;

		}

 
		
		
		$catNestedIds2 = (object)[
			'parentId'     => $catId,
			'id'           => $catId,
			 
		];
		$customFields2 = $this->getCategoryFieldsBufferApp($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		foreach($customFields2 as $field){
		    $arr[] =  $field;

		}



		$catNestedIds3 = (object)[
			'parentId'     => $level3,
			'id'           => $level3,
		 
		];
		$customFields3 = $this->getCategoryFieldsBufferApp($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
		foreach($customFields3 as $field){
		    $arr[] =  $field;

		}



		$catNestedIds4 = (object)[
			'parentId'     => $level4,
			'id'           => $level4,
		 
		];
		$customFields4 = $this->getCategoryFieldsBufferApp($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
		foreach($customFields4 as $field){
		    $arr[] =  $field;
		}


		$data = [
			'customFields' => $arr,
		];
		
		


		$_GET['catid']= $request->input('catId');
		$_GET['catid2']= $request->input('subCatId');
		$postId = $request->input('postId');
		$_GET['catid3']= $request->input('level3');
		$_GET['catid4']= $request->input('level4');
		$_GET['catid']= $request->input('level4');
		
	//	$customFields1="";$customFields2="";$customFields3="";$customFields4="";$customFields5="";
			
			$sql = Category::transIn($languageCode)->where('id', $_GET['catid4'])->orderBy('lft')->get(); 
			foreach($sql as $parent){
			  $catId1=$parent->translation_of;
			  $catNestedIds1 = (object)[
					  'parentId' =>$catId1,
					  'id'       =>$catId1,
				  ];
				  $customFields1 = $this->getCategoryFieldsBufferApp($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
				 
		  }
		  
		  
			 $data = [
				'customFields_org' => $customFields1,
				'customFields_org2' => $customFields2,
				'customFields_org3' => $customFields3,
				'customFields_org4' => $customFields4,
				'customFields' => $arr,
				'xxx' => "22222",
				 
				
			];
		  
		  








		
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
	}
	

	 
	public function getCustomFieldsAPP(Request $request)
	{
		 
		$languageCode = $request->input('languageCode');
		$parentCatId = $request->input('catId');
		$catId = $request->input('subCatId');
		$postId = $request->input('postId');
		$level3 = $request->input('level3');
		$level4 = $request->input('level4');
if(!empty($parentCatId)){$actual_link = 'https://www.tmmat.com/en/posts/create_step3/'.$parentCatId;}
if(!empty($catId)){$actual_link = 'https://www.tmmat.com/en/posts/create_step3/'.$catId;}
if(!empty($level3)){$actual_link = 'https://www.tmmat.com/en/posts/create_step3/'.$level3;}
if(!empty($level4)){$actual_link = 'https://www.tmmat.com/en/posts/create_step3/'.$level4;}
		 
		
		// Custom Fields vars
		$errors = stripslashes($request->input('errors'));
		$errors = collect(json_decode($errors, true));
		$oldInput = stripslashes($request->input('oldInput'));
		$oldInput = json_decode($oldInput, true);
		
		
		//catid="+$catid+'&catid1='+$catid1+'&catid2='+$catid2+'&catid3='+$catid3+'&catid4='+$catid4+'&postid='+postId+'&url='+$url
		   if(isset($_GET['postid'])){ $postId=$_GET['postid'];}
		    if(isset($_GET['posttype'])){ $posttype=$_GET['posttype'];}else{$posttype="";}
			
			
		// Get Category nested IDs
		$catNestedIds = (object)[
			'parentId' => $parentCatId,
			'id'       => $catId,
		];
		// Get the Category's Custom Fields buffer
		$customFields = $this->getCategoryFieldsBufferApp($catNestedIds, $languageCode, $errors, $oldInput, $postId);
		 
        //print_r($customFields);
		// Get Result's Data
		$data = [
			'customFields' => $customFields,
			'xxxx' => "111111",
			'xxxx' => $postId,
			
		];
		if(!empty($postId) && empty($posttype)){
		//2-7-2020 commented by abdelhay becouse this will return only one level (first catogry) 
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
		}


	    $languageCode = $request->input('languageCode');
	    if(isset($_GET['catid'])){
	        $catId=$_GET['catid'];
	        
	    } else {
			//$actual_link = 'https://www.tmmat.com/en/posts/create_step3/43359';
	          // $actual_link = $_SERVER["HTTP_HOST"] . $_SERVER["HTTP_REFERER"];
	          $link=explode("/",$actual_link);
	          if($languageCode=='en'){
	              $catId=$link[5];
	          } else {
	              $catId=$link[6];
	          }
	          if($catId=='edit'){
	       $catId = $request->input('subCatId');
	          }
	       //$catId = $request->input('catId');
	    }

		$test1=0;$test2=0;$test3=0;
		$parentCatId = $request->input('catId');
		$postId = $request->input('postId');
	
		// Custom Fields vars
		$errors = stripslashes($request->input('errors'));
		$errors = collect(json_decode($errors, true));
		$oldInput = stripslashes($request->input('oldInput'));
		$oldInput = json_decode($oldInput, true);
		
		if($languageCode==""){
	        if(isset($_GET['url'])){
    	       $lang=$_GET['url'];
    	       $langcode=explode("/",$lang);
    	       if($langcode[3]!="posts"){
    	           $languageCode=$langcode[3];
    	       } else {
    	           $languageCode="en";
    	       }
	        } 
	    }
	 
	 // $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	//  $actual_link = 'https://www.tmmat.com/en/posts/create_step3/43359';
	       
	 $link=explode("/",$actual_link);
	
	 
	    
		 
	    // $actual_link = $_SERVER["HTTP_HOST"] . $_SERVER["HTTP_REFERER"];
	     $link=explode("/",$actual_link);
	     if($link[3]!="posts"){
			 
			 $test2=$link[3];
			 $test3=$link[6];
	         $catid=$link[6];
			 
	        $sql1 = Category::transIn($languageCode)->where('translation_of', $catid)->orderBy('lft')->get();
	        foreach($sql1 as $parent1){
				
	            if($parent1->parent_id!=0){
					
					
	                $catId1=$parent1->translation_of;
	                $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBufferApp($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
		         $sql2 = Category::transIn($languageCode)->where('translation_of', $parent1->parent_id)->orderBy('lft')->get();
		         foreach($sql2 as $parent2){
					 
					 
		             if($parent2->parent_id!=0){
		                 $catId2=$parent2->translation_of;
		                 $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBufferApp($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		        $sql3 = Category::transIn($languageCode)->where('translation_of', $parent2->parent_id)->orderBy('lft')->get();
		        foreach($sql3 as $parent3){
					
		            if($parent3->parent_id!=0){
		                $catId3=$parent3->translation_of;
		                 $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBufferApp($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
		        $sql4 = Category::transIn($languageCode)->where('translation_of', $parent3->parent_id)->orderBy('lft')->get();
				
		        foreach($sql4 as $parent4){
					
		            if($parent4->parent_id!=0){	
						$catId4=$parent4->translation_of;
		                 $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBufferApp($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
		           $sql5 = Category::transIn($languageCode)->where('translation_of', $parent4->parent_id)->orderBy('lft')->get();
		        foreach($sql5 as $parent5){
					
		            if($parent5->parent_id!=0){
						$catId5=$parent5->translation_of;
		                 $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBufferApp($catNestedIds5, $languageCode, $errors, $oldInput, $postId);
		           
				   
				   
				    } else {
		              $catId5=$parent5->translation_of;
		                 $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBufferApp($catNestedIds5, $languageCode, $errors, $oldInput, $postId);  
		            }
		        }  
				   
				   
				   
				   
				    } else {
		              $catId4=$parent4->translation_of;
		                 $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBufferApp($catNestedIds4, $languageCode, $errors, $oldInput, $postId);  
		            }
		        }
		            } else {
		            $catId3=$parent3->translation_of;
		                 $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBufferApp($catNestedIds3, $languageCode, $errors, $oldInput, $postId);    
		            }
		        }
		             } else {
		                 $catId2=$parent2->translation_of;
		                 $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBufferApp($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		             }
		         } 
				
				} else {
	                $catId1=$parent1->translation_of;
	                $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBufferApp($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
	            }
	        }
	     }
	    

		    	$catNestedIds = (object)[
			'parentId' => $catId,
			'id'       => $catId,
		];

		// Get the Category's Custom Fields buffer
	
		$customFields = $this->getCategoryFieldsBufferApp($catNestedIds, $languageCode, $errors, $oldInput, $postId);
      
		// Get Result's Data
		if(!isset($customFields1)){ $customFields1=array(); }
		if(!isset($customFields2)){ $customFields2=array(); }
		if(!isset($customFields3)){ $customFields3=array(); }
		if(!isset($customFields4)){ $customFields4=array(); }
		if(!isset($customFields5)){ $customFields5=array(); }
		
		
		/*$arr = array();
		//print_r($customFields1);
		//print_r($customFields2);
		//print_r($customFields3);
		// print_r($customFields4);
		//print_r($customFields5);
		foreach($customFields1 as $field){ $arr[] =  $field;}
		foreach($customFields2 as $field){ $arr[] =  $field;}
		foreach($customFields3 as $field){ $arr[] =  $field;}
		foreach($customFields4 as $field){ $arr[] =  $field;}
		foreach($customFields5 as $field){ $arr[] =  $field;}

		$data = [
			'customFields' => $arr,
		];*/
// $all_rec = array_merge($customFields1,$customFields2,$customFields3,$customFields4,$customFields5);	
$newcst=array();
foreach($customFields3 as $cst){
	$newcst[]=$cst;

}	
		$data = [
			//'customFields' => $customFields1.$customFields2.$customFields3.$customFields4.$customFields5,
			'customFields_org1' => $customFields1,
			'customFields_org2' => $customFields2,
			'customFields_org3' => $newcst,
			'customFields_org4' => $customFields4,
			'customFields_org5' => $customFields5,
			'languageCode' => $languageCode,
			 
		 
			
		]; 
		
			 
		  
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
		    
	 
		 
		 
	}
	 





	 
	public function getCustomFieldsNewAPP(Request $request)
	{
		 
		$languageCode = $request->input('languageCode');
		$catId = $request->input('last_category_id');
		$postId = $request->input('postId');
		 
	 
 
	
		// Custom Fields vars
		$errors = stripslashes($request->input('errors'));
		$errors = collect(json_decode($errors, true));
		$oldInput = stripslashes($request->input('oldInput'));
		$oldInput = json_decode($oldInput, true);
		
	 
	 
	    
		  
			 
	        $sql1 = Category::transIn($languageCode)->where('translation_of', $catId)->orderBy('lft')->get();
	        foreach($sql1 as $parent1){
				
	            if($parent1->parent_id!=0){
					
					
	                $catId1=$parent1->translation_of;
	                $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBufferApp($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
		         $sql2 = Category::transIn($languageCode)->where('translation_of', $parent1->parent_id)->orderBy('lft')->get();
		         foreach($sql2 as $parent2){
					 
					 
		             if($parent2->parent_id!=0){
		                 $catId2=$parent2->translation_of;
		                 $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBufferApp($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		        $sql3 = Category::transIn($languageCode)->where('translation_of', $parent2->parent_id)->orderBy('lft')->get();
		        foreach($sql3 as $parent3){
					
		            if($parent3->parent_id!=0){
		                $catId3=$parent3->translation_of;
		                 $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBufferApp($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
		        $sql4 = Category::transIn($languageCode)->where('translation_of', $parent3->parent_id)->orderBy('lft')->get();
				
		        foreach($sql4 as $parent4){
					
		            if($parent4->parent_id!=0){	
						$catId4=$parent4->translation_of;
		                 $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBufferApp($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
		           $sql5 = Category::transIn($languageCode)->where('translation_of', $parent4->parent_id)->orderBy('lft')->get();
		        foreach($sql5 as $parent5){
					
		            if($parent5->parent_id!=0){
						$catId5=$parent5->translation_of;
		                 $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBufferApp($catNestedIds5, $languageCode, $errors, $oldInput, $postId);
		           
				   
				   
				    } else {
		              $catId5=$parent5->translation_of;
		                 $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBufferApp($catNestedIds5, $languageCode, $errors, $oldInput, $postId);  
		            }
		        }  
				   
				   
				   
				   
				    } else {
		              $catId4=$parent4->translation_of;
		                 $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBufferApp($catNestedIds4, $languageCode, $errors, $oldInput, $postId);  
		            }
		        }
		            } else {
		            $catId3=$parent3->translation_of;
		                 $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBufferApp($catNestedIds3, $languageCode, $errors, $oldInput, $postId);    
		            }
		        }
		             } else {
		                 $catId2=$parent2->translation_of;
		                 $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBufferApp($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		             }
		         } 
				
				} else {
	                $catId1=$parent1->translation_of;
	                $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBufferApp($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
	            }
	        }
	     
	    

		    	$catNestedIds = (object)[
			'parentId' => $catId,
			'id'       => $catId,
		];

		 
      
		// Get Result's Data
		if(!isset($customFields1)){ $customFields1=array(); }
		if(!isset($customFields2)){ $customFields2=array(); }
		if(!isset($customFields3)){ $customFields3=array(); }
		if(!isset($customFields4)){ $customFields4=array(); }
		if(!isset($customFields5)){ $customFields5=array(); }
		
		$customFields3=array();
		foreach($customFields3 as $cst){
			$customFields3[]=$cst;
		
		}
		  $arr = array();
		//print_r($customFields1);
		//print_r($customFields2);
		//print_r($customFields3);
		// print_r($customFields4);
		//print_r($customFields5);
		foreach($customFields1 as $field){ $arr[] =  $field;}
		foreach($customFields2 as $field){ $arr[] =  $field;}
		foreach($customFields3 as $field){ $arr[] =  $field;}
		foreach($customFields4 as $field){ $arr[] =  $field;}
		foreach($customFields5 as $field){ $arr[] =  $field;}
		
		/*$data = [
			'customFields' => $arr,
		];	
		
        $all_rec = array_merge($customFields1,$customFields2,$customFields3,$customFields4,$customFields5);	
	     */
		 
		$data = [
			//'customFields' => $customFields1.$customFields2.$customFields3.$customFields4.$customFields5,
			//'customFields_org1' => $customFields1,
			//'customFields_org2' => $customFields2,
			//'customFields_org3' => $newcst,
			//'customFields_org4' => $customFields4,
			//'customFields_org5' => $customFields5,
			//'languageCode' => $languageCode,
			'customFields' =>  $arr,
			 
		 
			
		]; 
		
			 
		  
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
		    
	 
		 
		 
	}
	 


	 
	public function getCustomFields(Request $request)
	{
		/* HAMADA */
		$languageCode = $request->input('languageCode');
		$parentCatId = $request->input('catId');
		$catId = $request->input('subCatId');
		$postId = $request->input('postId');
		$level3 = $request->input('level3');
		$level4 = $request->input('level4');
		
		// Custom Fields vars
		$errors = stripslashes($request->input('errors'));
		$errors = collect(json_decode($errors, true));
		$oldInput = stripslashes($request->input('oldInput'));
		$oldInput = json_decode($oldInput, true);
		
		
		//catid="+$catid+'&catid1='+$catid1+'&catid2='+$catid2+'&catid3='+$catid3+'&catid4='+$catid4+'&postid='+postId+'&url='+$url
		   if(isset($_GET['postid'])){ $postId=$_GET['postid'];}
		    if(isset($_GET['posttype'])){ $posttype=$_GET['posttype'];}else{$posttype="";}
			
			
		// Get Category nested IDs
		$catNestedIds = (object)[
			'parentId' => $parentCatId,
			'id'       => $catId,
		];
		// Get the Category's Custom Fields buffer
		$customFields = $this->getCategoryFieldsBuffer($catNestedIds, $languageCode, $errors, $oldInput, $postId);
       // console.log($customFields);
		// Get Result's Data
		$data = [
			'customFields' => $customFields,
			'xxxx' => "111111",
			'xxxx' => $postId,
			
		];
		if(!empty($postId) && empty($posttype)){
		//2-7-2020 commented by abdelhay becouse this will return only one level (first catogry) 
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
		}


	    $languageCode = $request->input('languageCode');
	    if(isset($_GET['catid'])){
	        $catId=$_GET['catid'];
	        
	    } else {
	           $actual_link = $_SERVER["HTTP_HOST"] . $_SERVER["HTTP_REFERER"];
	          $link=explode("/",$actual_link);
	          if($languageCode=='en'){
	              $catId=$link[5];
	          } else {
	              $catId=$link[6];
	          }
	          if($catId=='edit'){
	       $catId = $request->input('subCatId');
	          }
	       //$catId = $request->input('catId');
	    }

		$test1=0;$test2=0;$test3=0;
		$parentCatId = $request->input('catId');
		$postId = $request->input('postId');
	
		// Custom Fields vars
		$errors = stripslashes($request->input('errors'));
		$errors = collect(json_decode($errors, true));
		$oldInput = stripslashes($request->input('oldInput'));
		$oldInput = json_decode($oldInput, true);
		
		if($languageCode==""){
	        if(isset($_GET['url'])){
    	       $lang=$_GET['url'];
    	       $langcode=explode("/",$lang);
    	       if($langcode[3]!="posts"){
    	           $languageCode=$langcode[3];
    	       } else {
    	           $languageCode="en";
    	       }
	        } 
	    }
	 
	  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	 $link=explode("/",$actual_link);
	
	 if(isset($_GET['url'])){
		 
		 $test3=3;
		 
		 
		 
	    if($languageCode=='en'){
			
		
			$_GET['catid2']=$level3;
			$_GET['catid3']=$level4;
			
			
			
	        if(isset($_GET['catid'])){
	    $catNestedIds1 = (object)[
			        'parentId' => $_GET['catid'],
			        'id'       => $_GET['catid'],
		        ];
		$customFields1 = $this->getCategoryFieldsBuffer($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
	        } else { $customFields1=""; }
	       
	        if(isset($_GET['catid1'])){
	    $catNestedIds2 = (object)[
			        'parentId' => $_GET['catid1'],
			        'id'       => $_GET['catid1'],
		        ];
		        $customFields2 = $this->getCategoryFieldsBuffer($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
	        } else { $customFields2=""; }
	        
	        if(isset($_GET['catid2'])){
	    $catNestedIds3 = (object)[
			        'parentId' => $_GET['catid2'],
			        'id'       => $_GET['catid2'],
		        ];
		        $customFields3 = $this->getCategoryFieldsBuffer($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
	        } else { $customFields3=""; }
	        
	       if(isset($_GET['catid3'])){
	    $catNestedIds4 = (object)[
			        'parentId' => $_GET['catid3'],
			        'id'       => $_GET['catid3'],
		        ];
		        $customFields4 = $this->getCategoryFieldsBuffer($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
	        } else { $customFields4=""; }  
		   
	    
		
		
		
		
		
		} else {
			
			
			
			
	       if(isset($_GET['catid'])){
	      $sql = Category::transIn($languageCode)->where('id', $_GET['catid'])->orderBy('lft')->get(); 
	      foreach($sql as $parent){
	        $catId1=$parent->translation_of;
	        $catNestedIds1 = (object)[
			        'parentId' =>$catId1,
			        'id'       =>$catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBuffer($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
		       
	    }
	       }
	       if(isset($_GET['catid1'])){
			   
	      $sql = Category::transIn($languageCode)->where('id', $_GET['catid1'])->orderBy('lft')->get(); 
	      foreach($sql as $parent){
	        $catId2=$parent->translation_of;
	        $catNestedIds2 = (object)[
			        'parentId' =>$catId2,
			        'id'       =>$catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBuffer($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		       
	    }
	       }
	       if(isset($_GET['catid2'])){
			   
	      $sql = Category::transIn($languageCode)->where('id', $_GET['catid2'])->orderBy('lft')->get(); 
	      foreach($sql as $parent){
	        $catId3=$parent->translation_of;
	        $catNestedIds3= (object)[
			        'parentId' =>$catId3,
			        'id'       =>$catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBuffer($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
		       
	    }
	       }
	        if(isset($_GET['catid3'])){
	      $sql = Category::transIn($languageCode)->where('id', $_GET['catid3'])->orderBy('lft')->get(); 
	      foreach($sql as $parent){
	        $catId4=$parent->translation_of;
	        $catNestedIds4= (object)[
			        'parentId' =>$catId4,
			        'id'       =>$catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBuffer($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
		       
	    }
	       }
	       if(isset($_GET['catid4'])){
	      $sql = Category::transIn($languageCode)->where('id', $_GET['catid4'])->orderBy('lft')->get(); 
	      foreach($sql as $parent){
	        $catId5=$parent->translation_of;
	        $catNestedIds5= (object)[
			        'parentId' =>$catId5,
			        'id'       =>$catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBuffer($catNestedIds5, $languageCode, $errors, $oldInput, $postId);
		       
	    }
	       }
	    
	    
		
		
		
		
		
		}
	    
	 } else {
	    
	     $actual_link = $_SERVER["HTTP_HOST"] . $_SERVER["HTTP_REFERER"];
	     $link=explode("/",$actual_link);
	     if($link[3]!="posts"){
			 
			 $test2=$link[3];
			 $test3=$link[6];
	         $catid=$link[6];
	        $sql1 = Category::transIn($languageCode)->where('id', $catid)->orderBy('lft')->get();
	        foreach($sql1 as $parent1){
				
	            if($parent1->parent_id!=0){
					
					
	                $catId1=$parent1->translation_of;
	                $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBuffer($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
		         $sql2 = Category::transIn($languageCode)->where('translation_of', $parent1->parent_id)->orderBy('lft')->get();
		         foreach($sql2 as $parent2){
					 
					 
		             if($parent2->parent_id!=0){
		                 $catId2=$parent2->translation_of;
		                 $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBuffer($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		        $sql3 = Category::transIn($languageCode)->where('translation_of', $parent2->parent_id)->orderBy('lft')->get();
		        foreach($sql3 as $parent3){
					
		            if($parent3->parent_id!=0){
		                $catId3=$parent3->translation_of;
		                 $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBuffer($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
		        $sql4 = Category::transIn($languageCode)->where('translation_of', $parent3->parent_id)->orderBy('lft')->get();
		        foreach($sql4 as $parent4){
					
		            if($parent4->parent_id!=0){
						
						
						
		                $catId4=$parent4->translation_of;
		                 $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBuffer($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
		           $sql5 = Category::transIn($languageCode)->where('translation_of', $parent4->parent_id)->orderBy('lft')->get();
		        foreach($sql5 as $parent5){
					
		            if($parent5->parent_id!=0){
						
						
						
		                $catId5=$parent5->translation_of;
		                 $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBuffer($catNestedIds5, $languageCode, $errors, $oldInput, $postId);
		           
				   
				   
				    } else {
		              $catId5=$parent5->translation_of;
		                 $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBuffer($catNestedIds5, $languageCode, $errors, $oldInput, $postId);  
		            }
		        }  
				   
				   
				   
				   
				    } else {
		              $catId4=$parent4->translation_of;
		                 $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBuffer($catNestedIds4, $languageCode, $errors, $oldInput, $postId);  
		            }
		        }
		            } else {
		            $catId3=$parent3->translation_of;
		                 $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBuffer($catNestedIds3, $languageCode, $errors, $oldInput, $postId);    
		            }
		        }
		             } else {
		                 $catId2=$parent2->translation_of;
		                 $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBuffer($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
		             }
		         }
		        
	            
				
				
				
				
				} else {
	                $catId1=$parent1->translation_of;
	                $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBuffer($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
	            }
	        }
	     }
	    else {
			
			$test1=1;
	    $sql1 = Category::trans()->where('id', $catId)->orderBy('lft')->get();
	    
	    foreach($sql1 as $parent1){
	        if($parent1->parent_id!=0){
	            $catId1=$parent1->id;
	            $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		       
			    $customFields1 = $this->getCategoryFieldsBuffer($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
		        
	            $sql2 = Category::trans()->where('id', $parent1->parent_id)->orderBy('lft')->get();
	            foreach($sql2 as $parent2){
	                if($parent2->parent_id!=0){
	                    $catId2=$parent2->id;
	                     $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
				
		        $customFields2 = $this->getCategoryFieldsBuffer($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
	                    $sql3 = Category::trans()->where('id', $parent2->parent_id)->orderBy('lft')->get();
	                    foreach($sql3 as $parent3){
	                        if($parent3->parent_id!=0){
	                            $catId3=$parent3->id;
	                            $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBuffer($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
	                            $sql4 = Category::trans()->where('id', $parent3->parent_id)->orderBy('lft')->get();
	                            foreach($sql4 as $parent4){
									
									if($parent4->parent_id!=0){
									
	                                $catId4=$parent4->id;
	                                $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBuffer($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
	                           
							   $sql5 = Category::trans()->where('id', $parent4->parent_id)->orderBy('lft')->get();
	                            foreach($sql5 as $parent5){
									
									 
									
	                                $catId5=$parent5->id;
	                                $catNestedIds5 = (object)[
			        'parentId' => $catId5,
			        'id'       => $catId5,
		        ];
		        $customFields5 = $this->getCategoryFieldsBuffer($catNestedIds5, $languageCode, $errors, $oldInput, $postId);
	                           
								}
							   
							   
									}else{
										
	                            $catId4=$parent4->id;
	                            $catNestedIds4 = (object)[
			        'parentId' => $catId4,
			        'id'       => $catId4,
		        ];
		        $customFields4 = $this->getCategoryFieldsBuffer($catNestedIds4, $languageCode, $errors, $oldInput, $postId);
	                        
										
										}
							   
							   
							    }
	                        } else {
	                            $catId3=$parent3->id;
	                            $catNestedIds3 = (object)[
			        'parentId' => $catId3,
			        'id'       => $catId3,
		        ];
		        $customFields3 = $this->getCategoryFieldsBuffer($catNestedIds3, $languageCode, $errors, $oldInput, $postId);
	                        }
	                    
						}
	                } else {
	                    $catId2=$parent2->id;
	                    $catNestedIds2 = (object)[
			        'parentId' => $catId2,
			        'id'       => $catId2,
		        ];
		        $customFields2 = $this->getCategoryFieldsBuffer($catNestedIds2, $languageCode, $errors, $oldInput, $postId);
	                }
	            }
	        } else {
	            $catId1=$parent1->id;
	             $catNestedIds1 = (object)[
			        'parentId' => $catId1,
			        'id'       => $catId1,
		        ];
		        $customFields1 = $this->getCategoryFieldsBuffer($catNestedIds1, $languageCode, $errors, $oldInput, $postId);
	        }
	    }
	    }
	   //print_r($customFields1); print_r($customFields2); print_r($customFields3); print_r($customFields4);exit();
	 }

		    	$catNestedIds = (object)[
			'parentId' => $catId,
			'id'       => $catId,
		];

		// Get the Category's Custom Fields buffer
	
		$customFields = $this->getCategoryFieldsBuffer($catNestedIds, $languageCode, $errors, $oldInput, $postId);
      
		// Get Result's Data
		if(!isset($customFields1)){ $customFields1=""; }
		if(!isset($customFields2)){ $customFields2=""; }
		if(!isset($customFields3)){ $customFields3=""; }
		if(!isset($customFields4)){ $customFields4=""; }
		if(!isset($customFields5)){ $customFields5=""; }
		$data = [
			'customFields' => $customFields1.$customFields2.$customFields3.$customFields4.$customFields5,
			'xxx' => "22222",
			'www' => $test1,
			'eee' => $test2,
			'ddd' => $test3,
			
		];
		
			/*$data = [
			'customFields' => $customFields1,
		];*/
	//	print_r($data);exit();
		if(isset($_GET['catid'])){
		   
		  print_r($data['customFields']);exit();
		} else {
		   if($data!=""){
		return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
		   }
		}
	}
	
	
// 	public function getrelocating(Request $request)
// 	{
// 	    $getdetail = \DB::table('posts')
// 	        ->where('id','=',$request->postid)
//           ->select('*')
//           ->first();
           
//           $json['from_city'] =  $getdetail->from_city;
//           $json['from_city_date'] =  $getdetail->from_city_date;
//           $json['where_city'] =  $getdetail->where_city;
//           $json['where_city_date'] =  $getdetail->where_city_date;
//           $json['from_country'] =  $getdetail->from_country;
//           $json['where_country'] =  $getdetail->where_country;
           
           
//           echo json_encode($json);
	        
// 	}
	
}
