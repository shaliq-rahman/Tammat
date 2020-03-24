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

namespace App\Models;


use App\Models\Scopes\StrictActiveScope;
use App\Observer\PaymentObserver;
use Larapen\Admin\app\Models\Crud;
use App\Models\Post;
use DB;
//use Illuminate\Support\Facades\DB;



class Payment extends BaseModel
{
	use Crud;
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';
    
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
    // public $timestamps = false;
    
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
    protected $fillable = ['post_id', 'postid', 'package_id', 'payment_method_id', 'transaction_id', 'active'];
    
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
		
		Payment::observe(PaymentObserver::class);
		
		static::addGlobalScope(new StrictActiveScope());
	}
	
    public function getPostTitleHtml()
    {
    	$out = '#' . $this->postid;
        if ($this->post_latest) {
        	$postUrl = url(config('app.locale') . '/' . $this->post_latest->uri);
			$out .= ' | ';
			$out .= '<a href="' . $postUrl . '" target="_blank">' . $this->post_latest->title . '</a>';
        }
        
        return $out;
    }
    
    
    
     public function getC()
    {
     
	   $postss = DB::table('posts')->where('id', $this->postid)->first();
        
       
    
    $user_id = optional($postss)->user_id;
    $userss = DB::table('users')->where('id', $user_id)->first();

    $user_name = optional($userss)->country_code;
        
        return $user_name ;
    }
    
    
     
     public function getUsername()
    {
    //	$out = '#' . $this->post_id;
       
	   $postss = DB::table('posts')->where('id', $this->postid)->first();
        
      // $posts['user_id'];
     
        
        
   ////      $user = DB::table('users')->where('id', $posts->user_id)
	   //     ->first();
	   
	   //$u = $posts->user_id;
        
       // $array = get_object_vars($posts);
       
       
   //    $post = Post::where('id' , $this->post_id)->first();
   
   $user_id = optional($postss)->user_id;
    $userss = DB::table('users')->where('id', $user_id)->first();

    $user_name = optional($userss)->username;
        
        return $user_name ;
    }
    
    
    public function getPackageNameHtml()
    {
        if (!empty($this->package)) {
            return $this->package->name . ' (' . $this->package->price . ' ' . $this->package->currency_code . ')';
        } else {
            return $this->package_id;
        }
    }

    public function getPaymentMethodNameHtml()
    {
        if (!empty($this->paymentMethod)) {
			if ($this->paymentMethod->name == 'offlinepayment') {
				return trans('offlinepayment::messages.Offline Payment');
			} else {
				return $this->paymentMethod->display_name;
			}
        } else {
            return '--';
        }
    }
    
    public function getReviewedHtml()
    {
        return ajaxCheckboxDisplay($this->{$this->primaryKey}, $this->getTable(), 'previewed', $this->previewed);
    }
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    
    
    public function post_latest()
    {
        return $this->belongsTo(Post::class, 'postid');
    }
    
    
    
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'translation_of')->where('translation_lang', config('app.locale'));
    }
    
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    
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
