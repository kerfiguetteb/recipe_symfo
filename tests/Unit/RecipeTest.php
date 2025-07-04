<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    // Crée et retourne une instance valide de l'entité Recipe
    public function getEntity(): Recipe
    {
        return (new Recipe())
            ->setName('Recipe #1')
            ->setDescription('Description #1')
            ->setIsFavorite(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }
    // Teste si une entité Recipe valide passe la validation sans erreurs
    public function testEntityIsValid(): void
    {
        // Démarre le kernel Symfony pour les tests
        self::bootKernel();
        $container = static::getContainer();
        // récuperation de l'entity Recipe valide
        $recipe = $this->getEntity();
        // validation de l'entity
        $errors = $container->get('validator')->validate($recipe);
        // Vérifie qu'il n'y a aucune erreur de validation
        $this->assertCount(0, $errors);
    }
    // Teste que la validation échoue si le nom de la recette est vide
    public function testInvalidName()
    {
        self::bootKernel();
        $container = static::getContainer();

        $recipe = $this->getEntity();
        $recipe->setName('');

        $errors = $container->get('validator')->validate($recipe);
        $this->assertCount(2, $errors);
    }
    // Teste la méthode getAverage() de la recette, qui calcule la moyenne des notes
    public function testGetAverage()
    {
        $recipe = $this->getEntity();
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

        for ($i = 0; $i < 5; $i++) {
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);

            $recipe->addMark($mark);
        }

        $this->assertTrue(2.0 === $recipe->getAverage());
    }
}
