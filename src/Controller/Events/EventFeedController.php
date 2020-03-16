<?php

namespace App\Controller\Events;

use App\Entity\Events\EventFeed;
use App\Entity\FeedFertilizer;
use App\Entity\Plant;
use App\Form\Events\EventFeedType;
use App\Model\PlantInterface;
use App\Repository\Events\EventFeedRepository;
use App\Repository\FeedFertilizerRepository;
use App\Repository\FertilizerRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/events/event/feed")
 */
class EventFeedController extends AbstractController
{
    /**
     * @Route("/", name="events_event_feed_index", methods={"GET"})
     * @param EventFeedRepository $eventRepository
     * @return Response
     */
    public function index(EventFeedRepository $eventRepository): Response
    {
        return $this->render('events/event_feed/index.html.twig', [
            'event_feeds' => $eventRepository->findBy([], ['happenedAt' => 'DESC']),
        ]);
    }

    /**
     * @Route("/clone/{feed}", name="clone_feed_event", methods={"GET","POST"})
     * @param EventFeed $feed
     * @param LoggerInterface $logger
     * @return RedirectResponse
     */
    public function cloneFeed(EventFeed $feed, LoggerInterface $logger)
    {
        $eventFeed = new EventFeed();
        $eventFeed->cloneSelf($feed);
        try {
            $plant = $eventFeed->getPlant();
            if ($plant !== null) {
                $eventFeed->setHappenedAt($plant->getLastHydrometerPeak());
            }
        } catch (\Throwable $ex) {
            $logger->critical($ex->getMessage());
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($eventFeed);
        $entityManager->flush();
        return $this->redirectToRoute('events_event_feed_edit', ['id' => $eventFeed->getId()]);
    }

    /**
     * @Route("/new", name="events_event_feed_new", methods={"GET","POST"})
     * @Route("/new/{plant}", name="events_event_feed_new_with_plant", methods={"GET","POST"})
     * @param Request $request
     * @param FertilizerRepository $fertiRepo
     * @param Plant $plant
     * @return Response
     */
    public function new(Request $request, FertilizerRepository $fertiRepo, Plant $plant = null): Response
    {
        $eventFeed = new EventFeed();
        if ($plant !== null) {
            $eventFeed->setPlant($plant);
            $eventFeed->setHappenedAt($plant->getLastHydrometerPeak());
        }
        $r = $request->request->all();
        $eventFeed->setType(EventFeed::class);
        $form = $this->createForm(EventFeedType::class, $eventFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if (array_key_exists('FertilizerType', $r) && array_key_exists('FertilizerAmount', $r)) {
                foreach ($r['FertilizerType'] as $key => $value) {
                    $ferti = $fertiRepo->findOneBy(['id' => $value]);
                    if ($ferti !== null) {
                        $newFertiFeed = new FeedFertilizer();
                        $newFertiFeed->setAmount($r['FertilizerAmount'][$key]);
                        $newFertiFeed->setFertilizer($ferti);
                        $eventFeed->addFertilizer($newFertiFeed);
                        $entityManager->persist($newFertiFeed);
                    } else {

                    }

                }
            }

            $entityManager->persist($eventFeed);
            $entityManager->flush();
            return $this->redirectToRoute('plant_show', [
                'id' => $eventFeed->getPlant()->getId(),
            ]);
        }

        return $this->render('events/event_feed/new.html.twig', [
            'event_feed' => $eventFeed,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_feed_show", methods={"GET"})
     * @param EventFeed $eventFeed
     * @return Response
     */
    public function show(EventFeed $eventFeed): Response
    {
        return $this->render('events/event_feed/show.html.twig', [
            'event_feed' => $eventFeed,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="events_event_feed_edit", methods={"GET","POST"})
     * @param Request $request
     * @param EventFeed $eventFeed
     * @param FertilizerRepository $fertiRepo
     * @param FeedFertilizerRepository $ffRepo
     * @param LoggerInterface $logger
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(Request $request, EventFeed $eventFeed, FertilizerRepository $fertiRepo, FeedFertilizerRepository $ffRepo, LoggerInterface $logger): Response
    {
        $form = $this->createForm(EventFeedType::class, $eventFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $r = $request->request->all();
            $entityManager = $this->getDoctrine()->getManager();
            if (array_key_exists('FertilizerType', $r) && array_key_exists('FertilizerAmount', $r)) {
                foreach ($r['FertilizerType'] as $key => $value) {
                    $ferti = $fertiRepo->findOneBy(['id' => $value]);
                    if ($ferti !== null) {
                        $newFertiFeed = $ffRepo->findOrCreate([
                            'fertilizer' => $ferti,
                            'event' => $eventFeed
                        ]);
                        if ($newFertiFeed !== null) {
                            $newFertiFeed->setAmount((float)$r['FertilizerAmount'][$key]);
                            $eventFeed->addFertilizer($newFertiFeed);
                            $entityManager->persist($newFertiFeed);
                        } else {

                        }

                    } else {

                    }

                }
            }

            try {
                $fertis = $eventFeed->getFertilizers();
                foreach ($fertis as $ferti) {
                    if ($ferti->getAmount() < 0.000001) {
                        $eventFeed->removeFertilizer($ferti);
                        $entityManager->remove($ferti);
                    }
                }
            } catch (\Throwable $ex) {
                $logger->critical($ex->getMessage());
            }


            $entityManager->flush();

            $refer_page = $request->request->get('refer_page');
            if ($refer_page !== '') {
                return $this->redirect($refer_page);
            }

            return $this->redirectToRoute('events_event_feed_index', [
                'id' => $eventFeed->getId(),
            ]);
        }

        return $this->render('events/event_feed/edit.html.twig', [
            'event_feed' => $eventFeed,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="events_event_feed_delete", methods={"DELETE"})
     * @param Request $request
     * @param EventFeed $eventFeed
     * @return Response
     */
    public function delete(Request $request, EventFeed $eventFeed): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventFeed->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $fertis = $eventFeed->getFertilizers();
            if ($fertis !== null) {
                foreach ($fertis as $ferti) {
                    $entityManager->remove($ferti);
                }
            }
            $entityManager->remove($eventFeed);
            $entityManager->flush();
        }

        return $this->redirectToRoute('events_event_feed_index');
    }
}
