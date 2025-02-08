<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Event\ContactRequestEvent;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        $contact = new ContactDTO();
        $contact->content = 'super';
        $contact->name = 'John Doe';
        $contact->email = 'john@doe.com';

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher->dispatch(new ContactRequestEvent($contact));
                $this->addFlash('success', 'Message envoyé.');
                return $this->redirectToRoute('contact');
            } catch (\Exception $exception) {
                $this->addFlash('danger', "Impossible d'envoyer une demande de contact.");
            }
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }
}
