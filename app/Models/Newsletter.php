<?php

// namespace App;
namespace App\Models;


use App\Models\Scopes\ActiveScope;
use Larapen\Admin\app\Models\Crud;

class Newsletter extends BaseModel
{
	use Crud;
	
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletter';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
     protected $primaryKey = 'news_letter_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
     protected $fillable = ['news_letter_email',];

}