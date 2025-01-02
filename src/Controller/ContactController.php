<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contact = new ContactDTO();
        $contact->content = 'super';
        $contact->name = 'John Doe';
        $contact->email = 'john@doe.com';

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /*
                    Use Inky for nice templates
                    https://symfony.com/doc/current/mailer.html#inky-email-templating-language
                */
                $email = (new TemplatedEmail())
                    ->from($contact->email)
                    ->to($contact->service)
                    ->subject('Demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['contact' => $contact]);
                $mailer->send($email);
                $this->addFlash('success', 'Mail sent.');
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
