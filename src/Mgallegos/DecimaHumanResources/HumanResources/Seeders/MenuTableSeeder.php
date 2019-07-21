<?php
/**
 * @file
 * SEC_User Table Seeder
 *
 * All DecimaHumanResources code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */
namespace Mgallegos\DecimaHumanResources\HumanResources\Seeders;

use DB;
use App\Kwaai\Security\Module;
use App\Kwaai\Security\Menu;
use App\Kwaai\Security\Permission;
use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder {

	public function run()
	{
		Module::create(array('name' => 'Human Resources', 'lang_key' => 'decima-human-resources::menu.hrModule', 'icon' => 'fa fa-users', 'created_by' => 1));
		$moduleId = DB::table('SEC_Module')->max('id');

		Menu::create(array('name' => 'Setup (HR)', 'lang_key' => 'decima-human-resources::menu.setup', 'url' => null, 'icon' => 'fa fa-gears', 'parent_id' => null, 'module_id' => $moduleId, 'created_by' => 1));

		$parentMenuId = DB::table('SEC_Menu')->max('id');

		Menu::create(array('name' => 'Initial Human Resources Setup', 'lang_key' => 'decima-human-resources::menu.initialHumanResourcesSetup', 'url' => '/human-resources/setup/initial-human-resources-setup', 'action_button_id' => '', 'action_lang_key' => 'decima-asset::menu.initialHumanResourcesSetupAction', 'icon' => 'fa fa-gear', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));
		Menu::create(array('name' => 'Department Management', 'lang_key' => 'decima-human-resources::menu.departmentManagement', 'url' => '/human-resources/setup/department-management', 'action_button_id' => 'hr-dm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.departmentManagementAction', 'icon' => 'fa fa-sitemap', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Department', 'key' => 'newDepartment', 'lang_key' => 'decima-human-resources::menu.newDepartment', 'url' => '/human-resources/setup/department-management/new', 'alias_url' => '/human-resources/setup/department-management', 'action_button_id' => 'hr-dm-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newDepartmentAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Edit Department', 'key' => 'editDepartment', 'lang_key' => 'decima-human-resources::menu.editDepartment', 'url' => '/human-resources/setup/department-management/edit', 'alias_url' => '/human-resources/setup/department-management', 'action_button_id' => 'hr-dm-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editDepartmentAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Department', 'key' => 'deleteDepartment', 'lang_key' => 'decima-human-resources::menu.deleteDepartment', 'url' => '/human-resources/setup/department-management/delete', 'alias_url' => '/human-resources/setup/department-management', 'action_button_id' => 'hr-dm-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deleteDepartmentAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Position Management', 'lang_key' => 'decima-human-resources::menu.positionManagement', 'url' => '/human-resources/setup/position-management', 'action_button_id' => 'hr-pm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.positionManagementAction', 'icon' => 'fa fa-briefcase', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Position', 'key' => 'newPosition', 'lang_key' => 'decima-human-resources::menu.newPosition', 'url' => '/human-resources/setup/position-management/new', 'alias_url' => '/human-resources/setup/position-management', 'action_button_id' => 'hr-pm-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newPositionAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Edit Position', 'key' => 'editPosition', 'lang_key' => 'decima-human-resources::menu.editPosition', 'url' => '/human-resources/setup/position-management/edit', 'alias_url' => '/human-resources/setup/position-management', 'action_button_id' => 'hr-pm-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editPositionAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Position', 'key' => 'deletePosition', 'lang_key' => 'decima-human-resources::menu.deletePosition', 'url' => '/human-resources/setup/position-management/delete', 'alias_url' => '/human-resources/setup/position-management', 'action_button_id' => 'hr-pm-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deletePositionAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Leave Type Management', 'lang_key' => 'decima-human-resources::menu.leaveTypeManagement', 'url' => '/human-resources/setup/leave-type-management', 'action_button_id' => 'hr-ltm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.leaveTypeManagementAction', 'icon' => 'fa fa-tags', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Leave Type', 'key' => 'newLeaveType', 'lang_key' => 'decima-human-resources::menu.newLeaveType', 'url' => '/human-resources/setup/leave-type-management/new', 'alias_url' => '/human-resources/setup/leave-type-management', 'action_button_id' => 'hr-ltm-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newLeaveTypeAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Edit Leave Type', 'key' => 'editLeaveType', 'lang_key' => 'decima-human-resources::menu.editLeaveType', 'url' => '/human-resources/setup/leave-type-management/edit', 'alias_url' => '/human-resources/setup/leave-type-management', 'action_button_id' => 'hr-ltm-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editLeaveTypeAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Leave Type', 'key' => 'deleteLeaveType', 'lang_key' => 'decima-human-resources::menu.deleteLeaveType', 'url' => '/human-resources/setup/leave-type-management/delete', 'alias_url' => '/human-resources/setup/leave-type-management', 'action_button_id' => 'hr-ltm-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deleteLeaveTypeAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Holiday Management', 'lang_key' => 'decima-human-resources::menu.holidayManagement', 'url' => '/human-resources/setup/holiday-management', 'action_button_id' => 'hr-hm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.holidayManagementAction', 'icon' => 'fa fa-calendar', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Holiday', 'key' => 'newHoliday', 'lang_key' => 'decima-human-resources::menu.newHoliday', 'url' => '/human-resources/setup/holiday-management/new', 'alias_url' => '/human-resources/setup/holiday-management', 'action_button_id' => 'hr-hm-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newHolidayAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Edit Holiday', 'key' => 'editHoliday', 'lang_key' => 'decima-human-resources::menu.editHoliday', 'url' => '/human-resources/setup/holiday-management/edit', 'alias_url' => '/human-resources/setup/holiday-management', 'action_button_id' => 'hr-hm-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editHolidayAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Holiday', 'key' => 'deleteHoliday', 'lang_key' => 'decima-human-resources::menu.deleteHoliday', 'url' => '/human-resources/setup/holiday-management/delete', 'alias_url' => '/human-resources/setup/holiday-management', 'action_button_id' => 'hr-hm-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deleteHolidayAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Phase Management', 'lang_key' => 'decima-human-resources::menu.phaseManagement', 'url' => '/human-resources/setup/phase-management', 'action_button_id' => 'hr-pm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.phaseManagementAction', 'icon' => 'fa fa-list-ol', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Phase', 'key' => 'newPhase', 'lang_key' => 'decima-human-resources::menu.newPhase', 'url' => '/human-resources/setup/phase-management/new', 'alias_url' => '/human-resources/setup/phase-management', 'action_button_id' => 'hr-pm-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newPhaseAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Edit Phase', 'key' => 'editPhase', 'lang_key' => 'decima-human-resources::menu.editPhase', 'url' => '/human-resources/setup/phase-management/edit', 'alias_url' => '/human-resources/setup/phase-management', 'action_button_id' => 'hr-pm-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editPhaseAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Phase', 'key' => 'deletePhase', 'lang_key' => 'decima-human-resources::menu.deletePhase', 'url' => '/human-resources/setup/phase-management/delete', 'alias_url' => '/human-resources/setup/phase-management', 'action_button_id' => 'hr-pm-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deletePhaseAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'AFP Management', 'lang_key' => 'decima-human-resources::menu.afpManagement', 'url' => '/human-resources/setup/afp-management', 'action_button_id' => 'hr-pm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.afpManagementAction', 'icon' => 'fa fa-building', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New AFP', 'key' => 'newAFP', 'lang_key' => 'decima-human-resources::menu.newAFP', 'url' => '/human-resources/setup/afp-management/new', 'alias_url' => '/human-resources/setup/afp-management', 'action_button_id' => 'hr-afp-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newAFPAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Edit AFP', 'key' => 'editAFP', 'lang_key' => 'decima-human-resources::menu.editAFP', 'url' => '/human-resources/setup/afp-management/edit', 'alias_url' => '/human-resources/setup/afp-management', 'action_button_id' => 'hr-afp-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editAFPAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete AFP', 'key' => 'deleteAFP', 'lang_key' => 'decima-human-resources::menu.deleteAFP', 'url' => '/human-resources/setup/afp-management/delete', 'alias_url' => '/human-resources/setup/afp-management', 'action_button_id' => 'hr-afp-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deleteAFPAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Transactions (HR)', 'lang_key' => 'decima-human-resources::menu.transactions', 'url' => null, 'icon' => 'fa fa-exchange', 'parent_id' => null, 'module_id' => $moduleId, 'created_by' => 1));

		$parentMenuId = DB::table('SEC_Menu')->max('id');

		Menu::create(array('name' => 'Employee Management', 'lang_key' => 'decima-human-resources::menu.employeeManagement', 'url' => '/human-resources/transactions/employee-management', 'action_button_id' => 'hr-em-btn-close', 'action_lang_key' => 'decima-human-resources::menu.employeeManagementAction', 'icon' => 'fa fa-users', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Employee', 'key' => 'newEmployee', 'lang_key' => 'decima-human-resources::menu.newEmployee', 'url' => '/human-resources/transactions/employee-management/new', 'alias_url' => '/human-resources/transactions/employee-management', 'action_button_id' => 'hr-em-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newEmployeeAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'New Leave Application', 'key' => 'newLeaveApplication', 'lang_key' => 'decima-human-resources::menu.newLeaveApplication', 'url' => '/human-resources/transactions/employee-management/new-leave-application', 'alias_url' => '/human-resources/transactions/employee-management', 'action_button_id' => 'hr-em-btn-new-leave-application-helper', 'action_lang_key' => 'decima-human-resources::menu.newLeaveApplicationAction', 'icon' => 'fa fa-pencil-square-o', 'is_only_shortcut' => false, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'View All Employees', 'key' => 'viewAllEmployees', 'lang_key' => 'decima-human-resources::menu.viewAllEmployees', 'url' => '/human-resources/transactions/employee-management/view-all-employees', 'alias_url' => '/human-resources/transactions/employee-management', 'action_button_id' => 'hr-tm-btn-view-all-employees-helper', 'action_lang_key' => 'decima-human-resources::menu.viewAllEmployeesAction', 'is_only_shortcut' => false, 'menu_id' => $lastMenuId, 'icon' => 'fa fa-eye', 'created_by' => 1, 'hidden' => false));
		Permission::create(array('name' => 'Edit Employee', 'key' => 'editEmployee', 'lang_key' => 'decima-human-resources::menu.editEmployee', 'url' => '/human-resources/transactions/employee-management/edit', 'alias_url' => '/human-resources/transactions/employee-management', 'action_button_id' => 'hr-em-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editEmployeeAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Employee', 'key' => 'deleteEmployee', 'lang_key' => 'decima-human-resources::menu.deleteEmployee', 'url' => '/human-resources/transactions/employee-management/delete', 'alias_url' => '/human-resources/transactions/employee-management', 'action_button_id' => 'hr-em-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deleteEmployeeAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Task Management', 'lang_key' => 'decima-human-resources::menu.taskManagement', 'url' => '/human-resources/transactions/task-management', 'action_button_id' => 'hr-tm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.taskManagementAction', 'icon' => 'fa fa-tasks', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'New Task', 'key' => 'newTask', 'lang_key' => 'decima-human-resources::menu.newTask', 'url' => '/human-resources/transactions/task-management/new', 'alias_url' => '/human-resources/transactions/task-management', 'action_button_id' => 'hr-tm-btn-new', 'action_lang_key' => 'decima-human-resources::menu.newTaskAction', 'icon' => 'fa fa-plus', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'View All Tasks', 'key' => 'viewAllTasks', 'lang_key' => 'decima-human-resources::menu.viewAllTasks', 'url' => '/human-resources/transactions/task-management/view-all-tasks', 'alias_url' => '/human-resources/transactions/task-management', 'action_button_id' => 'hr-tm-btn-view-all-tasks-helper', 'action_lang_key' => 'decima-human-resources::menu.newLeaveApplicationAction', 'icon' => 'fa fa-eye', 'is_only_shortcut' => false, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => false));
		Permission::create(array('name' => 'Edit Task', 'key' => 'editTask', 'lang_key' => 'decima-human-resources::menu.editTask', 'url' => '/human-resources/transactions/task-management/edit', 'alias_url' => '/human-resources/transactions/task-management', 'action_button_id' => 'hr-tm-btn-edit-helper', 'action_lang_key' => 'decima-human-resources::menu.editTaskAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));
		Permission::create(array('name' => 'Delete Task', 'key' => 'deleteTask', 'lang_key' => 'decima-human-resources::menu.deleteTask', 'url' => '/human-resources/transactions/task-management/delete', 'alias_url' => '/human-resources/transactions/task-management', 'action_button_id' => 'hr-tm-btn-delete-helper', 'action_lang_key' => 'decima-human-resources::menu.deleteTaskAction', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => true));

		Menu::create(array('name' => 'Worked Hour Management', 'lang_key' => 'decima-human-resources::menu.workedHourManagement', 'url' => '/human-resources/transactions/worked-hour-management', 'action_button_id' => 'hr-whm-btn-close', 'action_lang_key' => 'decima-human-resources::menu.workedHourManagementAction', 'icon' => 'fa fa-clock-o', 'parent_id' => $parentMenuId, 'module_id' => $moduleId, 'created_by' => 1));

		$lastMenuId = DB::table('SEC_Menu')->max('id');

		Permission::create(array('name' => 'View All Worked Hours', 'key' => 'viewAllWorkedHours', 'lang_key' => 'decima-human-resources::menu.viewAllWorkedHours', 'url' => '/human-resources/transactions/worked-hour-management/view-all-worked-hours', 'alias_url' => '/human-resources/transactions/worked-hour-management', 'action_button_id' => 'hr-whm-btn-view-all-worked-hours-helper', 'action_lang_key' => 'decima-human-resources::menu.viewAllWorkedHoursAction', 'icon' => 'fa fa-eye', 'is_only_shortcut' => false, 'menu_id' => $lastMenuId, 'created_by' => 1, 'hidden' => false));
		Permission::create(array('name' => 'Check-in', 'key' => 'newCheckIn', 'lang_key' => 'decima-human-resources::menu.checkIn', 'url' => '/human-resources/transactions/worked-hour-management/check-in', 'alias_url' => '/human-resources/transactions/worked-hour-management', 'action_button_id' => 'hr-whm-btn-start', 'action_lang_key' => 'decima-human-resources::menu.checkInAction', 'icon' => 'fa fa-sign-in', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
		Permission::create(array('name' => 'Check-out', 'key' => 'newCheckOut', 'lang_key' => 'decima-human-resources::menu.checkOut', 'url' => '/human-resources/transactions/worked-hour-management/check-out', 'alias_url' => '/human-resources/transactions/worked-hour-management', 'action_button_id' => 'hr-whm-btn-end', 'action_lang_key' => 'decima-human-resources::menu.checkOutAction', 'icon' => 'fa fa-sign-out', 'is_only_shortcut' => true, 'menu_id' => $lastMenuId, 'created_by' => 1));
	}
}
