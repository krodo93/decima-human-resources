<?php
/**
 * @file
 * Module App Management Interface Implementation.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveApplicationManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication\EloquentLeaveApplicationGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication\LeaveApplicationInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\LeaveTypeInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class LeaveApplicationManager implements LeaveApplicationManagementInterface {

  /**
   * Authentication Management Interface
   *
   * @var App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface
   *
   */
  protected $AuthenticationManager;

  /**
  * Journal Management Interface (Security)
  *
  * @var App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface
  *
  */
  protected $JournalManager;

  /**
  * Journal (Security)
  *
  * @var App\Kwaai\Security\Repositories\Journal\JournalInterface
  *
  */
  protected $Journal;

  /**
	 * Grid Encoder
	 *
	 * @var Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface
	 *
	 */
	protected $GridEncoder;

  /**
	 * Eloquent Grid Repository
	 *
	 * @var Vendor\DecimaModule\Module\Repositories\LeaveApplication\EloquentLeaveApplicationGridRepository
	 *
	 */
	protected $EloquentLeaveApplicationGridRepository;

  /**
	 *  Module Table Name Interface
	 *
	 * @var Vendor\DecimaModule\Module\Repositories\LeaveApplication\LeaveApplicationInterface
	 *
	 */
	protected $LeaveApplication;

  /**
   *  Module Table Name Interface
   *
   * @var Vendor\DecimaModule\Module\Repositories\LeaveApplication\LeaveApplicationInterface
   *
   */
  protected $LeaveType;

  /**
   *  Module Table Name Interface
   *
   * @var Vendor\DecimaModule\Module\Repositories\Employee\EmployeeInterface
   *
   */
  protected $Employee;

  /**
   * Carbon instance
   *
   * @var Carbon\Carbon
   *
   */
  protected $Carbon;

  /**
   * Laravel Database Manager
   *
   * @var Illuminate\Database\DatabaseManager
   *
   */
  protected $DB;

  /**
   * Laravel Translator instance
   *
   * @var Illuminate\Translation\Translator
   *
   */
  protected $Lang;

  /**
   * Laravel Repository instance
   *
   * @var Illuminate\Config\Repository
   *
   */
  protected $Config;

	public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentLeaveApplicationGridRepository $EloquentLeaveApplicationGridRepository, LeaveApplicationInterface $LeaveApplication,  LeaveTypeInterface $LeaveType, EmployeeInterface $Employee, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
	{
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentLeaveApplicationGridRepository = $EloquentLeaveApplicationGridRepository;

    $this->LeaveApplication = $LeaveApplication;

    $this->LeaveType = $LeaveType;

    $this->Employee = $Employee;

    $this->Carbon = $Carbon;

    $this->DB = $DB;

		$this->Lang = $Lang;

		$this->Config = $Config;
	}

  /**
   * Echo grid data in a jqGrid compatible format
   *
   * @param array $post
   *	All jqGrid posted data
   *
   * @return void
   */
  public function getGridData(array $post)
  {
    $this->GridEncoder->encodeRequestedData($this->EloquentLeaveApplicationGridRepository, $post);
  }

  /**
   * Get Halfday
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getHalfday()
  {
    $halfday = array();

    array_push($halfday, array('label'=> $this->Lang->get('decima-human-resources::employee-management.y'), 'value'=> 'Y'));
    array_push($halfday, array('label'=> $this->Lang->get('decima-human-resources::employee-management.n'), 'value'=> 'N'));

    return $halfday;
  }

  /**
   * Get Status
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getStatus()
  {
    $status = array();

    array_push($status, array('label'=> $this->Lang->get('decima-human-resources::employee-management.A'), 'value'=> 'A'));
    array_push($status, array('label'=> $this->Lang->get('decima-human-resources::employee-management.R'), 'value'=> 'R'));
    array_push($status, array('label'=> $this->Lang->get('decima-human-resources::employee-management.B'), 'value'=> 'B'));

    return $status;
  }
  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getLeaveApplications()
  {
    $leaveApplication = array();

    $this->LeaveApplication->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($LeaveApplication) use (&$leaveApplication)
    {
      array_push($leaveApplication, array('label'=> $LeaveApplication->name , 'value'=>$LeaveApplication->id));
    });

    return $leaveApplication;
  }

  /**
	 * Create a new ...
	 *
	 * @param array $input
   * 	An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
	 * @return JSON encoded string
	 *  A string as follows:
	 *	In case of success: {"success" : form.defaultSuccessSaveMessage}
	 */
	public function create(array $input)
	{
    unset($input['_token'], $input['is_half_day_label'], $input['status_label'], $input['leave_type_label'], $input['names'], $input['departament'], $input['departament_id'], $input['leave_approver_label']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $input = eloquent_array_filter_for_insert($input);
		$input = array_add($input, 'organization_id', $organizationId);
    $input['from_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['from_date'])->format('Y-m-d');
    $input['to_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['to_date'])->format('Y-m-d');

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId)
		{
      $LeaveApplication = $this->LeaveApplication->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $LeaveApplication->applicant_id, 'journalized_type' => $this->Employee->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::employee-management.addedJournalApplication', array('app' => $LeaveApplication->from_date . ' ' . $LeaveApplication->to_date)), $Journal));

    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage')));
  }

  /**
   * Update an existing ...
   *
   * @param array $input
   * 	An array as follows: array('id' => $id, 'field0'=>$field0, 'field1'=>$field1
   *
   * @return JSON encoded string
   *  A string as follows:
   *	In case of success: {"success" : form.defaultSuccessUpdateMessage}
   */
  public function update(array $input)
  {
    unset($input['_token'], $input['is_half_day_label'], $input['status_label'], $input['leave_type_label'], $input['names'],$input['departament'], $input['departament_id'], $input['leave_approver_label']);
    $input = eloquent_array_filter_for_update($input);

    $input['from_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['from_date'])->format('Y-m-d');
    $input['to_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['to_date'])->format('Y-m-d');
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use (&$input)
    {
      $LeaveApplication = $this->LeaveApplication->byId($input['id']);
      $unchangedLeaveApplicationValues = $LeaveApplication->toArray();
      $this->LeaveApplication->update($input, $LeaveApplication);

      $diff = 0;

      foreach ($input as $key => $value)
      {
        if($unchangedLeaveApplicationValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $LeaveApplication->applicant_id, 'journalized_type' => $this->Employee->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
          }

          if($key == 'status' || $key == 'halfday')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $this->Lang->get('decima-human-resources::employee-management.' . $unchangedLeaveApplicationValues[$key]), 'new_value' => $this->Lang->get('decima-human-resources::employee-management.' . $value)), $Journal);
          }
          else if ($key == 'LeaveApplication')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('module::app.field1'), 'field_lang_key' => 'module::app.field1', 'old_value' => ' ', 'new_value' => ''), $Journal);
          }
          else if ($key == 'date_birth')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $this->Carbon->createFromFormat('Y-m-d', $unchangedLeaveApplicationValues[$key], 'UTC')->format($this->Lang->get('form.phpShortDateFormat')), 'new_value' => $this->Carbon->createFromFormat('Y-m-d', $value, 'UTC')->format($this->Lang->get('form.phpShortDateFormat'))), $Journal);
          }
          else if ($key == 'start_date')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $this->Carbon->createFromFormat('Y-m-d', $unchangedLeaveApplicationValues[$key], 'UTC')->format($this->Lang->get('form.phpShortDateFormat')), 'new_value' => $this->Carbon->createFromFormat('Y-m-d', $value, 'UTC')->format($this->Lang->get('form.phpShortDateFormat'))), $Journal);
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('module::app.' . camel_case($key)), 'field_lang_key' => 'module::app.' . camel_case($key), 'old_value' => $unchangedLeaveApplicationValues[$key], 'new_value' => $value), $Journal);
          }
        }
      }
    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage')));
  }



  /**
   * Delete existing ... (soft delete)
   *
   * @param array $input
	 * 	An array as follows: array($id0, $id1,…);
   *
   * @return JSON encoded string
   *  A string as follows:
   *	In case of success: {"success" : form.defaultSuccessDeleteMessage}
   */
   public function delete(array $input)
   {
     $count = 0;

     $this->DB->transaction(function() use ($input, &$count, &$Journal)
     {
       $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
       $organizationId = $this->AuthenticationManager->getCurrentUserOrganization('id');

       foreach ($input['id'] as $key => $id)
       {
         $count++;

         $LeaveApplication = $this->LeaveApplication->byId($id);

         if(is_null($Journal))
         {
           $Journal = $this->Journal->create(array('journalized_id' => $LeaveApplication->applicant_id, 'journalized_type' => $this->Employee->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
         }

         $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::employee-management.deletedJournalApplication', array('app' => $LeaveApplication->from_date . ' ' . $LeaveApplication->to_date))), $Journal);

         $this->LeaveApplication->delete(array($id));
       }
     });

     if($count == 1)
     {
       return json_encode(array('success' => $this->Lang->get('decima-human-resources::employee-management.successDeleted0Message')));
     }
     else
     {
       return json_encode(array('success' => $this->Lang->get('decima-human-resources::employee-management.successDeleted1Message')));
     }
   }
}
