<?php

namespace DonkeyCode\MailBundle\Mailer;

class Mailer
{
    private $swift;
    private $twig;
    private $from;
    private $logo_url;
    private $message;
    private $options;

    /**
    * $twig : Twig_Environment or \Twig\Environment
    */
    public function __construct(\Swift_Mailer $swift, $twig, $from, $replyTo, array $options)
    {
        $this->swift = $swift;
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
        $this->message = new \Swift_Message();

        $this->message->setFrom($this->from);
        $this->message->setReplyTo($this->replyTo);

        return $this;
    }

    /**
     * @return \Swift_Message
     */
    public function getMessage()
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
    public function setTemplate($templateName, array $vars = array())
    {
        $vars = array_merge($vars, $this->twig->getGlobals(), [ 'donkeycode_mail' => $this->options ]);

        // Load the template
        $template = $this->twig->loadTemplate($templateName);

        if ($template->hasBlock('subject', [])) {
            $this->getMessage()->setSubject(trim($template->renderBlock('subject', $vars)));
        }

        if ($template->hasBlock('body', [])) {
            $body = $template->renderBlock('body', $vars);
            $this->getMessage()->setBody($body, 'text/html');

            if (!$template->hasBlock('text', [])) {
                $this->getMessage()->addPart($this->html2txt($body), 'text/plain');
            }
        }

        if ($template->hasBlock('text', [])) {
            $text = $template->renderBlock('text', $vars);

            if (!$template->hasBlock('body', [])) {
                $this->getMessage()->setBody($text, 'text/plain');
            } else {
                $this->getMessage()->addPart($this->html2txt($text), 'text/plain');
            }
        }

        if ($template->hasBlock('from_email', [])) {
            if ($template->hasBlock('from_name', [])) {
                $this->getMessage()->setFrom([
                    $template->renderBlock('from_email', $vars) => $template->renderBlock('from_name', $vars),
                ]);
            } else {
                $this->getMessage()->setFrom($template->renderBlock('from_email', $vars));
            }
        }

        if ($template->hasBlock('reply_to', [])) {
            $this->getMessage()->setReplyTo($template->renderBlock('reply_to', $vars));
        }

        return $this;
    }

    /**
     * @param array[\Swift_Attachment] $attachments
     *
     * @return $this
     */
    public function attachArray(array $attachments)
    {
        foreach ($attachments as $attachment) {
            $this->getMessage()->attach($attachment);
        }

        return $this;
    }

    /**
     * @see \Swift_Mailer::send()
     */
    public function send()
    {
        return $this->swift->send($this->getMessage());
    }

    /**
     * Give the hand to \Swift_Message functions.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this
     */
    public function __call($method, array $args = array())
    {
        call_user_func_array(array($this->getMessage(), $method), $args);

        return $this;
    }

    private function html2txt($document)
    {
        $parts = explode('</style>', $document, 2);

        return str_replace('&#13;', '', strip_tags($parts[sizeof($parts) - 1]));
    }
}
