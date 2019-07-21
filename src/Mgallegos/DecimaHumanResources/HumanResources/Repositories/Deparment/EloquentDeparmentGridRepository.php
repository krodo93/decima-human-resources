<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use Illuminate\Database\DatabaseManager;

use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

use Illuminate\Translation\Translator;

class EloquentDepartmentGridRepository extends EloquentRepositoryAbstract {

	public function __construct(DatabaseManager $DB, AuthenticationManagementInterface $AuthenticationManager)
	{
		// $this->DB = $DB;
		// $this->DB->connection()->enableQueryLog();

		$this->Database = $DB->connection($AuthenticationManager->getCurrentUserOrganizationConnection())
								->table('HR_Department AS d')
								->leftjoin('ACCT_Cost_Center AS c' , 'c.id', '=', 'd.cost_center_id')
								->leftjoin('INV_Warehouse AS w' , 'w.id', '=', 'd.raw_materials_warehouse_id')
								->leftjoin('INV_Warehouse AS v' , 'v.id', '=', 'd.finished_goods_warehouse_id')
								->leftjoin('INV_Warehouse AS u' , 'u.id', '=', 'd.consumable_warehouse_id')

								->where('d.organization_id', '=', $AuthenticationManager->getCurrentUserOrganizationId());
								//->whereNull('t.deleted_at');

		$this->visibleColumns = array('d.id AS hr_department_id',
																	'd.name AS hr_department_name',
																	'c.id AS hr_dep_cost_center_id',
																	'd.raw_materials_warehouse_id AS hr_department_raw_materials_warehouse_id',
																	'w.name AS hr_department_raw_materials_warehouse_label',
																	'd.finished_goods_warehouse_id AS hr_department_finished_goods_warehouse_id',
																	'v.name AS hr_department_finished_goods_warehouse_label',
																	'd.consumable_warehouse_id AS hr_department_consumable_warehouse_id',
																	'u.name AS hr_department_consumable_warehouse_label',
																	$DB->raw('CONCAT(c.key,\' \',c.name) AS hr_cost_center_name'));
		$this->orderBy = array(array('d.id', 'asc'));

		// $this->treeGrid = true;

		// $this->parentColumn = 'parent_id';

		// $this->leafColumn = 'is_leaf';
	}

}
