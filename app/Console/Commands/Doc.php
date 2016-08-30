<?php namespace App\Console\Commands;

use App\Services\CodeReader;
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
    protected $description = 'Creates code documentation.';

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
        $this->createHtml();
    }

    /**
     * Creates html file with documentation and saves it to storage folder doc.
     */
    private function createHtml()
    {
        $path = $this->ask('Where should we store documentation file inside ' . storage_path() . '?', 'doc');

        $path = trim($path, '/');
        $path = '/' . $path;

        if ( ! is_dir(storage_path() . $path)) {
            if ( ! mkdir(storage_path() . $path)) {
                $this->error('We could not create that directory, please try with another directory.');
            }
            $this->info(storage_path() . $path . ' have been created');
        }

        $this->info('Fetching documentation from php files');
        $code_reader = new CodeReader(app_path());

        $this->info('Creating html file for documentation.');
        $created = file_put_contents(storage_path() . $path . '/index.html',
            View::make('doc.index')->with('docs', $code_reader->getClasses())->render());

        if ($created === false) {
            $this->error('We could not create the documentation for some reason, please try agian.');
        } else {
            $this->info('Documentation have been created.');
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
