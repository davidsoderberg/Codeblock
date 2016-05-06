<?php namespace App\Repositories\Article;

use App\Models\Article;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentArticleRepository
 * @package App\Repositories\Article
 */
class EloquentArticleRepository extends CRepository implements ArticleRepository
{

    /**
     * Fetch on or all articles.
     * @param null $id
     *
     * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
     */
    public function get($id = null)
    {
        if (is_null($id)) {
            return $this->cache('all', Article::where('id', '!=', 0));
        } else {
            if (is_numeric($id)) {
                return CollectionService::filter($this->get(), 'id', $id, 'first');
            } else {
                return CollectionService::filter($this->get(), 'slug', $id, 'first');
            }
        }
    }

    /**
     * Creates or updates a article.
     *
     * @param $input
     * @param null $id
     *
     * @return bool
     */
    public function createOrUpdate($input, $id = null)
    {
        if (is_null($id)) {
            $Article = new Article();
        } else {
            $Article = $this->get($id);
        }

        if (isset($input['title'])) {
            $Article->title = $this->stripTrim($input['title']);
            $Article->slug = $Article->getSlug($Article->title);
        }

        if (isset($input['body'])) {
            $Article->body = $this->stripTrim($input['body']);
        }

        if ($Article->save()) {
            return true;
        } else {
            $this->errors = $Article::$errors;
            return false;
        }
    }

    /**
     * Delets a article.
     *
     * @param $id
     *
     * @return bool|mixed
     */
    public function delete($id)
    {
        $Article = $this->get($id);
        if ($Article != null) {
            return $Article->delete();
        }
        return false;
    }
}
