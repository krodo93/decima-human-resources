<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\Department;

class EloquentDepartment implements DepartmentInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\Department;
   *
   */
  protected $Department;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $Department, $databaseConnectionName)
  {
      $this->Department = $Department;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Department->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Department->getTable();
  }

  /**
   * Get a ... by ID
   *
   * @param  int $id
   *
   * @return Mgallegos\DecimaAccounting\Account
   */
  public function byId($id, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    return $this->Department->on($this->databaseConnectionName)->find($id);
  }

  /**
   * Retrieve document types by name and by organization
   *
   * @param string $name
   * @param int $organizationId
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function byNameAndByOrganizationId($name, $organizationId, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    return $this->Department->setConnection($databaseConnectionName)
      ->where('name', '=', $name)
      ->where('organization_id', '=', $organizationId)
      ->get();
  }

  /**
   * Retrieve ... by organization
   *
   * @param  int $id Organization id
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function byOrganization($id, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    return $this->Department->setConnection($databaseConnectionName)->where('organization_id', '=', $id)->get();
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
  public function create(array $data, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    $Department = new Department();
    $Department->setConnection($databaseConnectionName);
    $Department->fill($data)->save();

    return $Department;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $Department
   *
   * @return boolean
   */
  public function update(array $data, $Department = null, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    if(empty($Department))
    {
      $Department = $this->byId($data['id'], $databaseConnectionName);
    }

    foreach ($data as $key => $value)
    {
      $Department->$key = $value;
    }

    return $Department->save();
  }
  /**
   * Delete existing ... (soft delete)
   *
   * @param array $data
   *  An array as follows: array($id0, $id1,…);
   * @return boolean
   */
  public function delete(array $data, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    foreach ($data as $key => $id)
    {
      $Department = $this->byId($id, $databaseConnectionName);
      $Department->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }
}
