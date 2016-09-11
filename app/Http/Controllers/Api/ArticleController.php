<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\Article\ArticleRepository;

/**
 * Class ArticleController
 * @package App\Http\Controllers\Api
 */
class ArticleController extends ApiController
{

    /**
     * Shows a article.
     *
     * @param ArticleRepository $article
     * @param null $id
     *
     * @return mixed
     *
     * @ApiDescription(section="Article", description="Get all articles")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/api/v1/articles/{id?}")
     * @ApiParams(name="id", type="integer", nullable=true, description="article id")
     */
    public function articles(ArticleRepository $article, $id = null)
    {
        return $this->response([$this->stringData => $this->getCollection($article, $id)], 200);
    }
}
