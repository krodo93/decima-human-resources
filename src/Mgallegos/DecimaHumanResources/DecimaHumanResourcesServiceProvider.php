<?php namespace Mgallegos\DecimaHumanResources;

use Carbon\Carbon;

use Illuminate\Support\ServiceProvider;

class DecimaHumanResourcesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	* Bootstrap any application services.
	*
	* @return void
	*/
	public function boot()
	{
		include __DIR__.'/../../routes.php';

		// include __DIR__.'/../../helpers.php';

		$this->loadViewsFrom(__DIR__.'/../../views', 'decima-human-resources');

		$this->loadTranslationsFrom(__DIR__.'/../../lang', 'decima-human-resources');

		$this->publishes([
				__DIR__ . '/../../config/config.php' => config_path('human-resources-general.php'),
		], 'config');

		$this->mergeConfigFrom(
				__DIR__ . '/../../config/config.php', 'human-resources-general'
		);

		$this->publishes([
				__DIR__ . '/../../config/journal.php' => config_path('human-resources-journal.php'),
		], 'config');

		$this->mergeConfigFrom(
				__DIR__ . '/../../config/journal.php', 'human-resources-journal'
		);

		$this->publishes([
    __DIR__.'/../../migrations/' => database_path('/migrations')
		], 'migrations');

		$this->registerJournalConfiguration();

		$this->registerAfpInterface();

		$this->registerAfpManagementInterface();

		$this->registerEmployeeInterface();

		$this->registerEmployeeManagementInterface();

		$this->registerPositionInterface();

		$this->registerPositionManagementInterface();

		$this->registerDepartmentInterface();

		$this->registerDepartmentManagementInterface();

		$this->registerLeaveTypeInterface();

		$this->registerLeaveTypeManagementInterface();

		$this->registerHolidayInterface();

		$this->registerHolidayManagementInterface();

		$this->registerPhaseInterface();

		$this->registerPhaseManagementInterface();

		$this->registerLeaveApplicationInterface();

		$this->registerLeaveApplicationManagementInterface();

		$this->registerTaskInterface();

		$this->registerTaskManagementInterface();

		$this->registerWorkedHourInterface();

		$this->registerWorkedHourManagementInterface();

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	* Register a new organization trigger.
	*
	* @return void
	*/
	protected function registerJournalConfiguration()
	{
		$journalConfiguration = $this->app->make('AppJournalConfigurations');

		$this->app->instance('AppJournalConfigurations', array_merge($journalConfiguration, $this->app['config']->get('human-resources-journal')));
	}

	/**
	* Register a Afp interface instance.
	*
	* @return void
	*/
	protected function registerAfpInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Afp\AfpInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Afp\EloquentAfp( new \Mgallegos\DecimaHumanResources\HumanResources\Afp() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register an Employee interface instance.
	*
	* @return void
	*/
	protected function registerEmployeeInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EloquentEmployee( new \Mgallegos\DecimaHumanResources\HumanResources\Employee() , $AuthenticationManager->getCurrentUserOrganizationConnection(), $app['db']);
		});
	}

	/**
	* Register a Position interface instance.
	*
	* @return void
	*/
	protected function registerPositionInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\PositionInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\EloquentPosition( new \Mgallegos\DecimaHumanResources\HumanResources\Position() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a Department interface instance.
	*
	* @return void
	*/
	protected function registerDepartmentInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\DepartmentInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\EloquentDepartment( new \Mgallegos\DecimaHumanResources\HumanResources\Department() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a LeaveApplication interface instance.
	*
	* @return void
	*/
	protected function registerLeaveApplicationInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication\LeaveApplicationInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication\EloquentLeaveApplication( new \Mgallegos\DecimaHumanResources\HumanResources\LeaveApplication() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a LeaveType interface instance.
	*
	* @return void
	*/
	protected function registerLeaveTypeInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\LeaveTypeInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\EloquentLeaveType( new \Mgallegos\DecimaHumanResources\HumanResources\LeaveType() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a Holiday interface instance.
	*
	* @return void
	*/
	protected function registerHolidayInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\HolidayInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\EloquentHoliday( new \Mgallegos\DecimaHumanResources\HumanResources\Holiday() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a Phase interface instance.
	*
	* @return void
	*/
	protected function registerPhaseInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\PhaseInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\EloquentPhase( new \Mgallegos\DecimaHumanResources\HumanResources\Phase() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a Task interface instance.
	*
	* @return void
	*/
	protected function registerTaskInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task\TaskInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task\EloquentTask( new \Mgallegos\DecimaHumanResources\HumanResources\Task() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}

	/**
	* Register a WorkedHour interface instance.
	*
	* @return void
	*/
	protected function registerWorkedHourInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\WorkedHourInterface', function($app)
		{
			$AuthenticationManager = $app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface');

			return new \Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\EloquentWorkedHour( new \Mgallegos\DecimaHumanResources\HumanResources\WorkedHour() , $AuthenticationManager->getCurrentUserOrganizationConnection());
		});
	}


	protected function registerAfpManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\AfpManagement\AfpManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\AfpManagement\AfpManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Afp\EloquentAfpGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Afp\AfpInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerEmployeeManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\EmployeeManagement\EmployeeManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\EmployeeManagement\EmployeeManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EloquentEmployeeGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
        $app->make('App\Kwaai\System\Repositories\Country\CountryInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\DepartmentInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\PositionInterface'),
				$app->make('Mgallegos\DecimaBank\Bank\Repositories\Bank\BankInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface'),
				$app->make('gravatar'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerPositionManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\PositionManagement\PositionManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\PositionManagement\PositionManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\EloquentPositionGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Position\PositionInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerDepartmentManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\DepartmentManagement\DepartmentManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\DepartmentManagement\DepartmentManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\EloquentDepartmentGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Department\DepartmentInterface'),
				$app->make('Mgallegos\DecimaAccounting\Accounting\Repositories\CostCenter\CostCenterInterface'),
				$app->make('Mgallegos\DecimaInventory\Inventory\Repositories\Warehouse\WarehouseInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerLeaveApplicationManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveApplicationManagement\LeaveApplicationManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveApplicationManagement\LeaveApplicationManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication\EloquentLeaveApplicationGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveApplication\LeaveApplicationInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\LeaveTypeInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerLeaveTypeManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveTypeManagement\LeaveTypeManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\LeaveTypeManagement\LeaveTypeManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\EloquentLeaveTypeGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\LeaveType\LeaveTypeInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerHolidayManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\HolidayManagement\HolidayManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\HolidayManagement\HolidayManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\EloquentHolidayGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Holiday\HolidayInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerPhaseManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\PhaseManagement\PhaseManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\PhaseManagement\PhaseManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\EloquentPhaseGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\PhaseInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerTaskManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\TaskManagement\TaskManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\TaskManagement\TaskManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task\EloquentTaskGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Task\TaskInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Employee\EmployeeInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\Phase\PhaseInterface'),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\WorkedHourInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	protected function registerWorkedHourManagementInterface()
	{
		$this->app->bind('Mgallegos\DecimaHumanResources\HumanResources\Services\WorkedHourManagement\WorkedHourManagementInterface', function($app)
		{
			return new \Mgallegos\DecimaHumanResources\HumanResources\Services\WorkedHourManagement\WorkedHourManager(
				$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
				$app->make('App\Kwaai\Security\Services\JournalManagement\JournalManagementInterface'),
				$app->make('App\Kwaai\Security\Repositories\Journal\JournalInterface'),
				new	\Mgallegos\LaravelJqgrid\Encoders\JqGridJsonEncoder($app->make('excel')),
				new	\Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\EloquentWorkedHourGridRepository(
					$app['db'],
					$app->make('App\Kwaai\Security\Services\AuthenticationManagement\AuthenticationManagementInterface'),
					$app['translator']
				),
				$app->make('Mgallegos\DecimaHumanResources\HumanResources\Repositories\WorkedHour\WorkedHourInterface'),
				new Carbon(),
				$app['db'],
				$app['translator'],
				$app['config']
			);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
