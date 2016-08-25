<?php namespace App\Http\Controllers;

use App\Repositories\Category\CategoryRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{

    /**
     * Constructor for CategoryController.
     *
     * @param CategoryRepository $category
     */
    public function __construct(CategoryRepository $category)
    {
        parent::__construct();
        $this->category = $category;
    }

    /**
     * Render index view for categories.
     * @permission view_categories
     * @param  int $id id
     * @return object
     */
    public function index($id = null)
    {
        $category = null;

        if (is_numeric($id)) {
            $category = $this->category->get($id);
        }

        return View::make('category.index')->with('title', 'Categories')->with('categories',
            $this->category->get())->with('category', $category);
    }

    /**
     * Creates or updates a category.
     * @permission create_update_categories
     * @param  int $id
     * @return object
     */
    public function createOrUpdate($id = null)
    {
        if ($this->category->createOrUpdate(Input::all(), $id)) {
            if (is_null($id)) {
                return Redirect::to('categories')->with('success', 'Your category has been created.');
            }
            return Redirect::to('categories')->with('success', 'Your category has been updated.');
        }

        return Redirect::back()->withErrors($this->category->getErrors())->withInput();
    }

    /**
     * Deletes a category
     * @permission delete_categories
     * @param  int $id
     * @return object
     */
    public function delete($id)
    {
        if ($this->category->delete($id)) {
            return Redirect::to('categories')->with('success', 'The category has been deleted.');
        }

        return Redirect::back()->with('error', 'The category could not be deleted.');
    }
}
