<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Entity\Sensor;
use App\Form\PlantType;
use App\Repository\EventRepository;
use App\Repository\PlantRepository;
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
    public function show(Plant $plant, EventRepository $eventsRepo, int $hours=84): Response
    {
        $events = array();
        try {
            if ($hours < 0) {
                $hours = 84;
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
        return $this->render('plant/show.html.twig', [
            'plant' => $plant,
//            'events' => $events,
            'sensors' => $sensors,
            'sensorsObj' => $sensorsObj,
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
}
