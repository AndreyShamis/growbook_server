<?php

namespace App\Controller;

use App\Entity\NodeCommand;
use App\Form\NodeCommandType;
use App\Repository\NodeCommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/node/command")
 */
class NodeCommandController extends AbstractController
{
    /**
     * @Route("/", name="node_command_index", methods={"GET"})
     */
    public function index(NodeCommandRepository $nodeCommandRepository): Response
    {
        return $this->render('node_command/index.html.twig', [
            'node_commands' => $nodeCommandRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="node_command_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $nodeCommand = new NodeCommand();
        $form = $this->createForm(NodeCommandType::class, $nodeCommand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nodeCommand);
            $entityManager->flush();

            return $this->redirectToRoute('node_command_index');
        }

        return $this->render('node_command/new.html.twig', [
            'node_command' => $nodeCommand,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="node_command_show", methods={"GET"})
     */
    public function show(NodeCommand $nodeCommand): Response
    {
        return $this->render('node_command/show.html.twig', [
            'node_command' => $nodeCommand,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="node_command_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, NodeCommand $nodeCommand): Response
    {
        $form = $this->createForm(NodeCommandType::class, $nodeCommand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('node_command_index', [
                'id' => $nodeCommand->getId(),
            ]);
        }

        return $this->render('node_command/edit.html.twig', [
            'node_command' => $nodeCommand,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="node_command_delete", methods={"DELETE"})
     */
    public function delete(Request $request, NodeCommand $nodeCommand): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nodeCommand->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nodeCommand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('node_command_index');
    }
}
