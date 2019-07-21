<?php
/**
 * @file
 * Description of the script.
 *
 * All ModuleName code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */

namespace Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee;

use Illuminate\Database\Eloquent\Model;

use App\Kwaai\Template\TableName;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\DatabaseManager;

use Mgallegos\DecimaHumanResources\HumanResources\Employee;

class EloquentEmployee implements EmployeeInterface {

  /**
   * Account
   *
   * @var App\Kwaai\Template\Employee;
   *
   */
  protected $Employee;

  /**
   * Database Connection
   *
   * @var string
   *
   */
  protected $databaseConnectionName;

  /**
   * DB
   *
   * @var Illuminate\Database\DatabaseManager
   *
   */
  protected $DB;

  public function __construct(Model $Employee, $databaseConnectionName, DatabaseManager $DB)
  {
      $this->Employee = $Employee;

      $this->databaseConnectionName = $databaseConnectionName;

      $this->Employee->setConnection($databaseConnectionName);

      $this->DB = $DB;
  }

  /**
   * Get table name
   *
   * @return string
   */
  public function getTable()
  {
    return $this->Employee->getTable();
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
  	return $this->Employee->on($this->databaseConnectionName)->find($id);
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
    return $this->Employee->where('organization_id', '=', $id)->get();
  }

  /**
  * Get Employee Info by Organization.
  *
  * @param  int $organizationId
  * @param  string $databaseConnectionName
  *
  * @return Illuminate\Database\Eloquent\Collection
  */
  public function byOrganizationWithPositionAndDepartment($organizationId, $databaseConnectionName = null)
  {
    if(empty($databaseConnectionName))
    {
      $databaseConnectionName = $this->databaseConnectionName;
    }

    return new Collection(
      $this->DB->connection($databaseConnectionName)
        ->table('HR_Employee AS he')
        ->leftJoin('HR_Position AS hp', 'hp.id', '=', 'he.position_id')
        ->leftJoin('HR_Department AS hd', 'hd.id', '=', 'he.departament_id')
        ->where('he.organization_id', '=', $organizationId)
        ->get(
          array(
            'he.id',
            $this->DB->raw('CONCAT(he.names," ", he.surnames) AS name'),
            'he.tax_id',
            'he.single_identity_document_number',
            'he.passport_number',
            $this->DB->raw('\'\' AS address'),
            // 'he.address',
            'he.personal_email',
            'he.residence_phone',
            'he.mobile_phone',
            'he.work_phone',
            'he.work_email',
            'hp.id AS position_id',
            'hp.name AS position_name',
            'hd.id AS department_id',
            'hd.name AS department_name'
          )
        )
      );

    // return new Collection(
    //   $this->DB->connection($databaseConnectionName)
    //     ->table('PURCH_Supplier AS s')
    //     ->leftJoin('PURCH_Taxpayer_Classification AS tc', 'tc.id', '=', 's.taxpayer_classification_id')
    //     ->where('s.organization_id', '=', $organizationId)
    //     ->select(
    //       's.id',
    //       's.name',
    //       's.payment_term_id AS ',
    //       's.trade_name',
    //       'tc.name AS taxpayer_classification_name'
    //     )
    //     ->get()
    //   );
  }

  /**
   * Retrieve Id by System user and by organization
   *
   * @param  int $id Organization id
   *
   * @return Illuminate\Database\Eloquent\Collection
   */
  public function bySystemUserIdAndByOrganization($systemUserId, $organizationId)
  {
    return $this->Employee->where('user_id', '=', $systemUserId)->where('organization_id', '=', $organizationId)->get();
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
    $Employee = new Employee();
    $Employee->setConnection($this->databaseConnectionName);
    $Employee->fill($data)->save();

    return $Employee;
  }

  /**
   * Update an existing ...
   *
   * @param array $data
   * 	An array as follows: array('field0'=>$field0, 'field1'=>$field1
   *                            );
   *
   * @param Mgallegos\DecimaAccounting\Account $Employee
   *
   * @return boolean
   */
  public function update(array $data, $Employee = null)
  {
    if(empty($Employee))
    {
      $Employee = $this->byId($data['id']);
    }

    foreach ($data as $key => $value)
    {
      $Employee->$key = $value;
    }

    return $Employee->save();
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
      $Employee = $this->byId($id);
      $Employee->delete();
    }
    // $this->Account->destroy($data);

    return true;
  }



}
