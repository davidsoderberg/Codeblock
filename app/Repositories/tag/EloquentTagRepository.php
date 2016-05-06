<?php namespace App\Repositories\Tag;

use App\Models\Tag;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentTagRepository
 * @package App\Repositories\Tag
 */
class EloquentTagRepository extends CRepository implements TagRepository
{

    /**
     * Fetch one or all tags.
     *
     * @param null $id
     *
     * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
     */
    public function get($id = null)
    {
        if (is_null($id)) {
            return $this->cache('all', Tag::where('id', '!=', 0));
        } else {
            if (is_numeric($id)) {
                return CollectionService::filter($this->get(), 'id', $id, 'first');
            } else {
                return CollectionService::filter($this->get(), 'name', $id, 'first');
            }
        }
    }

    /**
     * Creates or updates a tag.
     *
     * @param $input
     * @param null $id
     *
     * @return bool
     */
    public function createOrUpdate($input, $id = null)
    {
        if (!is_numeric($id)) {
            $Tag = new Tag;
        } else {
            $Tag = $this->get($id);
        }

        if (isset($input['name'])) {
            $Tag->name = $this->stripTrim($input['name']);
        }


        if ($Tag->save()) {
            return true;
        } else {
            $this->errors = Tag::$errors;
            return false;
        }
    }

    /**
     * Deletes a tag.
     *
     * @param $id
     *
     * @return bool|mixed
     */
    public function delete($id)
    {
        $Tag = $this->get($id);
        if ($Tag == null) {
            return false;
        }
        if (!empty($Tag->posts[0])) {
            $Tag->posts->detach();
        }
        return $Tag->delete();
    }
}
