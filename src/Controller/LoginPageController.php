<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;

use Doctrine\Persistence\ManagerRegistry;


class LoginPageController extends AbstractController
{
    public function index(AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('index/login.html.twig', [
        'last_username' => $lastUsername,
        'error'         => $error,
        ]);
    }

    
    // This function created to add user with password manual 
    public function createUser(AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine) : Response 
    {
        $entityManager = $doctrine->getManager();

        $user = new User();

        $plaintextPassword = 'user';

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setUsername('user');
      
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response(
            '<html><body> User has been created successfully </body></html>'
        );
        
    }
}
