<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 24.04.16
 * Time: 14:03.
 */
namespace AppBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * EntityToIdTransformer constructor.
     *
     * @param EntityManager $em
     * @param string        $entityName
     */
    public function __construct(EntityManager $em, $entityName)
    {
        $this->em = $em;
        $this->entityName = $entityName;
    }

    /**
     * @param mixed $value
     * 
     * @return int
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_object($value) || !method_exists($value, 'getId')) {
            throw new TransformationFailedException();
        }

        return $value->getId();
    }

    /**
     * @param mixed $value
     *
     * @return null|object
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_int($value)) {
            throw new TransformationFailedException();
        }

        $object = $this->em->getRepository($this->entityName)->find($value);

        return $object;
    }
}
