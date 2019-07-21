<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\Holiday;

class EloquentHoliday implements HolidayInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\Holiday;
   *
   */
  protected $Holiday;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $Holiday, $databaseConnectionName)
  {
      $this->Holiday = $Holiday;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Holiday->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Holiday->getTable();
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
    return $this->Holiday->on($this->databaseConnectionName)->find($id);
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
    return $this->Holiday->where('organization_id', '=', $id)->get();
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
    $Holiday = new Holiday();
    $Holiday->setConnection($this->databaseConnectionName);
    $Holiday->fill($data)->save();

    return $Holiday;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $Holiday
   *
   * @return boolean
   */
  public function update(array $data, $Holiday = null)
  {
    if(empty($Holiday))
    {
      $Holiday = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $Holiday->$key = $value;
    }

    return $Holiday->save();
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
      $Holiday = $this->byId($id);
      $Holiday->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }
}
