<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{
    // Teste que la connexion avec des identifiants valides réussit correctement
    public function testIfLoginIsSuccessful(): void
    {
        $client = static::createClient();  // Crée un client HTTP pour simuler les requêtes

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router"); // Récupère le générateur d'URL

        // Envoie une requête GET vers la page de login
        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        // Récupère le formulaire de login et le remplit avec un username et un password valides
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.fr",
            "_password" => "password"
        ]);

        $client->submit($form);  // Soumet le formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Vérifie que la réponse est une redirection (302)

        $client->followRedirect(); // Suit la redirection (généralement vers la page d'accueil)

        $this->assertRouteSame('home.index'); // Vérifie que la route atteinte est bien la page d'accueil
    }

    // Teste que la connexion échoue si le mot de passe est incorrect
    public function testIfLoginFailedWhenPasswordIsWrong(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        // Requête GET vers la page de login
        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        // Remplit le formulaire de login avec un mot de passe incorrect
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@symrecipe.fr",
            "_password" => "password_"
        ]);

        $client->submit($form); // Soumet le formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Vérifie la redirection après soumission

        $client->followRedirect(); // Suit la redirection (probablement vers la page de login)

        $this->assertRouteSame('security.login'); // Vérifie que la route est toujours celle de login (échec de connexion)

        // Vérifie que la page contient un message d'erreur indiquant des identifiants invalides
        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials.");
    }
}
