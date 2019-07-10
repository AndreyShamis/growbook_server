<?php

namespace App\Controller\Events;

use App\Entity\Events\EventFeed;
use App\Entity\Events\EventFertilize;
use App\Form\Events\EventFertilizeType;
use App\Repository\Events\EventFertilizeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/events/event/fertilize")
 */
class EventFertilizeController extends AbstractController
{
    /**
     * @Route("/", name="events_event_fertilize_index", methods={"GET"})
     */
    public function index(EventFertilizeRepository $eventFertilizeRepository): Response
    {
        return $this->render('events/event_fertilize/index.html.twig', [
            'event_fertilizes' => $eventFertilizeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="events_event_fertilize_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $eventFertilize = new EventFertilize();
        $eventFertilize->setType(EventFertilize::class);
        $form = $this->createForm(EventFertilizeType::class, $eventFertilize);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventFertilize);
            $entityManager->flush();

            return $this->redirectToRoute('events_event_fertilize_index');
        }

        return $this->render('events/event_fertilize/new.html.twig', [
            'event_fertilize' => $eventFertilize,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_fertilize_show", methods={"GET"})
     */
    public function show(EventFertilize $eventFertilize): Response
    {
        return $this->render('events/event_fertilize/show.html.twig', [
            'event_fertilize' => $eventFertilize,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="events_event_fertilize_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EventFertilize $eventFertilize): Response
    {
        $form = $this->createForm(EventFertilizeType::class, $eventFertilize);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('events_event_fertilize_index', [
                'id' => $eventFertilize->getId(),
            ]);
        }

        return $this->render('events/event_fertilize/edit.html.twig', [
            'event_fertilize' => $eventFertilize,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_fertilize_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EventFertilize $eventFertilize): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventFertilize->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eventFertilize);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_event_fertilize_index');
    }
}
