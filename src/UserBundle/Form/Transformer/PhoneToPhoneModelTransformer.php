<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 20.04.16
 * Time: 13:24.
 */
namespace UserBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use UserBundle\Model\Phone;

class PhoneToPhoneModelTransformer implements DataTransformerInterface
{
    /**
     * @var bool
     */
    protected $allowNulls;

    /**
     * @param mixed $value
     *
     * @return null|Phone
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        // for empty strings and other
        if ($value == null && $this->allowNulls) {
            return null;
        }

        $phoneModel = new Phone();
        $phoneModel->setPhone($value);

        return $phoneModel;
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     */
    public function transform($value)
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Phone) {
            throw new TransformationFailedException();
        }

        return $value->getPhone();
    }

    /**
     * PhoneToPhoneModelTransformer constructor.
     *
     * @param bool $allowNulls
     */
    public function __construct($allowNulls = false)
    {
        $this->allowNulls = $allowNulls;
    }
}
