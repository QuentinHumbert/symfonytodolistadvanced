<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Task;
use App\Entity\Categories;

class TaskController extends AbstractController
{
    #[Route('/task/listing', name: 'app_listing_task')]
    public function listing(ManagerRegistry $doctrine): Response
    {
        $tasks = $doctrine->getRepository(Task::class)->findall();
        return $this->render('task/listing.html.twig', ['tasks'=>$tasks]);
    }

    #[Route('/task/add', name: 'app_add_task')]
    public function add(): Response
    {
        $task = new Task();
        $task->setNameTask('Nommer votre tâche');
        $task->setDescriptionTask('Decrire votre tâche');
        $task->setDueDateTask(new \DateTime('tomorrow'));
        $task->setPriorityTask('Choisir la priorité');
        // $task->setCategory();

        $form = $this->createFormBuilder($task)
        ->add('nameTask', TextType::class, [
            'attr' => [
                'class'=>'form-control'
                ]
        ])
        ->add('descriptionTask', TextareaType::class, [
            'attr' => [
                'class'=>'form-control'
                ]
        ])
        ->add('dueDateTask', DateType::class, [
            'widget'=>'single_text',
            'attr' => [
                'class'=>'form-control'
                ]
        ])
        ->add('priorityTask', ChoiceType::class, [
            'choices' => [
                'Haute' => 'Haute',
                'Normale' => 'Normale',
                'Basse' => 'Basse',
            ],
            'attr' => [
                'class'=>'form-control'
                ]
        ])
        ->add('category', EntityType::class, [
            'class' => Categories::class,
            'choice_label' => 'libelleCategory'
        ])
        ->add('save', SubmitType::class, ['label' => 'Créer tâche'])
        ->getForm();       

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/update', name: 'app_update_task')]
    public function update(): Response
    {
        return $this->render('task/update.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    #[Route('/task/delete', name: 'app_delete_task')]
    public function delete(): Response
    {
        return $this->render('task/delete.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
