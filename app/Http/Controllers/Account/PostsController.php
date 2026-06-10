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



namespace App\Http\Controllers\Account;



use App\Helpers\Arr;

use App\Helpers\Search;

use App\Http\Controllers\Search\Traits\PreSearchTrait;

use App\Models\Post;

use App\Models\Category;

use App\Models\SavedPost;

use App\Models\SavedUser;

use App\Models\User;

use App\Models\SavedSearch;

use App\Models\Message;

use App\Models\Scopes\ReviewedScope;

use App\Mail\PostDeleted;

use App\Models\Scopes\VerifiedScope;

use Carbon\Carbon;

use Illuminate\Http\Request as HttpRequest;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Request;

use Torann\LaravelMetaTags\Facades\MetaTag;

use DB;

use Illuminate\Support\Facades\Storage;





class PostsController extends AccountBaseController

{

    use PreSearchTrait;



    private $perPage = 12;



    public function __construct()

    {

        parent::__construct();



        $this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;

    }



    /**

     * @param $pagePath

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

     */

    public function getPage($pagePath)

    {



        switch ($pagePath) {

            case 'my-posts':

                return $this->getMyPosts();

                break;

            case 'approved':

                return $this->getApprovedPosts($pagePath);

                break;

            case 'archived':

                return $this->getArchivedPosts($pagePath);

                break;

            case 'rejected':

                return $this->getRejectedPosts($pagePath);

                break;

            case 'favourite':

                return $this->getFavouritePosts();

                break;

			case 'favourite-user':

                return $this->getFavouritePostsUsers();

                break;

            case 'pending-approval':

                return $this->getPendingApprovalPosts();

                break;

            default:

                abort(404);

        }

        



    }



    /**

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */

    public function getMyPosts()

    {

        if(request()->ajax()){

            return Post::where('user_id',auth()->user()->id)->where(function($query) {

                $query->where('title', 'like', '%'.request()->qqq.'%')

                    ->orWhere('description', 'like', '%'.request()->qqq.'%');

            })->get()->pluck('title');

        }

        $data = [];

        if(request()->qqq){

            $data['posts'] = Post::where('user_id',auth()->user()->id)->where(function($query) {

                    $query->where('title', 'like', '%'.request()->qqq.'%')

                        ->orWhere('description', 'like', '%'.request()->qqq.'%');

                })->paginate($this->perPage);

        } else {

            $data['posts'] = Post::where('user_id',auth()->user()->id)->paginate($this->perPage);

        }

        $data['type'] = 'my-posts';

		 $data['pagePath'] = 'my-posts';

		 



        // Meta Tags

        MetaTag::set('title', t('My ads'));

        MetaTag::set('description', t('My ads on :app_name', ['app_name' => config('settings.app.name')]));



        return view('account.posts', $data);

    }





    /**

     * @param $pagePath

     * @param null $postId

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

     */

    public function getApprovedPosts($pagePath, $postId = null)

    {

        // If repost

        if (str_contains(url()->current(), $pagePath . '/' . $postId . '/repost')) {

            $res = false;

            if (is_numeric($postId) and $postId > 0) {

                $res = Post::find($postId)->update([

                    'archived'   => 0,

                    'reviewed'   => 1,

                    'created_at' => Carbon::now(),

                ]);

                

                $query_update =  \DB::table('payments')

                       ->where('post_id', $postId)

                       ->update(['post_id' => '0']);

                

            }

            if (!$res) {

                flash(t("The repost has done successfully."))->success();

            } else {

                flash(t("The repost has failed. Please try again."))->error();

            }



            return redirect()->back();

            

            // return redirect(config('app.locale') . '/account/' . $pagePath);

        }



        $data = [];

        if(request()->ajax()){

            return $this->approvedPosts->whereHas('post', function($query) {

                $query->where('title', 'like', '%'.request()->qqq.'%');

                $query->orWhere('description', 'like', '%'.request()->qqq.'%');

            })->get()->pluck('post.title');

        }

    

        if(request()->qqq){

            $data['posts'] = $this->approvedPosts->where(function($query) {

                    $query->where('title', 'like', '%'.request()->qqq.'%')

                        ->orWhere('description', 'like', '%'.request()->qqq.'%');

                })->paginate($this->perPage);

        } else {

            $data['posts'] = $this->approvedPosts->paginate($this->perPage);

        }



        // Meta Tags

        MetaTag::set('title', t('My active ads'));

        MetaTag::set('description', t('My approved ads on :app_name', ['app_name' => config('settings.app.name')]));



        view()->share('pagePath', $pagePath);



        return view('account.posts', $data);

    }



