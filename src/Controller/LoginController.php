<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{

    /**
     * @Route("/inscription", name="app_registration")
     */
    public function registration(Request             $request, ManagerRegistry $managerRegistry,
                                 UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $managerRegistry->getManager();
            //Hasher le mot de passe
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setRole("ROLE_USER");


            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('login/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function index(): Response
    {
        return $this->render('login/index.html.twig', []);
    }

    /**
     * @Route("/secure-area", name="secure-area")
     */
    public function indexAction()
    {

        if($this->getUser()->getRoles() == ['ROLE_ADMIN'])
            return $this->redirect($this->generateUrl('app_admin'));
        elseif($this->getUser()->getRoles() == ['ROLE_USER'])
            return $this->redirect($this->generateUrl('app_produit'));
        throw new \Exception(AccessDeniedException::class);
    }
}
