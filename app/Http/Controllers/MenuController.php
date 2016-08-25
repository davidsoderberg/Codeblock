<?php namespace App\Http\Controllers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\CRepository;
use App\Services\Jwt;
use App\Services\LaravelLogViewer;
use App\Services\LogViewer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Orangehill\Iseed\Facades\Iseed;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

/**
 * Controller for some independent menuoptions.
 *
 * Class MenuController
 * @package App\Http\Controllers
 */
class MenuController extends Controller
{

    /**
     * Runs commands user submit.
     *
     * @param  string $command
     * @param  string $password
     * @param  string $param
     *
     * @return object
     */
    public function command($command, $password, $param = null)
    {
        $content = '';
        $title = '';
        if ($password == env('APP_KEY')) {
            switch ($command) {
                case 'seed':
                    Artisan::call('db:seed');
                    $content = "<pre>" . Lang::get('app.SeedingDone') . "</pre>";
                    $title = Lang::get('app.ArtisanSeed');
                    break;
                case 'migrate':
                    if ($param != null) {
                        $call = 'migrate:' . $param;
                    } else {
                        $call = 'migrate';
                    }
                    Artisan::call($call);
                    $content = "<pre>" . Lang::get('app.MigrationDone') . "</pre>";
                    $title = Lang::get('app.ArtisanMigrate');
                    break;
                case 'iseed':
                    $title = Lang::get('app.ArtisanISeed');
                    if ($param == null) {
                        $param = 'users';
                    }
                    try {
                        Iseed::generateSeed($param);
                        $content = "<pre>" . Lang::get('app.ISeedingDone', ['table' => $param]) . "</pre>";
                        $content .= "<a href='/command/seed/" . $password . "'>" . Lang::get('app.SeedNow') . "<a>";
                    } catch (Exception $e) {
                        $content = "<pre>" . Lang::get('app.ISeedingError', ['table' => $param]) . "</pre>";
                    }
                    break;
                case 'env':
                    $title = Lang::get('app.CheckEnvironment');
                    $content = app()->environment();
                    break;
                default:
                    $title = Lang::get('app.NoCommand');
                    break;
            }
        } else {
            $title = Lang::get('app.WrongPassword');
        }

        return View::make('command')->with('title', $title)->with('content', $content);
    }

    /**
     * Render view for index.
     * @return object
     */
    public function index()
    {
        if (Auth::check()) {
            return Redirect::action('MenuController@browse');
        }

        return View::make('index')->with('title', 'Home');
    }

    /**
     * Render view for browse posts.
     *
     * @param CategoryRepository $category
     * @param TagRepository $tag
     *
     * @return object
     */
    public function browse(CategoryRepository $category, TagRepository $tag)
    {
        return View::make('browse')
            ->with('title', 'Browse')
            ->with('tags', $tag->get())
            ->with('categories', $category->get());
    }

    /**
     * Render view for license
     * @return object
     */
    public function license()
    {
        return View::make('license')->with('title', 'Codeblock License');
    }

    /**
     * Render view for contact form.
     *
     * @return object
     */
    public function contact()
    {
        return View::make('contact')->with('title', 'Contact');
    }

    /**
     * Displays markdown helper view.
     *
     * @return mixed
     */
    public function markdown()
    {
        return View::make('markdown')->with('title', 'Markdown');
    }

    /**
     * Sends contact forms data.
     *
     * @param CRepository $ClassRepo
     *
     * @return object
     */
    public function sendContact(CRepository $ClassRepo)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'subject' => 'required|min:3',
            'message' => 'required|min:3',
        ];
        $input = $this->request->all();

        foreach ($input as $key => $value) {
            $input[$key] = trim(strip_tags($value));
        }

        $v = Validator::make($input, $rules);

        if ($v->passes()) {
            $input['Cmessage'] = $input['message'];
            $emailInfo = [
                'subject' => 'Contact message',
            ];
            if ($ClassRepo->sendEmail('emails.contact', $emailInfo, $input) == 1) {
                return Redirect::back()->with('success', 'Your contact message have been send.');
            }

            return Redirect::back()
                ->with('error', 'Sorry your contact message could not been send, please try again');
        } else {
            return Redirect::back()->withErrors($v->messages())->withInput();
        }
    }

    /**
     * Render logfiles content in view.
     *
     * @permission view_log
     * @return mixed
     */
    public function log()
    {
        $logs = new Collection();
        $count = 0;
        foreach (LogViewer::getFiles() as $file) {
            LogViewer::setFile($file);

            foreach (LogViewer::all() as $item) {
                $logs->add($item);
            }

            $count++;
            if ($count >= 2) {
                break;
            }
        }

        $logs = $this->createPaginator($logs);

        return View::make('log')
            ->with('title', 'Log')
            ->with('logs', $logs['data'])
            ->with('paginator', $logs['paginator']);
    }

    public function pusher()
    {
        try {
            $user = Jwt::decode($this->request->headers->get('X-Auth-Token'));

            if (Str::contains($_POST['channel_name'], 'presence-')) {
                return $this->client->pusher->presence_auth(
                    $_POST['channel_name'],
                    $_POST['socket_id'],
                    $user->id,
                    ['id' => $user->id]
                );
            }

            return $this->client->pusher->socket_auth(
                $_POST['channel_name'],
                $_POST['socket_id'],
                ['id' => $user->id]
            );
        } catch (\Exception $e) {
            return response('Forbidden', 403);
        }
    }
}
