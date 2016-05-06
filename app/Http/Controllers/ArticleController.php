<?php namespace App\Http\Controllers;

use App\Repositories\Article\ArticleRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

/**
 * Class ArticleController
 * @package App\Http\Controllers
 */
class ArticleController extends Controller
{

    /**
     * Constructor for ArticleController.
     *
     * @param ArticleRepository $Article
     */
    public function __construct(ArticleRepository $Article)
    {
        parent::__construct();
        $this->Article = $Article;
    }

    /**
     * Shows index view for article.
     * @param  int $id id for article to show.
     * @return objekt     with value where user should be redirected to.
     */
    public function index($id = null)
    {
        $Article = null;

        if ($id != null) {
            $Articles = array($this->Article->get($id));
            $Article = $Articles[0];
        } else {
            $Articles = $this->Article->get();
            $articlesArray = [];
            $i = 0;
            foreach ($Articles as $art) {
                if ($i < 11) {
                    $articlesArray[] = $art;
                    $i++;
                } else {
                    break;
                }
            }
            $Articles = $articlesArray;
        }

        return View::make('article.index')->with('title', 'Articles')->with('articles', $Articles)->with('article',
            $Article);
    }

    /**
     * Creates an article.
     * @permission create_article
     * @return object    with value where user should be redirected to.
     */
    public function create()
    {
        if ($this->Article->createOrUpdate($this->request->all())) {
            return Redirect::action('ArticleController@index')->with('success', 'Your article has been created.');
        }

        return Redirect::back()->withErrors($this->Article->getErrors())->withInput();
    }

    /**
     * Updates an article.
     * @permission update_article
     * @param  int $id id for article to update.
     * @return object     with value where user should be redirected to.
     */
    public function update($id)
    {
        if ($this->Article->createOrUpdate($this->request->all(), $id)) {
            return Redirect::action('ArticleController@index')->with('success', 'Your article has been updated.');
        }

        return Redirect::back()->withErrors($this->Article->getErrors())->withInput();
    }

    /**
     * Deletes an article.
     * @permission delete_article
     * @param  int $id id for article to delete.
     * @return object     with value where user should be redirected to.
     */
    public function delete($id)
    {
        $article = $this->Article->get($id);
        if ($this->Article->delete($id)) {
            if (str_contains(URL::previous(), $id) || str_contains(URL::previous(), $article->slug)) {
                return Redirect::action('ArticleController@index')->with('success', 'The Article has been deleted.');
            }
            return Redirect::back()->with('success', 'The Article has been deleted.');
        }
        return Redirect::back()->with('error', 'The Article could not be deleted.');
    }
}
