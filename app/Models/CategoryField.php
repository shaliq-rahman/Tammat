<?php

/**

 * LaraClassified - Geo Classified Ads Software

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



namespace App\Models;

use App\Models\FieldApp;



use Larapen\Admin\app\Models\Crud;

use Illuminate\Support\Facades\App;



class CategoryField extends BaseModel

{

	use Crud;

	

	/**

	 * The table associated with the model.

	 *

	 * @var string

	 */

	protected $table = 'category_field';

	public static $languageCodeForTranslation;

	

	/**

	 * The primary key for the model.

	 *

	 * @var string

	 */

	// protected $primaryKey = 'id';

	

	/**

	 * Indicates if the model should be timestamped.

	 *

	 * @var boolean

	 */

	public $timestamps = false;

	

	/**

	 * The attributes that aren't mass assignable.

	 *

	 * @var array

	 */

	protected $guarded = ['id'];

	

	/**

	 * The attributes that are mass assignable.

	 *

	 * @var array

	 */

	protected $fillable = [

		'category_id',

		'field_id',

		'disabled_in_subcategories',

		'parent_id',

		'lft',

		'rgt',

		'depth',

	];

	

	/**

	 * The attributes that should be hidden for arrays

	 *

	 * @var array

	 */

	// protected $hidden = [];

	

	/**

	 * The attributes that should be mutated to dates.

	 *

	 * @var array

	 */

	// protected $dates = [];

	

	/*

	|--------------------------------------------------------------------------

	| FUNCTIONS

	|--------------------------------------------------------------------------

	*/

	protected static function boot()

	{

		parent::boot();

	}

	

	public function getCategoryHtml()

	{

		$out = '';

		if (!empty($this->category)) {

			$currentUrl = preg_replace('#/(search)$#', '', url()->current());

			$editUrl = $currentUrl . '/' . $this->category->id . '/edit';

			

			$out .= '<a href="' . $editUrl . '">' . $this->category->name . '</a>';

		} else {

			$out .= '--';

		}

		

		return $out;

	}

	

	public function getFieldHtml()

	{

		$out = '';

		if (!empty($this->field)) {

			$currentUrl = preg_replace('#/(search)$#', '', url()->current());

			$editUrl = $currentUrl . '/' . $this->field->id . '/edit';

			

			$out .= '<a href="' . $editUrl . '" style="float:left;">' . $this->field->name . '</a>';

			

			if (in_array($this->field->type, ['select', 'radio', 'checkbox_multiple'])) {

				$optionUrl = url(config('larapen.admin.route_prefix', 'admin') . '/custom_fields/' . $this->field->id . '/options');

				$out .= ' ';

				$out .= '<span style="float:right;">';

				$out .= '<a class="btn btn-xs btn-danger" href="' . $optionUrl . '">xxx<i class="fa fa-cog"></i> ' . mb_ucfirst(trans('admin::messages.options')) . '</a>';

				$out .= '</span>';

			}

		} else {

			$out .= '--';

		}

		

		return $out;

	}

	

	public function getDisabledInSubCategoriesHtml()

	{

		return checkboxDisplay($this->disabled_in_subcategories);

	}

	

	/**

	 * Get Fields details in details page only

	 *

	 * @param $catNestedIds

	 * @param null $postId

	 * @param null $languageCode (Required for AJAX Requests in v4.8 and lower)

	 * @return \Illuminate\Support\Collection

	 */

	public static function getFieldsDetailsPage($catNestedIds, $postId = null, $languageCode = null)

	{

		

		  

    

    

    

		$fields = [];

		

		// Make sure that the Category nested IDs variables exist

		if (!isset($catNestedIds->parentId) || !isset($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Make sure that the category nested IDs variable are not empty

		if (empty($catNestedIds->parentId) && empty($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Get Post's Custom Fields values

		$postFieldsValues = collect([]);

		if (!empty($postId) && trim($postId) != '') {

			$postFieldsValues = PostValue::where('post_id', $postId)->get();

			$postFieldsValues = self::keyingByFieldId($postFieldsValues);

		}

		

		// Get Category's fields

		$ids=array();

		$ids[]=$catNestedIds->id;

		if (!empty($catNestedIds->parentId) && !empty($catNestedIds->id)) {

			

			$x=Category::where('id',$catNestedIds->id)->first();

			if(!empty($x->parent_id)){if($x->parent_id > 0){

				$ids[]=$x->parent_id;

				$x2=Category::where('id',$x->parent_id)->first();

				if(!empty($x2->parent_id)){if($x2->parent_id > 0){

					$ids[]=$x2->parent_id;

				    $x3=Category::where('id',$x2->parent_id)->first();

					if(!empty($x3->parent_id)){if($x3->parent_id > 0){

					$ids[]=$x3->parent_id;

				    $x4=Category::where('id',$x3->parent_id)->first();

					if(!empty($x4->parent_id)){if($x4->parent_id > 0){

					$ids[]=$x4->parent_id;

				    $x5=Category::where('id',$x4->parent_id)->first();

					if(!empty($x5->parent_id)){if($x5->parent_id > 0){

					$ids[]=$x5->parent_id;			    

					

					}}

					

					}}

					

					}}

					

					}}

				}}

			

			//dd($ids);

		

		if($postId){

			$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				//->orWhere('category_id', $catNestedIds->id)

				->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();}else{

					

					

					$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				//->orWhere('category_id', $catNestedIds->id)

				//->orwhere('category_id', $catNestedIds->parentId)

				->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();

					

					

					

					}

		} else {

			

			

			if (!empty($catNestedIds->parentId)) {

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->parentId)

					->availableForSubCats()

					->orderBy('lft', 'ASC')

					->get();

			} else {

				

				

				if($postId){

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					->orwhereIn('category_id', $ids)

					->orderBy('lft', 'ASC')

					->get();

					

					}else{

						

							$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					//->orwhere('category_id', $catNestedIds->parent_id)

					->orderBy('lft', 'ASC')

					->get();

						

						

						

						}

					

					

					

			}

		

		

		

		}

		

		if ($catFields->count() > 0) {

			foreach ($catFields as $key => $catField) {

				if (!empty($catField->field)) {

					$fields[$key] = $catField->field;

					

					// Retrieve the Field's Default value

					if ($postFieldsValues->count() > 0) {

						if ($postFieldsValues->has($catField->field->tid)) {

							$postValue = $postFieldsValues->get($catField->field->tid);

							if (isset($postValue->value)) {

								$defaultValue = $postValue->value;

							} else {

								if ($catField->field->options->count() > 0) {

									$selectedOptions = [];

									foreach ($catField->field->options as $option) {

										if (isset($postValue[$option->tid])) {

											$selectedOptions[$option->tid] = $option;

										}

									}

									$defaultValue = $selectedOptions;

								} else {

									$defaultValue = [];

								}

							}

							

							$fields[$key]->default = $defaultValue;

						}

					}

					

				}

			}

		}

		

		return collect($fields);

	

	}

	

	

	/**

	 * Get Fields details

	 *

	 * @param $catNestedIds

	 * @param null $postId

	 * @param null $languageCode (Required for AJAX Requests in v4.8 and lower)

	 * @return \Illuminate\Support\Collection

	 */

	public static function getFields($catNestedIds, $postId = null, $languageCode = null)

	{

		

		  

    

    

    

		$fields = [];

		

		// Make sure that the Category nested IDs variables exist

		if (!isset($catNestedIds->parentId) || !isset($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Make sure that the category nested IDs variable are not empty

		if (empty($catNestedIds->parentId) && empty($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Get Post's Custom Fields values

		$postFieldsValues = collect([]);

		if (!empty($postId) && trim($postId) != '') {

			$postFieldsValues = PostValue::where('post_id', $postId)->get();

			$postFieldsValues = self::keyingByFieldId($postFieldsValues);

		}

		

		// Get Category's fields

		$ids=array();

		$ids[]=$catNestedIds->id;

		if (!empty($catNestedIds->parentId) && !empty($catNestedIds->id)) {

			

			$x=Category::where('id',$catNestedIds->id)->first();

			if(!empty($x->parent_id)){if($x->parent_id > 0){

				$ids[]=$x->parent_id;

				$x2=Category::where('id',$x->parent_id)->first();

				if(!empty($x2->parent_id)){if($x2->parent_id > 0){

					$ids[]=$x2->parent_id;

				    $x3=Category::where('id',$x2->parent_id)->first();

					if(!empty($x3->parent_id)){if($x3->parent_id > 0){

					$ids[]=$x3->parent_id;

				    $x4=Category::where('id',$x3->parent_id)->first();

					if(!empty($x4->parent_id)){if($x4->parent_id > 0){

					$ids[]=$x4->parent_id;

				    $x5=Category::where('id',$x4->parent_id)->first();

					if(!empty($x5->parent_id)){if($x5->parent_id > 0){

					$ids[]=$x5->parent_id;			    

					

					}}

					

					}}

					

					}}

					

					}}

				}}

			

			//dd($ids);

		

		if($postId){

			$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				//->orWhere('category_id', $catNestedIds->id)

				->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();}else{

					

					

					$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				 ->orWhere('category_id', $catNestedIds->id)

				//->orwhere('category_id', $catNestedIds->parentId)

				//->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();

					

					

					

					}

		} else {

			

			

			if (!empty($catNestedIds->parentId)) {

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->parentId)

					->availableForSubCats()

					->orderBy('lft', 'ASC')

					->get();

			} else {

				

				

				if($postId){

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					->orwhereIn('category_id', $ids)

					->orderBy('lft', 'ASC')

					->get();

					

					}else{

						

							$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					//->orwhere('category_id', $catNestedIds->parent_id)

					->orderBy('lft', 'ASC')

					->get();

						

						

						

						}

					

					

					

			}

		

		

		

		}

		

		if ($catFields->count() > 0) {

			foreach ($catFields as $key => $catField) {

				if (!empty($catField->field)) {

					$fields[$key] = $catField->field;

					

					// Retrieve the Field's Default value

					if ($postFieldsValues->count() > 0) {

						if ($postFieldsValues->has($catField->field->tid)) {

							$postValue = $postFieldsValues->get($catField->field->tid);

							if (isset($postValue->value)) {

								$defaultValue = $postValue->value;

							} else {

								if ($catField->field->options->count() > 0) {

									$selectedOptions = [];

									foreach ($catField->field->options as $option) {

										if (isset($postValue[$option->tid])) {

											$selectedOptions[$option->tid] = $option;

										}

									}

									$defaultValue = $selectedOptions;

								} else {

									$defaultValue = [];

								}

							}

							

							$fields[$key]->default = $defaultValue;

						}

					}

					

				}

			}

		}

		

		return collect($fields);

	

	}

	

	

	

	

	public static function ShowFields_only($catNestedIds, $postId = null )

	{ 

		

		$fields = [];

		

		// Make sure that the Category nested IDs variables exist

		if (!isset($catNestedIds->parentId) || !isset($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Make sure that the category nested IDs variable are not empty

		if (empty($catNestedIds->parentId) && empty($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Get Post's Custom Fields values

		$postFieldsValues = collect([]);

		if (!empty($postId) && trim($postId) != '') {

			$postFieldsValues = PostValue::where('post_id', $postId)->get();

			$postFieldsValues = self::keyingByFieldId($postFieldsValues);

		}

		

		// Get Category's fields

		$ids=array();

		$ids[]=$catNestedIds->id;

		if (!empty($catNestedIds->parentId) && !empty($catNestedIds->id)) {

			

			$x=Category::where('id',$catNestedIds->id)->first();

			if(!empty($x->parent_id)){if($x->parent_id > 0){

				$ids[]=$x->parent_id;

				$x2=Category::where('id',$x->parent_id)->first();

				if(!empty($x2->parent_id)){if($x2->parent_id > 0){

					$ids[]=$x2->parent_id;

				    $x3=Category::where('id',$x2->parent_id)->first();

					if(!empty($x3->parent_id)){if($x3->parent_id > 0){

					$ids[]=$x3->parent_id;

				    $x4=Category::where('id',$x3->parent_id)->first();

					if(!empty($x4->parent_id)){if($x4->parent_id > 0){

					$ids[]=$x4->parent_id;

				    $x5=Category::where('id',$x4->parent_id)->first();

					if(!empty($x5->parent_id)){if($x5->parent_id > 0){

					$ids[]=$x5->parent_id;			    

					

					}}

					

					}}

					

					}}

					

					}}

				}}

			

			//dd($ids);

		

		if($postId){

			$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				//->orWhere('category_id', $catNestedIds->id)

				->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();}else{

					

					

					$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				 ->orWhere('category_id', $catNestedIds->id)

				//->orwhere('category_id', $catNestedIds->parentId)

				//->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();

					

					

					

					}

		} else {

			

			

			if (!empty($catNestedIds->parentId)) {

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->parentId)

					->availableForSubCats()

					->orderBy('lft', 'ASC')

					->get();

			} else {

				

				

				if($postId){

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					->orwhereIn('category_id', $ids)

					->orderBy('lft', 'ASC')

					->get();

					

					}else{

						

							$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					//->orwhere('category_id', $catNestedIds->parent_id)

					->orderBy('lft', 'ASC')

					->get();

						

						

						

						}

					

					

					

			}

		

		

		

		}

		

		if ($catFields->count() > 0) {

			foreach ($catFields as $key => $catField) {

				if (!empty($catField->field)) {

					$fields[$key] = $catField->field;

					

					// Retrieve the Field's Default value

					if ($postFieldsValues->count() > 0) {

						if ($postFieldsValues->has($catField->field->tid)) {

							$postValue = $postFieldsValues->get($catField->field->tid);

							if (isset($postValue->value)) {

								$defaultValue = $postValue->value;

							} else {

								if ($catField->field->options->count() > 0) {

									$selectedOptions = [];

									foreach ($catField->field->options as $option) {

										if (isset($postValue[$option->tid])) {

											$selectedOptions[$option->tid] = $option;

										}

									}

									$defaultValue = $selectedOptions;

								} else {

									$defaultValue = [];

								}

							}

							

							$fields[$key]->default = $defaultValue;

						}

					}

					

				}

			}

		}

		

		return collect($fields);

	

	}

	

	



	public static function getFields_app($catNestedIds, $postId = null )

	{ 

		

		$fields = [];

		

		// Make sure that the Category nested IDs variables exist

		if (!isset($catNestedIds->parentId) || !isset($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Make sure that the category nested IDs variable are not empty

		if (empty($catNestedIds->parentId) && empty($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Get Post's Custom Fields values

		$postFieldsValues = collect([]);

		if (!empty($postId) && trim($postId) != '') {

			$postFieldsValues = PostValue::where('post_id', $postId)->get();

			$postFieldsValues = self::keyingByFieldId($postFieldsValues);

		}

		

		// Get Category's fields

		$ids=array();

		$ids[]=$catNestedIds->id;

		if (!empty($catNestedIds->parentId) && !empty($catNestedIds->id)) {

			

			$x=Category::where('id',$catNestedIds->id)->first();

			if(!empty($x->parent_id)){if($x->parent_id > 0){

				$ids[]=$x->parent_id;

				$x2=Category::where('id',$x->parent_id)->first();

				if(!empty($x2->parent_id)){if($x2->parent_id > 0){

					$ids[]=$x2->parent_id;

				    $x3=Category::where('id',$x2->parent_id)->first();

					if(!empty($x3->parent_id)){if($x3->parent_id > 0){

					$ids[]=$x3->parent_id;

				    $x4=Category::where('id',$x3->parent_id)->first();

					if(!empty($x4->parent_id)){if($x4->parent_id > 0){

					$ids[]=$x4->parent_id;

				    $x5=Category::where('id',$x4->parent_id)->first();

					if(!empty($x5->parent_id)){if($x5->parent_id > 0){

					$ids[]=$x5->parent_id;			    

					

					}}

					

					}}

					

					}}

					

					}}

				}}

			

			//dd($ids);

		

		if($postId){

			$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				//->orWhere('category_id', $catNestedIds->id)

				->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();}else{

					

					

					$catFields = self::with(['field' => function ($builder) {

				$builder->with(['options']);

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id',  $catNestedIds->parentId)->availableForSubCats();

				})

				 ->orWhere('category_id', $catNestedIds->id)

				//->orwhere('category_id', $catNestedIds->parentId)

				//->orwhereIn('category_id', $ids)

				->orderBy('lft', 'ASC')

				->distinct('field')

				->get();

					

					

					

					}

		} else {

			

			

			if (!empty($catNestedIds->parentId)) {

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->parentId)

					->availableForSubCats()

					->orderBy('lft', 'ASC')

					->get();

			} else {

				

				

				if($postId){

				$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					->orwhereIn('category_id', $ids)

					->orderBy('lft', 'ASC')

					->get();

					

					}else{

						

							$catFields = self::with(['field' => function ($builder) {

					$builder->with(['options']);

				}])

					->where('category_id', $catNestedIds->id)

					//->orwhere('category_id', $catNestedIds->parent_id)

					->orderBy('lft', 'ASC')

					->get();

						

						

						

						}

					

					

					

			}

		

		

		

		}

		

		if ($catFields->count() > 0) {

			foreach ($catFields as $key => $catField) {

				if (!empty($catField->field)) {

					$fields[$key] = $catField->field;

					

					// Retrieve the Field's Default value

					if ($postFieldsValues->count() > 0) {

						if ($postFieldsValues->has($catField->field->tid)) {

							$postValue = $postFieldsValues->get($catField->field->tid);

							if (isset($postValue->value)) {

								$defaultValue = $postValue->value;

							} else {

								if ($catField->field->options->count() > 0) {

									$selectedOptions = [];

									foreach ($catField->field->options as $option) {

										if (isset($postValue[$option->tid])) {

											$selectedOptions[$option->tid] = $option;

										}

									}

									$defaultValue = $selectedOptions;

								} else {

									$defaultValue = [];

								}

							}

							

							$fields[$key]->default = $defaultValue;

						}

					}

					

				}

			}

		}

		

		return collect($fields);

	

	}

	

	

	

	

	public static function getFields_app_old($catNestedIds, $postId = null, $languageCode = null)

	{ 

	    self::$languageCodeForTranslation = $languageCode;

		$fields = [];

		

		 



        //Session::put('locale', $languageCode);



        App::setLocale($languageCode);



		// Make sure that the Category nested IDs variables exist

		if (!isset($catNestedIds->parentId) || !isset($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Make sure that the category nested IDs variable are not empty

		if (empty($catNestedIds->parentId) && empty($catNestedIds->id)) {

			return collect($fields);

		}

		

		// Get Post's Custom Fields values

		$postFieldsValues = collect([]);

		if (!empty($postId) && trim($postId) != '') {

			$postFieldsValues = PostValue::where('post_id', $postId)->get();

			$postFieldsValues = self::keyingByFieldId($postFieldsValues);

		}



		

		// Get Category's fields

		if (!empty($catNestedIds->parentId) && !empty($catNestedIds->id)) {

		

		

			$catFields = self::with(['field' => function ($builder) use($languageCode){

 

				$builder->with(['NewOptions' => function ($query) use ($languageCode) {



					$query->where('translation_lang', $languageCode);

				}] );



				

				

			}])

				->where(function ($query) use ($catNestedIds) {

					$query->where('category_id', $catNestedIds->parentId)->availableForSubCats();

				})

				->orWhere('category_id', $catNestedIds->id)

				->orderBy('lft', 'ASC')

				->get();



			//	 echo "rrrrrrrrrrrrr".$catNestedIds->id."=========".$catNestedIds->parentId."wwwwwwwwww"; print_r($catFields);

				

		} else {

			if (!empty($catNestedIds->parentId)) {

				$catFields = self::with(['field'  => function ($builder) use ($languageCode) {

				   

				//	$builder->with(['options']);

					$builder->with(['options' => function ($query) use ($languageCode) {



						$query->where('translation_lang', $languageCode);

					}] );



				}])

					->where('category_id', $catNestedIds->parentId)

					->where('translation_lang', $languageCode)

					->availableForSubCats()

					->orderBy('lft', 'ASC')

					->get();

			} else {

				$catFields = self::with(['field'  => function ($builder) use ($languageCode)  {

				//	$builder->with(['options'])->where('translation_lang', 'en');

					$builder->with(['options' => function ($query) use ($languageCode) {



						$query->where('translation_lang', $languageCode);

					}] );



				}])

					->where('category_id', $catNestedIds->id)

					->where('translation_lang', $languageCode)

					->orderBy('lft', 'ASC')

					->get();

			}

		}

		

	

		if ($catFields->count() > 0) {

			foreach ($catFields as $key => $catField) {

				if (!empty($catField->field)) {

				    $fId =$catField->field->translation_of;

					$fields[$key] = $catField->field;

					

					// Retrieve the Field's Default value

					if ($postFieldsValues->count() > 0) {

						if ($postFieldsValues->has($catField->field->tid)) {

							$postValue = $postFieldsValues->get($catField->field->tid);

							if (isset($postValue->value)) {

								$defaultValue = $postValue->value;

							} else {

								if ($catField->field->options->count() > 0) {

									$selectedOptions = [];

									foreach ($catField->field->options as $option) {

										if (isset($postValue[$option->tid])) {

											$selectedOptions[$option->tid] = $option;

										}

									}

									//echo $selectedOptions;

									//echo '<br>';

									if(is_array($selectedOptions))

									{

									//echo 'a';

									$defaultValue = $selectedOptions;

									//$defaultValue = [];

									}

									else

									{

									//echo 'b';

									$defaultValue = $selectedOptions;

									}

								} else {

									$defaultValue = [];

								}

							}

							

							$fields[$key]->default = $defaultValue;

						}

					}

					

				}

			}

		}

		

		return collect($fields);

	}

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	

	/**

	 * @param $values

	 * @return \Illuminate\Support\Collection

	 */

	private static function keyingByFieldId($values)

	{

		if (empty($values) || $values->count() <= 0) {

			return $values;

		}

		

		$postValues = [];

		foreach ($values as $value) {

			if (!empty($value->option_id)) {

				$postValues[$value->field_id][$value->option_id] = $value;

			} else {

				$postValues[$value->field_id] = $value;

			}

		}

		

		return collect($postValues);

	}

	

	/*

	|--------------------------------------------------------------------------

	| RELATIONS

	|--------------------------------------------------------------------------

	*/

	public function category($languageCode = null)

	{

	    if(!empty(self::$languageCodeForTranslation)){

	        

            return $this->belongsTo(Category::class, 'category_id', 'translation_of')->where('translation_lang', self::$languageCodeForTranslation);	   

        }

	    else {

	        return $this->belongsTo(Category::class, 'category_id', 'translation_of')->where('translation_lang', config('app.locale'));

	    }

		

	}

	

	public function field($languageCode = null)

	{



	    if(!empty(self::$languageCodeForTranslation)){

	        

	        return $this->belongsTo(Field::class, 'field_id', 'translation_of')->where('translation_lang', self::$languageCodeForTranslation);

	    }

	    else{

	        

	        

		return $this->belongsTo(Field::class, 'field_id', 'translation_of')->where('translation_lang', config('app.locale'));

	    }

	}



	public function fieldapp($languageCode = null)

	{



	    if(!empty(self::$languageCodeForTranslation)){

	        

	        return $this->belongsTo(FieldApp::class, 'field_id', 'translation_of')->where('translation_lang', self::$languageCodeForTranslation);

	    }

	    else{

	        

	        

		return $this->belongsTo(FieldApp::class, 'field_id', 'translation_of')->where('translation_lang', config('app.locale'));

	    }

	}





	public function field1($languageCode)

	{

	    

	    if(!empty(self::$languageCodeForTranslation)){

	        

	        return $this->belongsTo(Field::class, 'field_id', 'translation_of')->where('translation_lang', self::$languageCodeForTranslation);

	    } else {

	        return $this->belongsTo(Field::class, 'field_id', 'translation_of')->where('translation_lang', $languageCode);

	    }

		

	}

	/*

	|--------------------------------------------------------------------------

	| SCOPES

	|--------------------------------------------------------------------------

	*/

	public function scopeAvailableForSubCats($builder)

	{

		return $builder->where('disabled_in_subcategories', '!=', 1);

	}

	

	public function scopeUnavailableForSubCats($builder)

	{

		return $builder->where('disabled_in_subcategories', 1);

	}

	

	/*

	|--------------------------------------------------------------------------

	| ACCESORS

	|--------------------------------------------------------------------------

	*/

	

	/*

	|--------------------------------------------------------------------------

	| MUTATORS

	|--------------------------------------------------------------------------

	*/

}

