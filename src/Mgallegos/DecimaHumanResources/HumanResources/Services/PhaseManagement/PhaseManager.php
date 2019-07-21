<?php
/**
 * @file
 * Module App Management Interface Implementation.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\PhaseManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\EloquentPhaseGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\PhaseInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class PhaseManager implements PhaseManagementInterface {

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
	 * @var Vendor\DecimaModule\Module\Repositories\Phase\EloquentPhaseGridRepository
	 *
	 */
	protected $EloquentPhaseGridRepository;

  /**
	 *  Module Table Name Interface
	 *
	 * @var Vendor\DecimaModule\Module\Repositories\Phase\PhaseInterface
	 *
	 */
	protected $Phase;

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

	public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentPhaseGridRepository $EloquentPhaseGridRepository, PhaseInterface $Phase, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
	{
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentPhaseGridRepository = $EloquentPhaseGridRepository;

    $this->Phase = $Phase;

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
    $this->GridEncoder->encodeRequestedData($this->EloquentPhaseGridRepository, $post);
  }

  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getPhases()
  {
    $Phases = array();

    $this->Phase->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Phase) use (&$Phases)
    {
      array_push($Phases, array('label'=> $Phase->name , 'value'=>$Phase->id));
    });

    return $Phases;
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
    unset($input['_token']);

    $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
    $organizationId = $this->AuthenticationManager->getCurrentUserOrganizationId();

    $input = eloquent_array_filter_for_insert($input);
		$input = array_add($input, 'organization_id', $organizationId);
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId, &$Phase)
		{

      $input = array_add($input, 'position', $this->Phase->getMaxPhaseNumber($input['organization_id']) + 1);

      $Phase = $this->Phase->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $Phase->id, 'journalized_type' => $this->Phase->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::phase-management.addedJournal', array('name' => $Phase->name)), $Journal));

    });

    return json_encode(array('success' => $this->Lang->get('form.defaultSuccessSaveMessage'), 'phases' => $this->getPhases()));
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
   public function updatePosition(array $input)
   {
     foreach($input as $key => $positions)
     {
       //Entra y los valores de $key son 1 ,2 ,3
       foreach($positions as $position)
       {
          //Entra y los valores son los del array
          $this->DB->transaction(function() use ($input, &$Phase)
       		{
            $Phase = $this->Phase->byId($input['id']);
            $this->Phase->update($position, $Phase);
          });
        }
      }
     return true;
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
    unset($input['_token']);
    $input = eloquent_array_filter_for_update($input);

    $this->DB->transaction(function() use (&$input)
    {
      $Phase = $this->Phase->byId($input['id']);
      $unchangedPhaseValues = $Phase->toArray();

      $this->Phase->update($input, $Phase);

      $diff = 0;

      foreach ($input as $key => $value)
      {
        if($unchangedPhaseValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $Phase->id, 'journalized_type' => $this->Phase->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }

          if($key == 'field0')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('module::app.field0'), 'field_lang_key' => 'module::app.field0', 'old_value' => $this->Lang->get('module::app.' . $unchangedPhaseValues[$key]), 'new_value' => $this->Lang->get('module::app.' . $value)), $Journal);
          }
          else if ($key == 'field1')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('module::app.field1'), 'field_lang_key' => 'module::app.field1', 'old_value' => ' ', 'new_value' => ''), $Journal);
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::phase-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::phase-management.' . camel_case($key), 'old_value' => $unchangedPhaseValues[$key], 'new_value' => $value), $Journal);
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

     $this->DB->transaction(function() use ($input, &$count)
     {
       $loggedUserId = $this->AuthenticationManager->getLoggedUserId();
       $organizationId = $this->AuthenticationManager->getCurrentUserOrganization('id');

       foreach ($input['id'] as $key => $id)
       {
         $count++;

         $Phase = $this->Phase->byId($id);

         $Journal = $this->Journal->create(array('journalized_id' => $id, 'journalized_type' => $this->Phase->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
         $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::phase-management.deletedJournal', array('name' => $Phase->name))), $Journal);

         $this->Phase->delete(array($id));
       }
     });

     if($count == 1)
     {
       return json_encode(array('success' => $this->Lang->get('decima-human-resources::phase-management.successDeletedMessage')));
     }
     else
     {
       return json_encode(array('success' => $this->Lang->get('decima-human-resources::phase-management.successDeletedMessage')));
     }
   }
}
