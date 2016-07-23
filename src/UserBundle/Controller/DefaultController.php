<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function authRequestAction(Request $request)
    {
        return new JsonResponse([
            'status' => 'Success',
            'message' => 'Успешно',
            'data' => [
                'secret' => 'somereallylongsecretyouneedtokeep',
            ],
        ], 200);
    }

    public function authConfirmAction(Request $request)
    {
        return new JsonResponse([
            'status' => 'Success',
            'message' => 'Успешно',
            'data' => [
                'access_token' => 'some-access-token-will-be-here',
                'is_new' => true,
            ],
        ], 200);
    }
}
