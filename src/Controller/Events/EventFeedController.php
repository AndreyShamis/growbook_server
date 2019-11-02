<?php

namespace App\Controller\Events;

use App\Entity\Event;
use App\Entity\Events\EventFeed;
use App\Entity\FeedFertilizer;
use App\Form\Events\EventFeedType;
use App\Repository\Events\EventFeedRepository;
use App\Repository\FeedFertilizerRepository;
use App\Repository\FertilizerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cloneFeed(EventFeed $feed)
    {
        $eventFeed = new EventFeed();
        $eventFeed->cloneSelf($feed);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($eventFeed);
        $entityManager->flush();
        return $this->redirectToRoute('events_event_feed_edit', ['id' => $eventFeed->getId()]);
    }

    /**
     * @Route("/new", name="events_event_feed_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request, FertilizerRepository $fertiRepo): Response
    {
        $eventFeed = new EventFeed();
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

            return $this->redirectToRoute('events_event_feed_index');
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
     */
    public function edit(Request $request, EventFeed $eventFeed, FertilizerRepository $fertiRepo, FeedFertilizerRepository $ffRepo): Response
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
