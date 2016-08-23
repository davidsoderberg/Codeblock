<?php namespace App\Http\Controllers;

use App\Models\NotificationType;
use App\Repositories\Post\PostRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Star\StarRepository;
use App\Repositories\Tag\TagRepository;
use App\Repositories\Rate\RateRepository;
use App\Services\Github;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Venturecraft\Revisionable\Revision;
use Illuminate\Support\Facades\Lang;
use App\Services\Analytics;

/**
 * Class PostController
 * @package App\Http\Controllers
 */
class PostController extends Controller
{

    /**
     * Constructor for PostController.
     * Setter for alot of repository properties.
     *
     * @param PostRepository $post
     * @param CategoryRepository $category
     * @param TagRepository $tag
     * @param RateRepository $rate
     */
    public function __construct(
        PostRepository $post,
        CategoryRepository $category,
        TagRepository $tag,
        RateRepository $rate
    ) {
        parent::__construct();
        $this->post = $post;
        $this->category = $category;
        $this->tag = $tag;
        $this->tags = $this->tag->get();
        $this->categories = $this->category->get();
        $this->rate = $rate;
    }

    /**
     * Render index view for posts.
     *
     * @permission view_posts
     * @return objekt objekt med allt som skall skickas till index vyn
     */
    public function index()
    {
        return View::make('post.index')->with('title', 'Posts')->with('posts', $this->post->get());
    }


    /**
     * Render embed view for choosen post.
     *
     * @param $id
     *
     * @return mixed
     */
    public function embed($id)
    {
        $post = $this->post->get($id);
        if ($post->private != 1) {
            return View::make('post.embed')->with('post', $post);
        } else {
            return View::make('errors.404')->with('title', '404')->with('post', $post);
        }
    }


    /**
     * Render choosen post view.
     *
     * @param $id
     * @param null $comment_id
     * @permission view_private_post:optional
     *
     * @return mixed
     */
    public function show($id, $comment_id = null)
    {
        $post = $this->post->get($id);
        if ($post->private != 1) {
            $comment = null;
            foreach ($post->comments as $CurrentComment) {
                if ($CurrentComment->id == $comment_id) {
                    $comment = $CurrentComment;
                }
            }

            return View::make('post.show')
                ->with('title', 'Codeblock: ' . $post->name)
                ->with('post', $post)
                ->with('rate', $this->rate)
                ->with('commentToEdit', $comment);
        } else {
            if (Auth::check()) {
                if (!empty($post->comments[0])) {
                    if ($post->private != 1) {
                        $post->comments = usort($post->comments->toArray(), function ($a, $b) {
                            return strcmp($this->rate->calc($a['id']), $this->rate->calc($b['id']));
                        });
                    }
                }
                if (Auth::user()->id == $post->user_id || Auth::user()
                        ->hasPermission($this->getPermission(), false)
                ) {
                    return View::make('post.show')->with('title', 'show')->with('post', $post);
                } else {
                    return Redirect::back()->with('error', 'You have no access to that codeblock.');
                }
            } else {
                return Redirect::back()->with('error', 'You have no access to that codeblock.');
            }
        }
    }

    /**
     * Undo choosen post action.
     *
     * @param $id
     *
     * @return mixed
     */
    public function undo($id)
    {
        $revision = Revision::find($id);
        if (!is_null($revision)) {
            $post = $this->post->get($revision->revisionable_id);
            if (!is_null($post)) {
                if (Auth::user()->id == $post->user_id || Auth::user()
                        ->hasPermission($this->getPermission(), false)
                ) {
                    $input = [$revision->fieldName() => $revision->oldValue()];
                    if ($this->post->undo($input, $revision->revisionable_id)) {
                        //$revision->delete($id);
                        return Redirect::back()->with('success', 'That change have been undone.');
                    }
                }
            }
        }

        return Redirect::back()->with('error', 'We could not undo that change.');
    }


    /**
     * Creates a post.
     *
     * @param Github $github
     *
     * @return mixed
     */
    public function create(Github $github)
    {
        return View::make('post.create')
            ->with('title', 'create')
            ->with('post', null)
            ->with('tags', $this->selectTags())
            ->with('categories', $this->selectCategories())
            ->with('teams', $this->selectTeams())
            ->with('hasRequest', $github->hasRequestLeft());
    }

    /**
     * Creates or updates a post.
     *
     * @param  int $id
     *
     * @return object
     */
    public function createOrUpdate($id = null)
    {
        if ($this->post->createOrUpdate($this->request->all(), $id)) {
            if (!is_null($id)) {
                return Redirect::to('posts/' . $id)->with('success', 'Your block has been saved.');
            } else {
                return Redirect::to('posts/' . $this->post->getId())
                    ->with('success', 'Your block has been created.');
            }
        }

        return Redirect::back()->withErrors($this->post->getErrors())->withInput();
    }


