<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/users")
 */
class UserController extends AbstractController
{
    /** @var \Doctrine\Common\Persistence\ObjectManager  */
    protected $em;

    /**
     * @param Container $container
     * @throws \LogicException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $this->getDoctrine()->getManager();

    }

    /**
     * @Route("/", name="user_index")
     * @param UserRepository $userRepo
     * @return Response
     */
    public function index(UserRepository $userRepo): Response
    {
        return $this->render('/user/index.html.twig', ['users' => $userRepo->findAll()]);
    }

    /**
     * Finds and displays a Users  entity.
     *
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $obj
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(User $obj): Response
    {
        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('view', $obj);
        return $this->render('/user/show.html.twig', array(
            'user' => $obj,
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param User $obj
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \LogicException
     */
    public function edit(Request $request, User $obj, UserPasswordEncoderInterface $passwordEncoder)
    {
        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('edit', $obj);
        $current_user= $this->get('security.token_storage')->getToken()->getUser();
        $can_change_permissions = $this->isGranted('ROLE_ADMIN');
        $editForm = $this->get('form.factory')->create(UserType::class, $obj, array(
            'edit_enabled' => true,
            'current_user' => $current_user,
            'can_change_permissions' => $can_change_permissions,
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /** @var User $edited_user */
            $edited_user = $editForm->getData();
            if ($edited_user->isLdapUser() === false && $edited_user->getPlainPassword() !== null && \strlen($edited_user->getPlainPassword()) > 2) {
                $password = $passwordEncoder->encodePassword($edited_user, $edited_user->getPlainPassword());
                $edited_user->setPassword($password);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', array('id' => $obj->getId()));
        }

        return $this->render('/user/edit.html.twig', array(
            'user' => $obj,
            'edit_form' => $editForm->createView(),
        ));
    }
}