    /**

     * @param $pagePath

     * @param null $postId

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

     */

    public function getArchivedPosts($pagePath, $postId = null)

    {

        // If repost

        if (str_contains(url()->current(), $pagePath . '/' . $postId . '/repost')) {

            $res = false;

            if (is_numeric($postId) and $postId > 0) {

                $res = Post::find($postId)->update([

                    'archived'   => 0,

                    'reviewed'   => 1,

                    'created_at' => Carbon::now(),

                ]);

                

                $query_update =  \DB::table('payments')

                       ->where('post_id', $postId)

                       ->update(['post_id' => '0']);

                

            }

            if (!$res) {

                flash(t("The repost has done successfully."))->success();

            } else {

                flash(t("The repost has failed. Please try again."))->error();

            }



            return redirect()->back();

            

            // return redirect(config('app.locale') . '/account/' . $pagePath);

        }



        $data = [];

        if(request()->ajax()){

            return $this->archivedPosts->whereHas('post', function($query) {

                $query->where('title', 'like', '%'.request()->qqq.'%');

                $query->orWhere('description', 'like', '%'.request()->qqq.'%');

            })->get()->pluck('post.title');

        }



        if(request()->qqq){

            $data['posts'] = $this->approvedPosts->where(function($query) {

                    $query->where('title', 'like', '%'.request()->qqq.'%')

                        ->orWhere('description', 'like', '%'.request()->qqq.'%');

                })->paginate($this->perPage);



        } else {

            //$data['posts'] = $this->approvedPosts->paginate($this->perPage);

			$data['posts'] = $this->archivedPosts->paginate($this->perPage);

        }



        // Meta Tags

        MetaTag::set('title', t('My archived ads'));

        MetaTag::set('description', t('My archived ads on :app_name', ['app_name' => config('settings.app.name')]));



        view()->share('pagePath', $pagePath);



        return view('account.posts', $data);

    }

    

        /**

     * @param $pagePath

     * @param null $postId

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

     */

    public function getRejectedPosts($pagePath, $postId = null)

    {

        // If repost

        if (str_contains(url()->current(), $pagePath . '/' . $postId . '/repost')) {

            $res = false;

            if (is_numeric($postId) and $postId > 0) {

                $res = Post::find($postId)->update([

                    'is_rejected'   => 0,

                    'reviewed'   => 1,

                    'created_at' => Carbon::now(),

                ]);

                

                $query_update =  \DB::table('payments')

                       ->where('post_id', $postId)

                       ->update(['post_id' => '0']);

                

            }

            if (!$res) {

                flash(t("The repost has done successfully."))->success();

            } else {

                flash(t("The repost has failed. Please try again."))->error();

            }



            return redirect()->back();

            

            // return redirect(config('app.locale') . '/account/' . $pagePath);

        }



        $data = [];

        if(request()->ajax()){

            return $this->rejectedPosts->whereHas('post', function($query) {

                $query->where('title', 'like', '%'.request()->qqq.'%');

                $query->orWhere('description', 'like', '%'.request()->qqq.'%');

            })->get()->pluck('post.title');

        }



        if(request()->qqq){

            $data['posts'] = $this->rejectedPosts->where(function($query) {

                    $query->where('title', 'like', '%'.request()->qqq.'%')

                        ->orWhere('description', 'like', '%'.request()->qqq.'%');

                })->paginate($this->perPage);



        } else {

            $data['posts'] = $this->rejectedPosts->paginate($this->perPage);

        }



        // Meta Tags

        MetaTag::set('title', t('My archived ads'));

        MetaTag::set('description', t('My archived ads on :app_name', ['app_name' => config('settings.app.name')]));



        view()->share('pagePath', $pagePath);



        return view('account.posts', $data);

    }





