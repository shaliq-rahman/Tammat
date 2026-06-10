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

use App\Models\Package;
use App\Models\PaymentMethod;
use Larapen\Admin\app\Http\Controllers\PanelController;
use Larapen\Admin\app\Http\Requests\Request as StoreRequest;
use Larapen\Admin\app\Http\Requests\Request as UpdateRequest;

class PaymentController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\Payment');
		$this->xPanel->with(['post_latest', 'package', 'paymentMethod']);
		$this->xPanel->setRoute(config('larapen.admin.route_prefix', 'admin') . '/payments');
		$this->xPanel->setEntityNameStrings(trans('admin::messages.payment'), trans('admin::messages.payments'));
		$this->xPanel->denyAccess(['create', 'update']);
// 		$this->xPanel->removeAllButtons(); // Remove also: 'create' & 'reorder' buttons
		/*
		$this->xPanel->removeButton('update');
		$this->xPanel->removeButton('delete');
		$this->xPanel->removeButton('preview');
		$this->xPanel->removeButton('revisions');
		*/
		$this->xPanel->addButtonFromModelFunction('top', 'bulk_delete_btn', 'bulkDeleteBtn', 'end');

		if (!request()->input('order')) {
			$this->xPanel->orderBy('created_at', 'DESC');
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
			'name'  => 'post_id',
			'type'  => 'text',
			'label' => trans('admin::messages.Ad') . ' (ID)',
		],
		false,
		function ($value) {
			$this->xPanel->addClause('where', 'post_id', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'package',
			'type'  => 'select2',
			'label' => trans('admin::messages.Package'),
		],
		$this->getPackages(),
		function ($value) {
			$this->xPanel->addClause('where', 'package_id', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'payment_method',
			'type'  => 'dropdown',
			'label' => trans('admin::messages.Payment Method'),
		],
		$this->getPaymentMethods(),
		function ($value) {
			$this->xPanel->addClause('where', 'payment_method_id', '=', $value);
		});
		// -----------------------
		$this->xPanel->addFilter([
			'name'  => 'status',
			'type'  => 'dropdown',
			'label' => trans('admin::messages.Status'),
		], [
			1 => trans('admin::messages.Unapproved'),
			2 => trans('admin::messages.Approved'),
		], function ($value) {
			if ($value == 1) {
				$this->xPanel->addClause('where', 'active', '=', 0);
			}
			if ($value == 2) {
				$this->xPanel->addClause('where', 'active', '=', 1);
			}
		});
		
		
		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// COLUMNS
		
		// COLUMNS

		$this->xPanel->addColumn([

			'name'  => 'id_name',

			'label' => '',

			'type'  => 'checkbox',

			// 'orderable' => true,

		]);
		
		
		$this->xPanel->addColumn([
			'name'  => 'id',
			'label' => "ID",
		]);
		$this->xPanel->addColumn([
			'name'  => 'created_at',
			'label' => trans("admin::messages.Date"),
		]);
		
		
		
		
			
			$this->xPanel->addColumn([
			'name'          => 'username',
			'label'         => 'UserName',
			'type'          => 'model_function',
			'function_name' => 'getUsername',
		]);
		
		
		
		
			
			$this->xPanel->addColumn([
			'name'          => 'country',
			'label'         => 'Country',
			'type'          => 'model_function',
			'function_name' => 'getC',
		]);
		
		
		
		
		
		$this->xPanel->addColumn([
			'name'          => 'post_id',
			'label'         => trans("admin::messages.Ad"),
			'type'          => 'model_function',
			'function_name' => 'getPostTitleHtml',
		]);
		
		
	
		
		$this->xPanel->addColumn([
			'name'          => 'package_id',
			'label'         => trans("admin::messages.Package"),
			'type'          => 'model_function',
			'function_name' => 'getPackageNameHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'transaction_id',
			'label'         => 'Transaction ID',
		]);
		
		$this->xPanel->addColumn([
			'name'          => 'payment_method_id',
			'label'         => trans("admin::messages.Payment Method"),
			'type'          => 'model_function',
			'function_name' => 'getPaymentMethodNameHtml',
		]);
		
		
		$this->xPanel->addColumn([
			'name'          => 'active',
			'label'         => trans("admin::messages.Approved"),
			'type'          => 'model_function',
			'function_name' => 'getActiveHtml',
		]);
		
		/*
		//committed by abdelhay 14-2-2024
		
		$this->xPanel->addColumn([
			'name'          => 'previewed',
			'label'         => trans("admin::messages.Reviewed"),
			'type'          => 'model_function',
			'function_name' => 'getReviewedHtml',
		]);
		*/
		
		// FIELDS
	}
	
	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}
	
	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}
	
	public function getPackages()
	{
		$entries = Package::trans()->where('price', '>', 0)->orderBy('currency_code', 'asc')->orderBy('lft', 'asc')->get();
		
		$arr = [];
		if ($entries->count() > 0) {
			foreach ($entries as $entry) {
				$arr[$entry->id] = $entry->name . ' (' . $entry->price . ' ' . $entry->currency_code . ')';
			}
		}
		
		return $arr;
	}
	
	public function getPaymentMethods()
	{
		$entries = PaymentMethod::orderBy('lft', 'asc')->get();
		
		$arr = [];
		if ($entries->count() > 0) {
			foreach ($entries as $entry) {
				$arr[$entry->id] = $entry->display_name;
			}
		}
		
		return $arr;
	}
}
