<?php namespace App\Console\Commands;

use App\Services\DocBlock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

/**
 * Class Websocket
 * @package App\Console\Commands
 */
class Doc extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var array
     */
    private $paths = [];

    /**
     * Create a new command instance.
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
    public function fire()
    {
        $this->getClasses();
        $this->loopClasses();
        $this->createHtml();
    }

    /**
     *
     */
    private function createHtml()
    {
        if (! is_dir(storage_path() . '/doc')) {
            mkdir(storage_path() . '/doc');
        }
        file_put_contents(storage_path() . '/doc/index.html', View::make('doc')->with('docs', $this->classes)->render());
    }

    /**
     *
     */
    private function loopClasses()
    {
        foreach ($this->paths as $class) {
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $this->create_class($reflection);
                $class = $reflection->getName();

                if (! empty($reflection->getTraits())) {
                    foreach ($reflection->getTraits() as $trait) {
                        $this->classes[$class]['traits'][] = $trait->getName();
                        $this->create_class($trait);
                    }
                }
            }
        }
    }

    /**
     * @param $class
     */
    private function create_class($class)
    {
        $class_name = $class->getName();
        $result = new DocBlock($class->getDocComment());
        if (in_array($class_name, $this->paths)) {
            $this->classes[$class_name] = [
                'desc' => $result->desc,
                'tags' => $result->tags,
                'properties' => [],
                'methods' => []
            ];

            foreach ($class->getProperties() as $property) {
                $result = new DocBlock($property->getDocComment());
                $this->classes[$class_name]['properties'][$property->getName()] = [
                    'desc' => $result->desc,
                    'tags' => $result->tags

                ];
            }
            foreach ($class->getMethods() as $method) {
                $result = new DocBlock($method->getDocComment());
                $this->classes[$class_name]['methods'][$method->getName()] = [
                    'desc' => $result->desc,
                    'tags' => $result->tags
                ];
            }
        }
    }

    /**
     * Fetch all classes from selected folder and its subfolders.
     *
     * @return array
     */
    private function getClasses()
    {
        $di = new \RecursiveDirectoryIterator(app_path(), \RecursiveDirectoryIterator::SKIP_DOTS);
        $it = new \RecursiveIteratorIterator($di);

        foreach ($it as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
                $file          = str_replace(base_path().'\\', '', $file->getRealPath());
                $file          = str_replace('.php', '', $file);
                $this->paths[] = ucfirst($file);
            }
        }
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
        return [];
    }
}
