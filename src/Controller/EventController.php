<?php

namespace App\Controller;

//use App\Entity\Events\EventTemperature;
//use App\Form\Events\EventTemperatureType;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\Events\EventTemperatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="events_index")
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAllTypes(),
        ]);
    }


    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('events_event_temperature_index');
        }

        return $this->render('events/event_temperature/new.html.twig', [
            'event_temperature' => $event,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/{type}", name="event_show", methods={"GET"}, options={})
     * @return Response
     */
    public function show($id, $type): Response
    {
        $event = $this->getDoctrine()->getManager()->find($type, $id);
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

}
