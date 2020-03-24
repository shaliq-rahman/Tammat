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

namespace App\Http\Controllers\Traits;

use App\Models\Page;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use ChrisKonnertz\OpenGraph\OpenGraph;
use Illuminate\Support\Facades\Route;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use App\Helpers\Localization\Country as CountryLocalization;
use App\Helpers\Localization\Helpers\Country as CountryLocalizationHelper;
use Jenssegers\Date\Date;
use Torann\LaravelMetaTags\Facades\MetaTag;

trait SettingsTrait
{
	public $cookieExpiration;
	public $cacheExpiration = 60; // In minutes (e.g. 60 for 1h)
	public $picturesLimit = 1;
	
	public $countries = null;
	
	public $paymentMethods;
	public $countPaymentMethods = 0;
	
	public $og;
	
	/**
	 * Set all the front-end settings
	 */
	public function applyFrontSettings()
	{
		// Cache Expiration Time
		$this->cacheExpiration = (int)config('settings.other.cache_expiration');
		view()->share('cacheExpiration', $this->cacheExpiration);
		
		// Ads photos number
		$picturesLimit = (int)config('settings.single.pictures_limit');
		if ($picturesLimit >= 1 and $picturesLimit <= 20) {
			$this->picturesLimit = $picturesLimit;
		}
		if ($picturesLimit > 20) {
			$this->picturesLimit = 20;
		}
		view()->share('picturesLimit', $this->picturesLimit);
		
		
		// Default language for Bots
		$crawler = new CrawlerDetect();
		if ($crawler->isCrawler()) {
			$lang = collect(config('country.lang'));
			if ($lang->has('abbr')) {
				Config::set('lang.abbr', $lang->get('abbr'));
				Config::set('lang.locale', $lang->get('locale'));
			}
			app()->setLocale(config('lang.abbr'));
		}
		
		// Set Date Locale
		if (!empty(config('lang.abbr'))) {
			Date::setLocale(config('lang.abbr'));
		}
		if (!empty(config('lang.locale'))) {
			setlocale(LC_ALL, config('lang.locale'));
		}
		
		
		// DNS Prefetch meta tags
		$dnsPrefetch = [
			str_replace(['http://', 'https://'], '', config('app.url')),
			'fonts.googleapis.com',
			'graph.facebook.com',
			'google.com',
			'apis.google.com',
			'ajax.googleapis.com',
			'www.google-analytics.com',
			'pagead2.googlesyndication.com',
			'gstatic.com',
			'cdn.api.twitter.com',
			'oss.maxcdn.com',
		];
		view()->share('dnsPrefetch', $dnsPrefetch);
		
		
		// SEO
		$title = getMetaTag('title', 'home');
		$description = getMetaTag('description', 'home');
		$keywords = getMetaTag('keywords', 'home');
		
		// Meta Tags
		MetaTag::set('title', $title);
		MetaTag::set('description', strip_tags($description));
		MetaTag::set('keywords', $keywords);
		
		
		// Open Graph
		$this->og = new OpenGraph();
		$locale = !empty(config('lang.locale')) ? config('lang.locale') : 'en_US';
		try {
			$this->og->siteName(config('settings.app.name'))->locale($locale)->type('website')->url(url()->current());
		} catch (\Exception $e) {};
		view()->share('og', $this->og);
		
		
		// CSRF Control
		// CSRF - Some JavaScript frameworks, like Angular, do this automatically for you.
		// It is unlikely that you will need to use this value manually.
		setcookie('X-XSRF-TOKEN', csrf_token(), $this->cookieExpiration, '/', getDomain());
		
		
		// Skin selection
		config(['app.skin' => getFrontSkin(request()->input('skin'))]);
		
		
		// Reset session Post view counter
		if (!str_contains(Route::currentRouteAction(), 'Post\DetailsController')) {
			if (session()->has('postIsVisited')) {
				session()->forget('postIsVisited');
			}
		}
		
		// Pages Menu
		$pages = Cache::remember('pages.' . config('app.locale') . '.menu', $this->cacheExpiration, function () {
			$pages = Page::trans()->where('excluded_from_footer', '!=', 1)->orderBy('lft', 'ASC')->get();
			
			return $pages;
		});
		view()->share('pages', $pages);
		
		
		// Get all installed plugins name
		view()->share('installedPlugins', array_keys(plugin_installed_list()));
		
		
		// Get all Countries
		$countries = CountryLocalizationHelper::transAll(CountryLocalization::getCountries());
		$cols = round($countries->count() / 4, 0, PHP_ROUND_HALF_EVEN);
		$cols = ($cols > 0) ? $cols : 1; // Fix array_chunk with 0
		view()->share('countryCols', $countries->chunk($cols)->all());
		
		
		// Get Payment Methods
		$this->paymentMethods = Cache::remember('paymentMethods.all', $this->cacheExpiration, function () {
			$paymentMethods = PaymentMethod::where(function ($query) {
				$query->whereRaw('FIND_IN_SET("' . config('country.icode') . '", LOWER(countries)) > 0')
					->orWhereNull('countries');
			})->orderBy('lft')->get();
			
			return $paymentMethods;
		});
		$this->countPaymentMethods = $this->paymentMethods->count();
		view()->share('paymentMethods', $this->paymentMethods);
		view()->share('countPaymentMethods', $this->countPaymentMethods);
	}
	
	/**
	 * Check & Add the missing entries in the /.env file
	 */
	public function checkDotEnvEntries()
	{
		$isChanged = false;
		
		// Check the App Config Locale
		if (!DotenvEditor::keyExists('APP_LOCALE')) {
			DotenvEditor::addEmpty();
			DotenvEditor::setKey('APP_LOCALE', config('appLang.abbr'));
			$isChanged = true;
		}
		
		// Check Purchase Code
		if (!DotenvEditor::keyExists('PURCHASE_CODE')) {
			if (!empty(config('settings.app.purchase_code'))) {
				DotenvEditor::addEmpty();
				DotenvEditor::setKey('PURCHASE_CODE', config('settings.app.purchase_code'));
				$isChanged = true;
			}
		}
		
		if ($isChanged) {
			DotenvEditor::save();
		}
	}
}
