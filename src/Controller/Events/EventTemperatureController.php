<?php

namespace App\Controller\Events;

use App\Entity\Events\EventTemperature;
use App\Form\Events\EventTemperatureType;
use App\Repository\Events\EventTemperatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/events/event/temperature")
 */
class EventTemperatureController extends AbstractController
{
    /**
     * @Route("/", name="events_event_temperature_index", methods={"GET"})
     * @param EventTemperatureRepository $eventTemperatureRepository
     * @return Response
     */
    public function index(EventTemperatureRepository $eventTemperatureRepository): Response
    {
        return $this->render('events/event_temperature/index.html.twig', [
            'event_temperatures' => $eventTemperatureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="events_event_temperature_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $eventTemperature = new EventTemperature();
        $form = $this->createForm(EventTemperatureType::class, $eventTemperature);
        $form->handleRequest($request);

        //$eventTemperature->setType(get_class($eventTemperature));
        if ($form->isSubmitted() && $form->isValid()) {
            if ($eventTemperature->getPlant() === null && $eventTemperature->getSensor() !== null) {
                $eventTemperature->setPlant($eventTemperature->getSensor()->getPlant());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($eventTemperature);
            $entityManager->flush();

            return $this->redirectToRoute('events_event_temperature_index');
        }

        return $this->render('events/event_temperature/new.html.twig', [
            'event_temperature' => $eventTemperature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_temperature_show", methods={"GET"})
     */
    public function show(EventTemperature $eventTemperature): Response
    {
        return $this->render('events/event_temperature/show.html.twig', [
            'event_temperature' => $eventTemperature,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="events_event_temperature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EventTemperature $eventTemperature): Response
    {
        $form = $this->createForm(EventTemperatureType::class, $eventTemperature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('events_event_temperature_index', [
                'id' => $eventTemperature->getId(),
            ]);
        }

        return $this->render('events/event_temperature/edit.html.twig', [
            'event_temperature' => $eventTemperature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_temperature_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EventTemperature $eventTemperature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventTemperature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($eventTemperature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_event_temperature_index');
    }
}
