<?php
/**
 * @file
 * Journals configuration config file.
 *
 * All DecimaHumanResources code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

return array(

	/*
 	|--------------------------------------------------------------------------
	| Employee Management
	|--------------------------------------------------------------------------
	|
	*/
	'employee-management' => array('journalizedType' => array('HR_Employee'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'position-management' => array('journalizedType' => array('HR_Position'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'department-management' => array('journalizedType' => array('HR_Department'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'leave-type-management' => array('journalizedType' => array('HR_Leave_Type'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'holiday-management' => array('journalizedType' => array('HR_Holiday'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'afp-management' => array('journalizedType' => array('HR_AFP'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'phase-management' => array('journalizedType' => array('HR_Phase'), 'recordsPerPage' => 4),

	/*
 	|--------------------------------------------------------------------------
	| Position Management
	|--------------------------------------------------------------------------
	|
	*/
	'worked-hour-management' => array('journalizedType' => array('HR_Worked_Hour'), 'recordsPerPage' => 4),
);
