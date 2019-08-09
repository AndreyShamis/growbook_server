<?php

namespace App\Controller;

use App\Entity\CustomField;
use App\Form\CustomFieldType;
use App\Repository\CustomFieldRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/custom/field")
 */
class CustomFieldController extends AbstractController
{
//    /**
//     * @Route("/", name="custom_field_index", methods={"GET"})
//     */
//    public function index(CustomFieldRepository $customFieldRepository): Response
//    {
//        return $this->render('custom_field/index.html.twig', [
//            'custom_fields' => $customFieldRepository->findAll(),
//        ]);
//    }

//    /**
//     * @Route("/new", name="custom_field_new", methods={"GET","POST"})
//     */
//    public function new(Request $request): Response
//    {
//        $customField = new CustomField();
//        $form = $this->createForm(CustomFieldType::class, $customField);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($customField);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('custom_field_index');
//        }
//
//        return $this->render('custom_field/new.html.twig', [
//            'custom_field' => $customField,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/{id}", name="custom_field_show", methods={"GET"})
     */
    public function show(CustomField $customField): Response
    {
        return $this->render('custom_field/show.html.twig', [
            'custom_field' => $customField,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="custom_field_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomField $customField): Response
    {
        $form = $this->createForm(CustomFieldType::class, $customField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('custom_field/edit.html.twig', [
            'custom_field' => $customField,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="custom_field_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomField $customField): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customField->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customField);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }
}
