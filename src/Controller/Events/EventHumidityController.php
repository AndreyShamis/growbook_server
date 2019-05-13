<?php

namespace App\Controller\Events;

use App\Entity\Events\EventHumidity;
use App\Form\Events\EventHumidityType;
use App\Repository\Events\EventHumidityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/events/event/humidity")
 */
class EventHumidityController extends AbstractController
{
    /**
     * @Route("/", name="events_event_humidity_index", methods={"GET"})
     */
    public function index(EventHumidityRepository $eventHumidityRepository): Response
    {
        return $this->render('events/event_humidity/index.html.twig', [
            'event_humidities' => $eventHumidityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="events_event_humidity_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $eventHumidity = new EventHumidity();
        $form = $this->createForm(EventHumidityType::class, $eventHumidity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($eventHumidity->getPlant() === null && $eventHumidity->getSensor() !== null) {
                $eventHumidity->setPlant($eventHumidity->getSensor()->getPlant());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventHumidity);
            $entityManager->flush();

            return $this->redirectToRoute('events_event_humidity_index');
        }

        return $this->render('events/event_humidity/new.html.twig', [
            'event_humidity' => $eventHumidity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_humidity_show", methods={"GET"})
     */
    public function show(EventHumidity $eventHumidity): Response
    {
        return $this->render('events/event_humidity/show.html.twig', [
            'event_humidity' => $eventHumidity,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="events_event_humidity_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EventHumidity $eventHumidity): Response
    {
        $form = $this->createForm(EventHumidityType::class, $eventHumidity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('events_event_humidity_index', [
                'id' => $eventHumidity->getId(),
            ]);
        }

        return $this->render('events/event_humidity/edit.html.twig', [
            'event_humidity' => $eventHumidity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_humidity_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EventHumidity $eventHumidity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventHumidity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eventHumidity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_event_humidity_index');
    }
}
