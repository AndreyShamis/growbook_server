<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Form\SensorType;
use App\Repository\EventRepository;
use App\Repository\SensorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sensors")
 */
class SensorController extends AbstractController
{
    /**
     * @Route("/", name="sensor_index", methods={"GET"})
     */
    public function index(SensorRepository $sensorRepository): Response
    {
        return $this->render('sensor/index.html.twig', [
            'sensors' => $sensorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sensor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sensor = new Sensor();
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sensor);
            $entityManager->flush();

            return $this->redirectToRoute('sensor_index');
        }

        return $this->render('sensor/new.html.twig', [
            'sensor' => $sensor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sensor_show", methods={"GET"})
     * @Route("/{id}/hours/{hours}", name="sensor_show_hours", methods={"GET"})
     * @param Sensor $sensor
     * @param EventRepository $eventsRepo
     * @param int $hours
     * @return Response
     */
    public function show(Sensor $sensor, EventRepository $eventsRepo, int $hours=25): Response
    {
        $events = array();
        try {
            if ($hours < 0) {
                $hours = 25;
            }
            $events = $eventsRepo->findAllBySensor($sensor, $hours);
        } catch (\Throwable $ex) {

        }
        return $this->render('sensor/show.html.twig', [
            'sensor' => $sensor,
            'events' => $events,
            'hours' => $hours,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sensor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sensor $sensor): Response
    {
        $form = $this->createForm(SensorType::class, $sensor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $refer_page = $request->request->get('refer_page');
            if ($refer_page !== '') {
                return $this->redirect($refer_page);
            }

            return $this->redirectToRoute('sensor_index', [
                'id' => $sensor->getId(),
            ]);
        }

        return $this->render('sensor/edit.html.twig', [
            'sensor' => $sensor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sensor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sensor $sensor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sensor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sensor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sensor_index');
    }
}
