<?php
/**
 * @file
 * TaskManagerement Interface Implementation.
 *
 * All TaskManageris copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\TaskManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task\EloquentTaskGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task\TaskInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\PhaseInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\WorkedHourInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class TaskManager implements TaskManagementInterface {

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
   * @var App\Kwaai\Template\Repositories\Task\EloquentTaskGridRepository
   *
   */
  protected $EloquentTaskGridRepository;

  /**
   *  TaskManagere Interface
   *
   * @var App\Kwaai\Template\Repositories\Task\TaskInterface
   *
   */
  protected $Task;

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

  /**
	 *  EmployeeManagere Interface
	 *
	 * @var App\Kwaai\Template\Repositories\Employee\EmployeeInterface
	 *
	 */
	protected $Employee;

  /**
   *  PhaseManagere Interface
   *
   * @var App\Kwaai\Template\Repositories\Phase\PhaseInterface
   *
   */
  protected $Phase;

  /**
   *  PhaseManagere Interface
   *
   * @var App\Kwaai\Template\Repositories\Phase\PhaseInterface
   *
   */
  protected $WorkedHour;

  public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentTaskGridRepository $EloquentTaskGridRepository, TaskInterface $Task, EmployeeInterface $Employee, PhaseInterface $Phase, WorkedHourInterface $WorkedHour, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
  {
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentTaskGridRepository = $EloquentTaskGridRepository;

    $this->Task = $Task;

    $this->Employee = $Employee;

    $this->Phase = $Phase;

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
   *  All jqGrid posted data
   *
   * @return void
   */
  public function getGridData(array $post)
  {
    $this->GridEncoder->encodeRequestedData($this->EloquentTaskGridRepository, $post);
  }

  public function getPriotity()
  {
    $priority = array();

    array_push($priority, array('label'=> $this->Lang->get('decima-human-resources::task-management.B'), 'value'=> 'B'));
    array_push($priority, array('label'=> $this->Lang->get('decima-human-resources::task-management.M'), 'value'=> 'M'));
    array_push($priority, array('label'=> $this->Lang->get('decima-human-resources::task-management.A'), 'value'=> 'A'));
    array_push($priority, array('label'=> $this->Lang->get('decima-human-resources::task-management.U'), 'value'=> 'U'));

    return $priority;
  }

  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getTask()
  {
    $task = array();
    $this->Task->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Task) use (&$task)
    {
      array_push($task, array('label'=> $Task->name , 'value'=>$Task->id));
    });
    return $task;
  }

  public function getcompletionPercentage()
  {
    $completionPercentage = array();

    for ($percentage = 0; $percentage <= 100; $percentage +=5)
    {
      array_push($completionPercentage, array('label' => $percentage . '%', 'value' => $percentage));
    }

    return $completionPercentage;
  }

  /**
   * Get phases
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getTaskByPhases()
  {
    $phases = array();

    $this->Phase->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Phase) use (&$phases)
    {
      $tasks = array();

      $this->Task->byPhase($Phase->id)->each(function($Task) use (&$tasks)
      {
        $Employee = $this->Employee->byId($Task->responsible_employee_id);

        $workedHours = $this->WorkedHour->getTaskWorkedHoursSum($Task->id);

        if(empty($workedHours))
        {
          $workedHours = 0;
        }
        else
        {
          $workedHours = round($workedHours/60, 2);
        }

        array_push($tasks, array('id'=> $Task->id,
                                 'name'                    => $Task->name,
                                 'responsible_employee_id' => $Task->responsible_employee_id,
                                 'planned_initial_hour'    => $Task->planned_initial_hour,
                                 'limit_date'              => $this->Carbon->createFromFormat('Y-m-d', $Task->limit_date)->format($this->Lang->get('form.phpShortDateFormat')),
                                 'priority'                => $Task->priority,
                                 'phase_id'                => $Task->phase_id,
                                 'position'                => $Task->position,
                                 'manual_reference'        => $Task->manual_reference,
                                 'completion_percentage'   => $Task->completion_percentage,
                                 'worked_hours'            => $workedHours,
                                 'url'                     => $Employee['profile_image_url']
           ));

      });

      array_push($phases, array('id' => $Phase->id, 'name' => $Phase->name, 'tasks' => $tasks));

      // $phases[$Phase->name] =  $tasks;

    });

    return $phases;
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
    unset($input['_token'], $input['phase_label'], $input['priority_label'], $input['priority_label'], $input['responsible_employee_label'], $input['completion_percentage_label']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $input = eloquent_array_filter_for_insert($input);
    $input = array_add($input, 'organization_id', $organizationId);

    $limitDateTemp = $input['limit_date'];
    $input['limit_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['limit_date'])->format('Y-m-d');

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId, &$Task)
    {
      $input = array_add($input, 'position', $this->Task->byPhase($input['phase_id'])->count());

      $Task = $this->Task->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $Task->id, 'journalized_type' => $this->Task->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::task-management.addedJournal', array('name' => $Task->name . ' ' , $Task->key)), $Journal));

    });

    $Employee = $this->Employee->byId($Task->responsible_employee_id);
    $tasks = $Task->toArray();
    $tasks['limit_date'] = $limitDateTemp;

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage'), 'task' => $tasks, 'url' => $Employee['profile_image_url']));
  }

  /**
   * Update an phases ...
   *
   * @param array $input
   *  An array as follows: array('id' => $id, 'field0'=>$field0, 'field1'=>$field1
   *
   * @return JSON encoded string
   *  A string as follows:
   *  In case of success: {"success" : form.defaultSuccessUpdateMessage}
   */
  public function updateTaskPhaseAndPosition(array $input)
  {
    unset($input['_token']);

    foreach($input['tasks'] as $key => $tasks)
    {
      //Entra y los valores de $key son 1 ,2 ,3
      foreach($tasks as $task)
      {
        $this->update($tasks);
      }
    }
    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage')));
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
    unset($input['_token'], $input['phase_label'], $input['priority_label'], $input['priority_label'], $input['responsible_employee_label'], $input['completion_percentage_label']);

    $input = eloquent_array_filter_for_update($input);

    if(isset($input['limit_date']) && $input['limit_date'] != '')
    {
      $input['limit_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['limit_date'])->format('Y-m-d');
    }
    //$Employee = $this->Employee->byId($input['responsible_employee_id']);
    $this->DB->transaction(function() use (&$input)
    {
      $Task = $this->Task->byId($input['id']);
      $unchangedTaskValues = $Task->toArray();

      $this->Task->update($input, $Task);

      $diff = 0;

      foreach ($input as $key => $value)
        {
          if($unchangedTaskValues[$key] != $value)
          {
            $diff++;

            if($diff == 1)
            {
              $Journal = $this->Journal->create(array('journalized_id' => $Task->id, 'journalized_type' => $this->Task->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
            }

            if($key == 'field0')
            {
              $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::task-management.'), 'field_lang_key' => 'decima-human-resources::task-management.', 'old_value' => $this->Lang->get('decima-human-resources::employee-management.' . $unchangedTaskValues[$key]), 'new_value' => $this->Lang->get('TaskManager' . $value)), $Journal);
            }
            else if ($key == 'field1')
            {
              $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::task-management.'), 'field_lang_key' => 'decima-human-resources::task-management.', 'old_value' => ' ', 'new_value' => ''), $Journal);
            }
            else
            {
              $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::task-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::task-management.' . camel_case($key), 'old_value' => $unchangedTaskValues[$key], 'new_value' => $value), $Journal);
            }
          }
        }
    });
    // return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage'), 'url' => $Employee['profile_image_url']));
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
   public function delete(array $input)
   {
     $id = $input['id'];
     $this->DB->transaction(function() use ($input, &$id)
     {
       $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
       $organizationId = $this->AuthenticationManager->getCurrentUserOrganization('id');
       $Task = $this->Task->byId($id);
       $Journal = $this->Journal->create(array('journalized_id' => $id, 'journalized_type' => $this->Task->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
       $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::position-management.deletedJournal', array('name' => $Task->name))), $Journal);
       $this->Task->delete(array($id));
     });

       return json_encode(array('success' => $this->Lang->get('form.defaultSuccessDeleteMessage'), 'id' => $id));
   }
}
