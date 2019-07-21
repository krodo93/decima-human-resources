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

use Mgallegos\DecimaHumanResources\HumanResources\Services\TaskManagement\TaskManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\EmployeeManagement\EmployeeManagementInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Services\PhaseManagement\PhaseManagementInterface;

use App\Kwaai\Security\Services\UserManagement\UserManagementInterface;

use App\Kwaai\Security\Services\AppManagement\AppManagementInterface;

use Illuminate\View\Factory;

use App\Http\Controllers\Controller;

use Illuminate\Translation\Translator;

class TaskManager extends Controller {

	/**
	 * Account Manager Service
	 *
	 * @var Mgallegos\DecimaAccounting\Accounting\Services\AccountManagement\AccountManagementInterface
	 *
	 */
	protected $TaskManagerService;

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
	 * @var Mgallegos\DecimaAccounting\Accounting\Services\AccountManagement\AccountManagementInterface
	 *
	 */
	protected $PhaseManagerService;

	/**
	   * Laravel Translator instance
	   *
	   * @var Illuminate\Translation\Translator
	   *
	   */
	 protected $Lang;

	public function __construct(TaskManagementInterface $TaskManagerService,
															UserManagementInterface $UserManagerService,
															AppManagementInterface $AppManagerService,
															Factory $View,
															Request $Input,
															SessionManager $Session,
															EmployeeManagementInterface $EmployeeManagerService,
															PhaseManagementInterface $PhaseManagerService,
															Translator $Lang)
	{
		$this->TaskManagerService = $TaskManagerService;

		$this->UserManagerService = $UserManagerService;

		$this->AppManagerService = $AppManagerService;

		$this->View = $View;

		$this->Input = $Input;

		$this->Session = $Session;

		$this->EmployeeManagerService = $EmployeeManagerService;

		$this->PhaseManagerService = $PhaseManagerService;

		$this->Lang = $Lang;
	}

	public function getIndex()
	{

		return $this->View->make('decima-human-resources::task-management')
						->with('newTaskAction', $this->Session->get('newTaskAction', false))
						->with('viewAllTaskAction', $this->Session->get('viewAllTaskAction', false))
						->with('editTaskAction', $this->Session->get('editTaskAction', false))
						->with('deleteTaskAction', $this->Session->get('deleteTaskAction', false))
						->with('currentDate', date($this->Lang->get('form.phpShortDateFormat')))
						->with('priority', $this->TaskManagerService->getPriotity())
						->with('completionPercentage', $this->TaskManagerService->getcompletionPercentage())
						->with('employees', $this->EmployeeManagerService->getEmployees())
						->with('loggedUser', $this->EmployeeManagerService->getLoggedEmployee())
						->with('phases', $this->PhaseManagerService->getPhases())
						->with('phasesWithTasks', $this->TaskManagerService->getTaskByPhases())
						//->with('accounts', $this->TaskManagerService->getGroupsAccounts())
						->with('appInfo', $this->AppManagerService->getAppInfo())
						->with('userOrganizations', $this->UserManagerService->getUserOrganizations())
						->with('userAppPermissions', $this->UserManagerService->getUserAppPermissions())
						->with('userActions', $this->UserManagerService->getUserActions());
	}

	public function postGridData()
	{
		return $this->TaskManagerService->getGridData( $this->Input->all() );
	}

	public function postCreate()
	{
		return $this->TaskManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdate()
	{
		return $this->TaskManagerService->update( $this->Input->json()->all() );
	}

	public function postDelete()
	{
		return $this->TaskManagerService->delete( $this->Input->json()->all() );
	}

	// public function postUpdatePhase()
	// {
	// 	return $this->TaskManagerService->updatePhase( $this->Input->json()->all() );
	// }

	public function postUpdateTaskPhaseAndPosition()
	{
		return $this->TaskManagerService->updateTaskPhaseAndPosition( $this->Input->json()->all() );
	}
}
