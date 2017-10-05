<?php

namespace Swivl\ClassroomBundle\Controller;

use Swivl\ClassroomBundle\Entity\Classes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class controller.
 *
 * @Route("class")
 */
class ClassesController extends Controller
{
    /**
     * Lists all class entities.
     *
     * @Route("/", name="class_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository('SwivlClassroomBundle:Classes')->findAll();

        return $this->render('@SwivlClassroom/classes/index.html.twig', array(
            'classes' => $classes,
        ));
    }

    /**
     * Creates a new class entity.
     *
     * @Route("/new", name="class_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $class = new Classes();
        $form = $this->createForm('Swivl\ClassroomBundle\Form\ClassesType', $class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($class);
            $em->flush();

            return $this->redirectToRoute('class_show', array('id' => $class->getId()));
        }

        return $this->render('@SwivlClassroom/classes/new.html.twig', array(
            'class' => $class,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a class entity.
     *
     * @Route("/{id}", name="class_show", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function showAction(Classes $class)
    {
        $deleteForm = $this->createDeleteForm($class);

        return $this->render('@SwivlClassroom/classes/show.html.twig', array(
            'class' => $class,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing class entity.
     *
     * @Route("/{id}/edit", name="class_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Classes $class)
    {
        $deleteForm = $this->createDeleteForm($class);
        $editForm = $this->createForm('Swivl\ClassroomBundle\Form\ClassesType', $class);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('class_edit', array('id' => $class->getId()));
        }

        return $this->render('@SwivlClassroom/classes/edit.html.twig', array(
            'class' => $class,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Edits an existing Printer entity.
     *
     * @Route("/{id}/update", name="class_update", requirements={"id": "\d+"})
     * @Method("POST")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SwivlClassroomBundle:Classes')->find($id);
        $requestData = $request->request->get('appbundle_classes');
        $isActive = isset($requestData['active']) ? $requestData['active'] : null;
        $status = ($isActive == 'yes') ? 'not_active' : 'active';
        $workflow = $this->container->get('workflow.class_status');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Printer entity.');
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createForm('Swivl\ClassroomBundle\Form\ClassesType', $entity);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            if ($workflow->can($entity, $status)) {
                $workflow->apply($entity, $status);
            }

            return $this->redirectToRoute('class_edit', array('id' => $entity->getId()));
        }

        return $this->render('@SwivlClassroom/classes/edit.html.twig', array(
            'class' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a class entity.
     *
     * @Route("/{id}", name="class_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Classes $class)
    {
        $form = $this->createDeleteForm($class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($class);
            $em->flush();
        }

        return $this->redirectToRoute('class_index');
    }

    /**
     * Creates a form to delete a class entity.
     *
     * @param Classes $class The class entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Classes $class)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('class_delete', array('id' => $class->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Return list of classes.
     *
     * @Route("/api/list/all", name="list_all")
     * @Method("GET")
     */
    public function getAllClassesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository('SwivlClassroomBundle:Classes')->findAll();

        $classes = $this->get('serializer')->serialize($classes, 'json');

        $response = new Response($classes);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Return one class.
     *
     * @Route("/api/list/{id}", name="class_one", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function getOneClasseAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $class = $em->getRepository('SwivlClassroomBundle:Classes')->find($id);

        $class = $this->get('serializer')->serialize($class, 'json');

        $response = new Response($class);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
