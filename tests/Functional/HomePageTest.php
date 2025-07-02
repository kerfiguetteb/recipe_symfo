<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    // Test fonctionnel basique de la page d'accueil
    public function testSomething(): void
    {
        $client = static::createClient();         // Création d'un client HTTP simulé
        $crawler = $client->request('GET', '/');  // Requête GET vers la racine (page d'accueil)

        $this->assertResponseIsSuccessful();      // Vérifie que la réponse HTTP est un succès (status 200)

        // Recherche un bouton avec les classes CSS 'btn btn-primary btn-lgz'
        $button = $crawler->filter('.btn.btn-primary.btn-lgz');
        $this->assertEquals(1, count($button));   // Vérifie qu'il y a exactement un bouton correspondant

        // Recherche les éléments avec la classe 'card' dans un conteneur avec la classe 'recipes'
        $recipes = $crawler->filter('.recipes .card');
        $this->assertEquals(3, count($recipes));  // Vérifie qu'il y a exactement 3 recettes affichées

        // Vérifie que la balise <h1> contient le texte "Bienvenue sur SymRecipe"
        $this->assertSelectorTextContains('h1', 'Bienvenue sur SymRecipe');
    }
}
