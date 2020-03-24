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

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use Larapen\Admin\app\Http\Controllers\PanelController;
use App\Http\Requests\Admin\Request as StoreRequest;
use App\Http\Requests\Admin\Request as UpdateRequest;

class HomeSectionController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\HomeSection');
		$this->xPanel->setRoute(config('larapen.admin.route_prefix', 'admin') . '/homepage');
		$this->xPanel->setEntityNameStrings(trans('admin::messages.homepage section'), trans('admin::messages.homepage sections'));
		$this->xPanel->denyAccess(['create', 'delete']);
		$this->xPanel->allowAccess(['reorder']);
		$this->xPanel->enableReorder('name', 1);
		if (!request()->input('order')) {
			$this->xPanel->orderBy('lft', 'ASC');
		}
		
		$this->xPanel->addButtonFromModelFunction('top', 'reset_homepage_reorder', 'resetHomepageReOrderBtn', 'end');
		$this->xPanel->addButtonFromModelFunction('top', 'reset_homepage_settings', 'resetHomepageSettingsBtn', 'end');
		$this->xPanel->removeButton('update');
		$this->xPanel->addButtonFromModelFunction('line', 'configure', 'configureBtn', 'beginning');
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->xPanel->addColumn([
			'name'          => 'name',
			'label'         => trans("admin::messages.Section"),
			'type'          => 'model_function',
			'function_name' => 'getNameHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'active',
			'label'         => trans("admin::messages.Active"),
			'type'          => 'model_function',
			'function_name' => 'getActiveHtml',
		]);
		
		// FIELDS
		$this->xPanel->addField([
			'name'       => 'name',
			'label'      => trans("admin::messages.Section"),
			'type'       => 'text',
			'attributes' => [
				'placeholder' => trans("admin::messages.Section"),
				'disabled'    => 'disabled',
			],
		]);
		
		$section = $this->xPanel->model->find(request()->segment(3));
		if (!empty($section)) {
			// getSearchForm
			if (in_array($section->method, ['getSearchForm'])) {
				$enableCustomFormField = [
					'name'     => 'enable_form_area_customization',
					'label'    => trans("admin::messages.Enable search form area customization"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'checkbox',
					'hint'     => trans("admin::messages.NOTE: The options below require to enable the search form area customization."),
				];
				$this->xPanel->addField($enableCustomFormField);
				
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_1',
					'type'  => 'custom_html',
					'value' => '<h3>' . trans('admin::messages.Background') . '</h3>',
				]);
				
				$backgroundColorField = [
					'name'                => 'background_color',
					'label'               => trans("admin::messages.Background Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#444",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code."),
				];
				$this->xPanel->addField($backgroundColorField);
				
				$backgroundImageField = [
					'name'     => 'background_image',
					'label'    => trans("admin::messages.Background Image"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'image',
					'upload'   => true,
					'disk'     => 'public',
					'hint'     => trans('admin::messages.Choose a picture from your computer.') . '<br>' .
						trans('admin::messages.You can set a background image by country in Settings -> International -> Countries -> [Edit] -> Background Image'),
				];
				$this->xPanel->addField($backgroundImageField);
				
				$heightField = [
					'name'              => 'height',
					'label'             => trans("admin::messages.Height"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => "450px",
					],
					'hint'              => trans('admin::messages.Enter a value greater than 50px.') . ' (' . trans('admin::messages.Example: 400px') . ')',
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($heightField);
				
				$parallaxField = [
					'name'              => 'parallax',
					'label'             => trans("admin::messages.Enable Parallax Effect"),
					'fake'              => true,
					'store_in'          => 'options',
					'type'              => 'checkbox',
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
						'style' => 'margin-top: 20px;',
					],
				];
				$this->xPanel->addField($parallaxField);
				
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_2',
					'type'  => 'custom_html',
					'value' => '<h3>' . trans('admin::messages.Search Form') . '</h3>',
				]);
				
				$hideFormField = [
					'name'     => 'hide_form',
					'label'    => trans("admin::messages.Hide the Form"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'checkbox',
				];
				$this->xPanel->addField($hideFormField);
				
				$formBorderColorField = [
					'name'                => 'form_border_color',
					'label'               => trans("admin::messages.Form Border Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#333",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code."),
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-3',
					],
				];
				$this->xPanel->addField($formBorderColorField);
				
				$formBorderSizeField = [
					'name'              => 'form_border_width',
					'label'             => trans("admin::messages.Form Border Width"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => "5px",
					],
					'hint'              => 'Enter a number with unit (eg. 5px)',
					'wrapperAttributes' => [
						'class' => 'form-group col-md-3',
					],
				];
				$this->xPanel->addField($formBorderSizeField);
				
				$formBtnBackgroundColorField = [
					'name'                => 'form_btn_background_color',
					'label'               => trans("admin::messages.Form Button Background Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#4682B4",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code."),
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-3',
					],
				];
				$this->xPanel->addField($formBtnBackgroundColorField);
				
				$formBtnTextColorField = [
					'name'                => 'form_btn_text_color',
					'label'               => trans("admin::messages.Form Button Text Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#FFF",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code."),
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-3',
					],
				];
				$this->xPanel->addField($formBtnTextColorField);
				
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_3',
					'type'  => 'custom_html',
					'value' => '<h3>' . trans('admin::messages.Titles') . '</h3>',
				]);
				
				$hideTitlesField = [
					'name'     => 'hide_titles',
					'label'    => trans("admin::messages.Hide Titles"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'checkbox',
				];
				$this->xPanel->addField($hideTitlesField);
				
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_3_1',
					'type'  => 'custom_html',
					'value' => '<h4>' . trans('admin::messages.Titles Content') . '</h4>',
				]);
				
				$this->xPanel->addField([
					'name'  => 'separator_3_2',
					'type'  => 'custom_html',
					'value' => 'NOTE: ' . trans("admin::messages.You can use dynamic variables such as {app_name}, {country}, {count_ads} and {count_users}."),
				]);
				
				$languages = Language::active()->get();
				if ($languages->count() > 0) {
					foreach ($languages as $language) {
						${'titleField' . $language->abbr} = [
							'name'              => 'title_' . $language->abbr,
							'label'             => trans("admin::messages.Title") . ' (' . $language->name . ')',
							'fake'              => true,
							'store_in'          => 'options',
							'attributes'        => [
								'placeholder' => t('Sell and buy near you', [], 'global', $language->abbr),
							],
							'wrapperAttributes' => [
								'class' => 'form-group col-md-6',
							],
						];
						$this->xPanel->addField(${'titleField' . $language->abbr});
						
						${'subTitleField' . $language->abbr} = [
							'name'              => 'sub_title_' . $language->abbr,
							'label'             => trans("admin::messages.Sub Title") . ' (' . $language->name . ')',
							'fake'              => true,
							'store_in'          => 'options',
							'attributes'        => [
								'placeholder' => t('Simple, fast and efficient', [], 'global', $language->abbr),
							],
							'wrapperAttributes' => [
								'class' => 'form-group col-md-6',
							],
						];
						$this->xPanel->addField(${'subTitleField' . $language->abbr});
					}
				}
				
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_3_3',
					'type'  => 'custom_html',
					'value' => '<h4>' . trans('admin::messages.Titles Color') . '</h4>',
				]);
				
				$bigTitleColorField = [
					'name'                => 'big_title_color',
					'label'               => trans("admin::messages.Big Title Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#FFF",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code."),
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($bigTitleColorField);
				
				$subTitleColorField = [
					'name'                => 'sub_title_color',
					'label'               => trans("admin::messages.Sub Title Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#FFF",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code."),
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($subTitleColorField);
			}
			
			// getCategories
			if (in_array($section->method, ['getCategories'])) {
				$typeOfDisplayField = [
					'name'        => 'type_of_display',
					'label'       => trans("admin::messages.Type of display"),
					'fake'        => true,
					'store_in'    => 'options',
					'type'        => 'select2_from_array',
					'options'     => [
						'c_normal_list'    => trans("admin::messages.Normal List"),
						'c_circle_list'    => trans("admin::messages.Circle List"),
						'c_check_list'     => trans("admin::messages.Check List"),
						'c_border_list'    => trans("admin::messages.Border List"),
						'c_picture_icon'   => trans("admin::messages.Picture as Icon"),
						'cc_normal_list'   => trans("admin::messages.Normal List (Categories + Children)"),
						'cc_normal_list_s' => trans("admin::messages.Normal List Styled (Categories + Children)"),
					],
					'allows_null' => false,
				];
				$this->xPanel->addField($typeOfDisplayField);
				
				$showIconField = [
					'name'              => 'show_icon',
					'label'             => trans("admin::messages.Show the categories icons"),
					'fake'              => true,
					'store_in'          => 'options',
					'type'              => 'checkbox',
					'hint'              => trans("admin::messages.NOTE: This will be applied for all of \"Types of display\", except \"Picture as Icon\"."),
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
						'style' => 'margin-top: 20px;',
					],
				];
				$this->xPanel->addField($showIconField);
				
				$maxSubCatsField = [
					'name'              => 'max_sub_cats',
					'label'             => trans("admin::messages.Max subcategories displayed by default"),
					'fake'              => true,
					'store_in'          => 'options',
					'hint'              => trans("admin::messages.NOTE: This will be applied for only the \"Categories + Children\" type of display."),
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($maxSubCatsField);
			}
			
			// getCategories, getSponsoredPosts & getLatestPosts
			if (in_array($section->method, ['getCategories', 'getSponsoredPosts', 'getLatestPosts'])) {
				$maxItemsField = [
					'name'              => 'max_items',
					'label'             => trans("admin::messages.Max Items"),
					'fake'              => true,
					'store_in'          => 'options',
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($maxItemsField);
			}
			
			// getSponsoredPosts & getLatestPosts
			if (in_array($section->method, ['getSponsoredPosts', 'getLatestPosts'])) {
				$orderByField = [
					'name'              => 'order_by',
					'label'             => trans("admin::messages.Order By"),
					'fake'              => true,
					'store_in'          => 'options',
					'type'              => 'select2_from_array',
					'options'           => [
						'date'   => trans("admin::messages.Date"),
						'random' => trans("admin::messages.Random"),
					],
					'allows_null'       => false,
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($orderByField);
			}
			
			// getLocations
			if ($section->method == 'getLocations') {
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_4',
					'type'  => 'custom_html',
					'value' => '<h3>' . trans('admin::messages.Locations') . '</h3>',
				]);
				
				$showCitiesField = [
					'name'              => 'show_cities',
					'label'             => trans("admin::messages.Show the Country Cities"),
					'fake'              => true,
					'store_in'          => 'options',
					'type'              => 'checkbox',
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
						//'style' => 'margin-top: 20px;',
					],
				];
				$this->xPanel->addField($showCitiesField);
				
				$showPostBtnField = [
					'name'              => 'show_post_btn',
					'label'             => trans("admin::messages.Show the bottom button"),
					'fake'              => true,
					'store_in'          => 'options',
					'type'              => 'checkbox',
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
						//'style' => 'margin-top: 20px;',
					],
				];
				$this->xPanel->addField($showPostBtnField);
				
				$backgroundColorField = [
					'name'                => 'background_color',
					'label'               => trans("admin::messages.Background Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($backgroundColorField);
				
				$borderWidthField = [
					'name'              => 'border_width',
					'label'             => trans("admin::messages.Border Width"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => '1px',
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($borderWidthField);
				
				$borderColorField = [
					'name'                => 'border_color',
					'label'               => trans("admin::messages.Border Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($borderColorField);
				
				$textColorField = [
					'name'                => 'text_color',
					'label'               => trans("admin::messages.Text Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($textColorField);
				
				$linkColorField = [
					'name'                => 'link_color',
					'label'               => trans("admin::messages.Links Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($linkColorField);
				
				$linkColorHoverField = [
					'name'                => 'link_color_hover',
					'label'               => trans("admin::messages.Links Color (Hover)"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($linkColorHoverField);
				
				$maxItemsField = [
					'name'              => 'max_items',
					'label'             => trans("admin::messages.Max Cities"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => 12,
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($maxItemsField);
				
				$cacheExpirationField = [
					'name'              => 'cache_expiration',
					'label'             => trans("admin::messages.Cache Expiration Time for this section"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => trans("admin::messages.In minutes (e.g. 60 for 1h, 0 or empty value to disable the cache)"),
					],
					'hint'              => trans("admin::messages.In minutes (e.g. 60 for 1h, 0 or empty value to disable the cache)"),
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($cacheExpirationField);
				
				// Separator
				$this->xPanel->addField([
					'name'  => 'separator_4_1',
					'type'  => 'custom_html',
					'value' => '<h3>' . trans('admin::messages.Country Map') . '</h3>',
				]);
				
				$showMapField = [
					'name'     => 'show_map',
					'label'    => trans("admin::messages.Show the Country Map"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'checkbox',
				];
				$this->xPanel->addField($showMapField);
				
				$mapBackgroundColorField = [
					'name'                => 'map_background_color',
					'label'               => trans("admin::messages.Map's Background Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "transparent",
					],
					'hint'                => trans("admin::messages.Enter a RGB color code or the word 'transparent'."),
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapBackgroundColorField);
				
				$mapBorderField = [
					'name'                => 'map_border',
					'label'               => trans("admin::messages.Map's Border"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'hint'                => '<br>',
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapBorderField);
				
				$mapHoverBorderField = [
					'name'                => 'map_hover_border',
					'label'               => trans("admin::messages.Map's Hover Border"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#c7c5c1",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapHoverBorderField);
				
				$mapBorderWidthField = [
					'name'              => 'map_border_width',
					'label'             => trans("admin::messages.Map's Border Width"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => 4,
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapBorderWidthField);
				
				$mapColorField = [
					'name'                => 'map_color',
					'label'               => trans("admin::messages.Map's Color"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#f2f0eb",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapColorField);
				
				$mapHoverField = [
					'name'                => 'map_hover',
					'label'               => trans("admin::messages.Map's Hover"),
					'fake'                => true,
					'store_in'            => 'options',
					'type'                => 'color_picker',
					'colorpicker_options' => [
						'customClass' => 'custom-class',
					],
					'attributes'          => [
						'placeholder' => "#4682B4",
					],
					'wrapperAttributes'   => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapHoverField);
				
				$mapWidthField = [
					'name'              => 'map_width',
					'label'             => trans("admin::messages.Map's Width"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => "300px",
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapWidthField);
				
				$mapHeightField = [
					'name'              => 'map_height',
					'label'             => trans("admin::messages.Map's Height"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => "300px",
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($mapHeightField);
			}
			
			// getSponsoredPosts
			if ($section->method == 'getSponsoredPosts') {
				$carouselAutoplayField = [
					'name'     => 'autoplay',
					'label'    => trans("admin::messages.Carousel's Autoplay"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'checkbox',
				];
				$this->xPanel->addField($carouselAutoplayField);
				
				$carouselAutoplayTimeout = [
					'name'              => 'autoplay_timeout',
					'label'             => trans("admin::messages.Carousel's Autoplay Timeout"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => 1500,
					],
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($carouselAutoplayTimeout);
			}
			
			// getLatestPosts
			if ($section->method == 'getLatestPosts') {
				$showViewMoreBtnField = [
					'name'     => 'show_view_more_btn',
					'label'    => trans("admin::messages.Show 'View More' Button"),
					'fake'     => true,
					'store_in' => 'options',
					'type'     => 'checkbox',
				];
				$this->xPanel->addField($showViewMoreBtnField);
			}
			
			// getSponsoredPosts, getLatestPosts & getCategories
			if (in_array($section->method, ['getSponsoredPosts', 'getLatestPosts', 'getCategories'])) {
				$cacheExpirationField = [
					'name'              => 'cache_expiration',
					'label'             => trans("admin::messages.Cache Expiration Time for this section"),
					'fake'              => true,
					'store_in'          => 'options',
					'attributes'        => [
						'placeholder' => trans("admin::messages.In minutes (e.g. 60 for 1h, 0 or empty value to disable the cache)"),
					],
					'hint'              => trans("admin::messages.In minutes (e.g. 60 for 1h, 0 or empty value to disable the cache)"),
					'wrapperAttributes' => [
						'class' => 'form-group col-md-6',
					],
				];
				$this->xPanel->addField($cacheExpirationField);
			}
		}
		
		// Separator
		$this->xPanel->addField([
			'name'  => 'separator_last',
			'type'  => 'custom_html',
			'value' => '<hr>',
		]);
		
		$activeField = [
			'name'  => 'active',
			'label' => trans("admin::messages.Active"),
			'type'  => 'checkbox',
		];
		if (!empty($section) && $section->method == 'getTopAdvertising') {
			$activeField['hint'] = trans('admin::messages.To enable this feature, you have to configure the top advertisement in the Admin panel -> Setup -> Advertising -> top (Edit)');
		}
		if (!empty($section) && $section->method == 'getBottomAdvertising') {
			$activeField['hint'] = trans('admin::messages.To enable this feature, you have to configure the bottom advertisement in the Admin panel -> Setup -> Advertising -> bottom (Edit)');
		}
		$this->xPanel->addField($activeField);
	}
	
	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}
	
	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}
}
