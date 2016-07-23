<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 19.02.16
 * Time: 18:49.
 */
namespace AppBundle\Serializer\Handlers;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;

class TimestampHandler implements SubscribingHandlerInterface
{
    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'Timestamp',
                'method' => 'serializeDateTimeToTimestamp',
            ],
        ];
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param \DateTime                $date
     * @param array                    $type
     * @param Context                  $context
     *
     * @return int
     */
    public function serializeDateTimeToTimestamp(JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context)
    {
        return $date ? $date->getTimestamp() : null;
    }
}
