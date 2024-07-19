<?php

namespace DonkeyCode\MailBundle\Mailer;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $twig;
    private $from;
    private $replyTo;
    private $message;
    private $options;

    public function __construct(MailerInterface $mailer, Environment $twig, string $from, string $replyTo, array $options)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
        $this->replyTo = $replyTo;
        $this->options = $options;
    }

    /**
     * @return $this
     */
    public function createMessage()
    {
        $this->message = (new Email())
            ->from($this->from)
            ->replyTo($this->replyTo);

        return $this;
    }

    /**
     * @return Email
     */
    public function getMessage(): Email
    {
        if (!$this->message) {
            $this->createMessage();
        }

        return $this->message;
    }

    /**
     * Load template and update message.
     *
     * @param string $templateName template path
     * @param array  $vars         contexts vars for template
     *
     * @return $this
     */
    public function setTemplate(string $templateName, array $vars = []): self
    {
        $vars = array_merge($vars, $this->twig->getGlobals(), [ 'donkeycode_mail' => $this->options ]);

        // Load the template
        $template = $this->twig->load($templateName);

        if ($template->hasBlock('subject', [])) {
            $this->getMessage()->subject(trim($template->renderBlock('subject', $vars)));
        }

        if ($template->hasBlock('body', [])) {
            $body = $template->renderBlock('body', $vars);
            $this->getMessage()->html($body);

            if (!$template->hasBlock('text', [])) {
                $this->getMessage()->text($this->html2txt($body));
            }
        }

        if ($template->hasBlock('text', [])) {
            $text = $template->renderBlock('text', $vars);

            if (!$template->hasBlock('body', [])) {
                $this->getMessage()->text($text);
            } else {
                $this->getMessage()->text($this->html2txt($text));
            }
        }

        if ($template->hasBlock('from_email', [])) {
            if ($template->hasBlock('from_name', [])) {
                $this->getMessage()->from(new Address($template->renderBlock('from_email', $vars), $template->renderBlock('from_name', $vars)));
            } else {
                $this->getMessage()->from($template->renderBlock('from_email', $vars));
            }
        }

        if ($template->hasBlock('reply_to', [])) {
            $this->getMessage()->replyTo($template->renderBlock('reply_to', $vars));
        }

        return $this;
    }

    /**
     * @param array $attachments
     *
     * @return $this
     */
    public function attachArray(array $attachments): self
    {
        foreach ($attachments as $attachment) {
            $this->getMessage()->attachFromPath($attachment);
        }

        return $this;
    }

    /**
     * @see send()
     */
    public function send()
    {
        $this->mailer->send($this->getMessage());
    }

    /**
     * Give the hand to Email functions.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this
     */
    public function __call(string $method, array $args = [])
    {
        call_user_func_array([$this->getMessage(), $method], $args);

        return $this;
    }

    private function html2txt(string $document): string
    {
        $parts = explode('</style>', $document, 2);

        return str_replace('&#13;', '', strip_tags($parts[count($parts) - 1]));
    }
}
