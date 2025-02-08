<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $contact = $event->contactDTO;
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
        $this->mailer->send($email);
    }

    public function onUserLoginSendEmail(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User)
            return;
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject('Connexion')
            ->text('Vous vous êtes connecté.');
        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onUserLoginSendEmail'
        ];
    }
}
