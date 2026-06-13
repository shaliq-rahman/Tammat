<?php

/*

|--------------------------------------------------------------------------

| Application Routes

|--------------------------------------------------------------------------

|

| Here is where you can register all of the routes for an application.

| It's a breeze. Simply tell Laravel the URIs it should respond to

| and give it the controller to call when that URI is requested.

|

*/

// https://www.dealnotdeal.com/en/countries  redirect in homepage to country location in this function setCountryParameters()




// Route removed for security reasons




	Route::get('storage/{filename}', function ($filename)

{

    $path = storage_path('public/' . $filename);



    if (!File::exists($path)) {

        abort(404);

    }



    $file = File::get($path);

    $type = File::mimeType($path);



    $response = Response::make($file, 200);

    $response->header("Content-Type", $type);



    return $response;

});



	Route::get('storage/{filename}', function ($filename)

{

    $path = storage_path('public/' . $filename);



    if (!File::exists($path)) {

        abort(404);

    }



    $file = File::get($path);

    $type = File::mimeType($path);



    $response = Response::make($file, 200);

    $response->header("Content-Type", $type);



    return $response;

});




/*

|--------------------------------------------------------------------------

| Upgrading

|--------------------------------------------------------------------------

|

| The upgrading process routes

|

*/

Route::group(['middleware' => ['web']], function () {

	Route::get('upgrade', [\App\Http\Controllers\UpgradeController::class, 'version']);

});



Route::get('/hhclear', function() {

    // dd(DB::table('users')->where('email', 'prof.alolayan@gmail.com')->get());

    // dd(DB::table('users')->select('* where email = prof.alolayan@gmail.com')->get());

});




// Route removed for security reasons




/*

|--------------------------------------------------------------------------

| Installation

|--------------------------------------------------------------------------

|

| The installation process routes

|

*/

Route::group([

	'middleware' => ['web', 'installChecker'],

], function () {

	Route::get('install', [\App\Http\Controllers\InstallController::class, 'starting']);

	Route::get('install/site_info', [\App\Http\Controllers\InstallController::class, 'siteInfo']);

	Route::post('install/site_info', [\App\Http\Controllers\InstallController::class, 'siteInfo']);

	Route::get('install/system_compatibility', [\App\Http\Controllers\InstallController::class, 'systemCompatibility']);

	Route::get('install/database', [\App\Http\Controllers\InstallController::class, 'database']);

	Route::post('install/database', [\App\Http\Controllers\InstallController::class, 'database']);

	Route::get('install/database_import', [\App\Http\Controllers\InstallController::class, 'databaseImport']);

	Route::get('install/cron_jobs', [\App\Http\Controllers\InstallController::class, 'cronJobs']);

	Route::get('install/finish', [\App\Http\Controllers\InstallController::class, 'finish']);

});




/*

|--------------------------------------------------------------------------

| Back-end

|--------------------------------------------------------------------------

|

| The admin panel routes

|

*/

