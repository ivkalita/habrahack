<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 23.07.16
 * Time: 20:51.
 */
namespace ArticleBundle\Admin;

use ArticleBundle\Entity\Article;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Model\Metadata;

class ArticleAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        parent::configureFormFields($form);
        $form
            ->add('title', 'text', ['label' => 'Заголовок'])
            ->add('videoUrl', 'text', ['label' => 'Ссылка на видео', 'required' => false])
            ->add('file', 'file', ['label' => 'Видеофайл', 'required' => false])
            ->add('placeholderUrl', 'text', ['label' => 'Изображение-заглушка']);
    }

    protected function configureListFields(ListMapper $list)
    {
        parent::configureListFields($list);
        $list
            ->addIdentifier('title', 'text', ['label' => 'Заголовок'])
            ->add('createdAt', 'datetime', ['label' => 'Время создания']);
    }

    public function getObjectMetadata($object)
    {
        /**
         * @var Article $object
         */
        return new Metadata($object->getTitle(), null, $object->getPlaceholderUrl());
    }

    public function preUpdate($object)
    {
        parent::preUpdate($object);
        $this->handleVideoFile($object);
    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        $this->handleVideoFile($object);
    }

    protected function handleVideoFile($object)
    {
        /**
         * @var Article $object
         */
        if (null === $object->getFile()) {
            return;
        }

        $fileName = uniqid() . '.' . $object->getFile()->guessExtension();
        $object->getFile()->move(
            $this->getConfigurationPool()->getContainer()->getParameter('video_directory'),
            $fileName
        );
        $object->setVideoUrl($this->getConfigurationPool()->getContainer()->getParameter('base_url') . "/uploads/$fileName");
        $object->setFile(null);
    }
}
