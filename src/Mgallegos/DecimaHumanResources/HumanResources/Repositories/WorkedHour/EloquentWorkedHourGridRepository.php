<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use Illuminate\Database\DatabaseManager;

use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

use Illuminate\Translation\Translator;

class EloquentWorkedHourGridRepository extends EloquentRepositoryAbstract {

	public function __construct(DatabaseManager $DB, AuthenticationManagementInterface $AuthenticationManager)
	{
		// $this->DB = $DB;
		// $this->DB->connection()->enableQueryLog();

		$this->Database = $DB->connection($AuthenticationManager->getCurrentUserOrganizationConnection())
								->table('HR_Worked_Hour AS w')
								->leftJoin('HR_Employee AS e', 'e.id', '=', 'w.responsible_employee_id')
								->leftJoin('HR_Task AS t', 't.id', '=', 'w.task_id')
								// ->leftJoin('MODULE_Table1 AS t1p', 't1.id', '=', 't1p.parent_id')
								// ->join('MODULE_Table2 AS t2', 't2.id', '=', 't1.table2_id')
								->where('w.organization_id', '=', $AuthenticationManager->getCurrentUserOrganizationId());
								// ->whereNull('t.deleted_at')

		// <a href="https://storage.googleapis.com/decimaerp/organizations/5/Logo_Cabal_pequeño.png

		$this->visibleColumns = array('w.id AS hr_whm_id',
																	'w.start_date AS hr_whm_start_date',
																	'w.end_date AS hr_whm_end_date',
																	'w.description AS hr_whm_description',
																	'w.url AS hr_whm_url',
																	$DB->raw('CONCAT(\'<a href="\',w.url,\'" target="_blank"><i class="fa fa-external-link" aria-hidden="true" style="font-size: 2em;color:#1E448B;"></i></a>\') AS hr_whm_url_html'),
																	'w.task_id AS hr_whm_task_id',
																	't.name AS hr_whm_task_label',
																	'w.responsible_employee_id AS hr_whm_responsible_employee_id',
																	$DB->raw('(w.worked_hours/60) AS hr_whm_worked_hours'),
																	$DB->raw('CONCAT(e.names,\' \',e.surnames) AS hr_whm_responsible_employee')
																);

		$this->orderBy = array(array('hr_whm_start_date', 'desc'));

		// $this->treeGrid = true;

		// $this->parentColumn = 'parent_id';

		// $this->leafColumn = 'is_leaf';
	}

}
