# Bolzen Overview
Bolzen is a powerful PHP skeleton web framework that is built using Symfony components and Twig for the template engine.
The possibilities of what you can build with Bolzen is unlimited. Bolzen is capable of building both
simple and complex web applications or websites. Bolzen follows the principle of Separation of Concern for building a
software. Bolzen is layout in a way that allow you to deploy your application in no time. It is designed in a way that
little to no configuration is required when deploying to various stages of development such as dev, staging and production.

# Inspiration behind the name
Bolzen is German for Bolt

# How is Bolzen different from other web frameworks ?
## Same
* Built using Symfony Components
* Built using Twig 2.0
* Support MVC design patterns and other design patterns
* OOP supported

## The difference
For this purpose, I will focus on two main differences

* Design to work well with shared hosting provider platforms
Existing web frameworks made the assumption that the user will have
access to the shell,virtualHost file and among other which result in 
massive configurations need to get them to work. Bolzen is design to work
right out of the box. Developers only need to set the configuration within
the config/ folder such as directory path,scheme, host, environment. Thus
eliminate the need for shell or virtualHost access
* Include project directory in the path
Currently project directory are not include in the route paths by default
in most web frameworks. Bolzen include the project directory that was supplied
in the config to all routes thus eliminate the need for the developer to include
it in the requests

# Framework layout
    
    ├── Project Directory
        ├── config
            ├── .env # create this file when you need to use database
            ├── config.php
        ├── core
            ├── AccessControl
            ├── Config
            ├── Container
            ├── Controller
            ├── Database
            ├── Framework
            ├── Model
            ├── Request
            ├── RouteCollection
            ├── Session
            ├── Tables
            ├── Twig
            ├── User
        ├── public
            ├── css
            ├── images
            ├── js
            ├── .htaccess
            ├── index.php
        ├── src
            ├── app.php
            ├── container.php
              
        ├── template
        ├── var
        ├── vendor
        .htaccess

# Getting start
* clone the repo git clone https://github.com/kemoycampbell/bolzen
* go into config/ and configure your environment as desired
* If you will be using the database, you must set enableDatabase to true and create a .env in the config/ with the following
attributes.
    ````
    DB_PREFIX= #the database prefix example mysql
    DB_USER= # the database username
    DB_PASS=# the database password
    DB_HOST= # the database host. Can be localhost or an ip
    DB_NAME= # the name of the database.
    ````
All of our codes will go into the src folder and our template(UI/ html codes) will go into the template folder.
If you need to add a image,css or js then those goes into the public/assets/ in its respectively folders.
## Src structures
You may structure your src/ folders as you see fit. such as
```
├── src/
    ├── Model
    ├── Controller
    ├── app.php
    ├── container.php
```
OR
```
├── src/
    ├── Staff
        ├── Model
        ├── Controller
    ├── Supervisor
        ├── Model
        ├── Controller
    ├── app.php
    ├── container.php
    
```
OR any other pattern you choose. You will notice that there are no "View" folder within both structures shown above. This is because
all views goes in the template folder which you can structure as you see fit.

Before we make our project complicate with database and all fancy stuff, let us create a 
simple project. First let us create a home page template called index.php. All template must end with .php.
If all is well, you should have a structure similar to below

```
├── template/
    ├── Home
        ├── index.php
```

with the following code in index.php
```
<html lang="en">
    <head>Index</head>
    <body>
        <h1> Hello world</h1>
    </body>
</html>

```
next we will need to create a controller that will be responsible for displaying this template.
I will create a controller class called HomeController. I will be using the second src structure as shown
above hence my src structure will now look like this
```
├── src/
    ├── Home
        ├── Controller
            ├──HomeController.php      
```
Our controller class will have the following code
```php
<?php

namespace Bolzen\Src\Home;

use Bolzen\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return $this->render($request);
    }
}

```
Finally, we need to add a route which will tell our app the path for the index(what the user type in the url),
the controller responsible for displaying the page and the path to the template/Home/index. We can do this by go
into src/app.php and add the path there. Add this line to your src/app.php. Your src/app.php should now look like this
```php
<?php
use Symfony\Component\Routing\Route;
use Bolzen\Core\RouteCollection\RouteCollection;

$config = $container->get('config');
$accessControl = $container->get('accessControl');
$routes = new RouteCollection($config, $accessControl);

####################################
# Do not modify the line above
# Your Routes goes here
##################################

$routes->add('Home/index', new Route("index", array(
    '_controller'=>'\Bolzen\Src\Home\HomeController::index'
    )));

###############################
# Do not modify below
##############################

return $routes->getRouteCollection();
```
A bit of explaination regarding each parameter, the first parameter is the template page path.
We do not need to add template/ and the .php extension since this is already done by the framework.
The second parameter is the url that goes in the browser and the third is the controller that
is responsible to do some action when the url requested. If all is well, you should be able to visit
http://localhost/projectDirectory/ and hello world should show up. Replace projectDirectory with your 
directory name. This is the output of our progress so far
![alt text](https://github.com/kemoycampbell/Bolzen/blob/master/index.png?raw=true "Bolzen")





  

