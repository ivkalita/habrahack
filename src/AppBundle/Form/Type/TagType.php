<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.05.16
 * Time: 12:39.
 */
namespace AppBundle\Form\Type;

use Doctrine\Common\Collections\Collection;
use SubscriptionBundle\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'ajax_route' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['tags'] = [];
        /** @var Collection|null $tags */
        $tags = $form->getData();
        if ($tags === null) {
            return;
        }

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $view->vars['tags'][] = [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
            ];
        }
    }

    public function getParent()
    {
        return TextType::class;
    }
}
