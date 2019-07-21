<?php
/**
 * @file
 * PositionManagerement Interface Implementation.
 *
 * All PositionManageris copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\PositionManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\EloquentPositionGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\PositionInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class PositionManager implements PositionManagementInterface {

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
   * @var App\Kwaai\Template\Repositories\Position\EloquentPositionGridRepository
   *
   */
  protected $EloquentPositionGridRepository;

  /**
   *  PositionManagere Interface
   *
   * @var App\Kwaai\Template\Repositories\Position\PositionInterface
   *
   */
  protected $Position;

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

  public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentPositionGridRepository $EloquentPositionGridRepository, PositionInterface $Position, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
  {
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentPositionGridRepository = $EloquentPositionGridRepository;

    $this->Position = $Position;

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
    $this->GridEncoder->encodeRequestedData($this->EloquentPositionGridRepository, $post);
  }

  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getPositions()
  {
    $position = array();
    $this->Position->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Position) use (&$position)
    {
      array_push($position, array('label'=> $Position->name , 'value'=>$Position->id));
    });
    return $position;
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
      $Position = $this->Position->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $Position->id, 'journalized_type' => $this->Position->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::position-management.addedJournal', array('name' => $Position->name . ' ' , $Position->key)), $Journal));

    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage'), 'positions' => $this->getPositions() ));
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
      $Position = $this->Position->byId($input['id']);
      $unchangedPositionValues = $Position->toArray();

      $this->Position->update($input, $Position);

      $diff = 0;

    foreach ($input as $key => $value)
      {
        if($unchangedPositionValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $Position->id, 'journalized_type' => $this->Position->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }

          if($key == 'field0')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::position-management.'), 'field_lang_key' => 'decima-human-resources::position-management.', 'old_value' => $this->Lang->get('decima-human-resources::employee-management.' . $unchangedPositionValues[$key]), 'new_value' => $this->Lang->get('PositionManager' . $value)), $Journal);
          }
          else if ($key == 'field1')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::position-management.'), 'field_lang_key' => 'decima-human-resources::position-management.', 'old_value' => ' ', 'new_value' => ''), $Journal);
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::position-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::position-management.' . camel_case($key), 'old_value' => $unchangedPositionValues[$key], 'new_value' => $value), $Journal);
          }
        }
      }
    });
    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessUpdateMessage'), 'positions' => $this->getPositions() ));
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
         $Position = $this->Position->byId($id);
         $Journal = $this->Journal->create(array('journalized_id' => $id, 'journalized_type' => $this->Position->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
         $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::position-management.deletedJournal', array('name' => $Position->name))), $Journal);
         $this->Position->delete(array($id));
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
