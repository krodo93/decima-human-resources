<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use Illuminate\Database\DatabaseManager;

use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

use Illuminate\Translation\Translator;

class EloquentLeaveApplicationGridRepository extends EloquentRepositoryAbstract {

	public function __construct(DatabaseManager $DB, AuthenticationManagementInterface $AuthenticationManager, Translator $Lang)
	{
		// $this->DB = $DB;
		// $this->DB->connection()->enableQueryLog();

		$this->Database = $DB->connection($AuthenticationManager->getCurrentUserOrganizationConnection())
								->table('HR_Leave_Application AS l')
								->leftJoin('HR_Leave_Type AS t', 't.id', '=', 'l.leave_type_id');
								// ->join('MODULE_Table2 AS t2', 't2.id', '=', 't1.table2_id')
								// ->where('t.organization_id', '=', $AuthenticationManager->getCurrentUserOrganizationId())
								// ->whereNull('t.deleted_at');

		$this->visibleColumns = array('l.id AS hr_em_la_id',
																	'l.from_date AS hr_em_la_from_date',
																	'l.to_date AS hr_em_la_to_date',
																	'l.total_leave_days AS hr_em_la_total_leave_days',
																	'l.is_half_day AS hr_em_la_is_half_day',
																	'l.reason AS hr_em_la_reason',
																	'l.status AS hr_em_la_status',
																	'l.leave_type_id AS hr_em_la_leave_type_id',
																	't.name AS hr_em_la_leave_type',
																	'l.applicant_id AS hr_em_la_applicant_id',
																	'l.leave_approver_id AS hr_em_la_leave_approver_id',
		$DB->raw('CASE WHEN l.is_half_day = \'Y\' THEN \'' . $Lang->get('decima-human-resources::employee-management.y') .'\' WHEN l.is_half_day = \'N\' THEN \'' . $Lang->get('decima-human-resources::employee-management.n') .'\' END AS hr_em_la_is_half_day_label'),
		$DB->raw('CASE WHEN l.status = \'A\' THEN \'' . $Lang->get('decima-human-resources::employee-management.A') .'\' WHEN l.status = \'R\' THEN \'' . $Lang->get('decima-human-resources::employee-management.R') .'\' WHEN l.status = \'B\' THEN \'' . $Lang->get('decima-human-resources::employee-management.B') .'\' END AS hr_em_la_status_label'));



		$this->orderBy = array(array('hr_em_la_id', 'asc'));

		// $this->treeGrid = true;

		// $this->parentColumn = 'parent_id';

		// $this->leafColumn = 'is_leaf';
	}

}
