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
use FOS\RestBundle\Routing\ClassResourceInterface;

class ArticleController extends BaseAPIController implements ClassResourceInterface
{
    public function cgetAction()
    {
        return $this->response(Payload::create([]));
    }
}
