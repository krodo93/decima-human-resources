<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\LeaveType;

class EloquentLeaveType implements LeaveTypeInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\LeaveType;
   *
   */
  protected $LeaveType;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $LeaveType, $databaseConnectionName)
  {
      $this->LeaveType = $LeaveType;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->LeaveType->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->LeaveType->getTable();
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
    return $this->LeaveType->on($this->databaseConnectionName)->find($id);
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
    return $this->LeaveType->where('organization_id', '=', $id)->get();
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
    $LeaveType = new LeaveType();
    $LeaveType->setConnection($this->databaseConnectionName);
    $LeaveType->fill($data)->save();

    return $LeaveType;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $LeaveType
   *
   * @return boolean
   */
  public function update(array $data, $LeaveType = null)
  {
    if(empty($LeaveType))
    {
      $LeaveType = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $LeaveType->$key = $value;
    }

    return $LeaveType->save();
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
      $LeaveType = $this->byId($id);
      $LeaveType->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }
}
