<html lang="en">
<head>
    <title>Index page</title>
</head>
<body>
<h1> Hello world</h1>
<h3>Here is a list of users</h3>
<ul>
    {% for user in users %}
    <li>{{user.username}}</li>
    {% endfor %}
</ul>
</body>
</html>