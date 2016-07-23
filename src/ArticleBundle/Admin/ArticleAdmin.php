<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 23.07.16
 * Time: 20:51
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
            ->add('videoUrl', 'text', ['label' => 'Ссылка на видео'])
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
}