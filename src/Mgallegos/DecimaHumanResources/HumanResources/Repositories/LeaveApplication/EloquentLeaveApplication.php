<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\LeaveApplication;

class EloquentLeaveApplication implements LeaveApplicationInterface {

  /**
   * LeaveApplication
   *
   * @var Vendor\DecimaModule\Module\LeaveApplication;
   *
   */
  protected $LeaveApplication;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $LeaveApplication, $databaseConnectionName)
  {
      $this->LeaveApplication = $LeaveApplication;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->LeaveApplication->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->LeaveApplication->getTable();
  }

  /**
   * Get a ... by ID
   *
   * @param  int $id
   *
   * @return Vendor\DecimaModule\Module\LeaveApplication
   */
  public function byId($id)
  {
  	return $this->LeaveApplication->on($this->databaseConnectionName)->find($id);
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
    return $this->LeaveApplication->where('organization_id', '=', $id)->get();
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
    $LeaveApplication = new LeaveApplication();
    $LeaveApplication->setConnection($this->databaseConnectionName);
    $LeaveApplication->fill($data)->save();

    return $LeaveApplication;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   * 	An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Vendor\DecimaModule\Module\LeaveApplication $LeaveApplication
   *
   * @return boolean
   */
  public function update(array $data, $LeaveApplication = null)
  {
    if(empty($LeaveApplication))
    {
      $LeaveApplication = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $LeaveApplication->$key = $value;
    }

    return $LeaveApplication->save();
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
      $LeaveApplication = $this->byId($id);
      $LeaveApplication->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }

}
