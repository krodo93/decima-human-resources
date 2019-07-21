<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use Illuminate\Database\DatabaseManager;

use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

use Illuminate\Translation\Translator;

class EloquentPhaseGridRepository extends EloquentRepositoryAbstract {

	public function __construct(DatabaseManager $DB, AuthenticationManagementInterface $AuthenticationManager)
	{
		// $this->DB = $DB;
		// $this->DB->connection()->enableQueryLog();

		$this->Database = $DB->connection($AuthenticationManager->getCurrentUserOrganizationConnection())
								->table('HR_Phase AS p');
								//->leftJoin('MODULE_Table1 AS t1p', 't1.id', '=', 't1p.parent_id')
								//->join('MODULE_Table2 AS t2', 't2.id', '=', 't1.table2_id')
								//->where('t.organization_id', '=', $AuthenticationManager->getCurrentUserOrganizationId())
							//	->whereNull('t.deleted_at');

		$this->visibleColumns = array('p.id AS hr_pm_id',
																	'p.name AS hr_pm_name',
																	'p.position AS hr_pm_position');

		$this->orderBy = array(array('p.id', 'asc'));

		// $this->treeGrid = true;

		// $this->parentColumn = 'parent_id';

		// $this->leafColumn = 'is_leaf';
	}

}
