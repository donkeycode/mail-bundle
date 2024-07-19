<?php

namespace DonkeyCode\MailBundle\Tests\Mailer;

use DonkeyCode\MailBundle\Mailer\Mailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class MailerTest extends TestCase
{
    private $twigLoader;
    private $mailer;

    public function setUp(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $this->twigLoader = new ArrayLoader([]);
        $twig = new Environment($this->twigLoader);

        $this->mailer = new Mailer($mailer, $twig, "from@mail.com", "reply@mail.com", []);
    }

    public function testCreateMessage()
    {
        $this->mailer->createMessage();

        $this->assertInstanceOf(Email::class, $this->mailer->getMessage(), 'The message is not an instance of Email');

        $this->mailer->getMessage()->subject('Test first');
        $this->mailer->createMessage();

        $this->assertNotEquals('Test first', $this->mailer->getMessage()->getSubject(), 'Create message always return a new message');
    }

    public function testGetMessage()
    {
        $this->mailer->getMessage()->subject('Test first');

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

        $textBody = $this->mailer->getMessage()->getTextBody();
        if ($textBody instanceof TextPart) {
            $textBody = $textBody->getBody();
        }

        $this->assertNotNull($textBody, 'Text body should not be null');
        $this->assertStringContainsString('The textblock simple var', $textBody, 'Body in text mode extracted from twig block text');
    }
}
