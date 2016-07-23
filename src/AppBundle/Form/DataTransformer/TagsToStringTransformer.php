<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.05.16
 * Time: 18:26.
 */
namespace AppBundle\Form\DataTransformer;

use Doctrine\Common\Collections\Collection;
use SubscriptionBundle\Entity\Manager\TagManager;
use SubscriptionBundle\Entity\Tag;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsToStringTransformer implements DataTransformerInterface
{
    /**
     * @var TagManager
     */
    protected $tagManager;

    /**
     * TagsToStringTransformer constructor.
     *
     * @param TagManager $tagManager
     */
    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function transform($value)
    {
        if ($value === null) {
            return '';
        }
        if (!is_array($value) && !$value instanceof Collection) {
            throw new TransformationFailedException();
        }
        $result = [];
        foreach ($value as $tag) {
            /**
             * @var Tag $tag
             */
            $result[] = $tag->getId();
        }

        return implode(',', $result);
    }

    /**
     * @param mixed $value
     *
     * @return Tag[]
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return [];
        }
        if (!is_string($value)) {
            throw new TransformationFailedException();
        }
        $ids = explode(',', $value);
        $tags = [];
        foreach ($ids as $id) {
            $tag = $this->tagManager->find($id);
            if (!$tag) {
                throw new TransformationFailedException();
            }
            $tags[] = $tag;
        }

        return $tags;
    }
}
