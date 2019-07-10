<?php

namespace App\Controller;

use App\Entity\Fertilizer;
use App\Form\FertilizerType;
use App\Repository\FertilizerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fertilizer")
 */
class FertilizerController extends AbstractController
{
    /**
     * @Route("/", name="fertilizer_index", methods={"GET"})
     */
    public function index(FertilizerRepository $fertilizerRepository): Response
    {
        return $this->render('fertilizer/index.html.twig', [
            'fertilizers' => $fertilizerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="fertilizer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fertilizer = new Fertilizer();
        $form = $this->createForm(FertilizerType::class, $fertilizer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fertilizer);
            $entityManager->flush();

            return $this->redirectToRoute('fertilizer_index');
        }

        return $this->render('fertilizer/new.html.twig', [
            'fertilizer' => $fertilizer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fertilizer_show", methods={"GET"})
     */
    public function show(Fertilizer $fertilizer): Response
    {
        return $this->render('fertilizer/show.html.twig', [
            'fertilizer' => $fertilizer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="fertilizer_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Fertilizer $fertilizer
     * @return Response
     */
    public function edit(Request $request, Fertilizer $fertilizer): Response
    {
        $form = $this->createForm(FertilizerType::class, $fertilizer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fertilizer_index', [
                'id' => $fertilizer->getId(),
            ]);
        }

        return $this->render('fertilizer/edit.html.twig', [
            'fertilizer' => $fertilizer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fertilizer_delete", methods={"DELETE"})
     * @param Request $request
     * @param Fertilizer $fertilizer
     * @return Response
     */
    public function delete(Request $request, Fertilizer $fertilizer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fertilizer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fertilizer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('fertilizer_index');
    }
}
