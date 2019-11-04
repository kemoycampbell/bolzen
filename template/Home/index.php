<html lang="en">
    <head>
        <title>Index page</title>
    </head>
    <body>
        <ul>
            {% for user in lists %}
                <li>{{user.username}}</li>
            {% endfor %}
        </ul>
    </body>
</html>