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

namespace App\Http\Middleware;

use App\Helpers\Curl;
use App\Models\TimeZone;
use Closure;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class InstallationChecker
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($request->segment(1) == 'install') {
			// Check if installation is processing
			$InstallInProgress = false;
			if (
				!empty($request->session()->get('database_imported')) ||
				!empty($request->session()->get('cron_jobs')) ||
				!empty($request->session()->get('install_finish'))
			) {
				$InstallInProgress = true;
			}
			if ($this->alreadyInstalled($request) && $this->properlyInstalled() && !$InstallInProgress) {
				return redirect('/');
			}
		} else {
			// Check if an update is available
			if ($this->envFileExists() && $this->updateIsAvailable()) {
				return headerLocation(getRawBaseUrl() . '/upgrade');
			}
			
			// Check if the website is installed
			if (!$this->alreadyInstalled($request) || !$this->properlyInstalled()) {
				return redirect(getRawBaseUrl() . '/install');
			}
			
			$this->checkPurchaseCode($request);
		}
		
		return $next($request);
	}
	
	/**
	 * If application is already installed.
	 *
	 * @param $request
	 * @return bool|\Illuminate\Http\RedirectResponse
	 */
	public function alreadyInstalled($request)
	{
		return true;
	}
	
	public function properlyInstalled()
	{
		return true;
	}
	
	/**
	 * Check if /.env file exists
	 *
	 * @return bool
	 */
	public function envFileExists()
	{
		return File::exists(base_path('.env'));
	}
	
	/**
	 * Check Purchase Code
	 * ===================
	 * Checking your purchase code. If you do not have one, please follow this link:
	 * https://codecanyon.net/item/laraclassified-geo-classified-ads-cms/16458425
	 * to acquire a valid code.
	 *
	 * IMPORTANT: Do not change this part of the code.
	 *
	 * @param $request
	 */
	private function checkPurchaseCode($request)
	{
		return;
	}
	
	/**
	 * Check if an update is available
	 *
	 * @return bool
	 */
	private function updateIsAvailable()
	{
		$updateIsAvailable = false;

		// Get eventual new version value & the current (installed) version value
		$lastVersionInt = strToInt(config('app.version'));
		$currentVersionInt = strToInt(getCurrentVersion());

		// Fall back to env() if DotenvEditor returns nothing (e.g. on Railway)
		if ($currentVersionInt === 0) {
			$currentVersionInt = strToInt(env('APP_VERSION', config('app.version')));
		}

		// Check the update
		if ($lastVersionInt > $currentVersionInt) {
			$updateIsAvailable = true;
		}

		return $updateIsAvailable;
	}
}
