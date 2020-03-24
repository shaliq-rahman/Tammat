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

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\Traits\VerificationTrait;
use Larapen\Admin\app\Http\Controllers\PanelController;
use App\Models\PostType;
use App\Models\Post;
use App\Models\Category;
use App\Http\Requests\Admin\PostRequest as StoreRequest;
use App\Http\Requests\Admin\PostRequest as UpdateRequest;

class PostController extends PanelController
{
	use VerificationTrait;
	
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\Post');
		$this->xPanel->with(['pictures', 'user', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }]);
		$this->xPanel->setRoute(config('larapen.admin.route_prefix', 'admin') . '/posts');
		$this->xPanel->setEntityNameStrings(trans('admin::messages.ad'), trans('admin::messages.ads'));
		$this->xPanel->denyAccess(['create']);
		
		if (!request()->input('order')) {
			if (config('settings.single.posts_review_activation')) {
				$this->xPanel->orderBy('reviewed', 'ASC');
			}
			$this->xPanel->orderBy('created_at', 'DESC');
		}
		
		$this->xPanel->addButtonFromModelFunction('top', 'bulk_delete_btn', 'bulkDeleteBtn', 'end');
		
		// Hard Filters
		if (request()->filled('active')) {
			if (request()->get('active') == 0) {
				$this->xPanel->addClause('where', 'verified_email', '=', 0);
				$this->xPanel->addClause('orWhere', 'verified_phone', '=', 0);
				if (config('settings.single.posts_review_activation')) {
					$this->xPanel->addClause('orWhere', 'reviewed', '=', 0);
				}
			}
			if (request()->get('active') == 1) {
				$this->xPanel->addClause('where', 'verified_email', '=', 1);
				$this->xPanel->addClause('where', 'verified_phone', '=', 1);
				if (config('settings.single.posts_review_activation')) {
					$this->xPanel->addClause('where', 'reviewed', '=', 1);
				}
			}
		}
		
		// Filters
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'id',
			'type'  => 'text',
			'label' => 'ID',
		],
		false,
		function ($value) {
			$this->xPanel->addClause('where', 'id', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'from_to',
			'type'  => 'date_range',
			'label' => trans('admin::messages.Date range'),
		],
		false,
		function ($value) {
			$dates = json_decode($value);
			$this->xPanel->addClause('where', 'created_at', '>=', $dates->from);
			$this->xPanel->addClause('where', 'created_at', '<=', $dates->to);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'title',
			'type'  => 'text',
			'label' => trans('admin::messages.Title'),
		],
		false,
		function ($value) {
			$this->xPanel->addClause('where', 'title', 'LIKE', "%$value%");
		});
		
		// -----------------------
// 		$this->xPanel->addFilter([
// 			'name'  => 'city_name',
// 			'type'  => 'text',
// 			'label' => 'City Name',
// 		],
// 		false,
// 		function ($value) {
// 			$this->xPanel->addClause('where', 'city_name', 'LIKE', "%$value%");
// 		});
		
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'country',
			'type'  => 'select2',
			'label' => trans('admin::messages.Country'),
		],
		getCountries(),
		function ($value) {
			$this->xPanel->addClause('where', 'country_code', '=', $value);
		});
		// -----------------------
