<?php
/**
 * @file
 * Account Manager Controller.
 *
 * All DecimaAccounting code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */
namespace Mgallegos\DecimaHumanResources\HumanResources\Controllers;

use Illuminate\Session\SessionManager;

use Illuminate\Http\Request;

use Mgallegos\DecimaHumanResources\HumanResources\Services\HolidayManagement\HolidayManagementInterface;

use App\Kwaai\Security\Services\UserManagement\UserManagementInterface;

use App\Kwaai\Security\Services\AppManagement\AppManagementInterface;

use Illuminate\View\Factory;

use App\Http\Controllers\Controller;

class HolidayManager extends Controller {

	/**
	 * Account Manager Service
	 *
	 * @var Vendor\DecimaModule\Module\Services\HolidayManagement\HolidayManagementInterface
	 *
	 */
	protected $HolidayManagerService;

	/**
	* User Manager Service
	*
	* @var App\Kwaai\Security\Services\UserManagement\UserManagementInterface
	*
	*/
	protected $UserManagerService;

	/**
	* App Manager Service
	*
	* @var App\Kwaai\Security\Services\AppManagement\AppManagementInterface;
	*
	*/
	protected $AppManagerService;

	/**
	 * View
	 *
	 * @var Illuminate\View\Factory
	 *
	 */
	protected $View;

	/**
	 * Input
	 *
	 * @var Illuminate\Http\Request
	 *
	 */
	protected $Input;

	/**
	 * Session
	 *
	 * @var Illuminate\Session\SessionManager
	 *
	 */
	protected $Session;

	public function __construct(HolidayManagementInterface $HolidayManagerService,
															UserManagementInterface $UserManagerService,
															AppManagementInterface $AppManagerService,
															Factory $View,
															Request $Input,
															SessionManager $Session)
	{
		$this->HolidayManagerService = $HolidayManagerService;

		$this->UserManagerService = $UserManagerService;

		$this->AppManagerService = $AppManagerService;

		$this->View = $View;

		$this->Input = $Input;

		$this->Session = $Session;


	}

	public function getIndex()
	{
		return $this->View->make('decima-human-resources::holiday-management')
						->with('newHolidayAction', $this->Session->get('newHolidayAction', false))
						->with('editHolidayAction', $this->Session->get('editHolidayAction', false))
						->with('deleteHolidayAction', $this->Session->get('deleteHolidayAction', false))
						->with('appInfo', $this->AppManagerService->getAppInfo())
						->with('userOrganizations', $this->UserManagerService->getUserOrganizations())
						->with('userAppPermissions', $this->UserManagerService->getUserAppPermissions())
						->with('userActions', $this->UserManagerService->getUserActions());
	}

	public function postGridData()
	{
		return $this->HolidayManagerService->getGridData( $this->Input->all() );
	}

	public function postCreate()
	{
		return $this->HolidayManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdate()
	{
		return $this->HolidayManagerService->update( $this->Input->json()->all() );
	}

	public function postDelete()
	{
		return $this->HolidayManagerService->delete( $this->Input->json()->all() );
	}
}
