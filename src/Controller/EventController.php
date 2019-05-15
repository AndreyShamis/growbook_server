<?php

namespace App\Controller;

//use App\Entity\Events\EventTemperature;
//use App\Form\Events\EventTemperatureType;
use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventTemperature;
use App\Form\EventType;
use App\Model\TypeEvent;
use App\Repository\EventRepository;
use App\Repository\Events\EventTemperatureRepository;
use App\Repository\PlantRepository;
use App\Repository\SensorRepository;
use App\Twig\AppExtension;
use App\Utils\RandomName;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
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
            'events' => $eventRepository->findBy(array(), ['updatedAt' => 'DESC']),
        ]);
    }


    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @param SensorRepository $sensors
     * @param PlantRepository $plants
     * @param EventRepository $events
     * @return Response
     */
    public function new(Request $request, LoggerInterface $logger, SensorRepository $sensors, PlantRepository $plants, EventRepository $events): Response
    {
        $event = new Event();
        $ev_req_type = array();
        $eventFound = $eventReqFound = false;
        $eventRequest = $request->request->get('event');
        $sensor = $plant = null;
        $automatic = false;
        $status = 200;
        $message = 'OK';
        try {
            if (array_key_exists('sensor_id', $eventRequest) && !array_key_exists('sensor', $eventRequest)) {
                $sensorId = trim($eventRequest['sensor_id']);
                $plantUniqId = '';
                if (array_key_exists('plant_id', $eventRequest)) {
                    $plantUniqId = trim($eventRequest['plant_id']);
                }
                if ($plant === null && strlen($plantUniqId) > 3) {
                    $plant = $plants->findOrCreate([
                        'name' => RandomName::getRandomTerm() . '__' . $plantUniqId,
                        'uniqId' => $plantUniqId,
                    ]);
                }

                $sensor = $sensors->findOrCreateByUniqId(array(
                    'uniqId' => $sensorId,
                    'name' => $sensorId,
                    'plant' => $plant,
                ));
                if ($sensor !== null) {
                    $eventRequest['sensor'] = $sensor->getId();
                    if ($plant !== null) {
                        $eventRequest['plant'] = $plant->getId();
                    }
                    unset($eventRequest['sensor_id'], $eventRequest['plant_id']);
                    $request->request->set('event', $eventRequest);
                    $automatic = true;
                }

            }
        } catch (\Throwable $ex) {
            $logger->critical('ERROR in ev_req_arr :' . $ex->getMessage());
        }
        try {
            $ev_req_arr = $request->query->all();
            $ev_req_type = $ev_req_arr['type'];
            $event = new $ev_req_type();
            $eventReqFound = true;
        } catch (\Throwable $ex) {
            $logger->critical('ERROR in ev_req_arr :' . $ex->getMessage());
        }
        try {
            //$ev_arr = $request->request->get('event');
            $ev_type = $eventRequest['type'];
            $event = new $ev_type();
            $eventFound = true;
        } catch (\Throwable $ex) {
            $logger->critical('ERROR in ev_type:' . $ex->getMessage() . ' Got:' . $ev_type);
        }
        if ($eventReqFound) {
            $tmpType = $ev_req_type;
            if ($tmpType !== null && (get_class($event) !== $tmpType || $event->getType() !== get_class($event))) {
                $event->setType($ev_req_type);
            }
        }
        $form = $this->createForm(EventType::class, $event);

        if ($eventReqFound) {
            $form->add('type', ChoiceType::class, TypeEvent::buildFormType(array($ev_req_type)));
        }
        $form->handleRequest($request);

        if ($eventFound && $form->isSubmitted() && $form->isValid()) {
            $lastEvent = null;
            try{
                if ($sensor === null && $event !== null && $event->getSensor() !== null) {
                    $sensor = $event->getSensor();
                    if ($plant === null && $event->getPlant() !== null) {
                        $plant = $event->getPlant();
                    }
                }
            } catch (\Throwable $ex) {

            }
            if ($automatic && $sensor !== null && $plant !== null) {
                $lastEvent = $events->findLast($event->getType(), $plant, $sensor);
            }
            $entityManager = $this->getDoctrine()->getManager();
            if ($lastEvent === null) {
                if ($automatic) {
                    $event->addNote('LAST_EVENT_NOT_FOUND::'.$sensor->getWriteForceEveryXseconds() . 'sec;;');
                } else {
                    $event->addNote('MANUAL::'.$request->getClientIp(). ';;');
                }

                if ($sensor !== null) {
                    $sensor->setLastEvent($event);
                }
                $entityManager->persist($event);
                $entityManager->flush();
                $status = 301;
                $message = 'Created ID:' . $event->getId();
            } else {
                // Need to check if last event have same value?
                if ($lastEvent->getValue() !== $event->getValue()) {
                    $needUpdate = true;
                    if ($event->getSensor() && $event->getSensor()->getSupportEvents()) {
                        if ($event->getType() === EventHumidity::class){
                            /** @var EventHumidity $event */
                            if (!$event->humDiff($lastEvent)) {
                                $needUpdate = false;
                            }
                        }
                        if ($event->getType() === EventTemperature::class){
                            /** @var EventTemperature $event */
                            if (!$event->tempDiff($lastEvent)) {
                                $needUpdate = false;
                            }
                        }
                    }

                    $tDiff = $lastEvent->getCreatedAt()->diff($event->getCreatedAt());
                    $seconds = (int)$tDiff->i*60 + (int)$tDiff->s;
                    if ($seconds <= 60) {
                        $message = 'Ignore diff less then 1 minute';
                    } else {
                        if ($needUpdate){
                            if ($sensor !== null) {
                                $sensor->setLastEvent($event);
                            }
                            $entityManager->detach($lastEvent);
                            $entityManager->persist($event);
                            $entityManager->flush();
                            $status = 301;
                            $message = 'Created ID:' . $event->getId();
                        }

                    }

                }
            }
            if ($automatic) {
                return new Response($message, $status);
            }
            return $this->redirectToRoute('events_index');
            //return $this->redirectToRoute('events_event_temperature_index');
        }

        if ($eventFound && $form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors();
            if (count($errors) > 0) {
                /** @var FormError $error */
                $error = $errors[0];

                $logger->critical('ERROR in event_new:' . $error->getMessage());
            } else {
                $logger->critical('ERROR in event_new - no error found');
            }

        }

        $newForm = $form->createView();
        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $newForm,
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
            $tmpType = $request->request->get('event')['type'];
            if ($tmpType !== null && get_class($event) !== $tmpType) {
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
