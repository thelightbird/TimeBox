<?php
namespace TimeBox\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class RegistrationControllerTest extends WebTestCase
{
	protected $em;
    protected $client;
	protected $testuser;

public function setUp() { 
    $kernel = static::createKernel();
    $kernel->boot();
    $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    $this->em->beginTransaction();

    $this->client = static::createClient();

    $usermanager = $kernel->getContainer()->get('fos_user.user_manager');
    $this->testuser = $usermanager->createUser();
    $this->testuser->setUsername('test');
    $this->testuser->setEmail('test@lemuria.org');
    $this->testuser->setPlainPassword('test');
    $usermanager->updateUser($this->testuser);
}


public function testLogin() {
    $crawler = $this->client->request('GET', 'TimeBox/source/TimeBox-website/web/register/');
    $form = $crawler->selectButton('_submit')->form(array(
        '_username'  => 'test',
        '_password'  => 'test',
        ));     
    $this->client->submit($form);

    $this->assertTrue($this->client->getResponse()->isRedirect(), 'should be redirected');
    $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/TimeBox/source/TimeBox-website/web/register/'), 'should be redirected to account page');

    $crawler = $this->client->followRedirect();
}