    /**
     * Render edit view for post.
     *
     * @param $id
     * @permission admin_edit_post:optional
     *
     * @return mixed
     */
    public function edit($id)
    {
        $post = $this->post->get($id);
        if (Auth::user()->id != $post->user_id && !Auth::user()->hasPermission($this->getPermission(), false)) {
            return Redirect::back()->with('error', 'That codeblock is not yours.');
        }
        $tagsarray = [];
        foreach ($post->tags as $tag) {
            $tagsarray[] = $tag->id;
        }
        $post->tags = $tagsarray;

        return View::make('post.edit')
            ->with('title', 'Edit')
            ->with('post', $post)
            ->with('tags', $this->selectTags())
            ->with('categories', $this->selectCategories())
            ->with('teams', $this->selectTeams());
    }

    /**
     * Creats an array with id as key and category name as value.
     *
     * @return array
     */
    private function selectCategories()
    {
        $selectArray = [];
        $selectArray[''] = 'Codeblock category';
        foreach ($this->categories as $category) {
            $selectArray[$category->id] = $category->name;
        }

        return $selectArray;
    }

    /**
     * Creats an array with id as key and team name as value.
     *
     * @return array
     */
    private function selectTeams()
    {
        $selectArray = [];
        $selectArray[0] = 'Select Team';
        $teams = Auth::user()->teams->merge(Auth::user()->ownedTeams);
        foreach ($teams as $team) {
            $selectArray[$team->id] = $team->name;
        }

        return $selectArray;
    }

    /**
     * Creats an array with id as key and tag name as value.
     *
     * @return array
     */
    private function selectTags()
    {
        $selectArray = [];
        foreach ($this->tags as $tag) {
            $selectArray[$tag->id] = $tag->name;
        }

        return $selectArray;
    }


    /**
     * Deletes a post.
     *
     * @param $id
     * @permission delete_post:optional
     *
     * @return mixed
     */
    public function delete($id)
    {
        $post = $this->post->get($id);
        if (!is_null($post)) {
            if (Auth::check() && Auth::user()->id == $post->user_id || Auth::user()
                    ->hasPermission($this->getPermission(), false)
            ) {
                if ($this->post->delete($id)) {
                    if (str_contains(URL::previous(), $id) || str_contains(URL::previous(), $post->slug)) {
                        $redirect = Redirect::to('user');
                    } else {
                        $redirect = Redirect::back();
                    }

                    return $redirect->with('success', 'Your codeblock has been deleted.');
                }
            } else {
                return Redirect::back()->with('error', 'You do not have permission to delete that codeblock.');
            }
        }

        return Redirect::back()->with('error', 'We could not delete that codeblock.');
    }


    /**
     * Render a list with all posts.
     *
     * @return mixed
     */
    public function listPosts()
    {
        return View::make('post.list')->with('title', 'All Codeblocks')->with('posts', $this->post->get()
            ->reverse());
    }

    /**
     * Render searchresult view.
     *
     * @return mixed
     */
    public function search()
    {
        $categories = $this->selectCategories();
        $categories[''] = "All categories";
        $tags = $this->selectTags();
        $tags[''] = "All tags";
        $term = trim(strip_tags($this->request->get('term')));
        $filter = [
            'category' => $this->request->get('category'),
            'tag' => $this->request->get('tag'),
            'only' => $this->request->get('only'),
        ];
        $posts = $this->post->search($term, $filter);

        return View::make('post.list')
            ->with('title', 'Search on: ' . $term)
            ->with('posts', $posts->reverse())
            ->with('term', $term)
            ->with('filter', $filter)
            ->with('categories', $categories)
            ->with('tags', $tags);
    }

    /**
     * Fetch posts bu logged in user only.
     *
     * @param $posts
     *
     * @return Collection
     */
    private function only($posts)
    {
        if (Auth::check()) {
            if (Auth::user()->showOnly() && $posts instanceof Collection) {
                $collection = new Collection();
                foreach ($posts as $post) {
                    if ($post->user_id == Auth::user()->id) {
                        $collection->add($post);
                    }
                }

                return $collection;
            }
        }

        return $posts;
    }

    /**
     * Render list of posts with choosen category.
     *
     * @param $id
     * @param string $sort
     *
     * @return mixed
     */
    public function category($id, $sort = 'date')
    {
        $id = urldecode($id);
        if ($id == str_slug(Lang::get('app.WhatsNew'))) {
            $this->category->name = Lang::get('app.WhatsNew');
            $posts = $this->post->getNewest();
            $category = $this->category;
        } elseif ($id == str_slug(Lang::get('app.MostPopular'))) {
            $this->category->name = Lang::get('app.MostPopular');
            $posts = $this->post->getPopular();
            $category = $this->category;
        } else {
            $category = $this->category->get($id);
            $posts = $this->post->getByCategory($category->id);
            $posts = $this->only($posts);
        }
        if ($sort != '') {
            $posts = $this->post->sort($posts, $sort);
        }

        $paginator = $this->createPaginator($posts);
        $posts = $paginator['data'];
        $paginator = $paginator['paginator'];

        return View::make('post.list')
            ->with('title', 'Posts in category: ' . $category->name)
            ->with('posts', $posts)
            ->with('category', $category)
            ->with('paginator', $paginator);
    }

