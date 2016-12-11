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

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($builder);die;
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('active', CheckboxType::class, ['label' => 'Active', 'required' => false])
            ->add('status', ChoiceType::class, [
                'choices'  => array(
                    'Save' => 0,
                    'Preview' => 1,
                    'Publish' => 2,
                )
                ]
            )
//            ->add('android')
            ->add('images', FileType::class, array('mapped'=>false, 'required' => false, 'label' => 'Image'))
            ->add('exporter', EntityType::class, array(
                'class' => 'AppBundle\Entity\Exporter',
                'query_builder' => function(\AppBundle\Repository\ExporterRepository $er) {
                    $var = $er->createQueryBuilder('e')
                        ->orderBy('e.name', 'ASC');
                },
                'mapped'=>false,
                'choice_label' => 'name'
            ));
//            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-default')));
//            ->add('file', FileType::class, array('label' => 'Image'))
//            ->add('created', DatetimeType::class)
        ;
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
