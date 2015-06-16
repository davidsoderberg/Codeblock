<?php namespace App\Console\Commands;

use App\Repositories\Permission\PermissionRepository;
use App\Services\Annotation\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InsertPermission extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'InsertPermissions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete all old permissions and insert permissions from all controllers to database.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(PermissionRepository $permissionRepository)
	{
		// Checks if all old permissions should be deleted.
		if(is_null($this->argument('onlyInsert'))) {
			$permissions = $permissionRepository->get();
			foreach($permissions as $permission){
				$permissionRepository->delete($permission->id);
			}
			DB::table('permissions')->truncate();
		}

		// Get all controllers.
		$handle = opendir(app_path().'/Http/Controllers');
		$classes = array();
		while (false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
				$class = explode('.', $entry);
				$classes[] = $class[0];
			}
		}

		// Inserts all permission.
		foreach($classes as $class) {
			try {
				$permissionAnnotation = new Permission('App\\Http\\Controllers\\' . $class);
			} catch (\Exception $e){
				$this->error($e->getMessage());
			}
			foreach($permissionAnnotation->getMethods() as $method){
				$permissionRepository->createOrUpdate(['permission' => $permissionAnnotation->getPermission($method)]);
			}
		}

		$this->info('All permissions has been inserted');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['onlyInsert', InputArgument::OPTIONAL, 'If only inserts should be done'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}
