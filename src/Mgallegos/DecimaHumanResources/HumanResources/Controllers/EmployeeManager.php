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

use Mgallegos\DecimaHumanResources\HumanResources\Services\EmployeeManagement\EmployeeManagementInterface;

use App\Kwaai\Organization\Services\OrganizationManagement\OrganizationManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\DepartmentManagement\DepartmentManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\PositionManagement\PositionManagementInterface;

use Mgallegos\DecimaBank\Bank\Services\BankManagement\BankManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveApplicationManagement\LeaveApplicationManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveTypeManagement\LeaveTypeManagementInterface;

use App\Kwaai\Security\Services\UserManagement\UserManagementInterface;

use App\Kwaai\Security\Services\AppManagement\AppManagementInterface;

use Illuminate\View\Factory;

use Illuminate\Translation\Translator;

use App\Http\Controllers\Controller;

class EmployeeManager extends Controller {

	/**
	 * Account Manager Service
	 *
	 * @var Mgallegos\DecimaAccounting\Accounting\Services\AccountManagement\AccountManagementInterface
	 *
	 */
	protected $EmployeeManagerService;

	/**
	 * Organization Manager Service
	 *
	 * @var App\Kwaai\Organization\Services\OrganizationManagement\OrganizationManagementInterface
	 *
	 */
	protected $OrganizationManagerService;

	/**
	 * Organization Manager Service
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Services\DepartmentManagement\DepartmentManagementInterface
	 *
	 */
	protected $DepartmentManagerService;

	/**
	 * Organization Manager Service
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Services\PositionManagement
	 *
	 */
	protected $PositionManagerService;

	/**
	 * Bank Manager Service
	 *
	 * @var Mgallegos\DecimaBank\Bank\Services\BankManagement\BankManagementInterface
	 *
	 */
	protected $BankManagerService;

	/**
	 * Organization Manager Service
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveApplicationManagement
	 *
	 */
	protected $LeaveApplicationManagerService;

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

	 protected $LeaveTypeManagerService;


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

	/**
	   * Laravel Translator instance
	   *
	   * @var Illuminate\Translation\Translator
	   *
	   */
	 protected $Lang;

	public function __construct(EmployeeManagementInterface $EmployeeManagerService,
															OrganizationManagementInterface $OrganizationManagerService,
														 	DepartmentManagementInterface $DepartmentManagerService,
															PositionManagementInterface $PositionManagerService,
															BankManagementInterface $BankManagerService,
															LeaveApplicationManagementInterface $LeaveApplicationManagerService,
															LeaveTypeManagementInterface $LeaveTypeManagerService,
															UserManagementInterface $UserManagerService,
															AppManagementInterface $AppManagerService,
															Factory $View,
															Request $Input,
															SessionManager $Session,
															Translator $Lang)
	{
		$this->EmployeeManagerService = $EmployeeManagerService;

		$this->OrganizationManagerService = $OrganizationManagerService;

		$this->DepartmentManagerService = $DepartmentManagerService;

		$this->PositionManagerService = $PositionManagerService;

		$this->BankManagerService = $BankManagerService;

		$this->LeaveApplicationManagerService = $LeaveApplicationManagerService;

		$this->LeaveTypeManagerService = $LeaveTypeManagerService;

		$this->UserManagerService = $UserManagerService;

		$this->AppManagerService = $AppManagerService;

		$this->View = $View;

		$this->Input = $Input;

		$this->Session = $Session;

		$this->Lang = $Lang;


	}

	public function getIndex()
	{
		return $this->View->make('decima-human-resources::employee-management')
						->with('newEmployeeAction', $this->Session->get('newEmployeeAction', false))
						->with('viewAllEmployeesAction', $this->Session->get('viewAllEmployeesAction', false))
						->with('newLeaveApplicationAction', $this->Session->get('newLeaveApplicationAction', false))
						->with('editEmployeeAction', $this->Session->get('editEmployeeAction', false))
						->with('deleteEmployeeAction', $this->Session->get('deleteEmployeeAction', false))
						->with('countries', $this->OrganizationManagerService->getSystemCountries())
						->with('genders', $this->EmployeeManagerService->getGenders())
						->with('statusl', $this->EmployeeManagerService->getStatus())
						->with('mstatus', $this->EmployeeManagerService->getmstatus())
						->with('department', $this->DepartmentManagerService->getDepartment())
						->with('position', $this->PositionManagerService->getPositions())
						->with('banks', $this->BankManagerService->getBanks())
						->with('currentDate', date($this->Lang->get('form.phpShortDateFormat')))
						->with('users', $this->OrganizationManagerService->getUsersByOrganization())
						->with('halfday', $this->LeaveApplicationManagerService->getHalfday())
						->with('status', $this->LeaveApplicationManagerService->getStatus())
						->with('leavetype', $this->LeaveTypeManagerService->getLeaveType())
						->with('leaveapprover', $this->EmployeeManagerService->getEmployees())
						->with('prefix', 'hr-em-')
						->with('appInfo', $this->AppManagerService->getAppInfo())
						->with('userOrganizations', $this->UserManagerService->getUserOrganizations())
						->with('userAppPermissions', $this->UserManagerService->getUserAppPermissions())
						->with('userActions', $this->UserManagerService->getUserActions());
						//->with('accounts', $this->EmployeeManagerService->getGroupsAccounts())

	}

	public function postGridData()
	{
		return $this->EmployeeManagerService->getGridData( $this->Input->all() );
	}

	public function postCreate()
	{
		return $this->EmployeeManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdate()
	{
		return $this->EmployeeManagerService->update( $this->Input->json()->all() );
	}

	public function postDelete()
	{
		return $this->EmployeeManagerService->delete( $this->Input->json()->all() );
	}

	public function postGridDataLeaveApplication()
	{
		return $this->LeaveApplicationManagerService->getGridData( $this->Input->all() );
	}

	public function postCreateLeaveApplication()
	{
		return $this->LeaveApplicationManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdateLeaveApplication()
	{
		return $this->LeaveApplicationManagerService->update( $this->Input->json()->all() );
	}

	public function postDeleteLeaveApplication()
	{
		return $this->LeaveApplicationManagerService->delete( $this->Input->json()->all() );
	}



}
