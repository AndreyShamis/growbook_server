<?php

namespace App\Controller;

use App\Entity\CustomField;
use App\Entity\Plant;
use App\Entity\Sensor;
use App\Form\PlantType;
use App\Repository\CustomFieldRepository;
use App\Repository\EventRepository;
use App\Repository\PlantRepository;
use App\Utils\RandomName;
use Psr\Log\LoggerInterface;
use SplObjectStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plant")
 */
class PlantController extends AbstractController
{
    /**
     * @Route("/", name="plant_index", methods={"GET"})
     */
    public function index(PlantRepository $plantRepository): Response
    {
        return $this->render('plant/index.html.twig', [
            'plants' => $plantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="plant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $plant = new Plant();
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plant);
            $entityManager->flush();

            return $this->redirectToRoute('plant_index');
        }

        return $this->render('plant/new.html.twig', [
            'plant' => $plant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plant_show", methods={"GET"})
     * @Route("/{id}/hours/{hours}", name="plant_show_hours", methods={"GET"})
     * @param Plant $plant
     * @param EventRepository $eventsRepo
     * @param int $hours
     * @return Response
     */
    public function show(Plant $plant, EventRepository $eventsRepo, CustomFieldRepository $fields, int $hours=25): Response
    {
        $events = array();
        try {
            if ($hours < 0) {
                $hours = 25;
            }
            $events = $eventsRepo->findAllByPlant($plant, $hours);
        } catch (\Throwable $ex) {

        }
        $sensors = array();
        $sensorsObj = array();
        foreach ($events as $event) {
            /** @var Sensor $sensor */
            $sensor = $event->getSensor();
            if ($sensor !== null) {
                $id = $sensor->getId();
                if (array_key_exists($id, $sensors)) {
                    $sensors[$id][] = $event;
                } else {
                    $sensors[$id] = array();
                    $sensors[$id][] = $event;
                    $sensorsObj[$id] = $sensor;
                }
            }
        }
        $prop = $plant->getProperties();

        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
//            'events' => $events,
            'sensors' => $sensors,
            'hours' => $hours,
            'sensorsObj' => $sensorsObj,
            'uptime' => $fields->findForObject($plant, 'uptime'),
            'temperature' => $fields->findForObject($plant, 'temperature'),
            //'temperature' => $prop->get('temperature'),
            'humidity' => $fields->findForObject($plant, 'humidity'),
            'light' => $fields->findForObject($plant, 'light'),
            'hydrometer' => $fields->findForObject($plant, 'hydrometer'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plant $plant): Response
    {
        $form = $this->createForm(PlantType::class, $plant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plant_index', [
                'id' => $plant->getId(),
            ]);
        }

        return $this->render('plant/edit.html.twig', [
            'plant' => $plant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Plant $plant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plant_index');
    }

    /**
     * @Route("/cli/{plant_uniq_id}", name="cli_post", methods={"POST", "GET"})
     * @Route("/cli/{plant_uniq_id}/{property}/{value}", name="cli", methods={"GET","POST"})
     * @param Request $request
     * @param PlantRepository $plants
     * @param string $plant_uniq_id
     * @param CustomFieldRepository $fieldsRepo
     * @param LoggerInterface $logger
     * @param \Swift_Mailer $mailer
     * @param string $property
     * @param string $value
     * @return Response
     */
    public function cli(Request $request, PlantRepository $plants, string $plant_uniq_id, CustomFieldRepository $fieldsRepo, LoggerInterface $logger, \Swift_Mailer $mailer, string $property=null, string $value=null): Response
    {
        $message = '';
        $status = 200;
        try {
            //$domain = $this->getParameter('DOMAIN');
            $em = $this->getDoctrine()->getManager();

            $plant = $plants->findOrCreate([
                'name' => RandomName::getRandomTerm() . '__' . $plant_uniq_id,
                'uniqId' => $plant_uniq_id,
            ]);
            if ($plant === null) {
                throw new \Exception('Plant not found', 404);
            }
            $input = $request->request->all();
            if (array_key_exists('key', $input) && array_key_exists('value', $input)) {
                $property = $input['key'];
                $value = $input['value'];
            } else if (count($input) >= 1) {
                $a = print_r($input, true);
                $fields = array();
                foreach ($input as $key => $val) {
                    $field = $fieldsRepo->findOrCreate([
                        'obj' => $plant,
                        'key' => $key
                    ]);
                    $field->setPropertyValue($val);
                    $plant->addProperty($field);
                    $em->persist($field);
                    //
                    $fields[] = $field;
                }
                $em->persist($plant);
                $em->flush();
                $message = count($fields) . ' fields added - ' . $a;
            } else {
                $message = 'Bad input';
                $status = 400;
                $plant = null;
            }

            if ($property !== null) {
                $get_func_name = $this->findSetter($plant, $property);
                /** @var CustomField $field */
                $field = $fieldsRepo->findOrCreate([
                    'obj' => $plant,
                    'key' => $property
                ]);
                $field->setPropertyValue($value);
                $plant->addProperty($field);
                $em->persist($plant);
                $em->persist($field);
                $em->flush();
                $message = 'Updated:'. $property . '=' . $value . '. ';
                if (method_exists($plant, $get_func_name)) {
                    call_user_func(array($plant, $get_func_name), $value);
                    try {
//                    $plant->addProperty($field);
                        $em->persist($plant);
//                    $entityManager->persist($field);
                        $em->flush();

                    } catch (\Throwable $ex) {
                        $message = $ex->getMessage();
                    }
                } else {
                    $message .= 'Method for property:[' . $property . '] not found.';
                    $status = 405;
                }
            }
            $message = $plant_uniq_id . ' : ' . $message;
        } catch (\Throwable $ex) {
            $message = $ex->getMessage();
            if ($ex->getCode() === 0) {
                $status = 500;
            } else {
                $status = $ex->getCode();
            }
        }

        if ($status === 400 || $status === 404) {
            $logger->critical($message);
        } else {
            $logger->notice($message);
        }
        if ($plant !== null && $plant->getLightChanged()) {
            try {
                $domain = $_ENV['DOMAIN'];
                $_time = new \DateTime();
                $message = (new \Swift_Message('[' . $domain. '] Light change detected on [' . $plant->getName() . '] at ' . $_time->format('Y-m-d H:i:s')))
                    ->setFrom('hicam.golda@gmail.com')
                    ->setTo('lolnik@gmail.com')
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'emails/light.changed.html.twig',
                            ['plant' => $plant, 'printTime' => $_time, 'domain' => $domain]
                        ),
                        'text/html'
                    )

                    // you can remove the following code if you don't define a text version for your emails
                    ->addPart(
                        $this->renderView(
                            'emails/registration.txt.twig',
                            ['plant' => $plant]
                        ),
                        'text/plain'
                    )
                ;

                $res = $mailer->send($message);
                $a = print_r($res, true);
            } catch (\Throwable $ex) {
                $logger->critical($ex->getMessage());
            }

        }
        return new Response($message, $status);
    }

    protected function findSetter(Plant $plant, string  $key): string
    {
        $funcs = get_class_methods($plant);
        $tmp_key = mb_strtolower($key);
        $tmp_key = 'set' . str_replace('_', '', $tmp_key);
        foreach ($funcs as $value) {
            if (strpos($value, 'set') === 0) {
                $real_func_name = $value;
                $func_name = mb_strtolower($real_func_name);
                $func_name = str_replace('_', '', $func_name);
                if ($func_name === $tmp_key) {
                    return $real_func_name;
                }
            }
        }
        return '';
    }
}
