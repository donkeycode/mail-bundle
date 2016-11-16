<?php

namespace DonkeyCode\MailBundle\Tests\Mailer;

use DonkeyCode\MailBundle\Mailer\Mailer;

use DonkeyCode\MailBundle\Tests\TestCase;
use Swift_Mailer;
use Swift_Transport_NullTransport;
use Swift_Events_SimpleEventDispatcher;
use Twig_Environment;
use Twig_Loader_Array;

class MailerTest extends TestCase
{
    public function setUp()
    {
        $swift = new Swift_Mailer(new Swift_Transport_NullTransport(new Swift_Events_SimpleEventDispatcher()));
        $this->twigLoader = new Twig_Loader_Array([]);
        $twig = new Twig_Environment($this->twigLoader);

        $this->mailer = new Mailer($swift, $twig, "from@mail.com", "reply@mail.com");
    }

    public function testCreateMessage()
    {
        $this->mailer->createMessage();

        $this->assertInstanceOf('Swift_Message', $this->mailer->getMessage(), 'The message is a Swift_Message');

        $this->mailer->getMessage()->setSubject('Test first');
        $this->mailer->createMessage();

        $this->assertNotEquals('Test first', $this->mailer->getMessage()->getSubject(), 'Create message always return a new message');
    }

    public function testGetMessage()
    {
        $this->mailer->getMessage()->setSubject('Test first');

        $this->assertEquals('Test first', $this->mailer->getMessage()->getSubject(), 'getMessage return the last message created');
    }

    public function testSetTemplate1()
    {
        $tpl = <<<TWIG
{% block subject %}The subject of mail with {{ var }}{% endblock %}
{% block text %}The textblock {{ var }}{% endblock %}
TWIG;
        $this->twigLoader->setTemplate('test', $tpl);

        $this->mailer->setTemplate('test', [
            'var' => 'simple var'
        ]);

        $this->assertEquals('The subject of mail with simple var', $this->mailer->getMessage()->getSubject(), 'Subject extracted from twig block subject');
        $this->assertEquals('The textblock simple var', $this->mailer->getMessage()->getBody(), 'Body in text mode extracted from twig block text');
        $this->assertEquals('The textblock simple var', $this->mailer->getMessage()->getBody(), 'Body in text mode extracted from twig block text');
    }
}
