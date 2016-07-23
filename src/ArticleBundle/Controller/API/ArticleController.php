<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 23.07.16
 * Time: 19:08.
 */
namespace ArticleBundle\Controller\API;

use AppBundle\Classes\Payload;
use AppBundle\Controller\BaseAPIController;
use AppBundle\Exceptions\NotFoundException;
use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\ArticleView;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends BaseAPIController implements ClassResourceInterface
{
    public function cgetAction(Request $request)
    {
        $count = $request->query->get('count', 10);
        $fromId = $request->query->get('fromId', null);
        $articles = $this->get('article.manager.article')->findOrderedResult($fromId, $count);

        return $this->response(Payload::create($articles), [Article::FULL_CARD]);
    }

    /**
     * @Post("article/{id}/view")
     */
    public function addViewAction($id)
    {
        /**
         * @var Article $article
         */
        $article = $this->get('article.manager.article')->find($id);
        if (!$article) {
            throw new NotFoundException();
        }
        $view = new ArticleView();
        $view->setArticle($article);
        $this->get('article.manager.article_view')->save($view);

        return $this->response(Payload::create());
    }
}
