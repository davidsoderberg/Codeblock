<?php namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Star\StarRepository;
use App\Models\Star;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentPostRepository
 * @package App\Repositories\Post
 */
class EloquentPostRepository extends CRepository implements PostRepository
{

    /**
     * Property to store current id in.
     *
     * @var
     */
    public $id;

    /**
     * Getter for id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Fetch one or all posts.
     *
     * @param null $id
     *
     * @return \App\Services\Model|array|Collection|null|static
     */
    public function get($id = null)
    {
        if (is_null($id)) {
            $posts = $this->cache('all', Post::where('id', '!=', 0));
        } else {
            if (is_numeric($id)) {
                $post = CollectionService::filter($this->get(), 'id', $id, 'first');
            } else {
                $post = CollectionService::filter($this->get(), 'slug', $id, 'first');
            }
            $posts = $post;
        }

        return $posts;
    }

    /**
     * Fetch all posts in selected category.
     *
     * @param $id
     *
     * @return Collection
     */
    public function getByCategory($id)
    {
        $posts = $this->get();
        $postsCollection = new Collection();
        foreach ($posts as $post) {
            if ($id != 0) {
                if (isset($post->category)) {
                    if ($post->category->id == $id) {
                        if ($post->private != 1) {
                            $postsCollection->add($post);
                        } else {
                            if (Auth::check()) {
                                if (Auth::user()->id == $post->user_id) {
                                    $postsCollection->add($post);
                                }
                            }
                        }
                    }
                }
            } else {
                // Creates an carbon object with correct timezone.
                $now = Carbon::now();
                $now->timezone = 'Europe/Stockholm';
                // Creates a timestamp for now.
                $nowTimestamp = strtotime($now);
                // Creates a timestamp for a week ago.
                $weekAgoTimestamp = strtotime($now->subWeek());
                // Checks if post created at timestamp is in beteewen now and a week ago.
                if (strtotime($post->created_at) >= $weekAgoTimestamp && strtotime($post->created_at) <= $nowTimestamp) {
                    if ($post->private != 1) {
                        $postsCollection->add($post);
                    }
                }
            }
        }

        return $postsCollection;
    }

    /**
     * Fetch popular posts.
     *
     * @param int $limit
     * @param int $min
     *
     * @return Collection
     */
    public function getPopular($limit = 10, $min = 0)
    {
        $posts = $this->sort($this->get()->take($limit), 'stars');
        $postsCollection = new Collection();
        foreach ($posts as $post) {
            if ($post->starcount > $min) {
                $postsCollection->add($post);
            }
        }

        return $postsCollection;
    }

    /**
     * Fetch newest posts.
     *
     * @param int $limit
     *
     * @return Collection
     */
    public function getNewest($limit = 10)
    {
        $posts = $this->get();
        $postsCollection = new Collection();
        foreach ($posts as $post) {
            // Creates a cabon object with correct timezone.
            $now = Carbon::now();
            $now->timezone = 'Europe/Stockholm';
            // Creates a timestamp with now.
            $nowTimestamp = strtotime($now);
            // Creates a timestamp with a week ago.
            $weekAgoTimestamp = strtotime($now->subWeek());
            // Checks if post created at timestamp is beteewen now and a week ago.
            if (strtotime($post->created_at) >= $weekAgoTimestamp && strtotime($post->created_at) <= $nowTimestamp) {
                if ($post->private != 1) {
                    $postsCollection->add($post);
                }
            }
            if ($postsCollection->count() == 10) {
                break;
            }
        }

        return $postsCollection;
    }

    /**
     * Fetch all posts with same tag.
     *
     * @param $id
     *
     * @return Collection
     */
    public function getByTag($id)
    {
        $posts = $this->get();
        $postsCollection = new Collection();
        foreach ($posts as $post) {
            foreach ($post->tags as $tag) {
                if ($id == $tag->id) {
                    if ($post->private != 1) {
                        $postsCollection->add($post);
                    }
                    break;
                }
            }
        }

        return $postsCollection;
    }

    /**
     * Sort posts.
     *
     * @param $posts
     * @param string $sort
     *
     * @return mixed
     */
    public function sort($posts, $sort = "date")
    {
        $sort = strtolower($sort);
        $posts = $posts->sortByDesc(function ($item) use ($sort) {
            switch ($sort) {
                case 'stars':
                    return $item->starcount;
                    break;
                case 'comments':
                    return count($item->comments);
                    break;
                case 'name':
                    return $item->name;
                    break;
                case 'category':
                    return $item->category->name;
                    break;
                default:
                    return $item->created_at;
                    break;
            }
        });

        if (in_array($sort, ['name', 'category'])) {
            $posts = $posts->reverse();
        }

        return $posts;
    }

    /**
     * Fork a post.
     *
     * @param $id
     *
     * @return bool|mixed
     */
    public function duplicate($id)
    {
        $post = $this->get($id);
        $input = [];
        $input['tags'] = [];
        foreach ($post->tags as $tag) {
            $input['tags'][] = $tag->id;
        }
        $existingPost = CollectionService::filter($this->get(), 'name', $post->name . ' ' . Auth::user()->id);
        if (count($existingPost) < 1) {
            $input['name'] = $post->name . ' ' . Auth::user()->id;
            $input['cat_id'] = $post->cat_id;
            $input['description'] = $post->description;
            $input['code'] = html_entity_decode($post->code);
            $input['private'] = 1;
            $input['org'] = $post->id;

            return $this->createOrUpdate($input);
        }

        return false;
    }

