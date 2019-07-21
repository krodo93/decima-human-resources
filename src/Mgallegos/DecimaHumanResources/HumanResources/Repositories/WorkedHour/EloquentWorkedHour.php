<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */
namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\WorkedHour;

class EloquentWorkedHour implements WorkedHourInterface {

  /**
   * WorkedHour
   *
   * @var Vendor\DecimaModule\Module\WorkedHour;
   *
   */
  protected $WorkedHour;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $WorkedHour, $databaseConnectionName)
  {
      $this->WorkedHour = $WorkedHour;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->WorkedHour->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->WorkedHour->getTable();
  }

  /**
   * Get a ... by ID
   *
   * @param  int $id
   *
   * @return Vendor\DecimaModule\Module\WorkedHour
   */
  public function byId($id)
  {
  	return $this->WorkedHour->on($this->databaseConnectionName)->find($id);
  }

  /**
   * Retrieve ... by organization
   *
   * @param  int $id Organization id
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function byOrganization($id)
  {
    return $this->WorkedHour->where('organization_id', '=', $id)->get();
  }


  /**
   * Retrieve ... by organization
   *
   * @param  int $id Organization id
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function getTaskWorkedHoursSum($id)
  {
    return $this->WorkedHour->where('task_id', '=', $id)->sum('worked_hours');
  }

  /**
   * Create a new ...
   *
   * @param array $data
   * 	An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @return boolean
   */
  public function create(array $data)
  {
    $WorkedHour = new WorkedHour();
    $WorkedHour->setConnection($this->databaseConnectionName);
    $WorkedHour->fill($data)->save();

    return $WorkedHour;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   * 	An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Vendor\DecimaModule\Module\WorkedHour $WorkedHour
   *
   * @return boolean
   */
  public function update(array $data, $WorkedHour = null)
  {
    if(empty($WorkedHour))
    {
      $WorkedHour = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $WorkedHour->$key = $value;
    }

    return $WorkedHour->save();
  }

  /**
   * Delete existing ... (soft delete)
   *
   * @param array $data
   * 	An array as follows: array($id0, $id1,…);
   * @return boolean
   */
  public function delete(array $data)
  {
    foreach ($data as $key => $id)
    {
      $WorkedHour = $this->byId($id);
      $WorkedHour->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }

}
