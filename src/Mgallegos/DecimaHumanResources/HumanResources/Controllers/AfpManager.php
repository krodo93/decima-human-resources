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

use Mgallegos\DecimaHumanResources\HumanResources\Services\AfpManagement\AfpManagementInterface;

use App\Kwaai\Security\Services\UserManagement\UserManagementInterface;

use App\Kwaai\Security\Services\AppManagement\AppManagementInterface;

use Illuminate\View\Factory;

use App\Http\Controllers\Controller;

class AfpManager extends Controller {

	/**
	 * Account Manager Service
	 *
	 * @var Vendor\DecimaModule\Module\Services\AfpManagement\AfpManagementInterface
	 *
	 */
	protected $AfpManagerService;


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

	public function __construct(AfpManagementInterface $AfpManagerService,
															UserManagementInterface $UserManagerService,
															AppManagementInterface $AppManagerService,
															Factory $View,
															Request $Input,
															SessionManager $Session)
	{
		$this->AfpManagerService = $AfpManagerService;

		$this->UserManagerService = $UserManagerService;

		$this->AppManagerService = $AppManagerService;

		$this->View = $View;

		$this->Input = $Input;

		$this->Session = $Session;


	}

	public function getIndex()
	{
		return $this->View->make('decima-human-resources::afp-management')
						->with('newAfpAction', $this->Session->get('newAfpAction', false))
						->with('editAfpAction', $this->Session->get('editAfpAction', false))
						->with('deleteAfpAction', $this->Session->get('deleteAfpAction', false))
						->with('appInfo', $this->AppManagerService->getAppInfo())
						->with('userOrganizations', $this->UserManagerService->getUserOrganizations())
						->with('userAppPermissions', $this->UserManagerService->getUserAppPermissions())
						->with('userActions', $this->UserManagerService->getUserActions());
	}

	public function postGridData()
	{
		return $this->AfpManagerService->getGridData( $this->Input->all() );
	}

	public function postCreate()
	{
		return $this->AfpManagerService->create( $this->Input->json()->all() );
	}

	public function postUpdate()
	{
		return $this->AfpManagerService->update( $this->Input->json()->all() );
	}

	public function postDelete()
	{
		return $this->AfpManagerService->delete( $this->Input->json()->all() );
	}
}
