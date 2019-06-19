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
     * @Route("/cli/{plant_uniq_id}", name="cli_post", methods={"POST"})
     * @Route("/cli/{plant_uniq_id}/{property}/{value}", name="cli", methods={"GET","POST"})
     * @param Request $request
     * @param PlantRepository $plants
     * @param string $plant_uniq_id
     * @param string $property
     * @param string $value
     * @param CustomFieldRepository $fieldsRepo
     * @param LoggerInterface $logger
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function cli(Request $request, PlantRepository $plants, string $plant_uniq_id, string $property, string $value, CustomFieldRepository $fieldsRepo, LoggerInterface $logger): Response
    {
        $message = '';
        $status = 200;
        $plant = $plants->findOrCreate([
            'name' => RandomName::getRandomTerm() . '__' . $plant_uniq_id,
            'uniqId' => $plant_uniq_id,
        ]);
        $input = $request->request->all();
        if (array_key_exists('key', $input) && array_key_exists('value', $input)) {
            $property = $input['key'];
            $value = $input['value'];
        } else {
            $message = 'Bad input';
            $status = 400;
            $plant = null;
        }

        if ($plant === null) {
            $message = 'Plant not found';
            $status = 404;
        } else {
            $get_func_name = $this->findSetter($plant, $property);
            $em = $this->getDoctrine()->getManager();
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
            if (method_exists($plant, $get_func_name)) {
                call_user_func(array($plant, $get_func_name), $value);
                try {
//                    $plant->addProperty($field);
                    $em->persist($plant);
//                    $entityManager->persist($field);
                    $em->flush();
                    $message = 'Updated:'. $property . '=' . $value;
                } catch (\Throwable $ex) {
                    $message = $ex->getMessage();
                }
            } else {
                $message = 'Method for property:[' . $property . '] not found';
                $status = 405;
            }
        }
        $message = $plant_uniq_id . ' : ' . $message;

        if ($status === 400 || $status === 404) {
            $logger->critical($message);
        } else {
            $logger->notice($message);
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
