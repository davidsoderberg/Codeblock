<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Category\CategoryRepository;

/**
 * Class CategoryController
 * @package App\Http\Controllers\Api
 */
class CategoryController extends ApiController
{

    /**
     * Shows a category.
     *
     * @param CategoryRepository $category
     * @param null $id
     *
     * @return mixed
     */
    public function Categories(CategoryRepository $category, $id = null)
    {
        return $this->response([$this->stringData => $this->getCollection($category, $id)], 200);
    }

    /**
     * Creating or updating a category.
     *
     * @permission create_update_categories
     *
     * @param CategoryRepository $category
     * @param null $id
     *
     * @return mixed
     */
    public function createOrUpdateCategory(CategoryRepository $category, $id = null)
    {
        if ($category->createOrUpdate($this->request->all(), $id)) {
            return $this->response([$this->stringMessage => 'Your category has been saved'], 201);
        }

        return $this->response([$this->stringErrors => $category->getErrors()], 400);
    }


    /**
     * Deletes a category.
     *
     * @param CategoryRepository $categoryRepository
     * @param $id
     *
     * @return mixed
     */
    public function deleteCategory(CategoryRepository $categoryRepository, $id)
    {
        if ($categoryRepository->delete($id)) {
            return $this->response([$this->stringMessage => 'The category has been deleted.'], 200);
        }

        return $this->response([$this->stringErrors => 'The category could not be deleted.'], 204);
    }
}
