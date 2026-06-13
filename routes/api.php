<?php



// https://www.dealnotdeal.com/en/countries  redirect in homepage to country location in this function setCountryParameters()



	 Route::get('getCountries', [\App\Http\Controllers\HomeController::class, 'countries_app']);

	 Route::post('getLanguages', [\App\Http\Controllers\HomeController::class, 'language_app']);

	 Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login_app']);

	 Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register_app']);

	 Route::post('valid_mobile', [\App\Http\Controllers\Auth\RegisterController::class, 'valid_mobile']);

   Route::post('account/close', [\App\Http\Controllers\Auth\LoginController::class, 'submit_app']);//check that



   Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail_app']);





  Route::post('approve/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register_approve_app']);//check that


  Route::get('getAllPosts', [\App\Http\Controllers\HomeController::class, 'getAllPosts_app']);//ok it's done

  //Route::post('getAllPosts', 'HomeController@getLatestPosts_app');//ok it's done

  Route::post('getLatestPosts', [\App\Http\Controllers\HomeController::class, 'getLatestPosts_app']);//please check 27-8-2020

  Route::post('getLatestPostsHomepage', [\App\Http\Controllers\HomeController::class, 'getLatestPostsHomepage']);

  Route::post('getLatestPostsByCategory', [\App\Http\Controllers\HomeController::class, 'getLatestPosts_appByCategory']);//please check 27-8-2020

  Route::post('getActivePostsByUser', [\App\Http\Controllers\HomeController::class, 'getActivePostsByUser']);//please check 27-8-2020



 // Route::post('getCategories', 'HomeController@getCategories_app');//check that 27-8-2020

 Route::post('getMainCategories', [\App\Http\Controllers\HomeController::class, 'GetMainCat_app']);//check that 11-7-2022 return main catogries only

 Route::post('getSubCategoriesNew', [\App\Http\Controllers\HomeController::class, 'getSubCategoriesNew_app']);//check that 11-7-2022 return main catogries only



 Route::post('getCategories', [\App\Http\Controllers\HomeController::class, 'getCat_app']);//check that 27-8-2020

 Route::post('getCat', [\App\Http\Controllers\HomeController::class, 'getCat_app']);//check that 27-8-2020

 Route::post('getCategoriesProductsCounts', [\App\Http\Controllers\HomeController::class, 'cat_Product_count_app']);//check that 27-8-2020



  Route::post('getSubCategories', [\App\Http\Controllers\HomeController::class, 'getSubcategory_app']);//check that 27-8-2020

  Route::post('getLocationSearch', [\App\Http\Controllers\HomeController::class, 'getLocations_app']);//check that 27-8-2020

  Route::post('getSponsoredPosts', [\App\Http\Controllers\HomeController::class, 'getSponsoredPosts_app']);//check that 27-8-2020

  Route::post('getPopularPosts', [\App\Http\Controllers\HomeController::class, 'getPopularPosts_post']);//check that 27-8-2020

  Route::post('customsearch', [\App\Http\Controllers\HomeController::class, 'customsearch_app']);//check that 27-8-2020

  //Route::post('customsearch', 'HomeController@customsearch_new_app');//check that 27-8-2020

  //Route::post('customsearch_new', 'HomeController@customsearch_new_app');//check that 27-8-2020

  Route::post('customsearch_new', [\App\Http\Controllers\HomeController::class, 'customsearch_app']);//check that 27-8-2020

  Route::post('getdetails', [\App\Http\Controllers\HomeController::class, 'getdetails_new']);//check 25-8-2020

  Route::post('nextSubcatProduct', [\App\Http\Controllers\HomeController::class, 'nextSubcatProduct_app']);//check that 27-8-2020

  Route::post('PageDetail', [\App\Http\Controllers\PageController::class, 'PageDetail']);//check that 2-9-2020

  Route::get('terms', [\App\Http\Controllers\PageController::class, 'termsConditionsApp']);//check that 2-9-2020

  Route::post('PostContact', [\App\Http\Controllers\PageController::class, 'PostContact']);//check that 2-9-2020




  // Route::post('account/archived', 'PostsApiController@getArchivedPosts_app');//check 31-8-2020





    //Route::post('customsearch', 'HomeController@customsearch_app');//check that 27-8-2020

    //userid 1063 FrontController

  	// POSTS






    Route::group(['namespace' => 'Account'], function ($router) {

       $router->pattern('userid', '[0-9]+');

       $router->pattern('id', '[0-9]+');

       $router->pattern('slug', '.*');

       $router->pattern('slug', '^(?=.*)((?!\/).)*$');



      Route::post('account/makeanoffers', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'index']);//check that 2-9-2020 still in postman

	    Route::post('rechargePoints', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'rechargePoints']);

	    Route::post('makeOffersNotDeal', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'makeOffersNotDeal']);



      Route::post('deleteOffer', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'destroy']);



	   Route::post('makeOffersDeal', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'makeOffersDeal']);//check that 2-9-2020 still in postman

	   Route::post('makeanoffers/store', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'store']);





	   Route::post('RecievedOffers', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'RecievedOffers']);//check that 2-9-2020 still in postman



       Route::post('SentOffers', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'SentOffers']);//check that 2-9-2020 still in postman

       Route::post('account/makeanofferDetail', [\App\Http\Controllers\Account\MakeanoffersappController::class, 'makeanofferDetail']);//check that 2-9-2020 still in postman



       Route::post('getTransactions', [\App\Http\Controllers\Account\TransactionsappController::class, 'getTransactions']);//check that 2-9-2020

       Route::get('account/messages/{id}/reply', [\App\Http\Controllers\Account\ConversationsappController::class, 'reply']);//check 25-8-2020

       //Route::post('account/messages', 'ConversationsappController@index');//check 25-8-2020

       Route::post('account/messages', [\App\Http\Controllers\Account\ConversationsappController::class, 'MessagePosts']);//check 25-8-2020

      // Route::post('account/MessageDetails', 'ConversationsappController@ConversationDetails');//check 25-8-2020

       Route::post('account/MessageDetails', [\App\Http\Controllers\Account\ConversationsappController::class, 'AllMessages']);//check 25-8-2020



       Route::post('account/messages/{id}/delete', [\App\Http\Controllers\Account\ConversationsappController::class, 'destroy']);//check 25-8-2020









      Route::get('search', [\App\Http\Controllers\Account\EditappController::class, 'getSearch_app']);//check 25-8-2020

      Route::post('account', [\App\Http\Controllers\Account\PostsappController::class, 'updateDetails']);//check 25-8-2020




      Route::post('leftMenuInfoApp', [\App\Http\Controllers\Account\PostsappController::class, 'leftMenuInfoApp']);//check 25-8-2020



      Route::post('PhoneVerify', [\App\Http\Controllers\Account\PostsappController::class, 'phone_verification']);//check 25-8-2020

      Route::post('ChangePassword', [\App\Http\Controllers\Account\PostsappController::class, 'ChangePassword']);//check 25-8-2020

      Route::post('CancelAccount', [\App\Http\Controllers\Account\PostsappController::class, 'cancel_account_app']);//check 25-8-2020

      Route::post('account/my-posts-old', [\App\Http\Controllers\Account\PostsappController::class, 'getMyPosts']);//check 31-8-2020

      Route::post('account/my-posts', [\App\Http\Controllers\Account\PostsappController::class, 'getMyPosts_app']);//check 31-8-2020



       Route::post('account/archived', [\App\Http\Controllers\Account\PostsappController::class, 'getArchivedPosts_app']);//check 31-8-2020





	     Route::post('account/myads', [\App\Http\Controllers\Account\PostsappController::class, 'getmyads']);//check 1-8-2022

       Route::post('account/getUserAds', [\App\Http\Controllers\Account\PostsappController::class, 'getUserAds']);//check 1-8-2022







      Route::post('account/rejected', [\App\Http\Controllers\Account\PostsappController::class, 'getRejectedPosts_app']);//check 31-8-2020

      Route::post('account/banners', [\App\Http\Controllers\Account\PostsappController::class, 'get_banners_app']);//check 31-8-2020





      Route::post('account/pending-approval', [\App\Http\Controllers\Account\PostsappController::class, 'getPendingApprovalPosts_app']);//check 31-8-2020

      Route::get('/account/pending-approval/{id}/deletepost', [\App\Http\Controllers\Account\PostsappController::class, 'destroypost']);//check that 2-9-2020



	    Route::post('account/favourite', [\App\Http\Controllers\Account\PostsappController::class, 'getFavouritePosts_app']);//check 31-8-2020

      Route::post('account/favourite_users', [\App\Http\Controllers\Account\PostsappController::class, 'getFavourite_users_app']);//check 31-8-2020

      Route::post('/account/favourite/{id}/deletefavpost', [\App\Http\Controllers\Account\PostsappController::class, 'deletepostfavourite']);//check that 2-9-2020

      Route::post('/account/deleteAllpostfavourite', [\App\Http\Controllers\Account\PostsappController::class, 'deleteAllpostfavourite']);//check that 2-9-2020

      Route::post('/account/deleteAllfavouriteUsers', [\App\Http\Controllers\Account\PostsappController::class, 'deleteAllfavouriteUsers']);//check that 2-9-2020

	    Route::post('account/notification', [\App\Http\Controllers\Account\PostsappController::class, 'get_notification_app']);//check that 2-9-2020



	  Route::get('account/{pagePath}/{id}/deleteuserfavourite', [\App\Http\Controllers\Account\PostsController::class, 'deleteuserfavourite']);

	  Route::get('account/{pagePath}/{id}/deleteuserfavourite', [\App\Http\Controllers\Account\PostsController::class, 'deleteuserfavourite']);





      Route::get('/account/my-posts/{id}/deletepost', [\App\Http\Controllers\Account\PostsappController::class, 'destroypost']);//check that 2-9-2020

      Route::get('/account/archived/{id}/deletepost', [\App\Http\Controllers\Account\PostsappController::class, 'destroypost']);//check that 2-9-2020

      Route::get('/account/archived/{id}/repost', [\App\Http\Controllers\Account\PostsappController::class, 'unarchivepost']);//check that 2-9-2020

      Route::get('/account/archived/{id}/archivepost', [\App\Http\Controllers\Account\PostsappController::class, 'archivepost']);//check that 2-9-2020

      Route::post('/account/updatePostVisit', [\App\Http\Controllers\Account\PostsappController::class, 'updatePostVisit']);//check that 2-9-2020





    });




       Route::post('save/fav_post', [\App\Http\Controllers\Ajax\PostController::class, 'savePost_app']);

       Route::post('GetPackages', [\App\Http\Controllers\Ajax\PostController::class, 'GetPackages']);

	   Route::post('AccountDetails', [\App\Http\Controllers\Ajax\PostController::class, 'AccountDetails']);//check 25-8-2020

	   Route::post('GetMenuCounts', [\App\Http\Controllers\Ajax\PostController::class, 'GetMenuCounts']);

	   Route::post('GetUserPackagesHistory', [\App\Http\Controllers\Ajax\PostController::class, 'GetUserPackagesHistory']);

     Route::post('/save/FavUser', [\App\Http\Controllers\Ajax\PostController::class, 'saveFavUser']);



    	// POSTS

      Route::group(['namespace' => 'Post'], function ($router) {

        $router->pattern('id', '[0-9]+');

        // $router->pattern('slug', '.*');

        $router->pattern('slug', '^(?=.*)((?!\/).)*$');

        Route::post('save/post', [\App\Http\Controllers\Post\CreateController::class, 'postForm_app']);//check that

        Route::post('edit/post', [\App\Http\Controllers\Post\CreateController::class, 'UpdatPostForm_app']);//check that



        Route::post('posts/{postid}/edit', [\App\Http\Controllers\Post\EditappController::class, 'postForm']);//check that

		//Route::post('GetPackages/', 'CreateController@GetPackages');//check that

        //postForm_app check that in paymentcontroller this for payment

        Route::get('details/{id}/{country}/{lang}/{user_id}', [\App\Http\Controllers\Post\DetailsappController::class, 'index']);//check 25-8-2020

        Route::get('customfields/{id}/{languageCode}', [\App\Http\Controllers\Post\DetailsappController::class, 'index_custom']);//check that 2-9-2020

        Route::post('/posts/contact', [\App\Http\Controllers\Post\DetailsappController::class, 'sendMessage_app']);//check that 2-9-2020

        Route::post('/posts/create/photos', [\App\Http\Controllers\Post\PhotoController::class, 'postForm_app']);//check that 2-9-2020

        Route::post('/posts/create/{postIdOrToken}/photos/{pictureId}/delete', [\App\Http\Controllers\Post\PhotoController::class, 'delete_app']);

		Route::post('SaveRechargePoints', [\App\Http\Controllers\Post\CreateController::class, 'postFormApp']);

		Route::get('post/hesabe-success-app', [\App\Http\Controllers\Post\CreateController::class, 'HesabeSuccess_app']);

		Route::get('post/hesabe-cancel-app', [\App\Http\Controllers\Post\CreateController::class, 'HesabeCancel_app']);





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

             Route::post('category/custom-fields', [\App\Http\Controllers\Ajax\CategoryController::class, 'getCustomFieldsApp']);

             Route::post('account/notifications', [\App\Http\Controllers\Ajax\ConversationController::class, 'checkNewMessages_app']);//check that

           });




           Route::group(['namespace' => 'Ajax'], function ($router) {

             Route::post('category/custom-fields-new', [\App\Http\Controllers\Ajax\CategoryController::class, 'getCustomFieldsNewApp']);

           });




           Route::group(['namespace' => 'Ajax'], function ($router) {

            Route::post('category/custom-fields_old', [\App\Http\Controllers\Ajax\CategoryController::class, 'getCustomFields_app']);

          });




?>
