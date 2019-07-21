<?php

/**
 * @file
 * Application Routes.
 *
 * All DecimaHumanResources code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

/*
|--------------------------------------------------------------------------
| Package Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::group(array('middleware' => array('auth', 'check.first.time.access', 'check.access', 'csrf'), 'prefix' => 'human-resources/setup'), function()
{
	// AdvancedRoute::controller('/initial-accounting-setup', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\SettingManager');
});

Route::group(array('middleware' => array('auth'), 'prefix' => 'human-resources'), function()
{
	Route::group(array('prefix' => '/setup'), function()
	{
		Route::get('/department-management/new', function()
		{
			return Redirect::to('human-resources/setup/departament-management')->with('newDepartmentAction', true);
		});

		Route::get('/department-management/edit', function()
		{
			return Redirect::to('human-resources/setup/departament-management')->with('editDepartmentAction', true);
		});

		Route::get('/department-management/delete', function()
		{
			return Redirect::to('human-resources/setup/departament-management')->with('deleteDepartmentAction', true);
		});

		Route::get('/position-management/new', function()
		{
			return Redirect::to('human-resources/setup/position-management')->with('newPositionAction', true);
		});

		Route::get('/position-management/edit', function()
		{
			return Redirect::to('human-resources/setup/position-management')->with('editPositionAction', true);
		});

		Route::get('/position-management/delete', function()
		{
			return Redirect::to('human-resources/setup/position-management')->with('deletePositionAction', true);
		});

		Route::get('/leave-type-management/new', function()
		{
			return Redirect::to('human-resources/setup/leave-type-management')->with('newLeaveTypeAction', true);
		});

		Route::get('/leave-type-management/edit', function()
		{
			return Redirect::to('human-resources/setup/leave-type-management')->with('editLeaveTypeAction', true);
		});

		Route::get('/leave-type-management/delete', function()
		{
			return Redirect::to('human-resources/setup/leave-type-management')->with('deleteLeaveTypeAction', true);
		});

		Route::get('/phase-management/new', function()
		{
			return Redirect::to('human-resources/setup/phase-management')->with('newPhaseAction', true);
		});

		Route::get('/phase-management/edit', function()
		{
			return Redirect::to('human-resources/setup/phase-management')->with('editPhaseAction', true);
		});

		Route::get('/phase-management/delete', function()
		{
			return Redirect::to('human-resources/setup/phase-management')->with('deletePhaseAction', true);
		});

		Route::get('/holiday-management/new', function()
		{
			return Redirect::to('human-resources/setup/holiday-management')->with('newHolidayAction', true);
		});

		Route::get('/holiday-management/edit', function()
		{
			return Redirect::to('human-resources/setup/holiday-management')->with('editHolidayAction', true);
		});

		Route::get('/holiday-management/delete', function()
		{
			return Redirect::to('human-resources/setup/holiday-management')->with('deleteHolidayAction', true);
		});

		Route::get('/afp-management/new', function()
		{
			return Redirect::to('human-resources/setup/afp-management')->with('newAfpAction', true);
		});

		Route::get('/afp-management/edit', function()
		{
			return Redirect::to('human-resources/setup/afp-management')->with('editAfpAction', true);
		});

		Route::get('/afp-management/delete', function()
		{
			return Redirect::to('human-resources/setup/afp-management')->with('deleteAfpAction', true);
		});

		Route::group(array('middleware' => array('check.first.time.access', 'check.access', 'csrf')), function()
		{
			AdvancedRoute::controller('/phase-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\PhaseManager');

			AdvancedRoute::controller('/department-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\DepartmentManager');

			AdvancedRoute::controller('/position-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\PositionManager');

			AdvancedRoute::controller('/leave-type-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\LeaveTypeManager');

			AdvancedRoute::controller('/holiday-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\HolidayManager');

			AdvancedRoute::controller('/afp-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\AfpManager');
		});
	});

	Route::group(array('prefix' => '/transactions'), function()
	{
		Route::get('/employee-management/new', function()
		{
			return Redirect::to('/human-resources/transactions/employee-management')->with('newEmployeeAction', true);
		});

		Route::get('/employee-management/new-leave-application', function()
		{
			return Redirect::to('/human-resources/transactions/employee-management')->with('newLeaveApplicationAction', true);
		});

		Route::get('/employee-management/edit', function()
		{
			return Redirect::to('/human-resources/transactions/employee-management')->with('editEmployeeAction', true);
		});

		Route::get('/employee-management/delete', function()
		{
			return Redirect::to('/human-resources/transactions/employee-management')->with('deleteEmployeeAction', true);
		});

		Route::get('/task-management/new', function()
		{
			return Redirect::to('/human-resources/transactions/task-management')->with('newTaskAction', true);
		});

		Route::get('/task-management/view-all-tasks', function()
		{
			return Redirect::to('/human-resources/transactions/task-management')->with('viewAllTasksAction', true);
		});

		Route::get('/task-management/edit', function()
		{
			return Redirect::to('/human-resources/transactions/task-management')->with('editTaskAction', true);
		});

		Route::get('/task-management/delete', function()
		{
			return Redirect::to('/human-resources/transactions/task-management')->with('deleteTaskAction', true);
		});

		Route::get('/worked-hour-management/view-all-worked-hours', function()
		{
			return Redirect::to('/human-resources/transactions/worked-hour-management')->with('viewAllWorkedHoursAction', true);
		});

		Route::get('/worked-hour-management/check-in', function()
		{
			return Redirect::to('/human-resources/transactions/worked-hour-management')->with('checkInAction', true);
		});

		Route::get('/worked-hour-management/check-out', function()
		{
			return Redirect::to('/human-resources/transactions/worked-hour-management')->with('checkOutAction', true);
		});

		Route::group(array('middleware' => array('check.first.time.access', 'check.access', 'csrf')), function()
		{
			AdvancedRoute::controller('/employee-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\EmployeeManager');

			AdvancedRoute::controller('/task-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\TaskManager');

			AdvancedRoute::controller('/worked-hour-management', 'Mgallegos\DecimaHumanResources\HumanResources\Controllers\WorkedHourManager');
		});
	});
});