    /**

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */

    public function getFavouritePosts()

    {

        if(request()->ajax()){

            return $this->favouritePosts->whereHas('post', function($query) {

                $query->where('title', 'like', '%'.request()->qqq.'%');

                $query->orWhere('description', 'like', '%'.request()->qqq.'%');

            })->get()->pluck('post.title');

        }

        $data = [];

        if(request()->qqq){

            $data['posts'] = $this->favouritePosts->where(function($query) {

                    $query->where('title', 'like', '%'.request()->qqq.'%')

                        ->orWhere('description', 'like', '%'.request()->qqq.'%');

                })->paginate($this->perPage);



        } else {

            $data['posts'] = $this->favouritePosts->paginate($this->perPage);

        }

        // Meta Tags

		 $data['pagePath'] = 'favourite';

        MetaTag::set('title', t('My favourite ads'));

        MetaTag::set('description', t('My favourite ads on :app_name', ['app_name' => config('settings.app.name')]));



        return view('account.posts', $data);

    }





 public function getFavouritePostsUsers()

    {

        if(request()->ajax()){

            return $this->favouriteUsers->whereHas('user', function($query) {

                $query->where('username', 'like', '%'.request()->qqq.'%');

                 

            })->get()->pluck('user.username');

        }

        $data = [];

        if(request()->qqq){

            $data['conversations'] = $this->favouriteUsers->where(function($query) {

                    $query->where('username', 'like', '%'.request()->qqq.'%');

                

                })->paginate($this->perPage);



        } else {

            $data['conversations'] = $this->favouriteUsers->whereHas('user', function($query) {

                $query->where('user_id', '=', auth()->user()->id);

                 

            })->paginate($this->perPage);

        }

		// dd($data['posts']);

        // Meta Tags

		 $data['pagePath'] = 'favourite-user';

        MetaTag::set('title', t('My favourite ads'));

        MetaTag::set('description', t('My favourite ads on :app_name', ['app_name' => config('settings.app.name')]));



        return view('account.favusers', $data);

    }

    /**

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */

    public function getPendingApprovalPosts()

    {

        $data = [];

        $data['posts'] = $this->pendingPosts->paginate($this->perPage);

        $data['pagePath'] = 'pending-approval';

        // Meta Tags

        MetaTag::set('title', t('My pending approval ads'));

        MetaTag::set('description', t('My pending approval ads on :app_name', ['app_name' => config('settings.app.name')]));



        return view('account.posts', $data);

    }



    /**

     * @param HttpRequest $request

     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View

     */

    public function getSavedSearch(HttpRequest $request)

