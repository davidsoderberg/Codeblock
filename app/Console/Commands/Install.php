<?php namespace App\Console\Commands;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Role\RoleRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\CRepository;
use App\Services\Github;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Install extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'Install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Installs codeblock.';

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
	public function fire(RoleRepository $roleRepository, PermissionRepository $permissionRepository, CategoryRepository $categoryRepository)
	{
		$startat = $this->option('startat');
		// tests the smtp configs.
		if(is_null($startat)){
			$mail = New CRepository();
			$data = array('subject' => 'Test mail', 'body' => 'This is a mail to test mail configuration.');
			$emailInfo = array('toEmail' => env('FROM_ADRESS'), 'toName' => env('FROM_NAME'), 'subject' => 'Test mail');
			if(!$mail->sendEmail('emails.notification', $emailInfo, $data)) {
				$this->error('The mail is not configured correctly, please try agian and use option startat with "Mail" as value.');
			}
			$startat = 'Github';
		}
		// tests the github configs.
		if($startat == 'Github') {
			$github = new Github();
			if(!$github->isToken(env('GITHUB_TOKEN', null))) {
				$this->error('The github token is not valid, please try agian and use option startat with "Github" as value.');
			}
			$startat = 'Database';
		}
		// tests the database configs.
		if($startat == 'Database') {
			try {
				DB::connection()->getDatabaseName();
			} catch (\Exception $e){
				$this->error('The database is not configured correctly, please try agian and use option startat with "Database" as value.');
			}
			$startat = 'Migration';
		}
		// migrates all migrations.
		if($startat == 'Migration') {
			try{
				Artisan::call('migrate');
			} catch (\Exception $e){
				$this->error('The migration could not be done for some reason, please try agian and use option startat with "Migration" as value.');
			}
			$startat = 'Seed';
		}
		// Seeds the database.
		if($startat == 'Seed' && count($categoryRepository->get()) == 0) {
			try {
				Artisan::call('db:seed');
			} catch(\Exception $e) {
				$this->error('The seed of database could not be done for some reason, please try agian and use option startat with "Seed" as value.');
			}
			$startat = 'Permissions';
		}
		// insert all permissions.
		if($startat == 'Permissions' && count($permissionRepository->get()) == 0 || count($permissionRepository->get()) == 0 ) {
			try {
				Artisan::call('InsertPermissions');
			} catch(\Exception $e) {
				$this->error('The permissions colud not be created for some reason, please try agian and use option startat with "Permissions" as value.');
			}
			$startat = 'Role';
		}
		// creating the default role.
		if($startat == 'Role' && count($roleRepository->get()) == 0 || count($roleRepository->get()) == 0) {
			$ids = array();
			foreach($permissionRepository->get() as $permission) {
				$ids[] = $permission->id;
			}
			if(!$roleRepository->createOrUpdate(array('name' => 'Super admin', 'default' => 1))) {
				$this->error('The role could not be created, please try agian.');
			}
			if(!$roleRepository->syncPermissions($roleRepository->role, $ids)) {
				$roleRepository->delete($roleRepository->role->id);
				$this->error('The role could get any permissions, please try agian and use option startat with "Role" as value.');
			}
		}
		$this->info('You have now installed codeblock.');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['startat', 'start', InputOption::VALUE_OPTIONAL, 'Start step is done.', null]
		];
	}

}
