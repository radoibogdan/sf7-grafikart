<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $ingredients = array_map(fn(string $name) => (new Ingredient)
            ->setName($name)
            ->setSlug(strtolower($this->slugger->slug($name))), [
            'chutney',
            'hot sauce',
            'cayenne pepper',
            'apple butter',
            'ketchup',
            'acorn squash',
            'pink beans',
            'olive oil',
            'noodle',
            'rice',
            'red pepper flake',
            'applesauce',
            'pink beans',
            'chicory',
            'ginger ale',
            'pumpkin',
            'split pea',
            'oats',
            'avocado',
            'chipotle pepper',
            'tuber',
            'caramel',
        ]);

        $units = ['g', 'kg', 'l', 'ml', 'cl', 'dl', 'c. à soupe', 'c. à café', 'pincée', 'verre'];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categories = ['Plat chaud', 'Dessert', 'Entrée', 'Goûter'];
        foreach ($categories as $categoryName) {
            $category = (new Category())
                ->setName($categoryName)
                ->setSlug($this->slugger->slug($categoryName))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($category);
            // Rajoute une reference vers l'objet categorie
            $this->addReference($categoryName, $category);
        }

        for ($i = 0; $i <= 10; $i++) {
            $title = $faker->foodName();
            $recipe = (new Recipe())
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(5, true))
                ->setDuration($faker->numberBetween(1,60))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class)) // récupère la bonne référence
                ->setUser($this->getReference('USER'.$faker->numberBetween(1,10), User::class))
            ;

            foreach ($faker->randomElements($ingredients, $faker->numberBetween(2,5)) as $ingredient) {
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->numberBetween(1, 100))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient)
                );
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Les RecipesFixtures depend de UserFixtures. Les UserFixtures seront créés avant.
        return [UserFixtures::class];
    }
}
