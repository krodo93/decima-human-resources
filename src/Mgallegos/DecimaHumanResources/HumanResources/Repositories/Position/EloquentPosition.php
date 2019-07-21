<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\Position;

class EloquentPosition implements PositionInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\Position;
   *
   */
  protected $Position;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $Position, $databaseConnectionName)
  {
      $this->Position = $Position;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Position->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Position->getTable();
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
    return $this->Position->on($this->databaseConnectionName)->find($id);
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
    return $this->Position->where('organization_id', '=', $id)->get();
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
    $Position = new Position();
    $Position->setConnection($this->databaseConnectionName);
    $Position->fill($data)->save();

    return $Position;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $Position
   *
   * @return boolean
   */
  public function update(array $data, $Position = null)
  {
    if(empty($Position))
    {
      $Position = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $Position->$key = $value;
    }

    return $Position->save();
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
      $Position = $this->byId($id);
      $Position->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }
}
