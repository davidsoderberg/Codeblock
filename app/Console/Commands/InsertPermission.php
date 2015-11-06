<?php namespace App\Console\Commands;

use App\Repositories\Permission\PermissionRepository;
use App\Services\Annotation\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class InsertPermission
 * @package App\Console\Commands
 */
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
	 *
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(PermissionRepository $permissionRepository) {
		// Checks if all old permissions should be deleted.
		if(is_null($this->argument('onlyInsert'))) {
			$this->deletePermissions($permissionRepository);
		}

		// Inserts all permission.
		foreach($this->getClasses() as $class) {
			try {
				$permissionAnnotation = new Permission('App\\Http\\Controllers\\' . $class);
			} catch(\Exception $e) {
				$this->error($e->getMessage());
			}
			foreach($permissionAnnotation->getMethods() as $method) {
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
	protected function getArguments() {
		return [
			['onlyInsert', InputArgument::OPTIONAL, 'If only inserts should be done'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [];
	}

	/**
	 * Deletes all permissons.
	 *
	 * @param PermissionRepository $permissionRepository
	 */
	private function deletePermissions(PermissionRepository $permissionRepository) {
		$permissions = $permissionRepository->get();
		foreach($permissions as $permission) {
			$permissionRepository->delete($permission->id);
		}
		DB::table('permissions')->truncate();
	}

	/**
	 * Gets all classes that should be walked through.
	 *
	 * @return array
	 */
	private function getClasses() {
		$handle = opendir(app_path() . '/Http/Controllers');
		$classes = [];
		while(false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
				$class = explode('.', $entry);
				$classes[] = $class[0];
			}
		}

		return $classes;
	}
}
