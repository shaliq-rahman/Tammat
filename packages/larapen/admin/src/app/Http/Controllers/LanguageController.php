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

namespace Larapen\Admin\app\Http\Controllers;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Admin\LanguageRequest as StoreRequest;
use App\Http\Requests\Admin\LanguageRequest as UpdateRequest;

class LanguageController extends PanelController
{
	/**
	 * LanguageController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\Language');
		$this->xPanel->setRoute(config('larapen.admin.route_prefix', 'admin') . '/languages');
		$this->xPanel->setEntityNameStrings(trans('admin::messages.language'), trans('admin::messages.languages'));
		
		$this->xPanel->addButtonFromModelFunction('top', 'sync_languages_files', 'syncLanguageFilesLinesBtn', 'end');
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		$this->xPanel->addColumn([
			'name'  => 'name',
			'label' => trans('admin::messages.language_name'),
		]);
		$this->xPanel->addColumn([
			'name'  => 'direction',
			'label' => trans("admin::messages.Direction"),
		]);
		$this->xPanel->addColumn([
			'name'          => 'active',
			'label'         => trans('admin::messages.active'),
			'type'          => "model_function",
			'function_name' => 'getActiveHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'default',
			'label'         => trans('admin::messages.default'),
			'type'          => "model_function",
			'function_name' => 'getDefaultHtml',
		]);
		
		// FIELDS
		$this->xPanel->addField([
			'name'              => 'name',
			'label'             => trans('admin::messages.language_name'),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans('admin::messages.language_name'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'native',
			'label'             => trans('admin::messages.native_name'),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans('admin::messages.native_name'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'abbr',
			'label'             => trans('admin::messages.code_iso639-1'),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans('admin::messages.code_iso639-1'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'locale',
			'label'             => trans('admin::messages.Locale Code (E.g. en_US)'),
			'type'              => 'text',
			'attributes'        => [
				'placeholder' => trans('admin::messages.Locale Code (E.g. en_US)'),
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'direction',
			'label'             => trans("admin::messages.Direction"),
			'type'              => 'enum',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'russian_pluralization',
			'label'             => trans('admin::messages.Russian Pluralization'),
			'type'              => 'checkbox',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6',
				'style' => 'margin-top: 20px;',
			],
		]);
		$this->xPanel->addField([
			'name'  => 'active',
			'label' => trans('admin::messages.active'),
			'type'  => 'checkbox',
		]);
		$this->xPanel->addField([
			'name'  => 'default',
			'label' => trans('admin::messages.default'),
			'type'  => 'checkbox',
		], 'update');
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
