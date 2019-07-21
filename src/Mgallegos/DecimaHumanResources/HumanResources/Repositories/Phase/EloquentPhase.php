<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase;

use Illuminate\Database\Eloquent\Model;

use Mgallegos\DecimaHumanResources\HumanResources\Phase;

class EloquentPhase implements PhaseInterface {

  /**
   * Phase
   *
   * @var Vendor\DecimaModule\Module\Phase;
   *
   */
  protected $Phase;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  public function __construct(Model $Phase, $databaseConnectionName)
  {
      $this->Phase = $Phase;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Phase->setConnection($databaseConnectionName);
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Phase->getTable();
  }

  /**
   * Get a ... by ID
   *
   * @param  int $id
   *
   * @return Vendor\DecimaModule\Module\Phase
   */
  public function byId($id)
  {
  	return $this->Phase->on($this->databaseConnectionName)->find($id);
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
    return $this->Phase->where('organization_id', '=', $id)->get();
  }

  /**
   * Retrieve ... by organization
   *
   * @param  int $id Organization id
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  // public function byOrganizationWithTasks($id)
  // {
  //
  //   // return $this->Phase->where('organization_id', '=', $id)->with(['tasks' => function($query)
  //   // {
  //   //   $query->orderBy('position', 'asc')->get();
  //   // }])->orderBy('position', 'asc')->get();
  // }

  /**
   * Get the max movement number
   *
   * @param  int $id Organization id
   *
   * @return integer
   */
  public function getMaxPhaseNumber($id)
  {
    return $this->Phase->where('organization_id', '=', $id)->max('position');
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
    $Phase = new Phase();
    $Phase->setConnection($this->databaseConnectionName);
    $Phase->fill($data)->save();

    return $Phase;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   * 	An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Vendor\DecimaModule\Module\Phase $Phase
   *
   * @return boolean
   */
  public function update(array $data, $Phase = null)
  {
    if(empty($Phase))
    {
      $Phase = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $Phase->$key = $value;
    }

    return $Phase->save();
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
      $Phase = $this->byId($id);
      $Phase->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }

}
