<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
    )
    {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->getPayload()->getString('username');

        // Sauvegarde en session le user tapé à la maine pour le reafficher dans le formulaire de connexion si le mdp est incorrect
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            // Tampon 1 : Récupérer un user depuis la BDD en utilisant le $username grace à un callable personnalisé (user email or user name)
            new UserBadge($username, fn(string $identifier) => $this->userRepository->findUserByEmailOrUsername($identifier)),
            // Tampon 2 : Va régarder si le mdp sauvegardé pour le user trouvé au niveau du TAMPON 1 correspond au "mdp" tappé dans le formulaire de connexion
            new PasswordCredentials($request->getPayload()->getString('password')),
            // Les tampons optionels :
            [
                # Tampon CSRF : Dde au système de valider le token csrf récupéré depuis le formulaire
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                # Tampon Cookie
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Rediriger vers la page principale après connexion
        return new RedirectResponse('/');
//        return new RedirectResponse($this->urlGenerator->generate('admin.recipe.index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
