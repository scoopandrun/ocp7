<?php

// Create a data fixture for the User and Customer entities
// Path: src/DataFixtures/UserFixture.php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private array $companyNames = [];
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Customers
        foreach ($this->generateCustomers() as $customer) {
            $manager->persist($customer);
        }

        // Users
        foreach ($this->generateUsers() as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return \Generator<Customer>
     */
    private function generateCustomers(): \Generator
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $customer = (new Customer())
                ->setName($faker->company)
                ->setCanUseApi($faker->boolean);

            $this->addReference("customer-" . $customer->getName(), $customer);

            array_push($this->companyNames, $customer->getName());

            yield $customer;
        }
    }

    /**
     * @return \Generator<User>
     */
    private function generateUsers(): \Generator
    {
        $faker = Faker\Factory::create();

        // Persistant users for testing
        $user1 = (new User())
            ->setEmail("user1@example.com")
            ->setCompany($this->getReference("customer-" . $this->companyNames[0]));
        $user1->setPassword($this->passwordHasher->hashPassword($user1, "password"));

        yield $user1;

        $user2 = (new User())
            ->setEmail("user2@example.com")
            ->setCompany($this->getReference("customer-" . $this->companyNames[1]));
        $user2->setPassword($this->passwordHasher->hashPassword($user2, "password"));

        yield $user2;

        foreach ($this->companyNames as $companyName) {
            for ($i = 0; $i < 15; $i++) {
                $user = (new User())
                    ->setEmail($faker->email)
                    ->setCompany($this->getReference("customer-" . $companyName));
                $user->setPassword($this->passwordHasher->hashPassword($user, $faker->password));

                yield $user;
            }
        }
    }
}
