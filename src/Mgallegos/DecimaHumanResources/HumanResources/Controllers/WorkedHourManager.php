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

use Mgallegos\DecimaHumanResources\HumanResources\Services\WorkedHourManagement\WorkedHourManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\EmployeeManagement\EmployeeManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\TaskManagement\TaskManagementInterface;

use App\Kwaai\Security\Services\UserManagement\UserManagementInterface;

use App\Kwaai\Security\Services\AppManagement\AppManagementInterface;

use Illuminate\Translation\Translator;

use Illuminate\View\Factory;

use App\Http\Controllers\Controller;

class WorkedHourManager extends Controller {
	/**
		 * Laravel Translator instance
		 *
		 * @var Illuminate\Translation\Translator
		 *
		 */
	 protected $Lang;
	/**
	 * Account Manager Service
	 *
	 * @var Mgallegos\DecimaAccounting\Accounting\Services\AccountManagement\AccountManagementInterface
	 *
	 */
	protected $EmployeeManagerService;

	/**
	 * Account Manager Service
	 *
	 * @var Vendor\DecimaModule\Module\Services\WorkedHourManagement\WorkedHourManagementInterface
	 *
	 */

	 protected $TaskManagerService;

 	/**
 	 * Account Manager Service
 	 *
 	 * @var Vendor\DecimaModule\Module\Services\WorkedHourManagement\WorkedHourManagementInterface
 	 *
 	 */

	protected $WorkedHourManagerService;

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

	public function __construct(WorkedHourManagementInterface $WorkedHourManagerService,
															UserManagementInterface $UserManagerService,
															AppManagementInterface $AppManagerService,
															Factory $View,
															Request $Input,
															SessionManager $Session,
															EmployeeManagementInterface $EmployeeManagerService,
															TaskManagementInterface $TaskManagerService,
															Translator $Lang)
	{
		$this->WorkedHourManagerService = $WorkedHourManagerService;

		$this->UserManagerService = $UserManagerService;

		$this->AppManagerService = $AppManagerService;

		$this->View = $View;

		$this->Input = $Input;

		$this->Session = $Session;

		$this->EmployeeManagerService = $EmployeeManagerService;

		$this->TaskManagerService = $TaskManagerService;

		$this->Lang = $Lang;


	}

	public function getIndex()
	{
		// var_dump ($this->EmployeeManagerService->getLoggedEmployee());
		return $this->View->make('decima-human-resources::worked-hour-management')
						->with('viewAllWorkedHoursAction', $this->Session->get('viewAllWorkedHoursAction', false))
						->with('checkInAction', $this->Session->get('checkInAction', false))
						->with('checkOutAction', $this->Session->get('checkOutAction', false))
						->with('employees', $this->EmployeeManagerService->getEmployees())
						->with('loggedUser', $this->EmployeeManagerService->getLoggedEmployee())
						->with('task', $this->TaskManagerService->getTask())
						->with('DateH', $this->WorkedHourManagerService->getCurrentDateAndTime())
						->with('appInfo', $this->AppManagerService->getAppInfo())
						->with('userOrganizations', $this->UserManagerService->getUserOrganizations())
						->with('userAppPermissions', $this->UserManagerService->getUserAppPermissions())
						->with('userActions', $this->UserManagerService->getUserActions());
	}

	public function postGridData()
	{
		return $this->WorkedHourManagerService->getGridData( $this->Input->all() );
	}

	public function postCreate()
	{
		return $this->WorkedHourManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdate()
	{
		return $this->WorkedHourManagerService->update( $this->Input->json()->all() );
	}

	public function postDelete()
	{
		return $this->WorkedHourManagerService->delete( $this->Input->json()->all() );
	}
}
