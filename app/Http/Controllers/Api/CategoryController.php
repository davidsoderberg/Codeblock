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
     *
     * @ApiDescription(section="Category", description="Get all or one category")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/api/v1/categories/{id?}")
     * @ApiParams(name="id", type="integer", nullable=true, description="category id")
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
    private function createOrUpdateCategory(CategoryRepository $category, $id = null)
    {
        if ($category->createOrUpdate($this->request->all(), $id)) {
            return $this->response([$this->stringMessage => 'Your category has been saved'], 201);
        }

        return $this->response([$this->stringErrors => $category->getErrors()], 400);
    }

    /**
     * Creating a category.
     *
     * @permission create_update_categories
     *
     * @param CategoryRepository $category
     *
     * @return mixed
     *
     * @ApiDescription(section="Category", description="Create category")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/categories")
     * @ApiParams(name="name", type="string", nullable=false, description="category name")
     */
    public function createCategory(CategoryRepository $category)
    {
        return $this->createOrUpdateCategory($category);
    }

    /**
     * Updating a category.
     *
     * @permission create_update_categories
     *
     * @param CategoryRepository $category
     * @param null               $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Category", description="Update category")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/api/v1/categories/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="category id")
     * @ApiParams(name="name", type="string", nullable=false, description="category name")
     */
    public function updateCategory(CategoryRepository $category, $id)
    {
        return $this->createOrUpdateCategory($category, $id);
    }


    /**
     * Deletes a category.
     *
     * @param CategoryRepository $categoryRepository
     * @param $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Category", description="Delete category")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/api/v1/categories/{id}")
     * @ApiParams(name="id", type="integer", nullable=false, description="category id")
     */
    public function deleteCategory(CategoryRepository $categoryRepository, $id)
    {
        if ($categoryRepository->delete($id)) {
            return $this->response([$this->stringMessage => 'The category has been deleted.'], 200);
        }

        return $this->response([$this->stringErrors => 'The category could not be deleted.'], 204);
    }
}