// 		$this->xPanel->addFilter([
// 			'name'  => 'city',
// 			'type'  => 'text',
// 			'label' => trans('admin::messages.City'),
// 		],
// 		false,
// 		function ($value) {
// 			$this->xPanel->query = $this->xPanel->query->whereHas('city', function ($query) use ($value) {
// 				$query->where('name', 'LIKE', "%$value%");
// 			});
// 		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'status',
			'type'  => 'dropdown',
			'label' => trans('admin::messages.Status'),
		], [
			1 => trans('admin::messages.Unactivated'),
			2 => trans('admin::messages.Activated'),
		], function ($value) {
			if ($value == 1) {
				$this->xPanel->addClause('where', 'verified_email', '=', 0);
				$this->xPanel->addClause('orWhere', 'verified_phone', '=', 0);
				if (config('settings.single.posts_review_activation')) {
					$this->xPanel->addClause('orWhere', 'reviewed', '=', 0);
				}
			}
			if ($value == 2) {
				$this->xPanel->addClause('where', 'verified_email', '=', 1);
				$this->xPanel->addClause('where', 'verified_phone', '=', 1);
				if (config('settings.single.posts_review_activation')) {
					$this->xPanel->addClause('where', 'reviewed', '=', 1);
				}
			}
		});
		
		
		$this->xPanel->addFilter([
			'name'  => 'archived',
			'type'  => 'dropdown',
			'label' => 'Archived Status',
		], [
			1 => 'no',
			2 => 'yes',
		], function ($value) {
			if ($value == 1) {
				$this->xPanel->addClause('where', 'archived', '=', 0);
			}
			if ($value == 2) {
				$this->xPanel->addClause('where', 'archived', '=', 1);
			}
		});
		
		
		
		
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->xPanel->addColumn([
			'name'  => 'id_name',
			'label' => '',
			'type'  => 'checkbox',
			// 'orderable' => true,
		]);
		
		
		$this->xPanel->addColumn([
			'name'  => 'id',
			'label' => 'ID',
			'type'          => 'model_function',
			'function_name' => 'getIDName',
			'orderable' => true,
	  	]);
		
		
// 		$this->xPanel->addColumn([
// 			'name'  => 'created_at',
// 			'label' => trans("admin::messages.Date"),
// 			'type'  => 'datetime',
// 		]);


		$this->xPanel->addColumn([
			'name'          => 'title',
			'label'         => trans("admin::messages.Title"),
			'type'          => 'model_function',
			'function_name' => 'getTitleHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'price', // Put unused field column
			'label'         => trans("admin::messages.Main Picture"),
			'type'          => 'model_function',
			'function_name' => 'getPictureHtml',
		]);
		$this->xPanel->addColumn([
			'name'  => 'contact_name',
			'label' => trans("admin::messages.User Name"),
		]);
		// $this->xPanel->addColumn([
		// 	'name'  => 'post_type_id',
		// 	'label' => trans("Post Type Id"),
		// 	'type'          => 'model_function',
		// 	'function_name' => 'getPostTypeName',
		// ]);
		$this->xPanel->addColumn([
			'name'          => 'archived',
			'label'         => 'Archived Status',
			'type'          => 'model_function',
			'function_name' => 'getArchivedHtml',
		]);
		 
// 		$this->xPanel->addColumn([
// 			'name'          => 'city_id',
// 			'label'         => trans("admin::messages.City"),
// 			'type'          => 'model_function',
// 			'function_name' => 'getCityHtml',
// 		]);
		$this->xPanel->addColumn([
			'name'          => 'country_code',
			'label'         => trans("admin::messages.Country"),
			'type'          => 'model_function',
			'function_name' => 'getCountryHtml',
		]);
