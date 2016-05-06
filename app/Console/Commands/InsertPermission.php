<?php namespace App\Console\Commands;

use App\Repositories\Permission\PermissionRepository;
use App\Services\Annotation\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class InsertPermission
 * @package App\Console\Commands
 */
class InsertPermission extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'insertPermissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts permissions from all controllers to database.';

    /**
     * Constructor for InserPermission.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param PermissionRepository $permissionRepository
     *
     * @return mixed
     */
    public function fire(PermissionRepository $permissionRepository)
    {
        // Checks if all old permissions should be deleted.
        if (!is_null($this->argument('truncate'))) {
            $this->deletePermissions($permissionRepository);
            $this->info('Your permission table have been truncated.');
        }

        // Inserts all permission.
        foreach ($this->getClasses() as $class) {
            try {
                $permissionAnnotation = new Permission('App\\Http\\Controllers\\' . $class);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            foreach ($permissionAnnotation->getMethods() as $method) {
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
            [
                'truncate',
                InputArgument::OPTIONAL,
                'Deletes all old permission and truncates the permission table before insert are done.',
            ],
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

    /**
     * Deletes all permissons.
     *
     * @param PermissionRepository $permissionRepository
     */
    private function deletePermissions(PermissionRepository $permissionRepository)
    {
        $permissions = $permissionRepository->get();
        foreach ($permissions as $permission) {
            $permissionRepository->delete($permission->id);
        }
        DB::table('permissions')->truncate();
    }

    /**
     * Gets all classes that should be walked through.
     *
     * @return array
     */
    private function getClasses()
    {
        $files = $this->getFilesFromFolder('/Http/Controllers');
        $classes = [];
        foreach ($files as $file) {
            $class = explode('.', $file);
            $classes[] = $class[0];
        }

        return $classes;
    }

    /**
     * Fetch all files from selected folder and its subfolders.
     *
     * @param $path
     *
     * @return array
     */
    private function getFilesFromFolder($path)
    {
        if (!Str::contains($path, app_path())) {
            $path = app_path() . $path;
        }
        $handle = opendir($path);
        $files = [];
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                if (is_dir($path . '/' . $entry)) {
                    $newFiles = $this->getFilesFromFolder($path . '/' . $entry);
                    for ($i = 0; $i < count($newFiles); $i++) {
                        $newFiles[$i] = $entry . '\\' . $newFiles[$i];
                    }
                    $files += $newFiles;
                } else {
                    $files[] = $entry;
                }
            }
        }

        return $files;
    }
}
