<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 12:45.
 */
namespace AppBundle\Entity;

use AppBundle\Model\TemporaryInterface;
use AppBundle\Model\TemporaryTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TemporaryEntity.
 *
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 */
class TemporaryEntity implements TemporaryInterface
{
    use TemporaryTrait;

    /**
     * @param string $alias
     *
     * @return string
     */
    public static function getIsActiveCondition($alias)
    {
        return "($alias.isActive IS NULL OR $alias.isActive = TRUE) AND
                $alias.since <= :now AND
                ($alias.until IS NULL OR $alias.until > :now)";
    }
}
