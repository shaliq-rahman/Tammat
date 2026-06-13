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

namespace App\Http\Controllers\Account;
use App\Http\Controllers\FrontController;
use App\Models\Post;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Makeanoffer;
use App\Models\SavedPost;
use App\Models\SavedSearch;
use App\Models\Scopes\VerifiedScope;
use App\Models\Scopes\ReviewedScope;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use DB;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

//class TransactionsappController extends AccountappBaseController
class TransactionsappController extends FrontController
{
	protected $perPage = 10;
	
	public function __construct()
	{
		parent::__construct();
		
		
	}
	
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index(Request $request)
	{
		$transactions = Payment::whereHas('post', function($query) {
				$query->whereHas('user', function($query) {
                    $query->where('user_id', $request->userid);
                });
			})
			->with(['post', 'paymentMethod', 'package' => function ($builder) { $builder->with(['currency']); }])
			->orderByDesc('id');
		
		$data = $transactions->get();
		$count = $transactions->count();
		return response()->json(['results'=>$data, 'num'=>$count]);
		
	}
	
	
	
	public function getTransactions(Request $request)
	{
	        $userid = $request->userid;
	        
	    	$transactions = Payment::whereHas('post', function($query) use ($userid) {
				$query->whereHas('user', function($query) use ($userid) {
                    $query->where('user_id', $userid);
                });
			})
		    ->select('*',\DB::raw('(SELECT CONCAT("https://www.tmmat.com/storage/", pictures.filename) as filename  FROM pictures WHERE pictures.post_id = payments.post_id AND pictures.position = 1 ) AS image'))  
			->with(['post', 'paymentMethod', 'package' => function ($builder) { $builder->with(['currency']); }])
			->orderByDesc('id');
		
		$data1 = $transactions->get();
		$count = $transactions->count();
		
		$i=0;
		foreach($data1 as $ndata)
		{
		$data1[$i]['created_at_app'] = date('d F Y H:i',strtotime($ndata['created_at']));
		
		$data1[$i]['custom_price'] = $ndata['package']['currency']['font_arial'].''.$ndata['package']['price'];
		
		$i++;
		}		 
		
		return response()->json(['results'=>$data1, 'num'=>$count]);
	}
	
	

	
	
	
	
	
	
	
	
}
