<?php
/**
 * @file
 * LeaveTypeManagerement Interface Implementation.
 *
 * All LeaveTypeManageris copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveTypeManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\EloquentLeaveTypeGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\LeaveTypeInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class LeaveTypeManager implements LeaveTypeManagementInterface {

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
   * @var App\Kwaai\Template\Repositories\LeaveType\EloquentLeaveTypeGridRepository
   *
   */
  protected $EloquentLeaveTypeGridRepository;

  /**
   *  LeaveTypeManagere Interface
   *
   * @var App\Kwaai\Template\Repositories\LeaveType\LeaveTypeInterface
   *
   */
  protected $LeaveType;

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

  public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentLeaveTypeGridRepository $EloquentLeaveTypeGridRepository, LeaveTypeInterface $LeaveType, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
  {
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentLeaveTypeGridRepository = $EloquentLeaveTypeGridRepository;

    $this->LeaveType = $LeaveType;

    $this->Carbon = $Carbon;

    $this->DB = $DB;

    $this->Lang = $Lang;

    $this->Config = $Config;
  }

  /**
   * Echo grid data in a jqGrid compatible format
   *
   * @param array $post
   *  All jqGrid posted data
   *
   * @return void
   */
  public function getGridData(array $post)
  {
    $this->GridEncoder->encodeRequestedData($this->EloquentLeaveTypeGridRepository, $post);
  }

  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getLeaveType()
  {
    $leavetype = array();
    $this->LeaveType->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($LeaveType) use (&$leavetype)
    {
      array_push($leavetype, array('label'=> $LeaveType->name , 'value'=>$LeaveType->id));
    });
    return $leavetype;
  }

  /**
   * Create a new ...
   *
   * @param array $input
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @return JSON encoded string
   *  A string as follows:
   *  In case of success: {"success" : form.defaultSuccessSaveMessage}
   */

  public function create(array $input)
  {
    unset($input['_token']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $input = eloquent_array_filter_for_insert($input);
    $input = array_add($input, 'organization_id', $organizationId);
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId)
    {
      $LeaveType = $this->LeaveType->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $LeaveType->id, 'journalized_type' => $this->LeaveType->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::leave-type-management.addedJournal', array('name' => $LeaveType->name . ' ' , $LeaveType->key)), $Journal));

    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage')));
  }

  /**
   * Update an existing ...
   *
   * @param array $input
   *  An array as follows: array('id' => $id, 'field0'=>$field0, 'field1'=>$field1
   *
   * @return JSON encoded string
   *  A string as follows:
   *  In case of success: {"success" : form.defaultSuccessUpdateMessage}
   */
  public function update(array $input)
  {
    unset($input['_token']);
    $input = eloquent_array_filter_for_update($input);
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use (&$input)
    {
      $LeaveType = $this->LeaveType->byId($input['id']);
      $unchangedLeaveTypeValues = $LeaveType->toArray();

      $this->LeaveType->update($input, $LeaveType);

      $diff = 0;

    foreach ($input as $key => $value)
      {
        if($unchangedLeaveTypeValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $LeaveType->id, 'journalized_type' => $this->LeaveType->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }

          if($key == 'is_leave_without_pay')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::leave-type-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::leave-type-management.' . camel_case($key), 'old_value' => $this->Lang->get('journal.' . $unchangedLeaveTypeValues[$key]), 'new_value' => $this->Lang->get('journal.' . $value)), $Journal);
          }else if($key == 'include_holidays_within_leaves_as_leaves')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::leave-type-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::leave-type-management.' . camel_case($key), 'old_value' => $this->Lang->get('journal.' . $unchangedLeaveTypeValues[$key]), 'new_value' => $this->Lang->get('journal.' . $value)), $Journal);
          }else if($key == 'field0')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::leave-type-management.'), 'field_lang_key' => 'decima-human-resources::leave-type-management.', 'old_value' => $this->Lang->get('decima-human-resources::employee-management.' . $unchangedLeaveTypeValues[$key]), 'new_value' => $this->Lang->get('LeaveTypeManager' . $value)), $Journal);
          }else if ($key == 'field1')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::leave-type-management.'), 'field_lang_key' => 'decima-human-resources::leave-type-management.', 'old_value' => ' ', 'new_value' => ''), $Journal);
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::leave-type-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::leave-type-management.' . camel_case($key), 'old_value' => $unchangedLeaveTypeValues[$key], 'new_value' => $value), $Journal);
          }
        }
      }
    });
    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage')));
  }

  /**
   * Delete an existing ... (soft delete)
   *
   * @param array $input
   *  An array as follows: array(id => $id);
   *
   * @return JSON encoded string
   *  A string as follows:
   *  In case of success: {"success" : form.defaultSuccessDeleteMessage}
   */


  /**
   * Delete existing ... (soft delete)
   *
   * @param array $input
   *  An array as follows: array($id0, $id1,…);
   *
   * @return JSON encoded string
   *  A string as follows:
   *  In case of success: {"success" : form.defaultSuccessDeleteMessage}
   */
   public function delete(array $input)
   {
     $count = 0;
     $this->DB->transaction(function() use ($input, &$count)
     {
       $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
       $organizationId = $this->AuthenticationManager->getCurrentUserOrganization('id');
       foreach ($input['id'] as $key => $id)
       {
         $count++;
         $LeaveType = $this->LeaveType->byId($id);
         $Journal = $this->Journal->create(array('journalized_id' => $id, 'journalized_type' => $this->LeaveType->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
         $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::leave-type-management.deletedJournal', array('name' => $LeaveType->name))), $Journal);
         $this->LeaveType->delete(array($id));
       }
     });
     if($count == 1)
     {
       return json_encode(array('success' => $this->Lang->get('form.defaultSuccessDeleteMessage')));
     }
     else
     {
       return json_encode(array('success' => $this->Lang->get('form.defaultSuccessDeleteMessage')));
     }
   }
}
