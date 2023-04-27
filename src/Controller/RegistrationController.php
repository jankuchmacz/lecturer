<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user= new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $user->setPassword(
                $passwordEncoder->hashPassword($user, $form->get('password')->getData()));
            $user->setUsername($form->get('username')->getData());
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('home'));
        }
        return $this->render('registration/index.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