    /**
     * Fetch original post selected post has been forked from.
     *
     * @param $id
     *
     * @return Collection|static
     */
    public function getForked($id)
    {
        return CollectionService::filter($this->get(), 'org', $id);
    }

    /**
     * Undo action on post.
     *
     * @param $input
     * @param $id
     *
     * @return bool
     */
    public function undo($input, $id)
    {
        if (is_numeric($id)) {
            $post = $this->get($id);
            //$post->setRevisionEnabled();
            if (isset($input['code'])) {
                $input['code'] = html_entity_decode($input['code']);
            }
            $return = $this->save($input, $post);

            //$post->setRevisionEnabled();
            return $return;
        }

        return false;
    }

    /**
     * Saves post.
     *
     * @param $input
     * @param $Post
     *
     * @return bool
     */
    private function save($input, $Post)
    {
        $except = ['tags', '_token', '_url', 'token', '_method', 'honeyName'];

        foreach ($input as $key => $value) {
            if (!in_array($key, $except)) {
                if ($key != 'code') {
                    $Post[$key] = $this->stripTrim($input[$key]);
                } else {
                    $Post[$key] = htmlentities($input[$key]);
                }
            }
        }

        if ($Post->slug == '') {
            $Post['slug'] = $Post->getSlug($Post->name);
        }

        if ($Post->save()) {
            $this->id = $Post->id;
            if (isset($input['tags'])) {
                $Post->tags()->sync($input['tags']);
            } else {
                if (count($Post->tags) > 0) {
                    $Post->tags()->sync([]);
                }
            }

            return true;
        } else {
            $this->errors = $Post::$errors;

            return false;
        }
    }

    /**
     * Creates or updates post.
     *
     * @param $input
     * @param null $id
     *
     * @return bool
     */
    public function createOrUpdate($input, $id = null)
    {
        if (!is_numeric($id)) {
            $Post = new Post;
            if (is_object(Auth::user())) {
                $Post->user_id = Auth::user()->id;
            } else {
                Session::flash('error', 'You have not logged in');

                return false;
            }
        } else {
            $Post = $this->get($id);
        }

        return $this->save($input, $Post);
    }

    /**
     * Deletes a post.
     *
     * @param $id
     *
     * @return bool|mixed
     */
    public function delete($id)
    {
        $Post = $this->get($id);
        if (!is_null($Post)) {
            return $Post->delete();
        }

        return false;
    }

    /**
     * Adds or removes a star from post.
     *
     * @param StarRepository $starRepository
     * @param $post_id
     *
     * @return array
     */
    public function createOrDeleteStar(StarRepository $starRepository, $post_id)
    {
        $stars = CollectionService::filter($starRepository->get(), 'user_id', Auth::user()->id);
        $star = CollectionService::filter($stars, 'post_id', $post_id, 'first');
        $boolean = false;
        $action = 'delete';
        if ($star != null) {
            $boolean = $star->delete();
        } else {
            $action = 'create';
            $star = new Star;
            $star->post_id = $post_id;
            $star->user_id = Auth::user()->id;
            $boolean = $star->save();
        }

        return [$boolean, $action];
    }

    /**
     * Search and returns posts that is matching search query.
     *
     * @param $term
     * @param array $filter
     *
     * @return static
     */
    public function search($term, $filter = ['tag' => null, 'category' => null, 'only' => 0])
    {

        // Checks if post contains search query.
        $posts = Post::where('name', 'LIKE', '%' . $term . '%')
            ->get()
            ->merge(Post::where('description', 'LIKE', '%' . $term . '%')->get());

        // Checks if posts has selected category name or tag name.
        foreach ($this->get() as $post) {
            if (strtolower($post->category->name) == strtolower($term) || strtolower($post->user->username) == strtolower($term)) {
                $posts->add($post);
                break;
            }
            foreach ($post->tags as $tag) {
                if (strtolower($tag->name) == strtolower($term)) {
                    $posts->add($post);
                    break;
                }
            }
        }

        if (!is_null($filter['category']) && $filter['category'] != '') {
            $category = $filter['category'];
            $posts = $posts->filter(function ($item) use ($category) {
                if ($item->category->id == $category) {
                    return $item;
                }
            });
        }

        if (!is_null($filter['tag']) && $filter['tag'] != '') {
            $tag = $filter['tag'];
            $posts = $posts->filter(function ($item) use ($tag) {
                foreach ($item->tags as $posttag) {
                    if ($posttag->id == $tag) {
                        return $item;
                    }
                }
            });
        }

        if (!is_null($filter['only']) && $filter['only'] != 0) {
            $posts = $posts->filter(function ($item) {
                if ($item->user_id == Auth::user()->id) {
                    return $item;
                }
            });
        }

        // Fetch all posts agian with all relations eagerloaded.
        $postCollection = new Collection();
        foreach ($posts as $post) {
            if ($post['private'] != 1) {
                $postCollection->add($this->get($post['id']));
            }
        }

        return $postCollection->unique();
    }
}
