<?php



// https://www.dealnotdeal.com/en/countries  redirect in homepage to country location in this function setCountryParameters()

    

	 Route::get('getCountries', 'HomeController@countries_app'); 

	 Route::post('getLanguages', 'HomeController@language_app'); 

	 Route::post('login', 'Auth\LoginController@login_app'); 

	 Route::post('register', 'Auth\RegisterController@register_app'); 

	 Route::post('valid_mobile', 'Auth\RegisterController@valid_mobile'); 

   Route::post('account/close', 'Auth\LoginController@submit_app');//check that  



   Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail_app');

 

   

  Route::post('approve/register', 'Auth\RegisterController@register_approve_app');//check that  
 

  Route::get('getAllPosts', 'HomeController@getAllPosts_app');//ok it's done 

  //Route::post('getAllPosts', 'HomeController@getLatestPosts_app');//ok it's done 

  Route::post('getLatestPosts', 'HomeController@getLatestPosts_app');//please check 27-8-2020

  Route::post('getLatestPostsHomepage', 'HomeController@getLatestPostsHomepage');

  Route::post('getLatestPostsByCategory', 'HomeController@getLatestPosts_appByCategory');//please check 27-8-2020

  Route::post('getActivePostsByUser', 'HomeController@getActivePostsByUser');//please check 27-8-2020

 

 // Route::post('getCategories', 'HomeController@getCategories_app');//check that 27-8-2020 

 Route::post('getMainCategories', 'HomeController@GetMainCat_app');//check that 11-7-2022 return main catogries only

 Route::post('getSubCategoriesNew', 'HomeController@getSubCategoriesNew_app');//check that 11-7-2022 return main catogries only 

 

 Route::post('getCategories', 'HomeController@getCat_app');//check that 27-8-2020

 Route::post('getCat', 'HomeController@getCat_app');//check that 27-8-2020

 Route::post('getCategoriesProductsCounts', 'HomeController@cat_Product_count_app');//check that 27-8-2020



  Route::post('getSubCategories', 'HomeController@getSubcategory_app');//check that 27-8-2020  

  Route::post('getLocationSearch', 'HomeController@getLocations_app');//check that 27-8-2020

  Route::post('getSponsoredPosts', 'HomeController@getSponsoredPosts_app');//check that 27-8-2020

  Route::post('getPopularPosts', 'HomeController@getPopularPosts_post');//check that 27-8-2020

  Route::post('customsearch', 'HomeController@customsearch_app');//check that 27-8-2020

  //Route::post('customsearch', 'HomeController@customsearch_new_app');//check that 27-8-2020

  //Route::post('customsearch_new', 'HomeController@customsearch_new_app');//check that 27-8-2020

  Route::post('customsearch_new', 'HomeController@customsearch_app');//check that 27-8-2020

  Route::post('getdetails', 'HomeController@getdetails_new');//check 25-8-2020

  Route::post('nextSubcatProduct', 'HomeController@nextSubcatProduct_app');//check that 27-8-2020

  Route::post('PageDetail', 'PageController@PageDetail');//check that 2-9-2020

  Route::get('terms', 'PageController@termsConditionsApp');//check that 2-9-2020

  Route::post('PostContact', 'PageController@PostContact');//check that 2-9-2020



 

  // Route::post('account/archived', 'PostsApiController@getArchivedPosts_app');//check 31-8-2020

  

 

    //Route::post('customsearch', 'HomeController@customsearch_app');//check that 27-8-2020

    //userid 1063 FrontController

  	// POSTS  



  

    

    Route::group(['namespace' => 'Account'], function ($router) {

       $router->pattern('userid', '[0-9]+');

       $router->pattern('id', '[0-9]+');

       $router->pattern('slug', '.*');

       $router->pattern('slug', '^(?=.*)((?!\/).)*$');	



      Route::post('account/makeanoffers', 'MakeanoffersappController@index');//check that 2-9-2020 still in postman

	    Route::post('rechargePoints', 'MakeanoffersappController@rechargePoints');

	    Route::post('makeOffersNotDeal', 'MakeanoffersappController@makeOffersNotDeal');

      

      Route::post('deleteOffer', 'MakeanoffersappController@destroy');

	   

	   Route::post('makeOffersDeal', 'MakeanoffersappController@makeOffersDeal');//check that 2-9-2020 still in postman

	   Route::post('makeanoffers/store', 'MakeanoffersappController@store');

	  

	   

	   Route::post('RecievedOffers', 'MakeanoffersappController@RecievedOffers');//check that 2-9-2020 still in postman

	  

       Route::post('SentOffers', 'MakeanoffersappController@SentOffers');//check that 2-9-2020 still in postman

       Route::post('account/makeanofferDetail', 'MakeanoffersappController@makeanofferDetail');//check that 2-9-2020 still in postman

       

       Route::post('getTransactions', 'TransactionsappController@getTransactions');//check that 2-9-2020

       Route::get('account/messages/{id}/reply', 'ConversationsappController@reply');//check 25-8-2020

       //Route::post('account/messages', 'ConversationsappController@index');//check 25-8-2020

       Route::post('account/messages', 'ConversationsappController@MessagePosts');//check 25-8-2020

      // Route::post('account/MessageDetails', 'ConversationsappController@ConversationDetails');//check 25-8-2020

       Route::post('account/MessageDetails', 'ConversationsappController@AllMessages');//check 25-8-2020



       Route::post('account/messages/{id}/delete', 'ConversationsappController@destroy');//check 25-8-2020

        

       

      
     

      Route::get('search', 'EditappController@getSearch_app');//check 25-8-2020

      Route::post('account', 'PostsappController@updateDetails');//check 25-8-2020

	



      Route::post('leftMenuInfoApp', 'PostsappController@leftMenuInfoApp');//check 25-8-2020



      Route::post('PhoneVerify', 'PostsappController@phone_verification');//check 25-8-2020

      Route::post('ChangePassword', 'PostsappController@ChangePassword');//check 25-8-2020

      Route::post('CancelAccount', 'PostsappController@cancel_account_app');//check 25-8-2020

      Route::post('account/my-posts-old', 'PostsappController@getMyPosts');//check 31-8-2020

      Route::post('account/my-posts', 'PostsappController@getMyPosts_app');//check 31-8-2020     



       Route::post('account/archived', 'PostsappController@getArchivedPosts_app');//check 31-8-2020

	   

	   

	     Route::post('account/myads', 'PostsappController@getmyads');//check 1-8-2022

       Route::post('account/getUserAds', 'PostsappController@getUserAds');//check 1-8-2022

	  

	  

	   

      Route::post('account/rejected', 'PostsappController@getRejectedPosts_app');//check 31-8-2020

      Route::post('account/banners', 'PostsappController@get_banners_app');//check 31-8-2020

      

      

      Route::post('account/pending-approval', 'PostsappController@getPendingApprovalPosts_app');//check 31-8-2020

      Route::get('/account/pending-approval/{id}/deletepost', 'PostsappController@destroypost');//check that 2-9-2020

	  

	    Route::post('account/favourite', 'PostsappController@getFavouritePosts_app');//check 31-8-2020     

      Route::post('account/favourite_users', 'PostsappController@getFavourite_users_app');//check 31-8-2020     

      Route::post('/account/favourite/{id}/deletefavpost', 'PostsappController@deletepostfavourite');//check that 2-9-2020

      Route::post('/account/deleteAllpostfavourite', 'PostsappController@deleteAllpostfavourite');//check that 2-9-2020

      Route::post('/account/deleteAllfavouriteUsers', 'PostsappController@deleteAllfavouriteUsers');//check that 2-9-2020

	    Route::post('account/notification', 'PostsappController@get_notification_app');//check that 2-9-2020

	  

	  Route::get('account/{pagePath}/{id}/deleteuserfavourite', 'PostsController@deleteuserfavourite');

	  Route::get('account/{pagePath}/{id}/deleteuserfavourite', 'PostsController@deleteuserfavourite');

	

	  

      Route::get('/account/my-posts/{id}/deletepost', 'PostsappController@destroypost');//check that 2-9-2020

      Route::get('/account/archived/{id}/deletepost', 'PostsappController@destroypost');//check that 2-9-2020

      Route::get('/account/archived/{id}/repost', 'PostsappController@unarchivepost');//check that 2-9-2020

      Route::get('/account/archived/{id}/archivepost', 'PostsappController@archivepost');//check that 2-9-2020

      Route::post('/account/updatePostVisit', 'PostsappController@updatePostVisit');//check that 2-9-2020

       

      

    });





       Route::post('save/fav_post', 'Ajax\PostController@savePost_app');

       Route::post('GetPackages', 'Ajax\PostController@GetPackages');

	   Route::post('AccountDetails', 'Ajax\PostController@AccountDetails');//check 25-8-2020

	   Route::post('GetMenuCounts', 'Ajax\PostController@GetMenuCounts');

	   Route::post('GetUserPackagesHistory', 'Ajax\PostController@GetUserPackagesHistory');

     Route::post('/save/FavUser', 'Ajax\PostController@saveFavUser');

     

    	// POSTS

      Route::group(['namespace' => 'Post'], function ($router) {

        $router->pattern('id', '[0-9]+');

        // $router->pattern('slug', '.*');

        $router->pattern('slug', '^(?=.*)((?!\/).)*$');			

        Route::post('save/post', 'CreateController@postForm_app');//check that

        Route::post('edit/post', 'CreateController@UpdatPostForm_app');//check that

        

        Route::post('posts/{postid}/edit', 'EditappController@postForm');//check that

		//Route::post('GetPackages/', 'CreateController@GetPackages');//check that

        //postForm_app check that in paymentcontroller this for payment 

        Route::get('details/{id}/{country}/{lang}/{user_id}', 'DetailsappController@index');//check 25-8-2020	

        Route::get('customfields/{id}/{languageCode}', 'DetailsappController@index_custom');//check that 2-9-2020		

        Route::post('/posts/contact', 'DetailsappController@sendMessage_app');//check that 2-9-2020

        Route::post('/posts/create/photos', 'PhotoController@postForm_app');//check that 2-9-2020

        Route::post('/posts/create/{postIdOrToken}/photos/{pictureId}/delete', 'PhotoController@delete_app');

		Route::post('SaveRechargePoints', 'CreateController@postFormApp');

		Route::get('post/hesabe-success-app', 'CreateController@HesabeSuccess_app');

		Route::get('post/hesabe-cancel-app', 'CreateController@HesabeCancel_app');

       

        

     /*account/pending-approval/4044/deletepost



        @GET("/account/my-posts/{id}/deletepost")

     myadddelete(

            @Path("id") Integer id

    );





    @GET("/account/pending-approval/{id}/deletepost")

      pendingdelete(

            @Path("id") Integer id

    );





    @GET("/details/{Postid}/KW/en")

      similar(

            @Path("Postid") String Postid

    );



       

       */ 

        



      });

      





         Route::group(['namespace' => 'Ajax'], function ($router) { 

             Route::post('category/custom-fields', 'CategoryController@getCustomFieldsApp');

             Route::post('account/notifications', 'ConversationController@checkNewMessages_app');//check that

           }); 





           Route::group(['namespace' => 'Ajax'], function ($router) { 

             Route::post('category/custom-fields-new', 'CategoryController@getCustomFieldsNewApp');

           }); 





           Route::group(['namespace' => 'Ajax'], function ($router) { 

            Route::post('category/custom-fields_old', 'CategoryController@getCustomFields_app');

          }); 



   

        

    







?>