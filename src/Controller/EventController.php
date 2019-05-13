<?php

namespace App\Controller;

//use App\Entity\Events\EventTemperature;
//use App\Form\Events\EventTemperatureType;
use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventTemperature;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\Events\EventTemperatureRepository;
use App\Twig\AppExtension;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            //'events' => $eventRepository->findAllTypes(),
            'events' => $eventRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @return Response
     */
    public function new(Request $request, LoggerInterface $logger): Response
    {
        $event = new Event();
        $ev_req_type = array();
        $eventFound = $eventReqFound = false;
        try {
            $ev_req_arr = $request->query->all();
            $ev_req_type = $ev_req_arr['type'];
            $event = new $ev_req_type();
            $eventReqFound = true;
        } catch (\Throwable $ex) {
            $logger->critical('ERROR :' . $ex->getMessage());
        }
        try {
            $ev_arr = $request->request->get('event');
            $ev_type = $ev_arr['type'];
            $event = new $ev_type();
            $eventFound = true;
        } catch (\Throwable $ex) {
            $logger->critical('ERROR :' . $ex->getMessage());
        }

        $form = $this->createForm(EventType::class, $event);
        if ($eventReqFound) {
            $form->add('type', ChoiceType::class, [
//                'widget' => 'single_text',
                'required' => true,
                'preferred_choices' => array($ev_req_type),
                'attr'=>array(
                    'style'=>'font: 10px;'
                ),
                'choices' => $this->buildTypeChoices(),
            ]);
        }

        $form->handleRequest($request);

        if ($eventFound && $form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('events_index');
            //return $this->redirectToRoute('events_event_temperature_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public static function buildTypeChoices(): array
    {
        $a = new AppExtension();
        $classes = array(Event::class, EventHumidity::class, EventTemperature::class);
        $ret = array();
        foreach ($classes as $class) {
            $ret[$a->parseType($class)] = $class;
        }
        return $ret;
    }
    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Event $event
     * @return Response
     */
    public function edit(Request $request, Event $event): Response
    {

        try {
            /** Cast object to another class */
            if (get_class($event) !== $request->request->get('event')['type']) {
                $event->setType($request->request->get('event')['type']);
                /** @var Event $event */
                $event = $event->castAs($request->request->get('event')['type']);


            }
        } catch (\Throwable $ex) { }
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        //$form->getData()
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('events_index', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}/{type}", name="event_show", methods={"GET"}, options={})
//     * @return Response
//     */
//    public function show($id, $type): Response
//    {
//        $event = $this->getDoctrine()->getManager()->find($type, $id);
//        return $this->render('event/show.html.twig', [
//            'event' => $event,
//        ]);
//    }
//
    /**
     * @Route("/{id}/{type}", name="event_show", methods={"GET"}, options={})
     * @ Entity(name="event", class="App\Entity\Event", expr="repository.findByIdAndType(id, type)")
     * @param Event $event
     * @param string $type
     * @return Response
     */
    public function show(Event $event, string $type=''): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'type' => $type
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     * @param Request $request
     * @param Event $event
     * @return Response
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_index');
    }
}
