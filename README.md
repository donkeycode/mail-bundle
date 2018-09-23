# MailBundle

An easy way to send mail with twig and swift

## Setup

````
composer require donkeycode/mail-bundle
````

Add in `AppKernel.php`

````
new DonkeyCode\MailBundle\DonkeyCodeMailBundle(),
````

## Configuration

````
donkey_code_mail:
    mail_from: null
    reply_to: null
    options:
        header_bg: '#2d7cff'
        header_txt_color: '#ffffff'
        bg: '#efefef'
        txt_color: '#555555'
        font_family: 'Helvetica Neue'
````


## Usage

Create your twig


````
{% block subject %}
Subject of mail
{% endblock %}

{% block body %}
    {% embed "DonkeyCodeMailBundle:Mails:layout.html.twig" %}
    {# For sf4 #}
    {% embed "@DonkeyCodeMail/mails/layout.html.twig" %}
        {% block title %}Header{% endblock %}
        {% block content %}
            Body Of mail
        {% endblock %}

    {% endembed %}
{% endblock %}
````



Send it simply

````
$mailer = $this->getContainer()
    ->get('donkeycode.mailer')
    ->createMessage()
    ->setTemplate('YourBundle:Mails:invoices.html.twig', [
        'invoices' => $invoices,
    ])
    ->setTo($destMail)
    ->send();
````

