<?php

namespace App\Controller;

//use App\Entity\Events\EventTemperature;
//use App\Form\Events\EventTemperatureType;
use App\Entity\Event;
use App\Entity\Events\EventHumidity;
use App\Entity\Events\EventSoilHydrometer;
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
        /** @var string $value3 */
        $value1 = $value2 = $value3 = null;
        $event = new Event();
        $ev_req_type = array();
        $eventFound = $eventReqFound = false;
        $eventRequest = $request->request->get('event');
        $all = $request->request->all();
        if (count($all) > 1) {
            $eventRequest = $all;
            if(array_key_exists('value3', $eventRequest)) {
                $value3 = $eventRequest['value3'];
                unset($eventRequest['value3']);
            }
            if(array_key_exists('value2', $eventRequest)) {
                $value2 = $eventRequest['value2'];
                unset($eventRequest['value2']);
            }
            if(array_key_exists('value1', $eventRequest)) {
                $value1 = $eventRequest['value1'];
                unset($eventRequest['value1']);
            }
        }
        $sensor = $plant = null;
        $automatic = false;
        $status = 200;
        $message = '';
        $entityManager = $this->getDoctrine()->getManager();
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

                    if ($plant !== null) {
                        $eventRequest['plant'] = $plant->getId();
                    }
                }

                $sensor = $sensors->findOrCreateByUniqId(array(
                    'uniqId' => $sensorId,
                    'name' => $sensorId,
                    'plant' => $plant,
                    'eventType' => $eventRequest['type'],
                    'ip' => $request->getClientIp()
                ));
                if ($sensor !== null) {
                    $update_palnt = false;
                    if (array_key_exists('uptime', $eventRequest) && $eventRequest['uptime'] > 1) {
                        $plant->setUptime($eventRequest['uptime']);
                        $update_palnt = true;
                    }
                    if (array_key_exists('rssi', $eventRequest)) {
                        $plant->setRssi($eventRequest['rssi']);
                        $update_palnt = true;
                    }
                    if ($update_palnt) {
                        $entityManager->persist($plant);
                        $entityManager->flush();
                    }
                    $eventRequest['sensor'] = $sensor->getId();
                    unset($eventRequest['sensor_id'], $eventRequest['plant_id'], $eventRequest['uptime'], $eventRequest['rssi']);
                    $request->request->set('event', $eventRequest);
                    $automatic = true;
                    if ($sensor->getPlant()->getId() !== $plant->getId()) {
                        $sensor->setPlant($plant);
                    }
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
            $message = 'ERROR in ev_type:' . $ex->getMessage() . ' Got:' . $ev_type;
            $logger->critical($message);
            $status = 501;
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
                $logger->critical('ERROR in looking for sensor:' . $ex->getMessage());
            }

            if ($automatic && $sensor !== null && $plant !== null) {
                $lastEvent = $events->findLast($event->getType(), $plant, $sensor);
            }

            $event->setIp($request->getClientIp());

            /**  */
            try {
                if ($value1 !== null) {
                    $event->setValue1($value1);
                }
            } catch (\Throwable $ex) {
                $logger->critical($ex->getMessage());
            }

            try {
                if ($value2 !== null) {
                    $event->setValue2($value2);
                }
            } catch (\Throwable $ex) {
                $logger->critical($ex->getMessage());
            }

            try {
                if ($value3 !== null) {
                    $event->setValue3($value3);
                }
            } catch (\Throwable $ex) {
                $logger->critical($ex->getMessage());
            }

            /**  */
            $message .= 'TYPE:' . $event->getType();
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
                $message = 'Last event not found in last ' .  $sensor->getWriteForceEveryXseconds() . ' sec : Created ID:' . $event->getId();
            } else {
                //if ($lastEvent->getValue() !== $event->getValue()) {
                $needUpdate = true;
                //$event->getSensor() !== null && $event->getSensor()->getSupportEvents() &&
                $_diff = $event->diff($lastEvent, true);
                $_th = $event->calculateThreshHold();
                if (!$event->needUpdate($lastEvent)) {
                    $needUpdate = false;
                    $message .= ' --- CANCEL ' . ' TH_USED:' . $_th . '. DIFF:' . $_diff. ' .';
                } else {
                    $message .= ' --- ACCEPT ';
                }
                $message .= $event->getNote();

                $tDiff = $lastEvent->getCreatedAt()->diff($event->getCreatedAt());
                $seconds = (int)$tDiff->i*60 + (int)$tDiff->s;
                if ($seconds <= 60) {
                    $message = 'Ignore diff less then 1 min [' . (string)$lastEvent . '] ETA: '. (60 - $seconds);
                } else if ($needUpdate){
                    if ($sensor !== null) {
                        $sensor->setLastEvent($event);
                    }
                    $entityManager->detach($lastEvent);
                    $entityManager->persist($event);
                    $entityManager->flush();
                    $status = 301;
                    $message .= 'Created ID:' . $event->getId();
                }
            }
            if ($automatic) {
                if ($sensor !== null) {
                    $message = '['. $sensor->getUniqId() . '] : ' . $message;
                }
                if ($plant !== null) {
                    $message = $plant->getUniqId() . ' :  ' . $message;
                }
                //$logger->warning('WARNING:' . $message);
                $logger->notice($message);
                //$logger->info('INFO:' . $message);

                return new Response($message, $status);
            }
            return $this->redirectToRoute('events_index');
            //return $this->redirectToRoute('events_event_temperature_index');
        }

        if ($automatic && $form->isSubmitted() && !$form->isValid()) {

            if ($eventFound) {
                $errors = $form->getErrors();

                if (count($errors) > 0) {
                    /** @var FormError $error */
                    $error = $errors[0];
                    $message = 'ERROR in event_new: submited form:' . $error->getMessage() . '-'. print_r($error->getMessageParameters(), true);
                    $logger->critical($message);
                } else {
                    $message = 'ERROR in event_new - no error found';
                    $logger->critical($message);
                }
            } else {
                if ($message === '') {
                    $message = 'Event not found';
                }
            }
            return new Response($message, $status);
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
            $tmpType = $request->request->get('event')['type'];
            if ($tmpType !== null && get_class($event) !== $tmpType) {
                $event->setType($request->request->get('event')['type']);
                /** @var Event $event */
                $event = $event->castAs($request->request->get('event')['type']);
            }
        } catch (\Throwable $ex) {}
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
            $refer_page = $request->request->get('refer_page');
            if ($refer_page !== '') {
                return $this->redirect($refer_page);
            }
        }

        return $this->redirectToRoute('events_index');
    }
}
