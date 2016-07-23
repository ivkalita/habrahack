<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 13:28.
 */
namespace AppBundle\Entity;

use AppBundle\Model\TemporaryInterface;
use AppBundle\Model\TemporaryTrait;
use AppBundle\Model\TimestampableInterface;
use AppBundle\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TemporaryTimestampableEntity.
 *
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 */
class TemporaryTimestampableEntity implements TemporaryInterface, TimestampableInterface
{
    use TemporaryTrait;
    use TimestampableTrait;
}
