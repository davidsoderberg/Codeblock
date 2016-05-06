<?php namespace App\Console\Commands;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\CRepository;
use App\Services\Github;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class Install
 * @package App\Console\Commands
 */
class Install extends Command
{

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
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @param RoleRepository $roleRepository
	 * @param PermissionRepository $permissionRepository
	 * @param CategoryRepository $categoryRepository
	 * @param UserRepository $userRepository
	 *
	 * @return mixed
	 */
	public function fire(
		RoleRepository $roleRepository,
		PermissionRepository $permissionRepository,
		CategoryRepository $categoryRepository,
		UserRepository $userRepository
	) {
		$startat = $this->option('startat');
		// tests the smtp configs.
		if (is_null($startat)) {
			$mail = New CRepository();
			$data = array('subject' => 'Test mail', 'body' => 'This is a mail to test mail configuration.');
			$emailInfo = array('toEmail' => env('FROM_ADRESS'), 'toName' => env('FROM_NAME'), 'subject' => 'Test mail');
			if (!$mail->sendEmail('emails.notification', $emailInfo, $data)) {
				$this->error('The mail is not configured correctly, please try agian and use option startat with "Mail" as value.');
			}
			$this->line('SMTP settings: ok');
			$startat = 'Github';
		}
		// tests the github configs.
		if ($startat == 'Github') {
			$github = new Github();
			if (!$github->isToken(env('GITHUB_TOKEN', null))) {
				$this->error('The github token is not valid, please try agian and use option startat with "Github" as value.');
			}
			$this->line('Github token: ok');
			$startat = 'Database';
		}
		// tests the database configs.
		if ($startat == 'Database') {
			try {
				DB::connection()->getDatabaseName();
			} catch (\Exception $e) {
				$this->error('The database is not configured correctly, please try agian and use option startat with "Database" as value.');
			}
			$this->line('Database connection: ok');
			$startat = 'Migration';
		}
		// migrates all migrations.
		if ($startat == 'Migration') {
			try {
				Artisan::call('migrate');
			} catch (\Exception $e) {
				$this->error('The migration could not be done for some reason, please try agian and use option startat with "Migration" as value.');
			}
			$this->line('Migration: done');
			$startat = 'Seed';
		}
		// Seeds the database.
		if ($startat == 'Seed' && count($categoryRepository->get()) == 0) {
			try {
				Artisan::call('db:seed');
			} catch (\Exception $e) {
				$this->error('The seed of database could not be done for some reason, please try agian and use option startat with "Seed" as value.');
			}
			$this->line('Seed: done');
			$startat = 'Permissions';
		}
		// insert all permissions.
		if ($startat == 'Permissions' && count($permissionRepository->get()) == 0 || count($permissionRepository->get()) == 0) {
			try {
				Artisan::call('InsertPermissions');
			} catch (\Exception $e) {
				$this->error('The permissions colud not be created for some reason, please try agian and use option startat with "Permissions" as value.');
			}
			$this->line('Permissions: created');
			$startat = 'Role';
		}
		// creating the default role.
		if ($startat == 'Role' && count($roleRepository->get()) == 0 || count($roleRepository->get()) == 0) {
			$ids = array();
			foreach ($permissionRepository->get() as $permission) {
				$ids[] = $permission->id;
			}
			$created = false;
			while ($created != true) {
				$this->ShowErrors($roleRepository);
				$role = $this->ask('Your admin role name');
				$created = $roleRepository->createOrUpdate(array('name' => $role));
			}
			$this->line('');
			$roleRepository->errors = null;
			if (!$roleRepository->syncPermissions($roleRepository->role, $ids)) {
				$roleRepository->delete($roleRepository->role->id);
				$this->error('The role could get any permissions, please try agian and use option startat with "Role" as value.');
			}

			$created = false;
			while ($created != true) {
				$this->ShowErrors($roleRepository);
				$role = $this->ask('Your user role name');
				$created = $roleRepository->createOrUpdate(array('name' => $role, 'default' => 1));
			}
			$this->line('');
			$this->line('Roles: created');
			$startat = 'User';
		}
		if ($startat = 'User' && count($userRepository->get()) == 0 || count($userRepository->get()) == 0) {
			$errorkeys = array('username', 'password', 'email');
			$info = array();
			$created = false;

			while ($created != true) {
				$keys = $this->ShowErrors($userRepository);
				if (!is_null($keys)) {
					$errorkeys = $keys;
				}
				$info = $this->getUserInfo($errorkeys, $info);
				$created = $userRepository->createOrUpdate($info + ['active' => 1]);
			}
			$this->line('First user: created & activated');
		}

		$this->line('Codeblock: installed');
	}

	/**
	 * Fetch user info from terminal.
	 *
	 * @param array $keys
	 * @param array $old
	 *
	 * @return array
	 */
	private function getUserInfo(array $keys = array(), array $old = array())
	{
		if (in_array('username', $keys)) {
			$username = $this->ask('Your admin username');
		} else {
			$username = $old['username'];
		}
		if (in_array('email', $keys)) {
			$email = $this->ask('Your admin email');
		} else {
			$email = $old['email'];
		}
		if (in_array('password', $keys)) {
			$password = $this->secret('Your admin password');
		} else {
			$password = $old['password'];
		}

		return ['email' => $email, 'username' => $username, 'password' => $password];
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

	/**
	 * Render errors in terminal.
	 *
	 * @param $repository
	 * @return mixed
	 */
	private function ShowErrors($repository)
	{
		if (is_object($repository->errors)) {
			if (count($repository->errors->all()) > 0) {
				$this->line('');
				$this->error('This errors do you need to fix:');
				foreach ($repository->errors->all() as $error) {
					$this->error(' * ' . $error);
				}
				$this->line('');
				$errorkeys = $repository->errors->keys();
				return $errorkeys;
			}
		}
		return null;
	}

}