    {

        $data = [];



        // Get QueryString

        $tmp = parse_url(url(Request::getRequestUri()));

        $queryString = (isset($tmp['query']) ? $tmp['query'] : 'false');

        $queryString = preg_replace('|\&pag[^=]*=[0-9]*|i', '', $queryString);



        // CATEGORIES COLLECTION

        $cats = Category::trans()->orderBy('lft')->get();

        $cats = collect($cats)->keyBy('translation_of');

        view()->share('cats', $cats);



        // Search

        $savedSearch = SavedSearch::currentCountry()

            ->where('user_id', auth()->user()->id)

            ->orderBy('created_at', 'DESC')

            ->simplePaginate($this->perPage,['*'],'pag');

		

        if (collect($savedSearch->getCollection())->keyBy('query')->keys()->contains($queryString))

        {

            parse_str($queryString, $queryArray);



            // QueryString vars

            $cityId = isset($queryArray['l']) ? $queryArray['l'] : null;

            $location = isset($queryArray['location']) ? $queryArray['location'] : null;

            $adminName = (isset($queryArray['r']) && !isset($queryArray['l'])) ? $queryArray['r'] : null;



            // Pre-Search

            $preSearch = [

                'city'  => $this->getCity($cityId, $location),

                'admin' => $this->getAdmin($adminName),

            ];

			

            if ($savedSearch->getCollection()->count() > 0) {

                // Search

                $search = new Search($preSearch);

                $data = $search->fechAll();

            }

        }

        $data['savedSearch'] = $savedSearch;



        // Meta Tags

        MetaTag::set('title', t('My saved search'));

        MetaTag::set('description', t('My saved search on :app_name', ['app_name' => config('settings.app.name')]));



        view()->share('pagePath', 'saved-search');



        return view('account.saved-search', $data);

    }

	

	/**

	 * @param $pagePath

	 * @param null $id

	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

	 */

	 

	 // delete add favourite

	 public function deletepostfavourite($pagePath, $id = null){

	      $ids = [];

            if (request()->filled('entries')) {

                $ids = request()->input('entries');

            } else {

                if (!is_numeric($id) && $id <= 0) {

                    $ids = [];

                } else {

                    $ids[] = $id;

                }

            }

            

	        $nb = 1;

            

            $savedPosts = SavedPost::where('user_id', auth()->user()->id)->whereIn('post_id', $ids);

            if ($savedPosts->count() > 0) {

                $nb = $savedPosts->delete();

            }

            $count = count($ids);

            if ($count > 1) {

                $message = t("x :entities has been deleted successfully.", ['entities' => t('ads'), 'count' => $count]);

            } else {

                $message = t("1 :entity has been deleted successfully.", ['entity' => t('ad')]);

            }

            flash($message)->success();

            

            if(config('app.locale')=='en')

            return redirect( '/en/account/' . $pagePath);

            else

             return redirect(config('app.locale') . '/account/' . $pagePath);

        

	 }// delete add favourite

	  



     public function deleteuserfavourite($pagePath, $id = null){

	      $ids = [];

            if (request()->filled('entries')) {

                $ids = request()->input('entries');

            } else {

                if (!is_numeric($id) && $id <= 0) {

                    $ids = [];

                } else {

                    $ids[] = $id;

                }

            }

            

	        $nb = 1;

            

            $savedPosts = SavedUser::where('user_id', auth()->user()->id)->whereIn('fav_user_id', $ids);

            if ($savedPosts->count() > 0) {

                $nb = $savedPosts->delete();

            }

            $count = count($ids);

            if ($count > 1) {

                $message = t("x :entities has been deleted successfully.", ['entities' => t('ads'), 'count' => $count]);

            } else {

                $message = t("1 :entity has been deleted successfully.", ['entity' => t('user')]);

            }

            flash($message)->success();

            

            if(config('app.locale')=='en')

            return redirect( '/en/account/' . $pagePath);

            else

             return redirect(config('app.locale') . '/account/' . $pagePath);

        

	 }// delete add favourite

	 

	 

	  

 public function archivepost($pagePath, $id = null){

        $ids = [];

          if (request()->filled('entries')) {

              $ids = request()->input('entries');

          } else {

              if (!is_numeric($id) && $id <= 0) {

                  $ids = [];

              } else {

                  $ids[] = $id;

              }

          }

          

          $nb = 1;

          

         // $savedPosts = SavedPost::where('user_id', auth()->user()->id)->whereIn('post_id', $ids);

          $savedPosts = Post::where('user_id', auth()->user()->id)->whereIn('id', $ids)->update([

            'archived'   => 1,                     

           // 'created_at' => Carbon::now(),

        ]);

          

          $count = count($ids);

          if ($count > 1) {

              $message = t("x :entities has been archived successfully.", ['entities' => t('ads'), 'count' => $count]);

          } else {

              $message = t("1 :entity has been archived successfully.", ['entity' => t('ad')]);

          }

          flash($message)->success();

          

          if(config('app.locale')=='en')

          return redirect( '/en/account/' . $pagePath);

          else

           return redirect(config('app.locale') . '/account/' . $pagePath);

      

   }



    

    

	 

	 

