<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use Illuminate\Database\DatabaseManager;

use Mgallegos\LaravelJqgrid\Repositories\EloquentRepositoryAbstract;

use Illuminate\Translation\Translator;

class EloquentEmployeeGridRepository extends EloquentRepositoryAbstract {

	public function __construct(DatabaseManager $DB, AuthenticationManagementInterface $AuthenticationManager, Translator $Lang)
	{

		    $this->DB = $DB;
				$this->DB->connection()->enableQueryLog();

		$this->Database = $DB->connection($AuthenticationManager->getCurrentUserOrganizationConnection())
								 ->table('HR_Employee AS e')
								//  ->join('SYS_Country AS s', 's.id', '=', 'e.country_id')
								 ->leftJoin('HR_Department AS d', 'd.id', '=', 'e.departament_id')
								 ->leftJoin('HR_Position AS p', 'p.id', '=', 'e.position_id')
								 ->leftJoin('BANK_Bank AS b', 'b.id', '=', 'e.bank_id')
								//  ->leftJoin('SEC_User AS u', 'u.id', '=', 'e.user_id')
								//->join('HR_Position AS p', 'p.id', '=', 'e.position_id')
								//->join('HR_Department AS d', 'd.id', '=', 'e.departament_id');
								->where('e.organization_id', '=', $AuthenticationManager->getCurrentUserOrganizationId());
								//->whereNull('t.deleted_at');

		$this->visibleColumns = array(
										'e.id AS hr_em_id',
										'e.names as hr_em_names',
										'e.surnames AS hr_em_surnames',
										'e.gender as hr_em_gender',
									  'e.marital_status AS hr_em_marital_status',
										'e.children_number AS hr_em_children_number',
										'e.place_birth AS hr_em_place_birth',
										'e.country_id AS hr_em_country_id',
										'e.passport_number AS hr_em_passport_number',
										'e.personal_email AS hr_em_personal_email',
									  'e.residence_phone AS hr_em_residence_phone',
										'e.mobile_phone AS hr_em_mobile_phone',
										'e.emergency_contact AS hr_em_emergency_contact',
									  'e.emergency_phone AS hr_em_emergency_phone',
										'e.blood_type AS hr_em_blood_type',
										// 's.name AS hr_em_country',
										'e.status AS hr_em_status',
										'e.departament_id AS hr_em_departament_id',
										'd.name AS hr_em_departament',
										'e.position_id AS hr_em_position_id',
										'p.name AS hr_em_position',
										'e.salary as hr_em_salary',
									  'e.start_date as hr_em_start_date',
										'e.bank_id as hr_em_bank_id',
										'b.name as hr_em_bank_label',
										'e.bank_account_number as hr_em_bank_account_number',
										'e.work_email as hr_em_work_email',
										'e.work_phone as hr_em_work_phone',
										'e.work_phone_extension as hr_em_work_phone_extension',
										'e.work_mobile as hr_em_work_mobile',
										'e.user_id as hr_em_user_id',
										'e.tax_id as hr_em_tax_id',
										'e.single_identity_document_number as hr_em_single_identity_document_number',
										'e.profile_image_url as hr_em_profile_image_url',
										'e.profile_image_medium_url as hr_em_profile_image_medium_url',
										'e.profile_image_small_url as hr_em_profile_image_small_url',
										'e.tax_id_name as hr_em_tax_id_name',
										'e.single_identity_document_number_name as hr_em_single_identity_document_number_name',
										'e.social_security_number as hr_em_social_security_number',
										'e.social_security_number_name as hr_em_social_security_number_name',
										// 'e.afp_id as hr_em_afp_id',
										'e.single_previsional_number as hr_em_single_previsional_number',
										'e.single_previsional_number_name as hr_em_single_previsional_number_name',
										'e.street1 as hr_em_street1',
										'e.street2 as hr_em_street2',
										'e.city_name as hr_em_city_name',
										'e.state_name as hr_em_state_name',
										'e.zip_code as hr_em_zip_code',
										'e.leave_approver_id as hr_em_leave_approver_id',
										'e.status as hr_em_status',
										// $DB->raw('CONCAT(u.firstname," ",u.lastname) AS hr_em_user'),
									  // $DB->raw('DATE_FORMAT(e.date_birth, "' . $Lang->get('form.mysqlDateFormat') . '") as hr_em_date_birth '),
										'e.date_birth AS hr_em_date_birth',
										$DB->raw('CASE WHEN e.gender = \'M\' THEN \'' . $Lang->get('decima-human-resources::employee-management.M') .'\' WHEN e.gender = \'F\' THEN \'' . $Lang->get('decima-human-resources::employee-management.F') .'\' END AS hr_em_gender_label'),
										$DB->raw('CASE WHEN e.marital_status = \'S\' THEN \'' . $Lang->get('decima-human-resources::employee-management.S') .'\' WHEN e.marital_status = \'C\' THEN \'' . $Lang->get('decima-human-resources::employee-management.C') .'\' WHEN e.marital_status = \'V\' THEN \'' . $Lang->get('decima-human-resources::employee-management.V') .'\' END AS hr_em_marital_status_label'),
										$DB->raw('CASE WHEN e.status = \'T\' THEN \'' . $Lang->get('decima-human-resources::employee-management.T') .'\' WHEN e.status = \'I\' THEN \'' . $Lang->get('decima-human-resources::employee-management.I') .'\' WHEN e.status = \'L\' THEN \'' . $Lang->get('decima-human-resources::employee-management.L') .'\' END AS hr_em_status_label')
										//  para AFP $DB->raw('CASE WHEN e.afp = \'T\' THEN \'' . $Lang->get('decima-human-resources::employee-management.T') .'\' WHEN e.status = \'I\' THEN \'' . $Lang->get('decima-human-resources::employee-management.I') .'\' WHEN e.status = \'L\' THEN \'' . $Lang->get('decima-human-resources::employee-management.L') .'\' END AS hr_em_status_label')
										);

		//$this->visibleColumns = array('t1.id AS module_app_id', $DB->raw('CASE t1.field0 WHEN 1 THEN 0 ELSE 1 END AS module_app_field0'),);

		$this->orderBy = array(array('e.id', 'asc'));

		// $this->treeGrid = true;

		// $this->parentColumn = 'parent_id';

		// $this->leafColumn = 'is_leaf';
	}

}
