<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppExtension extends \Twig_Extension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * AppExtension constructor.
     *
     * @param UrlGeneratorInterface $generator
     * @param RequestStack          $requestStack
     */
    public function __construct(UrlGeneratorInterface $generator, RequestStack $requestStack)
    {
        $this->generator = $generator;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'previous_url' => new \Twig_Function_Method($this, 'previousUrl'),
        ];
    }

    /**
     * @param string $routeName
     * @param array  $parameters
     *
     * @return array|string
     */
    public function previousUrl($routeName, array $parameters = [])
    {
        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
        if ($referer) {
            return $referer;
        }

        return $this->generator->generate($routeName, $parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}
