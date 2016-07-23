<?php

namespace AppBundle\Entity;

use AppBundle\Model\TimestampableInterface;
use AppBundle\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 *
 * @JMS\ExclusionPolicy("all")
 */
class TimestampableEntity implements TimestampableInterface
{
    use TimestampableTrait;
}
