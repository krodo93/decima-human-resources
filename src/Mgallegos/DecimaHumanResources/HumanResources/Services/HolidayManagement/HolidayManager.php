<?php
/**
 * @file
 * Module App Management Interface Implementation.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Services\HolidayManagement;

use App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface;

use App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface;

use App\Kwaai\Security\Repositories\Journal\JournalInterface;

use Mgallegos\LaravelJqgrid\Encoders\RequestedDataInterface;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\EloquentHolidayGridRepository;

use Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\HolidayInterface;

use Carbon\Carbon;

use Illuminate\Config\Repository;

use Illuminate\Translation\Translator;

use Illuminate\Database\DatabaseManager;


class HolidayManager implements HolidayManagementInterface {

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
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\EloquentHolidayGridRepository
	 *
	 */
	protected $EloquentHolidayGridRepository;

  /**
	 *  Module Table Name Interface
	 *
	 * @var Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\HolidayInterface
	 *
	 */
	protected $Holiday;

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

	public function __construct(AuthenticationManagementInterface $AuthenticationManager, JournalManagementInterface $JournalManager, JournalInterface $Journal, RequestedDataInterface $GridEncoder, EloquentHolidayGridRepository $EloquentHolidayGridRepository, HolidayInterface $Holiday, Carbon $Carbon, DatabaseManager $DB, Translator $Lang, Repository $Config)
	{
    $this->AuthenticationManager = $AuthenticationManager;

    $this->JournalManager = $JournalManager;

    $this->Journal = $Journal;

    $this->GridEncoder = $GridEncoder;

    $this->EloquentHolidayGridRepository = $EloquentHolidayGridRepository;

    $this->Holiday = $Holiday;

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
    $this->GridEncoder->encodeRequestedData($this->EloquentHolidayGridRepository, $post);
  }

  /**
   * Get ...
   *
   * @return array
   *  An array of arrays as follows: array( array('label'=>$name0, 'value'=>$id0), array('label'=>$name1, 'value'=>$id1),…)
   */
  public function getHolidays()
  {
    $Holidays = array();

    $this->Holiday->byOrganization($this->AuthenticationManager->getCurrentUserOrganizationId())->each(function($Holiday) use (&$Holidays)
    {
      array_push($Holidays, array('label'=> $Holiday->name , 'value'=>$Holiday->id));
    });

    return $Holidays;
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

    $this->DB->transaction(function() use ($input, $loggedUserId, $organizationId)
		{
      $Holiday = $this->Holiday->create($input);

      $Journal = $this->Journal->create(array('journalized_id' => $Holiday->id, 'journalized_type' => $this->Holiday->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
      $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::holiday-management.addedJournal', array('name' => $Holiday->description)), $Journal));

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
    unset($input['_token']);
    $input = eloquent_array_filter_for_update($input);
    // $input['date'] = $this->Carbon->createFromFormat($this->Lang->get('form.phpShortDateFormat'), $input['date'])->format('Y-m-d');

    $this->DB->transaction(function() use (&$input)
    {
      $Holiday = $this->Holiday->byId($input['id']);
      $unchangedHolidayValues = $Holiday->toArray();

      $this->Holiday->update($input, $Holiday);

      $diff = 0;

      foreach ($input as $key => $value)
      {
        if($unchangedHolidayValues[$key] != $value)
        {
          $diff++;

          if($diff == 1)
          {
            $Journal = $this->Journal->create(array('journalized_id' => $Holiday->id, 'journalized_type' => $this->Holiday->getTable(), 'user_id' => $this->AuthenticationManager->getLoggedUserId(), 'organization_id' => $this->AuthenticationManager->getCurrentUserOrganizationId()));
          }

          if($key == 'internal_reference')
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::holiday-management.internalReference'), 'field_lang_key' => 'decima-human-resources::holiday-management.internalReference', 'old_value' => $this->Lang->get($unchangedHolidayValues[$key]), 'new_value' => $this->Lang->get($value)), $Journal);
          }
          else
          {
            $this->Journal->attachDetail($Journal->id, array('field' => $this->Lang->get('decima-human-resources::holiday-management.' . camel_case($key)), 'field_lang_key' => 'decima-human-resources::holiday-management.' . camel_case($key), 'old_value' => $unchangedHolidayValues[$key], 'new_value' => $value), $Journal);
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

         $Holiday = $this->Holiday->byId($id);

         $Journal = $this->Journal->create(array('journalized_id' => $id, 'journalized_type' => $this->Holiday->getTable(), 'user_id' => $loggedUserId, 'organization_id' => $organizationId));
         $this->Journal->attachDetail($Journal->id, array('note' => $this->Lang->get('decima-human-resources::holiday-management.deletedJournal', array('name' => $Holiday->description))), $Journal);

         $this->Holiday->delete(array($id));
       }
     });

     if($count == 1)
     {
       return json_encode(array('success' => $this->Lang->get('decima-human-resources::holiday-management.successDeletedMessage')));
     }
     else
     {
       return json_encode(array('success' => $this->Lang->get('decima-human-resources::holiday-management.successDeletedMessage')));
     }
   }
}
