<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 13.02.16
 * Time: 11:06.
 */
namespace UserBundle\Entity\Manager;

use AppBundle\Entity\Manager\BaseEntityManager;
use Doctrine\ORM\NoResultException;

class DeviceManager extends BaseEntityManager
{
    /**
     * @param $platform
     * @param $deviceId
     *
     * @return array|null
     */
    public function findOneByPlatformAndDeviceId($platform, $deviceId)
    {
        $qb =  $this->getRepository()->createQueryBuilder('d')
            ->where('d.deviceId = :device_id')
            ->andWhere('d.platform = :platform')
            ->setParameter('device_id', $deviceId)
            ->setParameter('platform', $platform)
            ->setMaxResults(1);
        try {
            $device = $qb->getQuery()->getSingleResult();
        } catch (NoResultException $exception) {
            $device = null;
        }

        return $device;
    }
}
