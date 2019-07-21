<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Afp;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\Afp;

class EloquentAfp implements AfpInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\Afp;
   *
   */
  protected $Afp;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $Afp, $databaseConnectionName)
  {
      $this->Afp = $Afp;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Afp->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Afp->getTable();
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
    return $this->Afp->on($this->databaseConnectionName)->find($id);
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
    return $this->Afp->where('organization_id', '=', $id)->get();
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
    $Afp = new Afp();
    $Afp->setConnection($this->databaseConnectionName);
    $Afp->fill($data)->save();

    return $Afp;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   *  An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $Afp
   *
   * @return boolean
   */
  public function update(array $data, $Afp = null)
  {
    if(empty($Afp))
    {
      $Afp = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $Afp->$key = $value;
    }

    return $Afp->save();
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
      $Afp = $this->byId($id);
      $Afp->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }
}
