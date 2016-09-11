<?php namespace App\Console\Commands;

use App\Services\CodeReader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;
use App\Services\ApiDoc;
use Crada\Apidoc\Exception;

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReplyController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\UserController;

/**
 * Class Doc
 * @package App\Console\Commands
 */
class Doc extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generats api and code documentation.';

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
        $path = $this->createDocDir();
        if (! is_null($path)) {
            $this->createApiHtml($path);
            $this->createDocHtml($path);
        }
    }

    /**
     * Create storage directory to save documentation in.
     *
     * @return string
     */
    private function createDocDir()
    {
        $path = $this->ask('Where should we store documentation files ' . storage_path() . '?', 'doc');
        $path = trim($path, '/');
        $path = storage_path() . '/' . $path;

        if (! is_dir($path)) {
            if (! mkdir($path)) {
                $this->error('We could not create that directory, please try with another directory.');
            }
            $this->info($path . ' have been created');

            return $path;
        } else {
            return $path;
        }
    }

    /**
     * Creates html file for api documentation and saves it to storage folder doc.
     *
     * @param string $path
     */
    private function createApiHtml($path)
    {
        $classes = [
            ArticleController::class,
            AuthController::class,
            CategoryController::class,
            CommentController::class,
            ForumController::class,
            NotificationController::class,
            PostController::class,
            ReplyController::class,
            TagController::class,
            TeamController::class,
            TopicController::class,
            UserController::class
        ];

        try {
            $api_doc = new ApiDoc($classes);
            $data    = View::make('doc.api.index')->with($api_doc->generate())->render();
            $file    = $path . '/api.html';

            if ($this->createHtmlFile($data, $file)) {
                $this->info('Api documentation have been created.');
            } else {
                $this->error('We could not create the api documentation, please try agian.');
            }
        } catch (Exception $e) {
            $this->error('There was an error generating the api documentation: ', $e->getMessage());
        }
    }

    private function createHtmlFile($data, $file)
    {
        return file_put_contents($file, $data);
    }

    /**
     * Creates html file for code documentation and saves it to storage folder doc.
     *
     * @param string $path
     */
    private function createDocHtml($path)
    {
        $this->info('Fetching documentation from php files');
        $code_reader = new CodeReader(app_path());

        $this->info('Creating html file for documentation.');
        $data = View::make('doc.index')->with('docs', $code_reader->getClasses())->render();
        $file = $path . '/index.html';

        if ($this->createHtmlFile($data, $file)) {
            $this->info('Documentation have been created.');
        } else {
            $this->error('We could not create the code documentation, please try agian.');
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
