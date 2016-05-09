<?php namespace App\Services;

use Dotenv\Loader;
use App;
use Dotenv\Dotenv;
use \Illuminate\Foundation\Application;

/**
 * Class DotEnvSaver
 * @package App\Services
 */
class Env extends Loader
{

    /**
     * @var Application
     */
    private $app;

    /**
     * @var
     */
    private $lines;

    /**
     * DotEnvSaver constructor.
     * @param Application $app
     * @param $path
     * @param $file
     */
    public function __construct(Application $app, $path = '', $file = '')
    {
        if (empty($path)) {
            $path = App::environmentPath();
        }
        if (empty($file)) {
            $file = App::environmentFile();
        }
        $this->filePath = $this->getfilePath($path, $file);
        $this->app = $app;
        $this->load();
    }

    /**
     * @return mixed
     */
    public function getLines()
    {
        return $this->lines;
    }

    public function getKeyValus($allowCommets = false)
    {
        $lines = $this->lines;
        $inputs = [];
        foreach ($lines as $line) {
            if (!$this->isComment($line) && $this->looksLikeSetter($line)) {
                $input = explode('=', $line);
                if (is_array($input) && count($input) === 2) {
                    $inputs[$input[0]] = $input[1];
                }
            }
            if($this->isComment($line) && $allowCommets){
                $inputs[] = trim(str_replace('#', '', $line));
            }
        }
        return $inputs;
    }

    public function save($inputs)
    {
        $lines = $this->lines;
        foreach ($inputs as $key => $value) {
            $currentLine = $this->processFilters($key, $value);
            $lineToInsert = $currentLine[0] . '=' . $currentLine[1];
            $exists = false;

            foreach ($lines as $index => $line) {
                if (strpos($line, $key) !== false) {
                    $lines[$index] = $lineToInsert;
                    $exists = true;
                }
            }
            if (!$exists) {
                $lines[] = $lineToInsert;
            }
        }
        $this->lines = $lines;
        $this->saveLinesToFile();
    }

    private function saveLinesToFile()
    {
        $this->immutable = false;
        foreach ($this->lines as $line) {
            $this->setEnvironmentVariable($line);
        }
        $this->immutable = true;
        file_put_contents($this->filePath, implode(PHP_EOL, $this->lines));
        $this->reloadEnvironment();
    }

    /**
     * Load `.env` file in given directory.
     *
     * @return array
     */
    public function load()
    {
        $this->immutable = true;

        $this->lines = parent::load();
    }

    /**
     * Load `.env` file in given directory.
     *
     * @return array
     */
    public function overload()
    {
        $this->immutable = false;

        $this->lines = parent::load();
    }

    /**
     *
     */
    private function reloadEnvironment()
    {
        App::make('Illuminate\Foundation\Bootstrap\DetectEnvironment')->bootstrap($this->app);
        $this->overload();
        App::make('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($this->app);
    }

    /**
     * Returns the full path to the file.
     *
     * @param string $path
     * @param string $file
     *
     * @return string
     */
    protected function getfilePath($path, $file)
    {
        if (!is_string($file)) {
            $file = '.env';
        }

        $filePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;

        return $filePath;
    }
}