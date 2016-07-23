<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city_id', TextType::class, [
                'property_path' => 'cityId',
            ])
            ->add('first_name', TextType::class, [
                'required' => false,
                'property_path' => 'firstName',
            ])
            ->add('middle_name', TextType::class, [
                'required' => false,
                'property_path' => 'middleName',
            ])
            ->add('last_name', TextType::class, [
                'required' => false,
                'property_path' => 'lastName',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'UserBundle\Model\User',
            'csrf_protection' => false,
        ]);
    }
}