    public function destroy($pagePath, $id = null)

    {

if ($pagePath == 'favourite') {

  return $this->deletepostfavourite($pagePath, $id = null);

}else{



        // Get Entries ID

        $ids = [];

        if (request()->filled('entries')) {

            $ids = request()->input('entries');

        } else {

            if (!is_numeric($id) && $id <= 0) {

                $ids = [];

            } else {

                $ids[] = $id;

				

				

            }

        }





        

		

		

      //  Post::where('user_id',  auth()->user()->id)->whereIn('id', $ids)->update(['archived' => 1]);

		

		

         $savedPosts = Post::where('user_id', auth()->user()->id)->whereIn('id', $ids)->delete();

         $pictures= \DB::table('pictures')->whereIn('post_id', $ids)->delete();

		 

				

					



        // Delete

    // $nb = 1;

    //     if ($pagePath == 'favourite') {

    //         $savedPosts = SavedPost::where('user_id', auth()->user()->id)->whereIn('post_id', $ids);

    //         if ($savedPosts->count() > 0) {

    //             $nb = $savedPosts->delete();

    //         }

    //     } elseif ($pagePath == 'saved-search') {

    //         $nb = SavedSearch::destroy($ids);

    //     } else {

    //         foreach ($ids as $item) {

    //             $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($item);

    //             if (!empty($post)) {

    //                 $tmpPost = Arr::toObject($post->toArray());



    //                 // Delete Entry

    //                 $nb = $post->delete();



    //                 // Send an Email confirmation

				// 	if (!empty($tmpPost->email)) {

				// 		try {

				// 			Mail::send(new PostDeleted($tmpPost));

				// 		} catch (\Exception $e) {

				// 			flash($e->getMessage())->error();

				// 		}

				// 	}

    //             }

    //         }

    //     }



        // Confirmation

        // if ($nb == 0) {

            // flash(t("No deletion is done. Please try again."))->error();

        // } else {

            $count = count($ids);

            if ($count > 1) {

                $message = t("x :entities has been deleted successfully.", ['entities' => t('ads'), 'count' => $count]);

            } else {

                $message = t("1 :entity has been deleted successfully.", ['entity' => t('ad')]);

            }

            flash($message)->success();

        // }



        return redirect(config('app.locale') . '/account/' . $pagePath);





    }

    }

    

    

    

    

    public function destroypost($pagePath, $id = null)

