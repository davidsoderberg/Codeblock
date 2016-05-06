<?php namespace App\Repositories\Forum;

use App\Models\Forum;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentForumRepository
 * @package App\Repositories\Forum
 */
class EloquentForumRepository extends CRepository implements ForumRepository
{

    /**
     * Fetch one or all forums.
     *
     * @param null $id
     *
     * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
     */
    public function get($id = null)
    {
        if (!is_null($id)) {
            return CollectionService::filter($this->get(), 'id', $id, 'first');
        } else {
            return $this->cache('all', Forum::where('id', '!=', 0));
        }
    }

    /**
     * Creates or updates a forum.
     *
     * @param $input
     * @param null $id
     *
     * @return bool
     */
    public function createOrUpdate($input, $id = null)
    {
        if (!is_numeric($id)) {
            $Forum = new Forum;
        } else {
            $Forum = $this->get($id);
        }

        if (isset($input['title'])) {
            $Forum->title = $this->stripTrim($input['title']);
        }

        if (isset($input['description'])) {
            $Forum->description = $this->stripTrim($input['description']);
        }

        if ($Forum->save()) {
            return true;
        } else {
            $this->errors = $Forum::$errors;
            return false;
        }
    }

    /**
     * Delete a forum.
     *
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $Forum = $this->get($id);
        if ($Forum == null) {
            return false;
        }
        return $Forum->delete();
    }
}
