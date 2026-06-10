<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests\PackageRequest;
use App\Models\Scopes\StrictActiveScope;
use Illuminate\Support\Facades\Session;
use App\Helpers\Payment as PaymentHelper;
use App\Http\Controllers\Post\Traits\PaymentTrait;
use App\Models\Currency;
use App\Helpers\Ip;
use App\Http\Controllers\Auth\Traits\VerificationTrait;
use App\Http\Controllers\Post\Traits\CustomFieldTrait;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Category;
use App\Models\Package;
use App\Models\Payment;
use App\Models\City;
use App\Models\Scopes\VerifiedScope;
use App\Models\User;
use App\Http\Controllers\FrontController;
use App\Models\Scopes\ReviewedScope;
use App\Mail\PostNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Torann\LaravelMetaTags\Facades\MetaTag;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use App\Helpers\Localization\Country as CountryLocalization;
use App\Http\Controllers\Post\Traits\EditTrait;
use Illuminate\Http\Request;  
use DB;


class RechargeController extends AccountBaseController
{
	 
	use PaymentTrait;
	use EditTrait, VerificationTrait, CustomFieldTrait;
	
	public $data;
    public $request;
    public $msg = [];
    public $uri = [];
    public $packages;
    public $paymentMethods;
	
	
	public function __construct()
	{
		parent::__construct();
		
		$this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
	}
	
	/**
	 * Conversations List
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	
	public function index()
	{
		$data = [];
		
		// Set the Page Path
		view()->share('pagePath', 'recharge_points');
		
		// Get the Conversations
		//$data['conversations'] = $this->conversations->paginate($this->perPage);
		$data['countPaymentMethods'] = $this->countPaymentMethods;
		// Meta Tags
		MetaTag::set('title', t('Recharge Points'));
		MetaTag::set('description', t('Recharge Points in :app_name', ['app_name' => config('settings.app.name')]));
		
		return view('account.recharge_points', $data);
	}
	
	
	
	
	public function postForm($tmpToken = null, request $request)
	{
		
		 $alreadyPaidPackage=false;
		$getpoint = \DB::table('points')->where('id', '=', $request->point_id)->first(); 
		// dd($getpoint);
		
		  if ($getpoint->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {
                if($request->payment_method_id == 2)
                { $kd_price=0.30*$getpoint->price;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "MerchantCode=8861618&Amount=".$kd_price."&SuccessUrl=".url(config('app.locale').'/point/hesabe-success')."&FailureUrl=".url(config('app.locale').'/point/hesabe-cancel')."&Variable1=$getpoint->id&Variable2=$getpoint->no_points&Variable3=$request->package_id");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec($ch);
                    curl_close ($ch);
                    $json_decode  = json_decode($server_output, true);
                    $token = $json_decode['data']['token'];
                    $paymenturl = $json_decode['data']['paymenturl'].$token;
                    header("Location:$paymenturl");
                    exit();
                    
                }
                else
                {
                    // Send the Payment
                    return $this->sendPaymentNew($request, $getpoint);
                }
            }
		
		return view('account.recharge_points', $data);
	}
	 
	
	
	public function postFormApp($tmpToken = null, request $request)
	{
		
		 $alreadyPaidPackage=false;
		 $getpoint = \DB::table('points')->where('id', '=', $request->point_id)->first(); 
		// dd($getpoint);
		
		  if ($getpoint->price > 0 && $request->filled('payment_method_id') && !$alreadyPaidPackage) {
                if($request->payment_method_id == 2)
                { $kd_price=0.30*$getpoint->price;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"https://www.hesabe.com/authpost");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "MerchantCode=8861618&Amount=".$kd_price."&SuccessUrl=".url(config('app.locale').'/point/hesabe-success')."&FailureUrl=".url(config('app.locale').'/point/hesabe-cancel')."&Variable1=$getpoint->id&Variable2=$getpoint->no_points&Variable3=$request->package_id");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec($ch);
                    curl_close ($ch);
                    $json_decode  = json_decode($server_output, true);
                    $token = $json_decode['data']['token'];
                    $paymenturl = $json_decode['data']['paymenturl'].$token;
                    header("Location:$paymenturl");
                    exit();
                    
                }
                else
                {
                    // Send the Payment
                    return $this->sendPaymentNew($request, $getpoint);
                }
            }
		
		return view('account.recharge_points', $data);
	}
	 
	 
	 
	 public function PointHesabeSuccess(Request $request)
	{
	    if($request->Status == 1)
	    {
			
			 $info_user = DB::table('users')
            ->where('id', auth()->user()->id)->first();
	       
	       
	       $query_update = DB::table('users')
            ->where('id', auth()->user()->id)
			->update(['no_points' => ($info_user->no_points+$request->Variable3)]);
	       
	       $payment= new Payment;
	       $payment->point_id = $request->Variable1;
	       $payment->no_points = $request->Variable3;
		   $payment->user_id = (auth()->check()) ? auth()->user()->id : 0;
	       $payment->transaction_id = !empty($request->PaymentId)?$request->PaymentId:'';
	       $payment->payment_method_id = 2;
	       $payment->active = 1;
	       $payment->save();

	        
           flash(t("We have received your payment."))->success();
		   return redirect(config('app.locale').'/posts/'.$request->Variable1.'/photos');    
	       
            
	    }
	    else
	    {
	       flash(t("We have not received your payment. Payment cancelled."))->error();
           return redirect(config('app.locale').'/posts/'.$request->Variable1.'/edit/?error=paymentCancelled'); 
	    }
	}
	
	public function HesabeSuccess_app(Request $request)
	{
	    if($request->Status == 1)
	    {
			
	       $query_update = DB::table('users')
            ->where('id', $request->post_id)
          //  ->update(['reviewed' => 1,'featured' => 1]);
	        ->update(['featured' => 1]);
	       $payment= new Payment;
	       $payment->post_id = $request->post_id;
	       $payment->package_id = $request->package_id;
	       $payment->transaction_id = !empty($request->PaymentId)?$request->PaymentId:'';
	       $payment->payment_method_id = 2;
	       $payment->active = 1;
	       $payment->save();

			
		   $post = DB::table('posts')->where(['id'=>$request->post_id])->get();
		   
		  
           $post[0]->package_id = $request->input('package_id');

        // Check if Payment is required
        //$package = Package::find($request->input('package_id'));	
	        
           //flash(t("We have received your payment."))->success();
	       //if(!empty($request->Variable2))
	       //{
	       //  return redirect(config('app.locale').'/posts/create/'.$request->Variable2.'/photos');    
	       //}
	       //else
	       //{
	           //return redirect(config('app.locale').'/posts/'.$request->Variable1.'/photos');    
	       //}
		   return response()->json(['results'=>"We have received your payment",'data'=>$post]);
            
	    }
	    else
	    {
	       //flash(t("We have not received your payment. Payment cancelled."))->error();
           //return redirect(config('app.locale').'/posts/'.$request->Variable1.'/edit/?error=paymentCancelled');
		   return response()->json(['results'=>"We have not received your payment. Payment cancelled."]); 
	    }
	}
	
	public function HesabeCancel(Request $request)
	{   
        flash(t("We have not received your payment. Payment cancelled."))->error();
        return redirect(config('app.locale').'/posts/'.$request->Variable1.'/edit/?error=paymentCancelled');
	}
	
	public function HesabeCancel_app(Request $request)
	{   
        return response()->json(['results'=>"We have not received your payment. Payment cancelled."]);
	}
	
	
	
	
	
}
