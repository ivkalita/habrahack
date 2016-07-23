<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 17:31.
 */
namespace UserBundle\Controller\API;

use AppBundle\Classes\Payload;
use AppBundle\Controller\BaseAPIController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Form\Type\ConfirmationType;
use UserBundle\Form\Type\PhoneChangeConfirmationType;
use UserBundle\Form\Type\PhoneType;
use UserBundle\Model\Phone as PhoneModel;

/**
 * Class AuthController.
 */
class AuthController extends BaseAPIController implements ClassResourceInterface
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Post("/auth/request")
     */
    public function postAuthRequestAction(Request $request)
    {
        /**
         * @var PhoneModel $phoneModel
         */
        $phoneModel = $this->handleJSONForm($request, $this->createForm(new PhoneType()));
        $payload = $this->get('user.handler.auth')->request($phoneModel->getPhone());

        return $this->response($payload);
    }

    /**
     * @param Request $request
     *
     * @throws \AppBundle\Exceptions\FormInvalidException
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     *
     * @Post("/auth/confirm")
     */
    public function postAuthConfirmAction(Request $request)
    {
        $confirmation = $this->handleJSONForm($request, $this->createForm(new ConfirmationType()));
        $payload = $this->get('user.handler.auth')->confirm($confirmation);

        return $this->response($payload);
    }

    /**
     * @Post("/logout")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postLogoutAction()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $this->get('user.manager.access_token')->deactivateToken($user->getRequestToken());

        return $this->response(Payload::create());
    }

    /**
     * @Post("/change-phone/request")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postPhoneChangeRequestAction(Request $request)
    {
        /**
         * @var PhoneModel $phoneModel
         */
        $phoneModel = $this->handleJSONForm($request, $this->createForm(PhoneType::class, null, ['phone_name' => 'new_phone']));
        $changePhoneRequest = $this->get('user.handler.auth')->changePhoneRequest($this->getUser(), $phoneModel);

        return $this->response(Payload::create(['secret' => $changePhoneRequest->getSecret()]));
    }

    /**
     * @Post("/change-phone/confirm")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postPhoneChangeConfirmAction(Request $request)
    {
        $phoneChangeConfirmation = $this->handleJSONForm($request, $this->createForm(PhoneChangeConfirmationType::class));
        $accessToken = $this->get('user.handler.auth')->confirmPhoneChangeRequest($this->getUser(), $phoneChangeConfirmation);

        return $this->response(Payload::create(['access_token' => $accessToken->getToken()]));
    }
}
