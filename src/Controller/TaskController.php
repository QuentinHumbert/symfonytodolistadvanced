<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Task;
use App\Entity\Categories;

class TaskController extends AbstractController
{
    #[Route('/task/listing', name: 'app_listing_task')]
    public function listing(ManagerRegistry $doctrine): Response
    {
        $tasks = $doctrine->getRepository(Task::class)->findall();
        return $this->render('task/listing.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/task/add', name: 'app_add_task')]
    public function add(Request $request, ManagerRegistry $doctrine, TranslatorInterface $translator): Response
    {
        $task = new Task();
        $task->setNameTask('');
        $task->setDescriptionTask('');
        $task->setDueDateTask(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('nameTask', TextType::class, [
                'label' => 'Nom de la tâche',

                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ecrivez votre tâche'
                ]
            ])
            ->add('descriptionTask', TextareaType::class, [
                'label' => 'Description de la tâche',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Décrivez votre tâche'
                ]
            ])
            ->add('dueDateTask', DateType::class, [
                'label' => $translator->trans('task.duedate'),
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('priorityTask', ChoiceType::class, [
                'label' => 'Priorité de la tâche',
                'choices' => [
                    $translator->trans('priority.high') => $translator->trans('priority.high'),
                    $translator->trans('priority.medium') => $translator->trans('priority.medium'),
                    $translator->trans('priority.low') => $translator->trans('priority.low')
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie de la tâche',
                'class' => Categories::class,
                'choice_label' => 'libelleCategory',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer tâche',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Réccupére toutes les données du formalaire
            $task = $form->getData();
            $task->setCreatedDateTask(new \DateTime('now'));
            $entityManager = $doctrine->getManager();
            // Indique à la doctrine la sauvegarde (Pas de requête)
            $entityManager->persist($task);
            // Execute la requête 
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'La tâche a était créée.'
            );

            return $this->redirectToRoute('app_listing_task');
        }
        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/update/{id}', name: 'app_update_task')]
    public function update($id,Request $request, ManagerRegistry $doctrine): Response
    {
        $task = $doctrine->getRepository(Task::class)->find($id);

        // Création du formulaire
        $form = $this->createFormBuilder($task)
            ->add('nameTask', TextType::class, [
                'label' => 'Nom de la tâche',

                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ecrivez votre tâche'
                ]
            ])
            ->add('descriptionTask', TextareaType::class, [
                'label' => 'Description de la tâche',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Décrivez votre tâche'
                ]
            ])
            ->add('dueDateTask', DateType::class, [
                'label' => 'Date effective',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('priorityTask', ChoiceType::class, [
                'label' => 'Priorité de la tâche',
                'choices' => [
                    'Haute' => 'Haute',
                    'Normale' => 'Normale',
                    'Basse' => 'Basse',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie de la tâche',
                'class' => Categories::class,
                'choice_label' => 'libelleCategory',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier tâche',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'La tâche a était modifié.'
            );

            return $this->redirectToRoute('app_listing_task');
        }

        return $this->render('task/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/delete/{id}', name: 'app_delete_task')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $task = $doctrine->getRepository(Task::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'La tâche a était supprimé.'
        );

        return $this->redirectToRoute('app_listing_task');
    }
}
