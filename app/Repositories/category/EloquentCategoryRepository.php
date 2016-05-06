<?php namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentCategoryRepository
 * @package App\Repositories\Category
 */
class EloquentCategoryRepository extends CRepository implements CategoryRepository
{

    /**
     * Fetch one or all categories.
     *
     * @param null $id
     *
     * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
     */
    public function get($id = null)
    {
        if (is_null($id)) {
            return $this->cache('all', Category::where('id', '!=', 0));
        } else {
            if (is_numeric($id)) {
                return CollectionService::filter($this->get(), 'id', $id, 'first');
            } else {
                return CollectionService::filter($this->get(), 'name', $id, 'first');
            }
        }
    }

    /**
     * Creats or updates a category.
     *
     * @param $input
     * @param null $id
     *
     * @return bool
     */
    public function createOrUpdate($input, $id = null)
    {
        if (!is_numeric($id)) {
            $Category = new Category;
        } else {
            $Category = $this->get($id);
        }

        if (isset($input['name'])) {
            $Category->name = $this->stripTrim($input['name']);
        }

        if ($Category->save()) {
            return true;
        } else {
            $this->errors = $Category::$errors;

            return false;
        }
    }

    /**
     * Deletes a category.
     *
     * @param $id
     *
     * @return bool|mixed
     */
    public function delete($id)
    {
        $Category = $this->get($id);
        if ($Category != null) {
            return $Category->delete();
        }

        return false;
    }
}
