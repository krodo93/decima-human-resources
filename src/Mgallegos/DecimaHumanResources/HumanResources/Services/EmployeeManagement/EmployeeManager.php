<?php
/**
 * @file
 * EmployeeManagerement Interface Implementation.
 *
 * All EmployeeManageris copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\EmployeeManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EloquentEmployeeGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface;

use App\Kwaai\System\Repositories\Country\CountryInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\DepartmentInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\PositionInterface;

use Mgallegos\DecimaBank\Bank\Repositories\Bank\BankInterface;

use App\Kwaai\Helpers\Gravatar;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class EmployeeManager implements EmployeeManagementInterface {

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
	 * @var App\Kwaai\Template\Repositories\Employee\EloquentEmployeeGridRepository
	 *
	 */
	protected $EloquentEmployeeGridRepository;

  /**
	 *  EmployeeManagere Interface
	 *
	 * @var App\Kwaai\Template\Repositories\Employee\EmployeeInterface
	 *
	 */
	protected $Employee;

  /**
	 *  Department Interface
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\DepartmentInterface
	 *
	 */
	protected $Department;

  /**
	 *  Position Interface
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\PositionInterface
	 *
	 */
	protected $Position;

  /**
	 *  Bank Interface
	 *
	 * @var Mgallegos\DecimaBank\Bank\Repositories\Bank\BankInterface
	 *
	 */
	protected $Bank;

  /**
	 *  Country Interface
	 *
	 * @var App\Kwaai\System\Repositories\Country\CountryInterface
	 *
	 */
	protected $Country;

  /**
	 *  Gravatar
	 *
	 * @var App\Kwaai\Helpers\Gravatar
	 *
	 */
	protected $Gravatar;

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

	public function __construct(
    AuthenticationManagementInterface $AuthenticationManager,
    JournalManagementInterface $JournalManager,
    JournalInterface $Journal,
    RequestedDataInterface $GridEncoder,
    EloquentEmployeeGridRepository $EloquentEmployeeGridRepository,
    CountryInterface $Country,
    DepartmentInterface $Department,
    PositionInterface $Position,
    BankInterface $Bank,
    EmployeeInterface $Employee,
    Gravatar $Gravatar,
    Carbon $Carbon,
    DatabaseManager $DB,
    Translator $Lang,
    Repository $Config
  )
	{
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentEmployeeGridRepository = $EloquentEmployeeGridRepository;

    $this->Employee = $Employee;

    $this->Country = $Country;

    $this->Department = $Department;

    $this->Position = $Position;

    $this->Bank = $Bank;

    $this->Gravatar = $Gravatar;

    $this->Carbon = $Carbon;

    $this->DB = $DB;

		$this->Lang = $Lang;

		$this->Config = $Config;

    $this->Journal = $Journal;

    // public function buildGravatarURL($email, $custom_size = null,$hash_email = true)
    // $this->Gravatar->buildGravatarURL
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
    $this->GridEncoder->encodeRequestedData($this->EloquentEmployeeGridRepository, $post);
  }

  /**
   * Get gender
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getGenders()
  {
    $gender = array();

    array_push($gender, array('label'=> $this->Lang->get('decima-human-resources::employee-management.M'), 'value'=> 'M'));
    array_push($gender, array('label'=> $this->Lang->get('decima-human-resources::employee-management.F'), 'value'=> 'F'));

    return $gender;
  }

  /**
   * Get status
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getStatus()
  {
    $statusl = array();

    array_push($statusl, array('label'=> $this->Lang->get('decima-human-resources::employee-management.A'), 'value'=> 'Aprovado'));
    array_push($statusl, array('label'=> $this->Lang->get('decima-human-resources::employee-management.I'), 'value'=> 'Inactivo'));
    array_push($statusl, array('label'=> $this->Lang->get('decima-human-resources::employee-management.L'), 'value'=> 'Con Licencia'));

    return $statusl;
  }

/**
   * Get marital_status
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getmstatus()
  {
    $mstatus = array();

    array_push($mstatus, array('label'=> $this->Lang->get('decima-human-resources::employee-management.S'), 'value'=> 'S'));
    array_push($mstatus, array('label'=> $this->Lang->get('decima-human-resources::employee-management.C'), 'value'=> 'C'));
    array_push($mstatus, array('label'=> $this->Lang->get('decima-human-resources::employee-management.V'), 'value'=> 'V'));
    return $mstatus;
  }

  /**
  * Get ...
  *
  * @return array
  *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
  */
   public function getEmployees()
   {
     $employee = array();
     $this->Employee->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Employee) use (&$employee)
     {
       array_push($employee, array('label'=> $Employee->names  . ' ' . $Employee->surnames , 'value'=>$Employee->id));
     });
     return $employee;
   }

  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getEmployeesInfo($organizationId = null, $databaseConnectionName = null)
  {
    $employees = array();

    if(empty($organizationId))
    {
      $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();
    }

    $this->Employee->byOrganizationWithPositionAndDepartment($organizationId, $databaseConnectionName)->each(function($Employee) use (&$employees)
    {
      array_push(
        $employees,
        array(
          'label'=> $Employee->name,
          'value'=>$Employee->id,
          'tax_id'=>$Employee->tax_id,
          'single_identity_document_number'=>$Employee->single_identity_document_number,
          'position_id'=>$Employee->position_id,
          'position_name'=>$Employee->position_name,
          'department_id'=>$Employee->department_id,
          'department_name'=>$Employee->department_name
        )
      );
    });

    return $employees;
  }



   public function getLoggedEmployee()
   {
     $employee =  $this->Employee->bySystemUserIdAndByOrganization($this->AuthenticationManager->getLoggedUserId(), $this->AuthenticationManager->getCurrentUserOrganizationId())->first();

     if(!empty($employee))
     {
       return $employee->toArray();
     }
     else {
        return array();
     }
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
    unset($input['_token'], $input['departament'], $input['bank_label'], $input['position'], $input['country'], $input['gender_label'], $input['marital_status_label'], $input['user'], $input['leave_approver_label'], $input['status_label'], $input['afp_label']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $input = eloquent_array_filter_for_insert($input);
		$input = array_add($input, 'organization_id', $organizationId);
    $input['date_birth'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date_birth'])->format('Y-m-d');

    if(!empty($input['personal_email']) && empty($input['profile_image_url']))
    {
        $input['profile_image_url'] = $this->Gravatar->buildGravatarURL($input['personal_email'], 245);
        $input['profile_image_medium_url'] = $this->Gravatar->buildGravatarURL($input['personal_email'],100);
        $input['profile_image_small_url'] = $this->Gravatar->buildGravatarURL($input['personal_email'], 50);
    }

    if (!empty($input['start_date']))
    {
      $input['start_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['start_date'])->format('Y-m-d');
    }
    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId)
		{
      $Employee = $this->Employee->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $Employee->id, 'journalized_type' => $this->Employee->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::employee-management.addedJournal', array('names' => $Employee->names.' '.$Employee->surnames,)), $Journal));
    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage'), 'employees' => $this->getEmployees()));
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
    $countryLabel=$input['country'];
    $departamentLabel=$input['departament'];
    $positionLabel=$input['position'];
    $userLabel =$input['user'];
    $bankLabel = $input['bank_label'];
    $info = false;

    unset($input['_token'],$input['departament'], $input['position'], $input['bank_label'], $input['country'], $input['gender_label'], $input['marital_status_label'], $input['user'], $input['leave_approver_label'], $input['status_label'], $input['afp_label']);

    $input = eloquent_array_filter_for_update($input);

    if(isset($input['date_birth']))
    {
      $input['date_birth'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date_birth'])->format('Y-m-d');
    }

    if(isset($input['start_date']))
    {
      $input['start_date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['start_date'])->format('Y-m-d');
    }

    // if(!empty($input['personal_email']) && empty($input['profile_image_url']))
    if(!empty($input['personal_email']))
    {
        $input['profile_image_url'] = $this->Gravatar->buildGravatarURL($input['personal_email'], 245);
        $input['profile_image_medium_url'] = $this->Gravatar->buildGravatarURL($input['personal_email'],100);
        $input['profile_image_small_url'] = $this->Gravatar->buildGravatarURL($input['personal_email'], 50);
    }

    $this->DB->transaction(function() use (&$input, $countryLabel, $departamentLabel, $positionLabel, $bankLabel, $userLabel, &$info)
    {
      $Employee = $this->Employee->byId($input['id']);
      $unchangedEmployeeValues = $Employee->toArray();

      $this->Employee->update($input, $Employee);

      $diff = 0;

      foreach ($input as $key => $value)
      {
        if($unchangedEmployeeValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $Employee->id, 'journalized_type' => $this->Employee->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }

          if($key == 'gender' || $key == 'marital_status')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $this->Lang->get('decima-human-resources::employee-management.' . $unchangedEmployeeValues[$key]), 'new_value' => $this->Lang->get('decima-human-resources::employee-management.' . $value)), $Journal);
          }
          else if ($key == 'country_id')
          {
            $Country = $this->Country->byId($unchangedEmployeeValues[$key]);
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('organization/organization-management.country'), 'field_lang_key' => 'organization/organization-management.country', 'old_value' => $Country->name, 'new_value' => $countryLabel), $Journal);
          }
          else if ($key == 'street1')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'organization/organization-management.street1PlaceHolder', 'old_value' => $unchangedEmployeeValues[$key], 'new_value' => $value), $Journal);
          }
          else if ($key == 'street2')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'organization/organization-management.street2PlaceHolder', 'old_value' => $unchangedEmployeeValues[$key], 'new_value' => $value), $Journal);
          }
          else if ($key == 'city_name')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'organization/organization-management.cityNamePlaceHolder', 'old_value' => $unchangedEmployeeValues[$key], 'new_value' => $value), $Journal);
          }
          else if ($key == 'state_name')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'organization/organization-management.stateNamePlaceHolder', 'old_value' => $unchangedEmployeeValues[$key], 'new_value' => $value), $Journal);
          }
          else if ($key == 'zip_code')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'organization/organization-management.zipCodePlaceHolder', 'old_value' => $unchangedEmployeeValues[$key], 'new_value' => $value), $Journal);
          }
          else if ($key == 'profile_image_url')
          {
            $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::employee-management.imageChanged', array('image_url' => $Employee->image_url))), $Journal);
          }
          else if ($key == 'profile_image_medium_url' || $key == 'profile_image_small_url')
          {
            continue;
          }
          //$key == 'departament_id' verificar
          else if ($key == 'departament_id')
          {
            if(!empty($unchangedEmployeeValues[$key]))
            {
              $Department = $this->Department->byId($unchangedEmployeeValues[$key]);
              $department = $Department->name;
            }
           else
           {
             $department = '';
           }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.department'), 'field_lang_key' => 'decima-human-resources::employee-management.department', 'old_value' => $department, 'new_value' => $departamentLabel), $Journal);
          }
          else if ($key == 'position_id')
          {
            if(!empty($unchangedEmployeeValues[$key]))
            {
            $Position = $this->Position->byId($unchangedEmployeeValues[$key]);
            $position = $Position->name;
            }
           else
           {
             $position = '';
           }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.position'), 'field_lang_key' => 'decima-human-resources::employee-management.position', 'old_value' => $position, 'new_value' => $positionLabel), $Journal);
          }
          else if ($key == 'bank_id')
          {
            if(!empty($unchangedEmployeeValues[$key]))
            {
            $Bank = $this->Bank->byId($unchangedEmployeeValues[$key]);
            $bank = $Bank->name;
            }
           else
           {
             $bank = '';
           }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.bankId'), 'field_lang_key' => 'decima-human-resources::employee-management.bankId', 'old_value' => $bank, 'new_value' => $bankLabel), $Journal);
          }
          else if ($key == 'user_id')
          {
            if(!empty($unchangedEmployeeValues[$key]))
            {
            $User = $this->User->byId($unchangedEmployeeValues[$key]);
            $user = $User->firstname . ' ' . $User->lastname;
            }
            else
            {
              $user = '';
            }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.user'), 'field_lang_key' => 'decima-human-resources::employee-management.user', 'old_value' => $user, 'new_value' => $userLabel), $Journal);
          }

          else if ($key == 'date_birth')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $this->Carbon->createFromFormat('Y-m-d', $unchangedEmployeeValues[$key], 'UTC')->format($this->Lang->get('form.phpShortDateFormat')), 'new_value' => $this->Carbon->createFromFormat('Y-m-d', $value, 'UTC')->format($this->Lang->get('form.phpShortDateFormat'))), $Journal);
          }
          else if ($key == 'start_date')
          {
            if(!empty($unchangedEmployeeValues[$key]))
            {
              $startdateOldValue = $this->Carbon->createFromFormat('Y-m-d', $unchangedEmployeeValues[$key], 'UTC')->format($this->Lang->get('form.phpShortDateFormat'));
            }
            else
            {
              $startdateOldValue = '';
            }

            if(!empty($value))
            {
              $startdateNewValue = $this->Carbon->createFromFormat('Y-m-d', $value, 'UTC')->format($this->Lang->get('form.phpShortDateFormat'));
            }
            else
            {
              $startdateNewValue = '';
            }

            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $startdateOldValue, 'new_value' => $startdateNewValue), $Journal);
          }

          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::employee-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::employee-management.' . camel_case($key), 'old_value' => $unchangedEmployeeValues[$key], 'new_value' => $value), $Journal);
          }
        }}
    });
    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage'), 'employees' => $this->getEmployees()));
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
    $this->DB->transaction(function() use ($input)
    {
      $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
      $organizationId = $this->AuthenticationManager->getCurrentUserOrganization('id');
      $Employee = $this->Employee->byId($input['id']);
      $Journal = $this->Journal->create(array('journalized_id' => $input['id'], 'journalized_type' => $this->Employee->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::employee-management.deletedJournal', array('names' => $Employee->names . ' ' . $Employee->surnames,  )), $Journal));
      $this->Employee->delete(array($input['id']));
    });
    return json_encode(array('success' => $this->Lang->get('decima-human-resources::employee-management.successDeletedMessage')));
  }

}
