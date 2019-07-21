<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Afp;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use Illuminate\Database\DatabaseManager;

use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

use Illuminate\Translation\Translator;

class EloquentAfpGridRepository extends EloquentRepositoryAbstract {

	public function __construct(DatabaseManager $DB, AuthenticationManagementInterface $AuthenticationManager)
	{
		// $this->DB = $DB;
		// $this->DB->connection()->enableQueryLog();

		$this->Database = $DB->connection($AuthenticationManager->getCurrentUserOrganizationConnection())
								->table('HR_AFP AS t1');
								//->leftJoin('MODULE_Table1 AS t1p', 't1.id', '=', 't1p.parent_id')
								//->join('MODULE_Table2 AS t2', 't2.id', '=', 't1.table2_id')
								//->where('t.organization_id', '=', $AuthenticationManager->getCurrentUserOrganizationId())
								//->whereNull('t.deleted_at');

		$this->visibleColumns = array('t1.id AS hr_afp_id',
		 															't1.name AS hr_afp_name',
																	't1.commission AS hr_afp_commission',
																	't1.employer_contribution AS hr_afp_employer_contribution',
																	't1.employee_contribution AS hr_afp_employee_contribution',
																	't1.organization_id AS hr_organization_id');

		$this->orderBy = array(array('t1.id', 'asc'));

		// $this->treeGrid = true;

		// $this->parentColumn = 'parent_id';

		// $this->leafColumn = 'is_leaf';
	}

}
