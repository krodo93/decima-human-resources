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

use Mgallegos\DecimaHumanResources\HumanResources\Services\DepartmentManagement\DepartmentManagementInterface;

use Mgallegos\DecimaAccounting\Accounting\Repositories\CostCenter\CostCenterInterface;

use Mgallegos\DecimaAccounting\Accounting\Services\JournalManagement\JournalManagementInterface;

use Mgallegos\DecimaInventory\Inventory\Services\WarehouseManagement\WarehouseManagementInterface;

use App\Kwaai\Security\Services\UserManagement\UserManagementInterface;

use App\Kwaai\Security\Services\AppManagement\AppManagementInterface;

use Illuminate\View\Factory;

use App\Http\Controllers\Controller;

class DepartmentManager extends Controller {

	/**
	 * Account Manager Service
	 *
	 * @var Mgallegos\DecimaAccounting\Accounting\Services\AccountManagement\AccountManagementInterface
	 *
	 */
	protected $DepartmentManagerService;

	/**
	 * Warehouse Manager Service
	 *
	 * @var Mgallegos\DecimaInventory\Inventory\Services\WarehouseManagement\WarehouseManagementInterface
	 *
	 */
	protected $WarehouseManagerService;

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
	 * Journal Manager Service
	 *
	 * @var Mgallegos\DecimaAccounting\Accounting\Services\JournalManagement\JournalManagementInterface
	 *
	 */
	protected $JournalManagerService;

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

	public function __construct(DepartmentManagementInterface $DepartmentManagerService,
															WarehouseManagementInterface $WarehouseManagerService,
															UserManagementInterface $UserManagerService,
															AppManagementInterface $AppManagerService,
															JournalManagementInterface $JournalManagerService,
															Factory $View,
															Request $Input,
															SessionManager $Session)
	{
		$this->DepartmentManagerService = $DepartmentManagerService;

		$this->WarehouseManagerService = $WarehouseManagerService;

		$this->UserManagerService = $UserManagerService;

		$this->AppManagerService = $AppManagerService;

		$this->JournalManagerService = $JournalManagerService;

		$this->View = $View;

		$this->Input = $Input;

		$this->Session = $Session;


	}

	public function getIndex()
	{
		return $this->View->make('decima-human-resources::department-management')
						->with('newDepartmentAction', $this->Session->get('newDepartmentAction', false))
						->with('editDepartmentAction', $this->Session->get('editDepartmentAction', false))
						->with('deleteDepartmentAction', $this->Session->get('deleteDepartmentAction', false))
					 	//->with('costCenters', $this->CostCenterManagement->getGroupsCostCenters());
						->with('warehouses', $this->WarehouseManagerService->getWarehouses())
					 	->with('costCenters', $this->JournalManagerService->getCostCenters())
						->with('appInfo', $this->AppManagerService->getAppInfo())
						->with('userOrganizations', $this->UserManagerService->getUserOrganizations())
						->with('userAppPermissions', $this->UserManagerService->getUserAppPermissions())
						->with('userActions', $this->UserManagerService->getUserActions());
	}

	public function postGridData()
	{
		return $this->DepartmentManagerService->getGridData( $this->Input->all() );
	}

	public function postCreate()
	{
		return $this->DepartmentManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdate()
	{
		return $this->DepartmentManagerService->update( $this->Input->json()->all() );
	}

	public function postDelete()
	{
		return $this->DepartmentManagerService->delete( $this->Input->json()->all() );
	}
}
