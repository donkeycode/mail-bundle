{% block subject %}
Réinitialisation mot de passe espace Cleany
{% endblock %}

{% block body %}
    {% embed "CleanyMailBundle:Mails:layout.html.twig" %}
        {% block title %}Réinitialisation mot de passe espace Cleany{% endblock %}
        {% block content %}
            Cher client,<br />
            <br />
            Vous avez demandé la réinitialisation de votre mot de passe.<br />
            Cliquez sur ce lien pour le réinitialiser : <a href="{{ front_url("reset", { token: user.confirmationToken } )}}
">{{ front_url("reset", { token: user.confirmationToken } )}}</a><br />
            <br />
            N'oubliez pas, vous pouvez retrouver à tout moment dans votre <a href="{{ front_url("home" )}}
">espace Cleany</a>, le planning de votre agent, les contacts de nos équipes et l'historique de toutes les factures.<br />
            <br />
            Bonne journée !<br />
            <br />
            L'équipe Cleany<br />
        {% endblock %}

    {% endembed %}
{% endblock %}
