<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Tests\Fixtures\Entity;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TodolistController extends Controller
{
    /**
     * @Route("/", name="todolist")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $todolist = $this->getDoctrine()
                    ->getRepository(Todo::class)
                    ->findAll();

        return $this->render('todolist/index.html.twig', [
            'message' =>'Welcome to Todolist',
            'todos' =>$todolist
        ]);
    }

    /**
     * @Route("/todo/create", name="create_todo")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo();

        $form = $this->createFormBuilder($todo)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px') ))

            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px') ))

            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px') ))
            ->add('priority', ChoiceType::class, array('choices'  => array(
                    'Normal' => 'Normal',
                    'Medium' => 'Medium',
                    'High' => 'High',
                    'Low' => 'Low',
                ),
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px')
            ))
            ->add('startDateTime', DateTimeType::class, array(
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                ),
                'attr' => array('class' => '', 'style' => 'margin-bottom:10px')
            ))
            ->add('save', SubmitType::class, array('label' => 'Create Todo', 'attr'=>array('class' => 'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $todo = $form->getData();

            $now = new \DateTime('now');
            $todo->setCreateDate($now);
//            $todo->setStartTime($now);

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($todo);
             $entityManager->flush();

            $this->addFlash('notice', 'you have added a todo!');

            return $this->redirectToRoute('todolist');
        }

        return $this->render('todolist/create.html.twig', [
            'message' =>'Create a To-Do',
            'form' => $form->createView(),

        ]);
    }


    /**
     * @Route("/todo/details/{id}", name="details_todo")
     */
    public function detailsAction($id,Request $request)
    {

        $current_todo = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->find($id);


        // replace this example code with whatever you need
        return $this->render('todolist/details.html.twig', [
            'message' =>'Your To-Do Details',
            'todo' =>$current_todo
        ]);
    }


    /**
     * @Route("/todo/edit/{id}", name="edit_todo")
     */
    public function editAction($id, Request $request)
    {
        $current_todo_edit = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->find($id);

           $form = $this->createFormBuilder($current_todo_edit)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px') ))

            ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px') ))

            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px') ))
            ->add('priority', ChoiceType::class, array('choices'  => array(
                'Normal' => 'Normal',
                'Medium' => 'Medium',
                'High' => 'High',
                'Low' => 'Low',
            ),
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:10px')
            ))
            ->add('startDateTime', DateTimeType::class, array(
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                ),
                'attr' => array('class' => '', 'style' => 'margin-bottom:10px')
            ))
            ->add('save', SubmitType::class, array('label' => 'Update Todo', 'attr'=>array('class' => 'btn btn-primary')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $todo = $form->getData();

            $now = new \DateTime('now');
            $todo->setCreateDate($now);
//            $todo->setStartTime($now);

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getRepository(Todo::class) ->find($id);
            $entityManager->flush();

            $this->addFlash('notice', 'you have Updated a todo!');

            return $this->redirectToRoute('todolist');
        }

        // replace this example code with whatever you need
        return $this->render('todolist/edit.html.twig', [
            'message' =>'Edit To-Do',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", name="delete_todo")
     */
    public function deleteAction($id, Request $request)
    {
        $current_todo_edit = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($current_todo_edit);
        $entityManager->flush();

        $this->addFlash('notice', 'To-Do deleted Successfully!');

        return $this->redirectToRoute('todolist');
    }

    /**
     * @Route("/todo/message/", name="flash_test")
     */
    public function messageAction(Request $request)
    {
        // replace this example code with whatever you need
        $this->addFlash(
            'notice', 'Flash message testing!');

        return $this->render('todolist/message.html.twig', [
            'message' =>'Message Test',
        ]);
    }

}
