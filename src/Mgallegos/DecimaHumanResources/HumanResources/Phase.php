<?php
/**
 * @file
 * Currency Model.
 *
 * All DecimaERP code is copyright by the original authors and released under the GNU Aferro General Public License version 3 (AGPLv3) or later.
 * See COPYRIGHT and LICENSE.
 */
namespace Mgallegos\DecimaHumanResources\HumanResources;

use Eloquent;

class Phase extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'HR_Phase';

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	/**
	 * Indicates if the model should soft delete.
	 *
	 * @var bool
	 */
	//protected $softDelete = true;

	/**
	 *  One-To-Many relation between HR_Phase and HR_Task
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\
	 */
	public function tasks()
	{
		return $this->hasMany('Mgallegos\DecimaHumanResources\HumanResources\Task', 'phase_id', 'id');
	}

}
