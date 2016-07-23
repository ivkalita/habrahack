<?php

namespace AppBundle\Serializer\Handlers;

use AppBundle\Entity\Image;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Sonata\MediaBundle\Provider\ImageProvider;

class ImageHandler implements SubscribingHandlerInterface
{
    private $imageProvider;

    private $baseUrl;

    public function __construct(ImageProvider $imageProvider, $baseUrl)
    {
        $this->imageProvider = $imageProvider;
        $this->baseUrl = $baseUrl;
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'AppBundle\Entity\Image',
                'method' => 'serializeImageToArray',
            ],
        ];
    }

    public function serializeImageToArray(JsonSerializationVisitor $visitor, Image $image, array $type, Context $context)
    {
        return $this->serialize($image);
    }

    public function serialize(Image $image)
    {
        if ($image === null) {
            return null;
        }

        $standardFormat = $this->imageProvider->getFormatName($image, 'standard');
        $thumbnailFormat = $this->imageProvider->getFormatName($image, 'thumbnail');

        return [
            'original' => $this->baseUrl . $this->imageProvider->generatePublicUrl($image, 'reference'),
            'standard' => $this->baseUrl . $this->imageProvider->generatePublicUrl($image, $standardFormat),
            'thumbnail' => $this->baseUrl . $this->imageProvider->generatePublicUrl($image, $thumbnailFormat),
        ];
    }
}
