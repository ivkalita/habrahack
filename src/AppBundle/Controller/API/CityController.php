<?php

namespace AppBundle\Controller\API;

use AppBundle\Classes\Payload;
use AppBundle\Controller\BaseAPIController;
use AppBundle\Entity\City;
use FOS\RestBundle\Routing\ClassResourceInterface;

class CityController extends BaseAPIController implements ClassResourceInterface
{
    public function cgetAction()
    {
        $data = $this->get('app.manager.city')->findAll();

        return $this->response(Payload::create($data), [City::LIST_CARD]);
    }
}
