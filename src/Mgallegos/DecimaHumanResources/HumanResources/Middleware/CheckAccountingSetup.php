<?php
/**
 * @file
 * Check HumanResources Setup Middleware.
 *
 * All DecimaHumanResources code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Middleware;

use Closure;
// use Mgallegos\DecimaHumanResources\HumanResources\Services\SettingManagement\SettingManagementInterface;

class CheckHumanResourcesSetup {

	/**
	 * Setting Manager Service
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Services\SettingManagement\SettingManagementInterface
	 *
	 */
	protected $SettingManagerService;

	/**
	 * Create a new filter instance.
	 *
	 * @param  SettingManagementInterface $SettingManagerService
	 * @return void
	 */
	public function __construct(SettingManagementInterface $SettingManagerService)
	{
		$this->SettingManagerService = $SettingManagerService;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// if(!$this->SettingManagerService->isHumanResourcesSetup())
		// {
		// 	return redirect('HumanResources/setup/initial-HumanResources-setup');
		// }

		return $next($request);
	}

}
