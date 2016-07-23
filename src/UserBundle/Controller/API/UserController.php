<?php

namespace UserBundle\Controller\API;

use AppBundle\Classes\Payload;
use AppBundle\Controller\BaseAPIController;
use AppBundle\Exceptions\PermissionDeniedException;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Feedback;
use UserBundle\Entity\User;
use UserBundle\Form\Type\FeedbackType;
use UserBundle\Form\Type\PlayerIdType;
use UserBundle\Form\Type\UserType;
use UserBundle\Model\PlayerId;

class UserController extends BaseAPIController implements ClassResourceInterface
{
    /**
     * @param $slug
     *
     * @throws PermissionDeniedException
     *
     * @return Response
     */
    public function getAction($slug)
    {
        $this->assertSelfSlug($slug);
        $user = $this->getUser();

        return $this->response(Payload::create($user), [User::INFO_CARD]);
    }

    /**
     * @param $slug
     * @param Request $request
     *
     * @throws PermissionDeniedException
     * @throws \AppBundle\Exceptions\FormInvalidException
     *
     * @return Response
     */
    public function putAction($slug, Request $request)
    {
        $this->assertSelfSlug($slug);
        $newInfo = $this->handleJSONForm($request, $this->createForm(new UserType()));
        $user = $this->getUser();

        $payload = $this->get('user.handler.user')->update($user, $newInfo);

        return $this->response($payload, [User::INFO_CARD]);
    }

    /**
     * @param $slug
     *
     * @Post("/users/{slug}/chip/lock")
     *
     * @return Response
     */
    public function postLockChipAction($slug)
    {
        $this->assertSelfSlug($slug);
        $this->get('admin.handler.user')->deactivateChip($this->getUser());

        return $this->response(Payload::create());
    }

    /**
     * @param Request $request
     * @param $slug
     *
     * @Post("/users/{slug}/feedback")
     *
     * @return Response
     */
    public function postFeedbackAction(Request $request, $slug)
    {
        $this->assertSelfSlug($slug);
        /**
         * @var Feedback $feedback
         */
        $feedback = $this->handleJSONForm($request, $this->createForm(FeedbackType::class));
        $feedback->setUser($this->getUser());
        $this->get('user.manager.feedback')->save($feedback);

        return $this->response(Payload::create());
    }

    /**
     * @param Request $request
     * @param $slug
     *
     * @Post("/users/{slug}/players")
     *
     * @return Response
     */
    public function postPlayerAction(Request $request, $slug)
    {
        $this->assertSelfSlug($slug);
        /**
         * @var PlayerId $playerIdModel
         */
        $playerIdModel = $this->handleJSONForm($request, $this->createForm(PlayerIdType::class));
        $this->get('user.handler.user')->addPlayerId($playerIdModel, $this->getUser());

        return $this->response(Payload::create());
    }
}
