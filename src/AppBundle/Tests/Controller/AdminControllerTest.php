<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 17.01.16
 * Time: 17:58
 */

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testIndex(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Statistic', $crawler->filter('h2')->text());
    }

    public function testArticles(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/articles');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Articles', $crawler->filter('h2')->text());
        $this->assertEquals(2, $crawler->filter('tr')->count());
    }

    public function testTags(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/tags');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Tags', $crawler->filter('h2')->text());
        $this->assertEquals(3, $crawler->filter('tr')->count());
    }

    public function testComments(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/comments');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Comments', $crawler->filter('h2')->text());
        $this->assertEquals(3, $crawler->filter('tr')->count());
    }

    public function testUsers(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Users', $crawler->filter('h2')->text());
        $this->assertEquals(2, $crawler->filter('tr')->count());
    }

    public function testUsersDelete(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/user/delete/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('You want to delete user "User name" (id: 1). Related records: articles (count: 1), comments (count: 1). You must to delete related records before.',
            $crawler->filter('.alert')->text());
    }

    public function testRoles(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/roles');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Roles', $crawler->filter('h2')->text());
        $this->assertEquals(2, $crawler->filter('tr')->count());
    }

    public function testRolesDelete(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/role/delete/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('You want to delete role "admin" (id: 1). Related records: users (count: 1). You must to delete related records before.',
            $crawler->filter('.alert')->text());
    }
}