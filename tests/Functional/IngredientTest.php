<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{
    // Test la création réussie d'un nouvel ingrédient
    public function testIfCreateIngredientIsSuccessfull(): void
    {
        $client = static::createClient(); // Création du client HTTP pour simuler la requête

        $urlGenerator = $client->getContainer()->get('router'); // Récupère le générateur d'URL

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager'); // Récupère l'entity manager pour interagir avec la BDD

        $user = $entityManager->find(User::class, 1); // Récupère l'utilisateur avec l'ID 1

        $client->loginUser($user); // Simule la connexion de cet utilisateur

        // Requête GET vers la page de création d'ingrédient
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

        $uniqueName = 'Un ingrédient ' . uniqid(); // Génère un nom unique pour l'ingrédient (évite les doublons)

        // Récupération du formulaire d'ingrédient et remplissage des champs
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => $uniqueName,
            'ingredient[price]' => floatval(33)
        ]);

        $client->submit($form); // Soumission du formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Vérifie qu'il y a une redirection (succès)

        $client->followRedirect(); // Suit la redirection

        // Vérifie que la page affiche un message de succès de création
        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été créé avec succès !');

        $this->assertRouteSame('ingredient.index'); // Vérifie que l'on est bien redirigé vers la liste des ingrédients
    }

    // Test l'affichage réussi de la liste des ingrédients
    public function testIfListingredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user); // Connexion de l'utilisateur

        // Requête GET vers la page listant les ingrédients
        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        $this->assertResponseIsSuccessful(); // Vérifie que la réponse HTTP est 200

        $this->assertRouteSame('ingredient.index'); // Vérifie que la route est bien celle de la liste des ingrédients
    }

    // Test la mise à jour réussie d'un ingrédient existant
    public function testIfUpdateAnIngredientIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        // Recherche un ingrédient lié à cet utilisateur
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        // Requête GET vers la page d'édition de l'ingrédient ciblé
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful(); // Vérifie que la page est accessible

        // Remplit le formulaire d'édition avec de nouvelles valeurs
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Un ingrédient 2",
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form); // Soumission du formulaire

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Vérifie la redirection

        $client->followRedirect();

        // Vérifie que la page affiche un message de succès pour la modification
        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été modifié avec succès !');

        $this->assertRouteSame('ingredient.index'); // Vérifie la redirection vers la liste des ingrédients
    }

    // Test la suppression réussie d'un ingrédient
    public function testIfDeleteAnIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        // Recherche un ingrédient lié à cet utilisateur
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        // Envoie une requête GET vers la route de suppression de l'ingrédient
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND); // Vérifie la redirection après suppression

        $client->followRedirect();

        // Vérifie que la page affiche un message de succès pour la suppression
        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été supprimé avec succès !');

        $this->assertRouteSame('ingredient.index'); // Vérifie la redirection vers la liste des ingrédients
    }
}