    {

		//return $id;

        // Get Entries ID

        $ids = [];

        if (request()->filled('entries')) {

            $ids = request()->input('entries');

        } else {

            if (!is_numeric($id) && $id <= 0) {

                $ids = [];

            } else {

                $ids[] = $id;

            }

        }





        // Delete

        $nb = 0;

        if ($pagePath == 'favourite') {

            $savedPosts = SavedPost::where('user_id', auth()->user()->id)->whereIn('post_id', $ids);

            if ($savedPosts->count() > 0) {

                $nb = $savedPosts->delete();

            }

        } elseif ($pagePath == 'saved-search') {

            $nb = SavedSearch::destroy($ids);

        } else {



            foreach ($ids as $item) {

                $post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($item);

                if (!empty($post)) {

                    

                    $query_update =  \DB::table('payments')

                       ->where('post_id', $item)

                       ->delete();

                    

                    $tmpPost = Arr::toObject($post->toArray());
                   //  dd($tmpPost);
                    $X= new PostDeleted($tmpPost,2);
                  //  dd($X);
                  

                    // Delete Entry

                    
                    \Mail::send(new PostDeleted($tmpPost,2));


                    // Send an Email confirmation

					if (!empty($tmpPost->email)) {

                      
						try {

							\Mail::send(new PostDeleted($tmpPost,2)); 

						} catch (\Exception $e) {

							flash($e->getMessage())->error();

						}


   
                        $from_email = 'admin@tmmat.com';

                        $fromname = 'Tammat';
        
                        $postvalue = Post::find($item); 
        
                        $uservalue = User::find($postvalue->user_id);
        
                        $data['post'] = $postvalue;
        
                        $data['user'] = $uservalue;
        
                        $toemail = $uservalue->email; 


                        $flag = 1;                         
                        $user = $uservalue;
                       

        
                        \Mail::send('emails.post.deleted',compact('flag','user','post'), function($message) use ($from_email, $fromname, $toemail)
        
                        {
        
                            $message->to($toemail);
        
                            $message->subject('Your Post is Deleted');
        
                            $message->from($from_email, $fromname);
        
                            $message->replyTo($from_email, $fromname);        
        
                            
        
                        }); 
                        
                        

                        \Mail::send('emails.post.reviewed', $data, function($message) use ($from_email, $fromname, $toemail)
        
                        {
        
                            $message->to($toemail);
        
                            $message->subject('Your Post is Deleted');
        
                            $message->from($from_email, $fromname);
        
                            $message->replyTo($from_email, $fromname);        
        
                            
        
                        }); 
        



















                        
					}

					

					

					

					$pictures= \DB::table('pictures')->where('post_id',$item)->get();

					

					foreach($pictures as $pich){

						

						

					 Storage::delete($pich->filename);					

					  $query_update =  \DB::table('pictures')

                       ->where('id', $pich->id)

                       ->delete();

					   

						}

					

					

					

					

					

					

					$nb = $post->delete();

                }

            }

        }



        // Confirmation

        if ($nb == 0) {

            flash(t("No deletion is done. Please try again."))->error();

        } else {

            $count = count($ids);

            if ($count > 1) {

                $message = t("x :entities has been deleted successfully.", ['entities' => t('ads'), 'count' => $count]);

            } else {

                $message = t("1 :entity has been deleted successfully.", ['entity' => t('ad')]);

            }

            flash($message)->success();

        }





        if($pagePath  == 'my-posts')

        {

             return redirect(config('app.locale') . '/account/' . $pagePath);    

        }

        else

        {

            return redirect()->back();    

        }

        

    }

    

    

    

    

    public function DeliveryPost(HttpRequest $request)

