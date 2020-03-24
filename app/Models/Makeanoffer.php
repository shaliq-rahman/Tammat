<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makeanoffer extends BaseModel 
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'makeanoffers';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['post_id', 'buyer_product_1', 'buyer_product_2', 'buyer_product_3', 'seller_product_1', 'seller_product_2', 'original_price', 'offer_price', 'description_text', 'buyer_id','seller_id', 'is_read_admin', 'is_read_professional', 'is_read_individual', 'approve_seller', 'approve_buyer', 'approve_admin', 'status', 'offer_parent','offer_maker_id'];

    public function pictures()
    {
        return $this->hasMany(Picture::class, 'post_id')->orderBy('position')->orderBy('id');
    }
    
    
    public function buyer_product_1_object(){
        return $this->belongsTo('App\Models\Post', 'buyer_product_1');
    }
    
    public function buyer_product_2_object(){
        return $this->belongsTo('App\Models\Post', 'buyer_product_2');
    }
    
    public function buyer_product_3_object(){
        return $this->belongsTo('App\Models\Post', 'buyer_product_3');
    }

    public function seller_product_1_object(){
        return $this->belongsTo('App\Models\Post', 'seller_product_1');
    }

    public function seller_product_2_object(){
        return $this->belongsTo('App\Models\Post', 'seller_product_2');
    }
    
    public function post_id_object(){
        return $this->belongsTo('App\Models\Post', 'post_id');
    }
    
	public function buyer_info()
	{
	    return $this->belongsTo('App\Models\User', 'buyer_id');
	}
	
	public function seller_info()
	{
	      return $this->belongsTo('App\Models\User', 'seller_id');
	}
	
    


    
}
