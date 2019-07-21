<?php
/**
 * @file
 * DepartmentManagerement Interface Implementation.
 *
 * All DepartmentManageris copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\DepartmentManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use Mgallegos\DecimaAccounting\Accounting\Repositories\CostCenter\CostCenterInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\EloquentDepartmentGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\DepartmentInterface;

use Mgallegos\DecimaInventory\Inventory\Repositories\Warehouse\WarehouseInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class DepartmentManager implements DepartmentManagementInterface {

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
   * @var App\Kwaai\Template\Repositories\Department\EloquentDepartmentGridRepository
   *
   */
  protected $EloquentDepartmentGridRepository;

  /**
   *  DepartmentManagere Interface
   *
   * @var App\Kwaai\Template\Repositories\Department\DepartmentInterface
   *
   */
  protected $Department;

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
  * Tax Interface
  *
  * @var Mgallegos\DecimaPurchase\Purchase\Repositories\Tax\TaxInterface
  *
  */
  protected $Warehouse;


  public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager , JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentDepartmentGridRepository $EloquentDepartmentGridRepository , DepartmentInterface $Department, CostCenterInterface $CostCenter, WarehouseInterface $Warehouse, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
  {
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentDepartmentGridRepository = $EloquentDepartmentGridRepository;

    $this->Department = $Department;

    $this->CostCenter = $CostCenter;

    $this->Warehouse = $Warehouse;

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
    $this->GridEncoder->encodeRequestedData($this->EloquentDepartmentGridRepository, $post);
  }
  /**
  * Get ...
  *
  * @return array
  *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
  */
   public function getDepartment()
   {
     $department = array();
     $this->Department->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Department) use (&$department)
     {
       array_push($department, array('label'=> $Department->name , 'value'=>$Department->id));
     });
     return $department;
   }
  /**
  * Get ...
  *
  * @return array
  *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
  */
   public function getSearchModalTableRows($organizationId = null, $databaseConnectionName = null, $returnJson = false)
   {
     $department = array();

     if(empty($organizationId))
     {
       $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();
     }

     $this->Department->byOrganization($organizationId, $databaseConnectionName)->each(function($Department) use (&$department)
     {
       $department['key' . $Department->id] = array('label'=> $Department->name , 'value'=>$Department->id);
     });

     if($returnJson)
     {
       return json_encode($department);
     }

     return $department;
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
    unset($input['_token'], $input['hr_cost_center_name'], $input['raw_materials_warehouse_label'], $input['consumable_warehouse_label'], $input['finished_goods_warehouse_label']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $input = eloquent_array_filter_for_insert($input);
    $input = array_add($input, 'organization_id', $organizationId);
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId)
    {
      $Department = $this->Department->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $Department->id, 'journalized_type' => $this->Department->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::department-management.addedJournal', array('name' => $Department->name . ' ' , $Department->key)), $Journal));

    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage'), 'department' => $this->getDepartment()));
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
    $costCenterlabel = $input['hr_cost_center_name'];
    $rawMaterialsWarehouselabel = $input['raw_materials_warehouse_label'];
    $finishedGoodsWarehouselabel = $input['finished_goods_warehouse_label'];
    $consumableWarehouselabel = $input['consumable_warehouse_label'];
    unset($input['_token'], $input['hr_cost_center_name'], $input['raw_materials_warehouse_label'], $input['consumable_warehouse_label'], $input['finished_goods_warehouse_label']);
    $input = eloquent_array_filter_for_update($input);
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use (&$input, $costCenterlabel, $rawMaterialsWarehouselabel, $finishedGoodsWarehouselabel, $consumableWarehouselabel)
    {
      $Department = $this->Department->byId($input['id']);
      $unchangedDepartmentValues = $Department->toArray();

      $this->Department->update($input, $Department);

      $diff = 0;

      foreach ($input as $key => $value)
      {
        if($unchangedDepartmentValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $Department->id, 'journalized_type' => $this->Department->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }
          if($key == 'field0')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::department-management.'), 'field_lang_key' => 'DepartmentManagerd0', 'old_value' => $this->Lang->get('decima-human-resources::department-management.' . $unchangedDepartmentValues[$key]), 'new_value' => $this->Lang->get('decima-human-resources::department-management.' . $value)), $Journal);
          }
          else if ($key == 'cost_center_id')
          {
            if(!empty($unchangedDepartmentValues[$key]))
            {
              $CostCenter = $this->CostCenter->byId($unchangedDepartmentValues[$key]);
              $costCenter = $CostCenter->key . ' ' . $CostCenter->name;
            }
            else
            {
              $costCenter = '';
            }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::department-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::department-management.' . camel_case($key), 'old_value' => $costCenter, 'new_value' => $costCenterlabel), $Journal);
          }
          else if($key == 'raw_materials_warehouse_id')
          {
            if(!empty($unchangedDepartmentValues[$key]))
            {
              $Warehouse = $this->Warehouse->byId($unchangedDepartmentValues[$key]);
              $warehouse = $Warehouse->name;
            }
            else
            {
              $warehouse =  '';
            }

            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-purchase::initial-purchases-setup.rawMaterialsWarehouseId'), 'field_lang_key' => 'decima-purchase::initial-purchases-setup.rawMaterialsWarehouseId', 'old_value' => $warehouse, 'new_value' => $rawMaterialsWarehouselabel), $Journal);
          }
          else if($key == 'finished_goods_warehouse_id')
          {
            if(!empty($unchangedDepartmentValues[$key]))
            {
              $Warehouse = $this->Warehouse->byId($unchangedDepartmentValues[$key]);
              $warehouse = $Warehouse->name;
            }
            else
            {
              $warehouse =  '';
            }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-purchase::initial-purchases-setup.finishedGoodsWarehouseId'), 'field_lang_key' => 'decima-purchase::initial-purchases-setup.finishedGoodsWarehouseId', 'old_value' => $warehouse, 'new_value' => $finishedGoodsWarehouselabel), $Journal);
          }
          else if($key == 'consumable_warehouse_id')
          {
            if(!empty($unchangedDepartmentValues[$key]))
            {
              $Warehouse = $this->Warehouse->byId($unchangedDepartmentValues[$key]);
              $warehouse = $Warehouse->name;
            }
            else
            {
              $warehouse =  '';
            }
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-purchase::initial-purchases-setup.consumableWarehouseId'), 'field_lang_key' => 'decima-purchase::initial-purchases-setup.consumableWarehouseId', 'old_value' => $warehouse, 'new_value' => $consumableWarehouselabel), $Journal);
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::department-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::department-management.' . camel_case($key), 'old_value' => $unchangedDepartmentValues[$key], 'new_value' => $value), $Journal);
          }
        }
      }
    });
    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage'), 'department' => $this->getDepartment()));
  }

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
         $Department = $this->Department->byId($id);
         $Journal = $this->Journal->create(array('journalized_id' => $id, 'journalized_type' => $this->Department->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
         $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::department-management.deletedJournal', array('name' => $Department->name))), $Journal);
         $this->Department->delete(array($id));
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
