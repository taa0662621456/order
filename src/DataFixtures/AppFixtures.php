<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\User\UserFactory;
use App\Factory\Vendor\VendorFactory;
use App\Factory\Product\ProductFactory;
use App\Factory\Project\ProjectFactory;
use App\Factory\Review\ReviewFactory;
use App\Factory\Message\MessageFactory;
use App\Factory\Payment\PaymentFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Создаём администратора
        UserFactory::new()->create([
            'email' => 'admin@example.com',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'admin123',
        ]);

        // Несколько пользователей
        UserFactory::createMany(20);

        // Вендоры
        VendorFactory::createMany(5);

        // Продукты
        ProductFactory::createMany(50);

        // Проекты
        ProjectFactory::createMany(10);

        // Отзывы
        ReviewFactory::createMany(100);

        // Сообщения
        MessageFactory::createMany(30);

        // Платежи
        PaymentFactory::createMany(20);

        $manager->flush();
    }
}
