<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($builder);die;
        $builder
            ->add('name', TextType::class, ['required' => true, 'attr' => ['maxlength' => 25], 'label' => 'Project name' ])
//            ->add('active', CheckboxType::class, ['label' => 'Active'])
            ->add('status', ChoiceType::class, [
                'choices'  => array(
                    'Private' => 0,
                    'Public' => 1,
                )
                ]
            )
            ->add('face', FileType::class, array('label' => 'Front', 'required' => false, 'attr' => ['class' => 'hidden'] ));
//            ->add('android')
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project'
        ));
    }
}