Route::group([

	'middleware' => ['admin'],

	'prefix'     => config('larapen.admin.route_prefix', 'admin'),

], function () {

	// CRUD

	CRUD::resource('advertisings', \App\Http\Controllers\Admin\AdvertisingController::class);

	CRUD::resource('blacklists', \App\Http\Controllers\Admin\BlacklistController::class);

	CRUD::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

	CRUD::resource('categories/{catId}/subcategories', \App\Http\Controllers\Admin\SubCategoryController::class);

	CRUD::resource('categories/{catId}/custom_fields', \App\Http\Controllers\Admin\CategoryFieldController::class);

	CRUD::resource('cities', \App\Http\Controllers\Admin\CityController::class);

	CRUD::resource('countries', \App\Http\Controllers\Admin\CountryController::class);

	CRUD::resource('countries/{countryCode}/cities', \App\Http\Controllers\Admin\CityController::class);

	CRUD::resource('countries/{countryCode}/admins1', \App\Http\Controllers\Admin\SubAdmin1Controller::class);

	CRUD::resource('currencies', \App\Http\Controllers\Admin\CurrencyController::class);

	CRUD::resource('custom_fields', \App\Http\Controllers\Admin\FieldController::class);

	CRUD::resource('custom_fields/{cfId}/options', \App\Http\Controllers\Admin\FieldOptionController::class);

	CRUD::resource('custom_fields/{cfId}/categories', \App\Http\Controllers\Admin\CategoryFieldController::class);

	CRUD::resource('genders', \App\Http\Controllers\Admin\GenderController::class);

	CRUD::resource('homepage', \App\Http\Controllers\Admin\HomeSectionController::class);

	CRUD::resource('admins1/{admin1Code}/cities', \App\Http\Controllers\Admin\CityController::class);

	CRUD::resource('admins1/{admin1Code}/admins2', \App\Http\Controllers\Admin\SubAdmin2Controller::class);

	CRUD::resource('admins2/{admin2Code}/cities', \App\Http\Controllers\Admin\CityController::class);

	CRUD::resource('meta_tags', \App\Http\Controllers\Admin\MetaTagController::class);

	CRUD::resource('packages', \App\Http\Controllers\Admin\PackageController::class);

	CRUD::resource('pages', \App\Http\Controllers\Admin\PageController::class);

	CRUD::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);

	CRUD::resource('payment_methods', \App\Http\Controllers\Admin\PaymentMethodController::class);

	CRUD::resource('pictures', \App\Http\Controllers\Admin\PictureController::class);

	CRUD::resource('posts', \App\Http\Controllers\Admin\PostController::class);

	CRUD::resource('p_types', \App\Http\Controllers\Admin\PostTypeController::class);

	CRUD::resource('report_types', \App\Http\Controllers\Admin\ReportTypeController::class);

	CRUD::resource('time_zones', \App\Http\Controllers\Admin\TimeZoneController::class);

	CRUD::resource('users', \App\Http\Controllers\Admin\UserController::class);

	// CRUD::resource('makeanoffer', \App\Http\Controllers\Admin\MakeanofferController::class);


	// Others

	Route::get('account', [\App\Http\Controllers\Admin\UserController::class, 'account']);


	Route::post('ajax/{table}/{field}', [\App\Http\Controllers\Admin\AjaxController::class, 'saveAjaxRequest']);



	// Actions

	Route::get('actions/clear_cache', [\App\Http\Controllers\Admin\ActionController::class, 'clearCache']);

	Route::get('actions/call_ads_cleaner_command', [\App\Http\Controllers\Admin\ActionController::class, 'callAdsCleanerCommand']);

	Route::post('actions/maintenance_down', [\App\Http\Controllers\Admin\ActionController::class, 'maintenanceDown']);

	Route::get('actions/maintenance_up', [\App\Http\Controllers\Admin\ActionController::class, 'maintenanceUp']);

	Route::get('actions/sync_languages_files', [\App\Http\Controllers\Admin\ActionController::class, 'syncLanguageFilesLines']);

	Route::get('actions/homepage/{action}', [\App\Http\Controllers\Admin\ActionController::class, 'homepage']);



	// Re-send Email or Phone verification message

	Route::get('verify/user/{id}/resend/email', [\App\Http\Controllers\Admin\UserController::class, 'reSendVerificationEmail']);

	Route::get('verify/user/{id}/resend/sms', [\App\Http\Controllers\Admin\UserController::class, 'reSendVerificationSms']);

	Route::get('verify/post/{id}/resend/email', [\App\Http\Controllers\Admin\PostController::class, 'reSendVerificationEmail']);

	Route::get('verify/post/{id}/resend/sms', [\App\Http\Controllers\Admin\PostController::class, 'reSendVerificationSms']);





	Route::get('messagecall', [\App\Http\Controllers\Admin\MessageCallController::class, 'messagecall']);

	Route::get('messagecall/delete/{id}', [\App\Http\Controllers\Admin\MessageCallController::class, 'messagecallDel']);

	Route::get('deliveryemail', [\App\Http\Controllers\Admin\MessageCallController::class, 'deliveryemail']);

	Route::get('add_delivery_email', [\App\Http\Controllers\Admin\MessageCallController::class, 'add_delivery_email']);

	Route::post('post_delivery_email', [\App\Http\Controllers\Admin\MessageCallController::class, 'post_delivery_email']);

	Route::post('post_delivery_email_edit', [\App\Http\Controllers\Admin\MessageCallController::class, 'post_delivery_email_edit']);







   Route::get('edit_delivery_email/{id}', [

        'as' => 'edit_delivery_email',

        'uses' => [\App\Http\Controllers\Admin\MessageCallController::class, 'edit_delivery_email'],

    ]);



    Route::get('delete_delivery_email/{id}', [

        'as' => 'delete_delivery_email',

        'uses' => [\App\Http\Controllers\Admin\MessageCallController::class, 'delete_delivery_email'],

    ]);









	// Plugins

	Route::get('plugins', [\App\Http\Controllers\Admin\PluginController::class, 'index']);

	Route::get('plugins/{plugin}/install', [\App\Http\Controllers\Admin\PluginController::class, 'install']);

	Route::get('plugins/{plugin}/uninstall', [\App\Http\Controllers\Admin\PluginController::class, 'uninstall']);

	Route::get('plugins/{plugin}/delete', [\App\Http\Controllers\Admin\PluginController::class, 'delete']);



// 	advertisings_banner

    Route::get('banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'advertisings_banner']);

    Route::get('add_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'add_banner']);

    Route::post('post_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'post_banner']);

    Route::post('update_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'update_banner']);



    Route::get('edit_banner/{id}', [

        'as' => 'edit_banner',

        'uses' => [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'edit_banner'],

    ]);



    Route::get('delete_banner/{id}', [

        'as' => 'delete_banner',

        'uses' => [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'delete_banner'],

    ]);

    Route::get('/maptest', function () {

    return view('maptest');

});



   // export

   Route::get('/test', function() {

  return File::get(public_path() . '/test/mapnew.html');

});




   Route::get('exportxcl', [\App\Http\Controllers\Admin\UserController::class, 'exportExcel']);

   Route::get('exportexcel', [\App\Http\Controllers\Admin\UserController::class, 'export']);



   Route::get('exportcat', [\App\Http\Controllers\Admin\CategoryController::class, 'export']);



   Route::get('exportxclp', [\App\Http\Controllers\Admin\PostController::class, 'exportExcel']);

   Route::get('exportexcelp', [\App\Http\Controllers\Admin\PostController::class, 'export']);





   Route::get('exportxclf', [\App\Http\Controllers\Admin\FieldController::class, 'exportExcel']);

   Route::get('exportexcelf', [\App\Http\Controllers\Admin\FieldController::class, 'export']);



   Route::get('exportxclfo/{id}', [\App\Http\Controllers\Admin\FieldOptionController::class, 'exportExcel']);

   Route::get('exportexcelfo/{id}', [\App\Http\Controllers\Admin\FieldOptionController::class, 'export']);











    Route::get('category_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'category_banner']);

    Route::get('add_category_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'add_category_banner']);

	Route::post('post_category_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'post_category_banner']);

	Route::post('update_category_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'update_category_banner']);

	 Route::get('edit_category_banner/{id}', [

        'as' => 'edit_category_banner',

        'uses' => [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'edit_category_banner'],

    ]);



     Route::get('delete_category_banner/{id}', [

        'as' => 'delete_category_banner',

        'uses' => [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'delete_category_banner'],

    ]);





    Route::get('side_bar_post_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'side_bar_post_banner']);

    Route::get('add_sidebar_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'add_sidebar_banner']);



    Route::post('post_sidebar_category_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'post_sidebar_category_banner']);

    Route::post('update_sidebar_category_banner', [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'update_sidebar_category_banner']);



    Route::get('delete_sidebar_category_banner/{id}', [

        'as' => 'delete_sidebar_category_banner',

        'uses' => [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'delete_sidebar_category_banner'],

    ]);



    Route::get('edit_sidebar_category_banner/{id}', [

        'as' => 'edit_sidebar_category_banner',

        'uses' => [\App\Http\Controllers\Admin\AdvertisingBannerController::class, 'edit_sidebar_category_banner'],

    ]);




	//newsletter add



    Route::get('newsletter', [\App\Http\Controllers\Admin\UserController::class, 'newsletter']);

    Route::get('download-newsletter', [\App\Http\Controllers\Admin\UserController::class, 'downloadNewsletter']);



    Route::get('newsletter-delete/{id}', [

        'as' => 'newsletter-delete',

        'uses' => [\App\Http\Controllers\Admin\UserController::class, 'DeleteNewsletter'],

    ]);



});




/*

|--------------------------------------------------------------------------

| Front-end

|--------------------------------------------------------------------------

|

| The not translated front-end routes

|

*/

Route::group([

	'middleware' => ['web', 'installChecker'],

], function ($router) {

	// SEO

	Route::get('sitemaps.xml', [\App\Http\Controllers\SitemapsController::class, 'index']);



	// Impersonate (As admin user, login as an another user)

	Route::group(['middleware' => 'auth'], function ($router) {

		Route::impersonate();

	});

});


// Route removed for security reasons




/*

|--------------------------------------------------------------------------

| Front-end

|--------------------------------------------------------------------------

|

| The translated front-end routes

|

*/

Route::group([

	'prefix'     => LaravelLocalization::setLocale(),

	'middleware' => ['locale'],

], function ($router) {

	Route::group(['middleware' => ['web', 'installChecker']], function ($router) {

		// HOMEPAGE

		Route::group(['middleware' => ['httpCache:yes']], function () {

			Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

			 Route::get(LaravelLocalization::transRoute('routes.countries'), [\App\Http\Controllers\CountriesController::class, 'index']);

		});





		Route::get('storage/{filename}', function ($filename)

{

    $path = storage_path('public/' . $filename);



    if (!File::exists($path)) {

        abort(404);

    }



    $file = File::get($path);

    $type = File::mimeType($path);



    $response = Response::make($file, 200);

    $response->header("Content-Type", $type);



    return $response;

});




		Route::get('/andriodCloseAccount', [\App\Http\Controllers\Auth\RegisterController::class, 'showCloseAccount']);

		Route::post('/andriodCloseAccount', [\App\Http\Controllers\Auth\RegisterController::class, 'saveCloseAccount']);



		     /*lang change*/

        Route::get('/switch/{lang}', [\App\Http\Controllers\HomeController::class, 'switchlang']);





        Route::get('expire-post-cron', [\App\Http\Controllers\CronController::class, 'ExpirePost']);



        /*set currency*/

        Route::get('/setCurrency/{currency}', [\App\Http\Controllers\HomeController::class, 'setCurrency']);



        // AUTH

		Route::group(['middleware' => ['guest', 'preventBackHistory']], function () {



            // Registration Routes...

			Route::get(LaravelLocalization::transRoute('routes.register'), [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm']);

			Route::post(LaravelLocalization::transRoute('routes.register'), [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

			Route::get('register/finish', [\App\Http\Controllers\Auth\RegisterController::class, 'finish']);

						Route::get('testfire', [\App\Http\Controllers\Auth\RegisterController::class, 'testfire']);



			// Authentication Routes...

			Route::get(LaravelLocalization::transRoute('routes.login'), [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);

			Route::post(LaravelLocalization::transRoute('routes.login'), [\App\Http\Controllers\Auth\LoginController::class, 'login']);




			Route::post('save-news-letter-email', [\App\Http\Controllers\Auth\LoginController::class, 'savenewsletteremail']);







			// Forgot Password Routes...

			Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm']);

			Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail']);

			Route::post('forgotTest', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'forgotTest_app']);

			// Reset Password using Token

			Route::get('password/token', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showTokenRequestForm']);

			Route::post('password/token', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetToken']);



			// Reset Password using Link (Core Routes...)

			Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm']);

			Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset']);



			// Social Authentication

			Route::get('auth/facebook', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToProvider']);

			Route::get('auth/facebook/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleProviderCallback']);

			Route::get('auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToProvider']);

			Route::get('auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleProviderCallback']);

			Route::get('auth/twitter', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToProvider']);

			Route::get('auth/twitter/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleProviderCallback']);

		});



		// Email Address or Phone Number verification

		$router->pattern('field', 'email|phone');

		Route::get('verify/user/{id}/resend/email', [\App\Http\Controllers\Auth\RegisterController::class, 'reSendVerificationEmail']);

		Route::get('verify/user/{id}/resend/sms', [\App\Http\Controllers\Auth\RegisterController::class, 'reSendVerificationSms']);

		Route::get('verify/user/{field}/{token?}', [\App\Http\Controllers\Auth\RegisterController::class, 'verification']);

		Route::post('verify/user/{field}/{token?}', [\App\Http\Controllers\Auth\RegisterController::class, 'verification']);



		// User Logout

		Route::get(LaravelLocalization::transRoute('routes.logout'), [\App\Http\Controllers\Auth\LoginController::class, 'logout']);





		// POSTS

		Route::group(['namespace' => 'Post'], function ($router) {

			$router->pattern('id', '[0-9]+');

			// $router->pattern('slug', '.*');

			$router->pattern('slug', '^(?=.*)((?!\/).)*$');







			Route::get('post/hesabe-success', [\App\Http\Controllers\Post\CreateController::class, 'HesabeSuccess']);

			Route::get('post/hesabe-cancel', [\App\Http\Controllers\Post\CreateController::class, 'HesabeCancel']);





			Route::get('posts/create/{tmpToken?}', [\App\Http\Controllers\Post\CreateController::class, 'getForm']);

			Route::post('posts/create', [\App\Http\Controllers\Post\CreateController::class, 'postForm']);

			Route::put('posts/create/{tmpToken}', [\App\Http\Controllers\Post\CreateController::class, 'postForm']);

			Route::get('posts/create/{tmpToken}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'getForm']);

			Route::post('posts/create/{tmpToken}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'postForm']);

			Route::post('posts/create/{tmpToken}/photos/{id}/delete', [\App\Http\Controllers\Post\PhotoController::class, 'delete']);

			Route::get('posts/create/{tmpToken}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'getForm']);

			Route::post('posts/create/{tmpToken}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'postForm']);

			Route::get('posts/create/{tmpToken}/finish', [\App\Http\Controllers\Post\CreateController::class, 'finish']);







		    Route::get('posts/create_step1/{tmpToken?}', [\App\Http\Controllers\Post\CreateController::class, 'getForm2']);



			Route::post('posts/create_step1', [\App\Http\Controllers\Post\CreateController::class, 'postForm']);

			Route::put('posts/create_step1/{tmpToken}', [\App\Http\Controllers\Post\CreateController::class, 'postForm']);

			Route::get('posts/create_step1/{tmpToken}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'getForm']);

			Route::post('posts/create_step1/{tmpToken}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'postForm']);

			Route::post('posts/create_step1/{tmpToken}/photos/{id}/delete', [\App\Http\Controllers\Post\PhotoController::class, 'delete']);

			Route::get('posts/create_step1/{tmpToken}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'getForm']);

			Route::post('posts/create_step1/{tmpToken}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'postForm']);

			Route::get('posts/create_step1/{tmpToken}/finish', [\App\Http\Controllers\Post\CreateController::class, 'finish']);



			 Route::get('posts/create_step2/{id}', [\App\Http\Controllers\Post\CreateController::class, 'getForm3']);

			 Route::get('posts/subcats/{id}', [\App\Http\Controllers\Post\CreateController::class, 'getForm6']);





			Route::get('posts/create_step3/finish', [\App\Http\Controllers\Post\CreateController::class, 'afterPaymentWindow']);

			Route::get('posts/create_step3/{id}', [\App\Http\Controllers\Post\CreateController::class, 'getForm4']);

			Route::post('posts/create_step3', [\App\Http\Controllers\Post\CreateController::class, 'postForm']);

			Route::put('posts/create_step3/{tmpToken}', [\App\Http\Controllers\Post\CreateController::class, 'postForm']);

			Route::get('posts/create_step3/{tmpToken}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'getForm']);

			Route::get('posts/post_me', [\App\Http\Controllers\Post\PhotoController::class, 'post_me']);

			Route::post('posts/create_step3/{tmpToken}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'postForm']);

			Route::post('posts/create_step3/{tmpToken}/photos/{id}/delete', [\App\Http\Controllers\Post\PhotoController::class, 'delete']);

			Route::get('posts/create_step3/{tmpToken}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'getForm']);

			Route::post('posts/create_step3/{tmpToken}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'postForm']);

			Route::get('posts/create_step3/{tmpToken}/finish', [\App\Http\Controllers\Post\CreateController::class, 'finish']);









			// Payment Gateway Success & Cancel

			Route::get('posts/create/{tmpToken}/payment/success', [\App\Http\Controllers\Post\PaymentController::class, 'paymentConfirmation']);

			Route::get('posts/create/{tmpToken}/payment/cancel', [\App\Http\Controllers\Post\PaymentController::class, 'paymentCancel']);



			// Email Address or Phone Number verification

			$router->pattern('field', 'email|phone');

			Route::get('verify/post/{id}/resend/email', [\App\Http\Controllers\Post\CreateController::class, 'reSendVerificationEmail']);

			Route::get('verify/post/{id}/resend/sms', [\App\Http\Controllers\Post\CreateController::class, 'reSendVerificationSms']);

			Route::get('verify/post/{field}/{token?}', [\App\Http\Controllers\Post\CreateController::class, 'verification']);

			Route::post('verify/post/{field}/{token?}', [\App\Http\Controllers\Post\CreateController::class, 'verification']);



			Route::group(['middleware' => 'auth'], function ($router) {

				$router->pattern('id', '[0-9]+');



				Route::get('posts/{id}/edit', [\App\Http\Controllers\Post\EditController::class, 'getForm']);

				Route::put('posts/{id}/edit', [\App\Http\Controllers\Post\EditController::class, 'postForm']);

				Route::get('posts/{id}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'getForm']);

				Route::post('posts/{id}/photos', [\App\Http\Controllers\Post\PhotoController::class, 'postForm']);

				Route::post('posts/{token}/photos/{id}/delete', [\App\Http\Controllers\Post\PhotoController::class, 'delete']);

				Route::get('posts/{id}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'getForm']);

				Route::post('posts/{id}/payment', [\App\Http\Controllers\Post\PaymentController::class, 'postForm']);



				// Payment Gateway Success & Cancel

				Route::get('posts/{id}/payment/success', [\App\Http\Controllers\Post\PaymentController::class, 'paymentConfirmation']);

				Route::get('posts/{id}/payment/cancel', [\App\Http\Controllers\Post\PaymentController::class, 'paymentCancel']);

			});



			// Post's Details

			Route::get(LaravelLocalization::transRoute('routes.post'), [\App\Http\Controllers\Post\DetailsController::class, 'index']);



			// Contact Post's Author

			Route::post('posts/{id}/contact', [\App\Http\Controllers\Post\DetailsController::class, 'sendMessage']);

			Route::post('posts/{id}/makeanoffer', [\App\Http\Controllers\Post\DetailsController::class, 'makeAnOffer']);

			// Route::get('makeanoffer', 'MakeanofferController@index');



			// Send report abuse

			Route::get('posts/{id}/report', [\App\Http\Controllers\Post\ReportController::class, 'showReportForm']);

			Route::post('posts/{id}/report', [\App\Http\Controllers\Post\ReportController::class, 'sendReport']);

		});





			Route::get('getrelocating', [\App\Http\Controllers\Ajax\CategoryController::class, 'getrelocating']);



		// ACCOUNT

		Route::group(['middleware' => ['auth', 'bannedUser', 'preventBackHistory'], 'namespace' => 'Account'], function ($router) {

			$router->pattern('id', '[0-9]+');





			// Users

			Route::get('account', [\App\Http\Controllers\Account\EditController::class, 'index']);

			//for Now Change Password

			Route::get('generalSettings', [\App\Http\Controllers\Account\EditController::class, 'generalSettings']);

			Route::group(['middleware' => 'impersonate.protect'], function () {

				Route::put('account', [\App\Http\Controllers\Account\EditController::class, 'updateDetails']);

				Route::put('account/settings', [\App\Http\Controllers\Account\EditController::class, 'updateSettings']);

				Route::put('account/preferences', [\App\Http\Controllers\Account\EditController::class, 'updatePreferences']);

			});

			Route::get('account/close', [\App\Http\Controllers\Account\CloseController::class, 'index']);

			Route::group(['middleware' => 'impersonate.protect'], function () {

				Route::post('account/close', [\App\Http\Controllers\Account\CloseController::class, 'submit']);

			});





			Route::group(['prefix' => 'ajax'], function ($router) {

			Route::post('messages/check', [\App\Http\Controllers\Account\Ajax\ConversationController::class, 'checkNewMessages']);

		});



		Route::post('ajax/messages/check', [\App\Http\Controllers\Account\Ajax\ConversationController::class, 'checkNewMessages']);



			// Posts

			Route::get('account/saved-search', [\App\Http\Controllers\Account\PostsController::class, 'getSavedSearch']);

			$router->pattern('pagePath', '(my-posts|archived|favourite|favourite-user|pending-approval|saved-search|rejected|approved)+');

			Route::get('account/{pagePath}', [\App\Http\Controllers\Account\PostsController::class, 'getPage']);

			Route::get('account/{pagePath}/{id}/repost', [\App\Http\Controllers\Account\PostsController::class, 'getArchivedPosts']);

			Route::get('account/{pagePath}/{id}/archivepost', [\App\Http\Controllers\Account\PostsController::class, 'archivepost']);//check that 2-9-2020

			Route::get('account/{pagePath}/{id}/delete', [\App\Http\Controllers\Account\PostsController::class, 'destroy']);

			Route::get('account/{pagePath}/{id}/deletepost', [\App\Http\Controllers\Account\PostsController::class, 'destroypost']);





			Route::get('account/{pagePath}/{id}/deletepostfavourite', [\App\Http\Controllers\Account\PostsController::class, 'deletepostfavourite']);

			Route::get('account/{pagePath}/{id}/deleteuserfavourite', [\App\Http\Controllers\Account\PostsController::class, 'deleteuserfavourite']);







			//export









			Route::post('delivery_post', [\App\Http\Controllers\Account\PostsController::class, 'DeliveryPost']);





			Route::post('account/{pagePath}/delete', [\App\Http\Controllers\Account\PostsController::class, 'destroy']);

			//Recharge Pionts

			Route::get('account/recharge_points', [\App\Http\Controllers\Account\RechargeController::class, 'index']);

			Route::post('account/recharge_points', [\App\Http\Controllers\Account\RechargeController::class, 'postForm']);







			// Conversations

			Route::get('account/conversations', [\App\Http\Controllers\Account\ConversationsController::class, 'index']);

			Route::get('account/conversations/{id}/delete', [\App\Http\Controllers\Account\ConversationsController::class, 'destroy']);

			Route::post('account/conversations/delete', [\App\Http\Controllers\Account\ConversationsController::class, 'destroy']);

			Route::post('account/conversations/{id}/reply', [\App\Http\Controllers\Account\ConversationsController::class, 'reply']);

			$router->pattern('msgId', '[0-9]+');

			Route::get('account/conversations/{id}/messages', [\App\Http\Controllers\Account\ConversationsController::class, 'messages']);

			Route::get('account/conversations/{id}/messages/{msgId}/delete', [\App\Http\Controllers\Account\ConversationsController::class, 'destroy']);

			Route::post('account/conversations/{id}/messages/delete', [\App\Http\Controllers\Account\ConversationsController::class, 'destroy']);



			// Transactions

			Route::get('account/transactions', [\App\Http\Controllers\Account\TransactionsController::class, 'index']);

			// Offer Maker

			Route::get('account/makeanoffers', [\App\Http\Controllers\Account\MakeanoffersController::class, 'index']);

			Route::get('account/makeanoffers/{id}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'makeanoffer']);

			Route::get('account/makeanoffers/{postId}/edit/{offerId}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'edit']);

			Route::get('account/makeanoffers/{id}/delete', [\App\Http\Controllers\Account\MakeanoffersController::class, 'destroy']);



			Route::get('account/makeanoffers/{id}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'makeanoffer']);



			Route::get('makeanoffers/counteroffer/{id}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'counteroffer']);

			Route::post('makeanoffers/counterofferapi', [\App\Http\Controllers\Account\MakeanoffersController::class, 'counterofferapi']);

			Route::get('account/closeoffer/{id}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'closeoffer']);





			Route::post('account/makeanoffers/store', [\App\Http\Controllers\Account\MakeanoffersController::class, 'store']);

			Route::post('account/makeanoffers/storeeditoffer', [\App\Http\Controllers\Account\MakeanoffersController::class, 'storeeditoffer']);

			Route::post('account/makeanoffers/storeeditoffer_api', [\App\Http\Controllers\Account\MakeanoffersController::class, 'storeeditoffer_api']);

			Route::get('account/makeanoffers/{postId}/dealseller/{offerId}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'dealseller']);

			Route::get('account/makeanoffers/{postId}/notdealseller/{offerId}', [\App\Http\Controllers\Account\MakeanoffersController::class, 'notdealseller']);

			Route::get('account/makeanoffers/{id}/dealbuyer', [\App\Http\Controllers\Account\MakeanoffersController::class, 'dealbuyer']);

			Route::get('account/makeanoffers/{id}/notdealbuyer', [\App\Http\Controllers\Account\MakeanoffersController::class, 'notdealbuyer']);

			Route::post('account/makeanoffers/{id}/addmore', [\App\Http\Controllers\Account\MakeanoffersController::class, 'addmore']);

			Route::post('account/makeanoffers/{id}/updatemakeanoffer', [\App\Http\Controllers\Account\MakeanoffersController::class, 'updatemakeanoffer']);

		});





		// AJAX

		Route::group(['prefix' => 'ajax'], function ($router) {

			Route::get('countries/{countryCode}/admins/{adminType}', [\App\Http\Controllers\Ajax\LocationController::class, 'getAdmins']);

			Route::get('countries/{countryCode}/admins/{adminType}/{adminCode}/cities', [\App\Http\Controllers\Ajax\LocationController::class, 'getCities']);

			Route::get('countries/{countryCode}/cities/{id}', [\App\Http\Controllers\Ajax\LocationController::class, 'getSelectedCity']);

			Route::post('countries/{countryCode}/cities/autocomplete', [\App\Http\Controllers\Ajax\LocationController::class, 'searchedCities']);

			Route::post('countries/{countryCode}/admin1/cities', [\App\Http\Controllers\Ajax\LocationController::class, 'getAdmin1WithCities']);

			Route::post('category/sub-categories', [\App\Http\Controllers\Ajax\CategoryController::class, 'getSubCategories']);





			Route::post('category/custom-fields', [\App\Http\Controllers\Ajax\CategoryController::class, 'getCustomFields']);









			Route::post('save/post', [\App\Http\Controllers\Ajax\PostController::class, 'savePost']);

			Route::post('/save/FavUser', [\App\Http\Controllers\Ajax\PostController::class, 'saveFavUser']);

			Route::post('save/search', [\App\Http\Controllers\Ajax\PostController::class, 'saveSearch']);

			Route::post('post/phone', [\App\Http\Controllers\Ajax\PostController::class, 'getPhone']);

			Route::post('post/pictures/reorder', [\App\Http\Controllers\Ajax\PostController::class, 'picturesReorder']);

			Route::post('messages/check', [\App\Http\Controllers\Ajax\ConversationController::class, 'checkNewMessages']);



		});









		// FEEDS

		Route::feeds();





		// Country Code Pattern

		$countryCodePattern = implode('|', array_map('strtolower', array_keys(getCountries())));

		$router->pattern('countryCode', $countryCodePattern);





		// XML SITEMAPS

		Route::get('{countryCode}/sitemaps.xml', [\App\Http\Controllers\SitemapsController::class, 'site']);

		Route::get('{countryCode}/sitemaps/pages.xml', [\App\Http\Controllers\SitemapsController::class, 'pages']);

		Route::get('{countryCode}/sitemaps/categories.xml', [\App\Http\Controllers\SitemapsController::class, 'categories']);

		Route::get('{countryCode}/sitemaps/cities.xml', [\App\Http\Controllers\SitemapsController::class, 'cities']);

		Route::get('{countryCode}/sitemaps/posts.xml', [\App\Http\Controllers\SitemapsController::class, 'posts']);





		// STATICS PAGES

		Route::group(['middleware' => 'httpCache:yes'], function () {

			Route::get(LaravelLocalization::transRoute('routes.page'), [\App\Http\Controllers\PageController::class, 'index']);

			Route::get(LaravelLocalization::transRoute('routes.contact'), [\App\Http\Controllers\PageController::class, 'contact']);

			Route::get(LaravelLocalization::transRoute('routes.maptest'), [\App\Http\Controllers\PageController::class, 'maptest']);

			Route::post(LaravelLocalization::transRoute('routes.contact'), [\App\Http\Controllers\PageController::class, 'contactPost']);

			Route::get(LaravelLocalization::transRoute('routes.sitemap'), [\App\Http\Controllers\SitemapController::class, 'index']);

		});



		// DYNAMIC URL PAGES

		$router->pattern('id', '[0-9]+');

		//$router->pattern('username', '[a-zA-Z0-9]+');

		Route::get(LaravelLocalization::transRoute('routes.search'), [\App\Http\Controllers\Search\SearchController::class, 'index']);

		Route::get(LaravelLocalization::transRoute('routes.search-user'), [\App\Http\Controllers\Search\UserController::class, 'index']);

		Route::get(LaravelLocalization::transRoute('routes.search-username'), [\App\Http\Controllers\Search\UserController::class, 'profile']);

		Route::get(LaravelLocalization::transRoute('routes.search-username'), [\App\Http\Controllers\Search\UserController::class, 'profile']);

		Route::get(LaravelLocalization::transRoute('routes.search-tag'), [\App\Http\Controllers\Search\TagController::class, 'index']);

		Route::get(LaravelLocalization::transRoute('routes.search-city'), [\App\Http\Controllers\Search\CityController::class, 'index']);

		Route::get(LaravelLocalization::transRoute('routes.search-subCat'), [\App\Http\Controllers\Search\CategoryController::class, 'index']);

		Route::get(LaravelLocalization::transRoute('routes.search-cat'), [\App\Http\Controllers\Search\CategoryController::class, 'index']);



		/* HaMaDa */

		Route::get('autocomplete', [\App\Http\Controllers\HomeController::class, 'autocomplete']);

		// Route::get('invcaptcha', 'FirebasesmsController@invcaptcha');

		Route::get('invcaptcha', [\App\Http\Controllers\FirebasesmsController::class, 'invisiblecaptcha'])->name('invisiblecaptcha');

		/* HaMaDa */

	});

});



/* HaMaDa */

Route::group(['middleware' => ['web', 'installChecker'], 'prefix' => 'ratings'], function ($router) {

	$router->pattern('post', '[0-9]+');

	Route::get('get/{post}', function ($post) {

		$rate = \DB::table('ratings')->where('post', $post)->select('rate')->get();

		$rate = count($rate) ? ($rate->sum('rate') * 5) / ($rate->count() * 5) : 0;

		return round($rate);

	});

	Route::post('set', function () {

		if (auth()->user()->id != request()->rater){

			return "Don't be sneaky, ID is not of the logged user.";

		}

		$in = request()->validate([

			'rated' => 'bail|required|numeric|min:1',

			'rater' => 'bail|required|numeric|min:1',

			'post' => 'bail|required|numeric|min:1',

			'rate' => 'bail|required|numeric|min:1|max:5',

		]);

		$rate = \DB::table('ratings')

		->where([

			'rated' => $in['rated'],

			'rater' => $in['rater'],

			'post' => $in['post'],

			])

		->get();

		if (!count($rate)){

			$rate = \DB::table('ratings')->insert(

				[

					'rated' => $in['rated'],

					'rater' => $in['rater'],

					'post' => $in['post'],

					'rate' => $in['rate'],

				]

			);

		} else {

			return 'You already rated this Item';

		}

		return 'Rated by '.$in['rate'];

	});

});

/* HaMaDa */



// Route::get('admin/makeanoffer', 'MakeanofferController@index');

// Route::resource('admin/makeanoffer', 'Makeanoffer\\MakeanofferController');
