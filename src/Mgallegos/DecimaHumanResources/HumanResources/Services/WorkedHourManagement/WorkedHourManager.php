<?php
/**
 * @file
 * Module App Management Interface Implementation.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\WorkedHourManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\EloquentWorkedHourGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\WorkedHourInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class WorkedHourManager implements WorkedHourManagementInterface {

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
	 * @var Vendor\DecimaModule\Module\Repositories\WorkedHour\EloquentWorkedHourGridRepository
	 *
	 */
	protected $EloquentWorkedHourGridRepository;

  /**
	 *  Module Table Name Interface
	 *
	 * @var Vendor\DecimaModule\Module\Repositories\WorkedHour\WorkedHourInterface
	 *
	 */
	protected $WorkedHour;

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

	public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentWorkedHourGridRepository $EloquentWorkedHourGridRepository, WorkedHourInterface $WorkedHour, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
	{
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentWorkedHourGridRepository = $EloquentWorkedHourGridRepository;

    $this->WorkedHour = $WorkedHour;

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
    $this->GridEncoder->encodeRequestedData($this->EloquentWorkedHourGridRepository, $post);
  }

  /**
   * Get WorkedHour
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getWorkedHours()
  {
    $workedHour = array();

    $this->WorkedHour->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($WorkedHour) use (&$workedHour)
    {
      array_push($workedHour, array('label' => $WorkedHour->name , 'value' => $WorkedHour->id));
    });

    return $workedHour;
  }

  /**
   * Get CurrentDateAndTime
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getCurrentDateAndTime()
  {
    // $this->Carbon->createFromFormat('Y-m-d H:i:s', $Journal->created_at, 'UTC')->setTimezone($this->AuthenticationManager->getLoggedUserTimeZone())->format($this->Lang->get('form.phpDateTimeFormat'));
    return str_replace(array('am', 'pm'), array('a.m.', 'p.m.'), $this->Carbon->now()->setTimezone($this->AuthenticationManager->getLoggedUserTimeZone())->format($this->Lang->get('form.phpDateTimeFormat')));
  }

  /**
	 * Create a new WorkedHour
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
    $employeeLabel=$input['employee_label'];
    unset($input['_token'], $input['employee_label']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $startDate = $input['start_date'];
    $input = eloquent_array_filter_for_insert($input);
		$input = array_add($input, 'organization_id', $organizationId);
    $input['start_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpDateTimeFormat'), $input['start_date'])->format('Y-m-d H:i:s');
    // $input['start_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpDateTimeFormat'), str_replace(array('a.m.', 'p.m.'), array('am', 'pm'), $input['start_date']), $this->AuthenticationManager->getLoggedUserTimeZone())->setTimezone('UTC')->format('Y-m-d H:i:s');
    // var_dump($input['start_date']);die();
    // $input['amount'] = remove_thousands_separator($input['amount']);

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId, $employeeLabel, $startDate)
		{
      $WorkedHour = $this->WorkedHour->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $WorkedHour->id, 'journalized_type' => $this->WorkedHour->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));

      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::worked-hour-management.addedJournal', array('hour' => $startDate, 'names' => $employeeLabel)), $Journal));
    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage')));
  }

  /**
   * Update an existing WorkedHour
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
    $taskLabel=$input['task_label'];
    $employeeLabel=$input['responsible_employee'];
    unset($input['_token'], $input['responsible_employee'], $input['task_label']);
    $input = eloquent_array_filter_for_update($input);
    $endDate = $input['end_date'];
    $input['start_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpDateTimeFormat'), $input['start_date']);
    $input['end_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpDateTimeFormat'), $input['end_date']);
    $totalDuration = $input['end_date']->diffInMinutes($input['start_date']);
    $input['start_date'] = $input['start_date']->format('Y-m-d H:i:s');
    $input['end_date'] = $input['end_date']->format('Y-m-d H:i:s');
    $input['worked_hours'] = $totalDuration;
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');
    // $input['amount'] = remove_thousands_separator($input['amount']);

    $this->DB->transaction(function() use (&$input, $employeeLabel, $taskLabel, $endDate)
    {
      $WorkedHour = $this->WorkedHour->byId($input['id']);
      $unchangedWorkedHourValues = $WorkedHour->toArray();

      $this->WorkedHour->update($input, $WorkedHour);

      $diff = 0;

      // var_dump($input, $unchangedWorkedHourValues);die();

      foreach ($input as $key => $value)
      {
        if($unchangedWorkedHourValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $WorkedHour->id, 'journalized_type' => $this->WorkedHour->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
            $Journal2 = $this->Journal->create(array('journalized_id' => $WorkedHour->id, 'journalized_type' => $this->WorkedHour->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }

          if ($key == 'task_id')
          {
            if(!empty($unchangedWorkedHourValues[$key]))
            {
            $Task = $this->Task->byId($unchangedWorkedHourValues[$key]);
            $task = $Task->name;
            }
           else
           {
             $task = '';
           }
           $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::worked-hour-management.task'), 'field_lang_key' => 'decima-human-resources::worked-hour-management.task', 'old_value' => $task, 'new_value' => $taskLabel), $Journal);
          }
          else if ($key == 'end_date')
          {
            // $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::worked-hour-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::worked-hour-management.' . camel_case($key), 'old_value' => '', 'new_value' => $endDate), $Journal);
            $this->Journal->attachDetail($Journal2->id, array('note' => $this->Lang->get('decima-human-resources::worked-hour-management.addedExitJournal', array('hour' => $endDate, 'names' => $employeeLabel)), $Journal2));
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::worked-hour-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::worked-hour-management.' . camel_case($key), 'old_value' => $unchangedWorkedHourValues[$key], 'new_value' => $value), $Journal);
          }
        }
      }
    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage')));
  }

  /**
   * Delete an existing WorkedHour (soft delete)
   *
   * @param array $input
	 * 	An array as follows: array(id => $id);
   *
   * @return JSON encoded string
   *  A string as follows:
   *	In case of success: {"success" : form.defaultSuccessDeleteMessage}
   */
  public function delete(array $input)
  {
    $this->DB->transaction(function() use ($input)
    {
      $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
      $organizationId = $this->AuthenticationManager->getCurrentUserOrganization('id');
      $WorkedHour = $this->WorkedHour->byId($input['id']);
      $Journal = $this->Journal->create(array('journalized_id' => $input['id'], 'journalized_type' => $this->WorkedHour->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::worked-hour-management.deletedJournal', array('names' => $WorkedHour->employee_label)), $Journal));
      $this->WorkedHour->delete(array($input['id']));
    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessDeleteMessage')));
  }

}
