<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\Task;

class EloquentTask implements TaskInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\Task;
   *
   */
  protected $Task;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $Task, $databaseConnectionName)
  {
      $this->Task = $Task;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Task->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Task->getTable();
  }

  /**
   * Get a ... by ID
   *
   * @param  int $id
   *
   * @return Mgallegos\DecimaAccounting\Account
   */
  public function byId($id)
  {
    return $this->Task->on($this->databaseConnectionName)->find($id);
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
    return $this->Task->where('organization_id', '=', $id)->get();
  }

  /**
   * Retrieve ... by organization
   *
   * @param  int $id Organization id
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function byPhase($id, $orderBy = 'asc')
  {
    return $this->Task->where('phase_id', '=', $id)->orderBy('position', $orderBy)->get();
  }

  /**
   * Create a new ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @return boolean
   */
  public function create(array $data)
  {
    $Task = new Task();
    $Task->setConnection($this->databaseConnectionName);
    $Task->fill($data)->save();

    return $Task;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $Task
   *
   * @return boolean
   */
  public function update(array $data, $Task = null)
  {
    if(empty($Task))
    {
      $Task = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $Task->$key = $value;
    }

    return $Task->save();
  }
  /**
   * Delete existing ... (soft delete)
   *
   * @param array $data
   *  An array as follows: array($id0, $id1,…);
   * @return boolean
   */
  public function delete(array $data)
  {
    foreach ($data as $key => $id)
    {
      $Task = $this->byId($id);
      $Task->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }
}
