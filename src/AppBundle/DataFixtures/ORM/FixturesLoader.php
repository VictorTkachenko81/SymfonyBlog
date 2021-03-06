<?php

/**
 * @link http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html
 * @link https://github.com/nelmio/alice
 * @link https://github.com/fzaninotto/Faker
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load($this->getFixtures(), $manager, ['providers' => [$this]]);
    }

    public function getFixtures()
    {
        $kernel = $GLOBALS['kernel'];
        $env = $kernel->getEnvironment();

        echo "\nEnvironment is: " . $env . "\n\n";

        if ($env == 'test') {
            return [
                __DIR__.'/test/role.yml',
                __DIR__.'/test/tag.yml',
                __DIR__.'/test/category.yml',
                __DIR__.'/test/user.yml',
                __DIR__.'/test/article.yml',
                __DIR__.'/test/comment.yml',
            ];
        }
        return [
            __DIR__.'/dev/role.yml',
            __DIR__.'/dev/tag.yml',
            __DIR__.'/dev/category.yml',
            __DIR__.'/dev/user.yml',
            __DIR__.'/dev/article.yml',
            __DIR__.'/dev/comment.yml',
        ];
    }

    public function role($count)
    {
        $roles = [
            'ROLE_ADMIN',
            'ROLE_MODERATOR',
            'ROLE_USER',
        ];

        return $roles[$count-1];
    }

    public function picture()
    {
        $picture = [
            'balloon',
            'buildings',
            'people',
        ];

        return $picture[array_rand($picture)];
    }

    public function photo()
    {
        $photo = [
            'user1.png',
            'user2.png',
            'user3.png',
            'user4.png',
            'user5.png',
        ];

        return $photo[array_rand($photo)];
    }
}