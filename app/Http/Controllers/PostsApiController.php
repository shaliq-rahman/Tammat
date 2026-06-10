<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Post;
use App\Models\Category;
use App\Models\SavedPost;
use App\Models\User;

class PostsApiController extends Controller
{
	
	public function getArchivedPosts_app(Request $request)
    {
		//dd($request);
     $user_id = $request->user_id;
     //  $user_id =1063;
     // Archived Posts
      $archivedPosts = Post::where('user_id', $user_id)
      ->archived()
      ->with(['pictures', 'city', 'latestPayment' => function ($builder) { $builder->with(['package']); }])
      ->orderByDesc('id')->get();


      $user_id = $request->user_id;
      //  $user_id =1063;
       $favouritePosts = SavedPost::whereHas('post', function($query) {
           // $query->currentCountry();
       })
       ->where('user_id', $user_id)
       ->with(['post.pictures', 'post.city'])
       ->orderByDesc('id')->get();

      
           foreach($archivedPosts as $post){

             $postimg=  $post['pictures'];

            // $post['shrt_img']=  $post['shrt_img']['id'];
               foreach($postimg as  $pictures){

                   $post['shrt_img']=  "https://www.tmmat.com/storage/".$pictures['filename'];

               }

        } 


   
        return response()->json(['results'=>$archivedPosts]);
    }
	
    //
}
