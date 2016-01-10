<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $date = new \DateTime('now');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Test title', $crawler->filter('h2 a')->text());
        $this->assertContains('User name', $crawler->filter('.post-author a')->text());
        $this->assertContains('Test category', $crawler->filter('.post-category a')->text());
        $this->assertContains('Test article', $crawler->filter('.excerpt p')->text());
        $this->assertContains('news', $crawler->filter('.post-tags a')->eq(0)->text());
        $this->assertContains('sport', $crawler->filter('.post-tags a')->eq(1)->text());
        $this->assertContains('1 Comments', $crawler->filter('.post-comment a')->text());
        $this->assertContains('Test category (1)', $crawler->filter('.widget .category-list a')->text());
        $this->assertContains('5 Test title', $crawler->filter('.widget .blogposts.popular a')->text());
        $this->assertContains($date->format('M d, Y').' | by User name', $crawler->filter('.widget .blogposts.popular .text-muted')->text());
        $this->assertContains('Test title', $crawler->filter('.widget .blogposts.latest a')->text());
        $this->assertContains($date->format('M d, Y').' | by User name', $crawler->filter('.widget .blogposts.latest .text-muted')->text());
        $this->assertContains('User name', $crawler->filter('.widget .recent-comments .media-heading a')->eq(0)->text());
        $this->assertContains('Test title', $crawler->filter('.widget .recent-comments .media-heading a')->eq(1)->text());
        $this->assertContains('Text comment', $crawler->filter('.widget .recent-comments .text-muted')->text());
        $this->assertContains('news', $crawler->filter('.widget .tag-list a')->eq(0)->text());
        $this->assertContains('sport', $crawler->filter('.widget .tag-list a')->eq(1)->text());
        $this->assertContains('1', $crawler->filter('.widget .tag-list a .badge')->eq(0)->text());
        $this->assertContains('1', $crawler->filter('.widget .tag-list a .badge')->eq(1)->text());
    }

    public function testShowArticle()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/article/test_title');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Test title', $crawler->filter('h2 a')->text());
    }

    public function testSortAuthor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/author/user_name');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Test title', $crawler->filter('h2 a')->text());
    }

    public function testSortCategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/category/test_category');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Test title', $crawler->filter('h2 a')->text());
    }

    public function testSortTags()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tag/news');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Test title', $crawler->filter('h2 a')->text());
    }

    public function testFormComment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/newCommentFor/test_title');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Rating', $crawler->filter('label')->eq(0)->text());
        $this->assertContains('Text', $crawler->filter('label')->eq(1)->text());
        $this->assertContains('User', $crawler->filter('label')->eq(2)->text());
        $this->assertContains('Name', $crawler->filter('label')->eq(3)->text());
        $this->assertContains('Email', $crawler->filter('label')->eq(4)->text());
        $this->assertContains('Submit Comment', $crawler->filter('button')->text());
    }

    public function testCommentsList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/comments/test_title');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Comments (1)', $crawler->filter('.section-heading')->text());
        $this->assertContains('Text comment', $crawler->filter('.media-body p')->text());
    }

    public function testSuccess()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/success');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Your data successfully saved.', $crawler->filter('.alert')->text());
    }
}
