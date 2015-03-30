<?php namespace App\Console\Commands;

use App\Repositories\Permission\PermissionRepository;
use App\Services\AnnotationService;
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
		if(is_null($this->argument('insert'))) {
			$permissions = $permissionRepository->get();
			foreach($permissions as $permission){
				$permissionRepository->delete($permission->id);
			}
			DB::table('permissions')->truncate();
		}

		$handle = opendir(app_path().'/Http/Controllers');
		$classes = array();
		while (false !== ($entry = readdir($handle))) {
			if($entry != '.' && $entry != '..') {
				$class = explode('.', $entry);
				$classes[] = $class[0];
			}
		}

		$permissions = array();
		foreach($classes as $class) {
			try {
				$annotationService = new AnnotationService('App\\Http\\Controllers\\' . $class, '@permission');
			} catch (\Exception $e){
				$this->error($e->getMessage());
			}
			$permissions = array_merge($permissions, $annotationService->getValues());
		}

		foreach($permissions as $permission){
			$permission = str_replace('_', ' ', $permission);
			$permissionRepository->createOrUpdate(['name' => $permission]);
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
			['insert', InputArgument::OPTIONAL, 'If only inserts should be done'],
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
