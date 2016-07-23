<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 12.02.16
 * Time: 11:01.
 */
namespace AppBundle\Controller;

use AppBundle\Classes\Payload;
use AppBundle\Exceptions\FormInvalidException;
use AppBundle\Exceptions\PermissionDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseAPIController.
 */
class BaseAPIController extends FOSRestController
{
    /**
     * @param Payload $payload
     * @param mixed   $serializerGroups
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(Payload $payload, $serializerGroups = null)
    {
        $serializerGroups = $serializerGroups ?: [];
        $serializerGroups[] = 'all';
        if (is_string($serializerGroups)) {
            $serializerGroups = [$serializerGroups];
        }

        $view = $this->view($payload->getForResponse(), $payload->getHttpCode());
        $view->getSerializationContext()
            ->setGroups($serializerGroups)
            ->setSerializeNull(true);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function getContentAsJson(Request $request)
    {
        $content = trim($request->getContent());

        return json_decode($content, true);
    }

    /**
     * @param Request $request
     * @param Form    $form
     *
     * @throws FormInvalidException
     *
     * @return mixed
     */
    public function handleJSONForm(Request $request, Form $form)
    {
        $content = $this->getContentAsJson($request);

        return $this->handleForm($form, $content);
    }

    /**
     * @param Request $request
     * @param Form    $form
     *
     * @throws FormInvalidException
     *
     * @return mixed
     */
    public function handleFormFromQuery(Request $request, Form $form)
    {
        $content = $request->query->all();

        return $this->handleForm($form, $content);
    }

    /**
     * @param Form $form
     * @param $content
     *
     * @throws FormInvalidException
     *
     * @return mixed
     */
    public function handleForm(Form $form, $content)
    {
        $form->submit($content);
        if (!$form->isValid() || !$form->isSubmitted()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = [
                    'field' => $error->getOrigin()->getName(),
                    'error' => method_exists($error, 'getMessageTemplate') ? $error->getMessageTemplate() : $error->getMessage(),
                ];
            }
            throw new FormInvalidException($errors);
        }

        return $form->getData();
    }

    /**
     * @param $slug
     *
     * @throws PermissionDeniedException
     */
    protected function assertSelfSlug($slug)
    {
        if ($slug !== 'me') {
            throw new PermissionDeniedException();
        }
    }
}