    {

        

        $getUserinfo = User::find(auth()->user()->id);

        $country_code = $getUserinfo->country_code;

        

        

        $getdetail = \DB::table('delivery_email')

           ->select('*')

           ->where('country_code','=',$country_code)

           ->first();

       

           if(!empty($getdetail->email))

           {

               $toemail = $getdetail->email;

           }

           else

           {

               $toemail = 'tmmatdelivery@gmail.com';

           }

           

       

        $Messagevalue = Message::find($request->message_id);

        

        

        

        

    	if(!empty($Messagevalue->delivery_info))

	    {

	        

	        if(!empty($Messagevalue->offer_id))

	        {

                $select_query = DB::table('makeanoffers')

                ->select("makeanoffers.*",

                        DB::raw("(SELECT title FROM posts WHERE  posts.id  = makeanoffers.buyer_product_1) as buyer_product_1_title"),

                        DB::raw("(SELECT title FROM posts WHERE  posts.id  = makeanoffers.buyer_product_2) as buyer_product_2_title"),

                        DB::raw("(SELECT title FROM posts WHERE  posts.id  = makeanoffers.buyer_product_3) as buyer_product_3_title"),

                        DB::raw("(SELECT title FROM posts WHERE  posts.id  = makeanoffers.seller_product_1) as seller_product_1_title"),

                        DB::raw("(SELECT title FROM posts WHERE  posts.id  = makeanoffers.seller_product_2) as seller_product_2_title")

                        )

                       ->where('id','=', $Messagevalue->offer_id)

                       ->first();

                       

                $data['buyer_product_1_title']  = !empty($select_query->buyer_product_1_title)?$select_query->buyer_product_1_title:'';

                $data['buyer_product_2_title']  = !empty($select_query->buyer_product_2_title)?$select_query->buyer_product_2_title:'';

                $data['buyer_product_3_title']  = !empty($select_query->buyer_product_3_title)?$select_query->buyer_product_3_title:'';

            

                

            

                $data['buyer_product_1_title_url']  = !empty($select_query->buyer_product_1_title)?url(slugify($select_query->buyer_product_1_title).'/'.$select_query->buyer_product_1):'';

                $data['buyer_product_2_title_url']  = !empty($select_query->buyer_product_2_title)?url(slugify($select_query->buyer_product_2_title).'/'.$select_query->buyer_product_2):'';

                $data['buyer_product_3_title_url']  = !empty($select_query->buyer_product_3_title)?url(slugify($select_query->buyer_product_3_title).'/'.$select_query->buyer_product_3):'';

            

            

            

                $data['seller_product_1_title']  = !empty($select_query->seller_product_1_title)?$select_query->seller_product_1_title:'';

                $data['seller_product_2_title']  = !empty($select_query->seller_product_2_title)?$select_query->seller_product_2_title:'';

                

                $data['seller_product_1_title_url']  = !empty($select_query->seller_product_1_title)?url(slugify($select_query->seller_product_1_title).'/'.$select_query->seller_product_1):'';

                $data['seller_product_2_title_url']  = !empty($select_query->seller_product_2_title)?url(slugify($select_query->seller_product_2_title).'/'.$select_query->seller_product_2):'';

            

                

                

                $data['buyer_offer_price']  = !empty($select_query->offer_price)?$select_query->offer_price:'';

                $data['seller_price']       = !empty($select_query->original_price)?$select_query->original_price:'';

                

	        }

	    }

	    

	    

	    $data['seller_product_title_url'] = !empty($request->postsubject)?url(slugify($request->postsubject).'/'.$Messagevalue->post_id):'';

	    

        $data['message_id'] = $request->message_id;

        $data['dateofpick'] = $request->dateofpick;

        $data['buyername'] = $request->buyername;

        $data['buyerphone'] = $request->phone;

        $data['buyer_address'] = $request->buyer_address;

        

        $data['message_string'] = $request->message;

        $data['postsubject'] = $request->postsubject;

        $data['sellerusername'] = $request->sellerusername;

        $data['seller_address'] = $request->seller_address;

        $data['DateTimePreference'] = $Messagevalue->date_time;



        

        $responce = \DB::table('delivery')->insert(

            ['message_id'   => $request->message_id, 

             'dateofpick'   => !empty($request->dateofpick)?$request->dateofpick:'',

             'buyername'    =>  !empty($request->buyername)?$request->buyername:'',

             'buyerphone'   =>    !empty($request->phone)?$request->phone:'',

             'buyer_address'    => !empty($request->buyer_address)?$request->buyer_address:'',

             'seller_address'   => !empty($request->seller_address)?$request->seller_address:'',

             'sellerusername'   => !empty($request->sellerusername)?$request->sellerusername:'',

             'message'          => !empty($request->message)?$request->message:'',

             'created_at'       => date('Y-m-d H:i:s'),

             'updated_at'       => date('Y-m-d H:i:s'),

            ]

        );

        

        $buyername = $request->buyername;

        $from_email =$Messagevalue->from_email;

         

        Mail::send('emails.post.delivery', $data, function($message) use ($buyername, $from_email, $toemail)

        {

            $message->to($toemail);

            $message->subject('Request a Delivery');

            if(!empty($from_email))

            {

                $message->replyTo($from_email, $buyername);        

            }

        });    

        

        return redirect()->back()->with('success',t('Message successfully sent'));

        

    }

    

}