    /**
     * Render list of posts with choosen tag.
     *
     * @param $id
     * @param string $sort
     *
     * @return mixed
     */
    public function tag($id, $sort = 'date')
    {
        $id = urldecode($id);
        $tag = $this->tag->get($id);
        $id = $tag->id;
        $posts = $this->post->getByTag($id);
        $posts = $this->only($posts);
        if ($sort != '') {
            $posts = $this->post->sort($posts, $sort);
        }

        $paginator = $this->createPaginator($posts);
        $posts = $paginator['data'];
        $paginator = $paginator['paginator'];

        return View::make('post.list')
            ->with('title', 'Posts with tag: ' . $tag->name)
            ->with('posts', $posts)
            ->with('tag', $tag)
            ->with('paginator', $paginator);
    }


    /**
     * Adds a star to choosen post.
     *
     * @param StarRepository $starRepository
     * @param $id
     *
     * @return mixed
     */
    public function star(StarRepository $starRepository, $id)
    {
        $star = $this->post->createOrDeleteStar($starRepository, $id);
        if ($star[0]) {
            if ($star[1] == 'create') {
                $post = $this->post->get($id);
                $this->send_notification($post->user_id, NotificationType::STAR, $post);

                return Redirect::back()->with('success', 'You have now add a star to this codblock.');
            }

            return Redirect::back()->with('success', 'You have now removed a star from this codblock.');
        }

        return Redirect::back()->with('error', 'Something went wrong, please try again.');
    }

    /**
     * Forks a post.
     *
     * @param $id
     *
     * @return mixed
     */
    public function fork($id)
    {
        if ($this->post->duplicate($id)) {
            $forkedPost = $this->post->get($id);
            Analytics::track(Analytics::CATEGORY_INTERNAL, Analytics::ACTION_FORK, [
                'forked_from' => $forkedPost->name,
                'fork_id' => $this->post->getId(),
            ]);

            return Redirect::to('/posts/edit/' . $this->post->getId())
                ->with('success', 'Your have forked a block and can now edit.');
        }

        return Redirect::back()->with('error', 'We could not fork this codeblock right now, please try again.');
    }

    /**
     * Render a list with all forks from choosen post.
     *
     * @param $id
     *
     * @return mixed
     */
    public function forked($id)
    {
        $post = $this->post->get($id);

        return View::make('post.list')
            ->with('title', 'Forked codeblock from: ' . $post->name)
            ->with('posts', $this->post->getForked($id));
    }

    /**
     * Creates a post from selected gist from github.
     *
     * @param Github $github
     *
     * @return mixed
     */
    public function forkGist(Github $github)
    {
        if ($github->hasRequestLeft()) {
            $id = $this->request->get('id');
            if (is_numeric($id)) {
                $data = $github->getGist($id);
                if ($data) {
                    $category = strtolower($data['language']);
                    $category_Id = 1;

                    foreach ($this->selectCategories() as $key => $value) {
                        if ($category == strtolower($value)) {
                            $category_Id = $key;
                            break;
                        }
                    }

                    $data = [
                        'name' => $data['filename'],
                        'description' => 'A forked <a href="https://api.github.com/gists/' . $id . '" target="_blank">gist</a>',
                        'cat_id' => $category_Id,
                        'code' => $data['content'],
                    ];

                    if ($this->post->createOrUpdate($data)) {
                        Analytics::track(Analytics::CATEGORY_SOCIAL, Analytics::ACTION_FORK, [
                            'Github_id' => $id,
                            'fork_id' => $this->post->getId(),
                            'name' => $data['name'],
                        ]);

                        return Redirect::to('/posts/' . $this->post->getId())
                            ->with('success',
                                'The requested <a href="https://gist.github.com/' . $id . '" target="_blank">gist</a> have been forked.');
                    }
                }
            }
            Analytics::track(Analytics::CATEGORY_ERROR, Analytics::ACTION_FORK, [
                'Github_id' => $id,
                'username' => Auth::user()->username,
            ]);

            return Redirect::back()
                ->with('error',
                    'The requested <a href="https://gist.github.com/' . $id . '" target="_blank">gist</a> could not be forked.');
        }
        Analytics::track(Analytics::CATEGORY_ERROR, Analytics::ACTION_FORK, 'Github');

        return Redirect::back()
            ->with('error', 'Sorry right now we not have any api request left please try agian later.');
    }
}
