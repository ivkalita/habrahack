<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 15.02.16
 * Time: 8:47.
 */
namespace UserBundle\Model\Partial;

use Symfony\Component\Validator\Constraints as Assert;

trait PhonePartial
{
    /**
     * @var string
     *
     * @Assert\Regex(
     *     pattern="/\+7\d{10}/",
     *     message="FormatInvalid"
     * )
     *
     * @Assert\NotNull(message="IsEmpty")
     */
    protected $phone = -1;

    public static function sanitize($phone)
    {
        $value = strval($phone);
        if (!$value) {
            $valid = false;
        } elseif ($value[0] === '+') {
            $clearPhone = intval(substr($value, 1));
            $valid = ($clearPhone >= 70000000000 && $clearPhone < 80000000000);
        } elseif ($value[0] === '7') {
            $clearPhone = intval($value);
            $valid = ($clearPhone >= 70000000000 && $clearPhone < 80000000000);
            $value = '+' . $value;
        } elseif ($value[0] === '8') {
            $clearPhone = intval($value);
            $valid = ($clearPhone >= 80000000000 && $clearPhone < 90000000000);
            $value = '+7' . substr($value, 1);
        } else {
            $valid = false;
        }
        if (!$valid) {
            $value = -1;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPhone($value)
    {
        $this->phone = self::sanitize($value);

        return $this;
    }
}