// 		$this->xPanel->addColumn([
// 			'name'          => 'verified_email',
// 			'label'         => trans("admin::messages.Verified Email"),
// 			'type'          => 'model_function',
// 			'function_name' => 'getVerifiedEmailHtml',
// 		]);

	    $this->xPanel->addColumn([
			'name'          => 'archived',
			'label'         => 'Archived',
			'type'          => 'model_function',
			'function_name' => 'getArchivedHtmlAjax',
		]);
		
		
		if (config('settings.sms.phone_verification')) {
			$this->xPanel->addColumn([
				'name'          => 'verified_phone',
				'label'         => trans("admin::messages.Verified Phone"),
				'type'          => 'model_function',
				'function_name' => 'getVerifiedPhoneHtml',
			]);
		}
		if (config('settings.single.posts_review_activation')) {
			$this->xPanel->addColumn([
				'name'          => 'reviewed',
				'label'         => trans("admin::messages.Reviewed"),
				'type'          => 'model_function',
				'function_name' => 'getReviewedHtml',
			]);
		}
		
		
		// FIELDS
		$this->xPanel->addField([
			'label'       => trans("admin::messages.Category"),
			'name'        => 'category_id',
			'type'        => 'select2_from_array',
			'options'     => $this->categories(),
			'allows_null' => false,
		]);
		$this->xPanel->addField([
			'name'       => 'title',
			'label'      => trans("admin::messages.Title"),
			'type'       => 'text',
			'attributes' => [
				'placeholder' => trans("admin::messages.Title"),
			],
		]);
		$this->xPanel->addField([
			'name'       => 'description',
			'label'      => trans("admin::messages.Description"),
			'type'       => (config('settings.single.simditor_wysiwyg'))
				? 'simditor'
				: ((!config('settings.single.simditor_wysiwyg') && config('settings.single.ckeditor_wysiwyg')) ? 'ckeditor' : 'textarea'),
			'attributes' => [
				'placeholder' => trans("admin::messages.Description"),
				'id'          => 'description',
				'rows'        => 10,
			],
		]);
		$this->xPanel->addField([
			'name'              => 'price',
			'label'             => trans("admin::messages.Price"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans('admin::messages.Enter a Price (or Salary)'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'negotiable',
			'label'             => trans("admin::messages.Negotiable Price"),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'label'     => trans("admin::messages.Pictures"),
			'name'      => 'pictures', // Entity method
			'entity'    => 'pictures', // Entity method
			'attribute' => 'filename',
			'type'      => 'read_images',
			'disk'      => 'public',
		]);
		$this->xPanel->addField([
			'name'              => 'contact_name',
			'label'             => trans("admin::messages.User Name"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans("admin::messages.User Name"),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'email',
			'label'             => trans("admin::messages.User Email"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans("admin::messages.User Email"),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'phone',
			'label'             => trans("admin::messages.User Phone"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans('admin::messages.User Phone'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'phone_hidden',
			'label'             => trans("admin::messages.Hide seller phone"),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'label'             => trans("admin::messages.Post Type"),
			'name'              => 'post_type_id',
			'type'              => 'select2_from_array',
			'options'           => $this->postType(),
			'allows_null'       => false,
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'tags',
			'label'             => trans("admin::messages.Tags"),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans("admin::messages.Tags"),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'verified_email',
			'label'             => trans("admin::messages.Verified Email"),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'verified_phone',
			'label'             => trans("admin::messages.Verified Phone"),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		if (config('settings.single.posts_review_activation')) {
			$this->xPanel->addField([
				'name'              => 'reviewed',
				'label'             => trans("admin::messages.Reviewed"),
				'type'              => 'checkbox',
				'wrapperAttributes' => [
					'class' => 'form-group col-md-6',
					'style' => 'margin-top: 20px;',
				],
			]);
		}
		$this->xPanel->addField([
			'name'              => 'archived',
			'label'             => trans("admin::messages.Archived"),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'name'       => 'ip_addr',
			'label'      => "IP",
			'type'       => 'text',
			'attributes' => [
				'disabled' => true,
			],
		]);
	}
	
	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}
	
	public function update(UpdateRequest $request)
	{
	    $post = Post::find($request->id);
	    
	    $editval=$request->input();
	    if(isset($editval['category_id'])){
	        $post['category_id']=$editval['category_id'];
	    }
	    
	    $post->created_at = date('Y-m-d H:i:s');
	    $post->updated_at = date('Y-m-d H:i:s');
	    
	    $post->save();
	    
		return parent::updateCrud();
	}
	
	public function postType()
	{
		$entries = PostType::trans()->get();
		
		return $this->getTranslatedArray($entries);
	}
	
	public function categories()
	{
	    
	    if(isset($_GET['kncategory'])){
	        
    	  	$entries1 = Category::trans()->where('id', $_GET['kncategory'])->orderBy('lft')->get();
    	  	
            if (!empty($entries1)) {
                $data2 = [];
				foreach ($entries1 as $Entrie) {
					$data2['CAT'][] =$Entrie;
					$subEntries2 = Category::trans()->where('parent_id', $Entrie->parent_id)->orderBy('lft')->get();
					if (!empty($subEntries2)) {
        			    
        		        foreach ($subEntries2 as $subEntrie2) {
        		            $data2['tab2'][]= $subEntrie2;
        		        
        				}
        				
        			}
        		
        		$entries2 = Category::trans()->where('id', $Entrie->parent_id)->orderBy('lft')->get();
					if (!empty($entries2)) {
        			    
        		        foreach ($entries2 as $Entrie2) {
        		            $data2['CAT1'][]= $Entrie2;
        		            $subEntries1 = Category::trans()->where('parent_id', $Entrie2->parent_id)->orderBy('lft')->get();
        					if (!empty($subEntries1)) {
                			    
                		        foreach ($subEntries1 as $subEntrie1) {
                		            $data2['tab1'][]= $subEntrie1;
                		        
                				}
                				
                			}
                $entries3 = Category::trans()->where('id', $Entrie2->parent_id)->orderBy('lft')->get();
					if (!empty($entries3)) {
        			    
        		        foreach ($entries3 as $Entrie3) {
        		            
        		            $data2['CAT2'][]= $Entrie3;
        		            $subEntries = Category::trans()->where('parent_id', $Entrie3->parent_id)->orderBy('lft')->get();
        					if (!empty($subEntries)) {
                			    
                		        foreach ($subEntries as $subEntrie) {
                		            $data2['tab3'][]= $subEntrie;
                		        
                				}
                				
                			}
                		
                	$entries4 = Category::trans()->where('id', $Entrie3->parent_id)->orderBy('lft')->get();
                	
					if (!empty($entries4)) {
        			    
        		        foreach ($entries4 as $Entrie4) {
        		            $data2['CAT3'][]= $Entrie4;
        		            $subEntries4 = Category::trans()->where('parent_id', $Entrie4->parent_id)->orderBy('lft')->get();
        					if (!empty($subEntries4)) {
                			    
                		        foreach ($subEntries4 as $subEntrie4) {
                		            $data2['tab4'][]= $subEntrie4;
                		        
                				}
                				
                			}
                			
                	$entries5 = Category::trans()->where('id', $Entrie4->parent_id)->orderBy('lft')->get();
                	
					if (!empty($entries5)) {
        			    
        		        foreach ($entries5 as $Entrie5) {
        		            $data2['CAT4'][]= $Entrie5;
        		            $subEntries5 = Category::trans()->where('parent_id', 0)->orderBy('lft')->get();
        					if (!empty($subEntries5)) {
                			    
                		        foreach ($subEntries5 as $subEntrie5) {
                		            $data2['tab5'][]= $subEntrie5;
                		        
                				}
                	$entries6 = Category::trans()->where('id', $Entrie5->parent_id)->orderBy('lft')->get();
                	
					if (!empty($entries6)) {
        			    
        		        foreach ($entries6 as $Entrie6) {
        		            $data2['CAT5'][]= $Entrie6;
        		            $subEntries6 = Category::trans()->where('parent_id', 0)->orderBy('lft')->get();
        					if (!empty($subEntries6)) {
                			    
                		        foreach ($subEntries6 as $subEntrie6) {
                		            $data2['tab6'][]= $subEntrie6;
                		        
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
        				
        			} 
				}
            } 
            
            if(isset($data2['CAT3'])){ $totcat3=count($data2['CAT3']); } else { $totcat3=0;}
            if(isset($data2['CAT2'])){ $totcat2=count($data2['CAT2']); } else { $totcat2=0;}
            if(isset($data2['CAT1'])){ $totcat1=count($data2['CAT1']); } else { $totcat1=0;}
            if(isset($data2['CAT'])){ $totcat=count($data2['CAT']); } else { $totcat=0;}
            if(isset($data2['CAT4'])){ $totcat4=count($data2['CAT4']); } else { $totcat4=0;}
            if(isset($data2['CAT5'])){ $totcat5=count($data2['CAT5']); } else { $totcat5=0;}
		$data2['size']=$totcat5+$totcat4+$totcat3+$totcat2+$totcat1+$totcat;

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
		   $subEntries = Category::trans()->where('parent_id', $_GET['cat'])->orderBy('lft')->get();
		   
			if (!empty($subEntries)) {
			    $data = [];
				foreach ($subEntries as $subEntrie) {
					$data['tab1'][]= $subEntrie;
				
				}
			}
			
		echo json_encode($data);exit();
		}
		if(isset($_GET['subcat'])){
		   $subEntries2 = Category::trans()->where('parent_id', $_GET['subcat'])->orderBy('lft')->get();
		   
			if (!empty($subEntries2)) {
			    $data2 = [];
		        foreach ($subEntries2 as $subEntrie2) {
		            $data2['tab2'][]= $subEntrie2;
		        
				}
			}
		
		echo json_encode($data2);exit();
		}
		if(isset($_GET['subcat2'])){
		   $subEntries3 = Category::trans()->where('parent_id', $_GET['subcat2'])->orderBy('lft')->get();
		   
			if (!empty($subEntries3)) {
			    $data3 = [];
		        foreach ($subEntries3 as $subEntrie3) {
		            $data3['tab3'][]= $subEntrie3;
		       
				}
			}
		
		echo json_encode($data3);exit();
		}
		if(isset($_GET['subcat3'])){
		   $subEntries4 = Category::trans()->where('parent_id', $_GET['subcat3'])->orderBy('lft')->get();
		   
			if (!empty($subEntries4)) {
			    $data4 = [];
		        foreach ($subEntries4 as $subEntrie4) {
		            $data4['tab4'][]= $subEntrie4;
		       
				}
			}
		
		echo json_encode($data4);exit();
		}
		
	
		
			/*$subEntries = Category::trans()->where('parent_id', $entry->id)->orderBy('lft')->get();
			if (!empty($subEntries)) {
				foreach ($subEntries as $subEntrie) {
					$tab[$subEntrie->tid] = "---| " . $subEntrie->name;
				
					$subEntries2 = Category::trans()->where('parent_id', $subEntrie->id)->orderBy('lft')->get();
					
		        				if (!empty($subEntries)) {
		                    		foreach ($subEntries2 as $subEntrie2) {
		                    			$tab[$subEntrie2->tid] = "---|----| " . $subEntrie2->name;
		                    			
		                    			$subEntries3 = Category::trans()->where('parent_id', $subEntrie2->id)->orderBy('lft')->get();
		                    				if (!empty($subEntries3)) {
		                    		foreach ($subEntries3 as $subEntrie3) {
		                    			$tab[$subEntrie3->tid] = "---|----|----| " . $subEntrie3->name;
		                    			
		                    			
	                    			}
		        				}
		                    			
		                    			
		                    			
	                    			}
		        				}
					
				}
			}*/
		
	
		return $tab;
		
	}
	
	
	
	 public function export()
    { 
        $user = Post::all();

        // the csv file with the first row
        $output = implode("\t", array('ID', 'Date','Title','Username','Country','Archived Status','Reviewed')) . "\r\n";
        $i = 0;
        foreach ($user as $row) {
            
            $timestamp = strtotime($row->created_at);
            $newDate = date('d-M-Y', $timestamp);
            
            $user_row = DB::table('users')->where('id', $row->user_id)->first();
            
            
            

            
            if($row->reviewed == 1)
            {
                $reviewedlab = "Yes";
            }
            else
            {
                $reviewedlab ="No";
            }
            
            if($row->archived == 1){
                $archivedlab = "Yes";
            
            }
            else{
                $archivedlab = "No";
            }
            // iterate over each tweet and add it to the csv
            $output .= implode("\t", array($row->id, $newDate,$row->title,$user_row->username,$row->country_code,$archivedlab,$reviewedlab)) . "\r\n"; // append each row
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ads_' . date('Y-m-d') . '.xls"',
        );

        // our response, this will be equivalent to your download() but
        // without using a local file
        return Response::make($output, 200, $headers);
    }
    public function exportExcel()
    {
         $user = Post::all();

        // the csv file with the first row
        $output = implode(",", array('ID', 'Date','Title','Username','Country','Archived Status','Reviewed')) . "\n";
        $i = 0;
        foreach ($user as $row) {
            
            $timestamp = strtotime($row->created_at);
            $newDate = date('d-M-Y', $timestamp);
            
             $user_row = DB::table('users')->where('id', $row->user_id)->first();
            
            if($row->reviewed == 1)
            {
                $reviewedlab = "Yes";
            }
            else
            {
                $reviewedlab ="No";
            }
            
            if($row->archived == 1){
                $archivedlab = "Yes";
            
            }
            else{
                $archivedlab = "No";
            }
            // iterate over each tweet and add it to the csv
            $output .= implode(",", array($row->id, $newDate,$row->title,$user_row->username,$row->country_code,$archivedlab,$reviewedlab)) . "\n"; // append each row
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ads_' . date('Y-m-d') . '.csv"',
        );

        // our response, this will be equivalent to your download() but
        // without using a local file
        return Response::make($output, 200, $headers);
    }
}
