<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: "home")]
    function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher) : Response
    {
//        $user = new User();
//        $user
//            ->setUsername('John Doe2')
//            ->setEmail('john2@doe.com')
//            ->setPassword($hasher->hashPassword($user, '0000'))
//            ->setRoles([]);
//        ;
//        $entityManager->persist($user);
//        $entityManager->flush();
        return $this->render('home/index.html.twig');
    }
}
