<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Appointment;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('schedule', TimeType::class,[
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => array("07"=>"07","08"=>"08","09"=>"09","10"=>"10","11"=>"11","12"=>"12","13"=>"13","14"=>"14","15"=>"15","16"=>"16","17"=>"17","18"=>"18","19"=>"19","20"=>"20"),
                'minutes' => array("0"=>"0","15"=>"15","30"=>"30","45"=>"45"),
            ])
            
            ->add('medecin', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->leftJoin('m.statut', 'statut')
                        ->andWhere('statut.name = :statut')
                        ->setParameter('statut', 'medecin')
                        ->orderBy('m.lastname', 'ASC')
                        ->orderBy('m.firstname', 'ASC');
                },
                'choice_label' => 'fullName',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
