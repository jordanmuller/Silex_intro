<?php
namespace Controller;

use Silex\Application;

class DemoController 
{
    public function helloWorldAction(Application $app) 
    {
        return $app['twig']->render('helloworld.html.twig');
    }
    
    /**
     * Le paramètre $name correspond à ce que contient {name} dans la route
     * $param Application $app de  l'instance de Silex\Application
     * $param string $name, la variale venant de l'URL
     */
    public function helloAction(Application $app, $name) 
    {
        return $app['twig']->render(
            'hello.html.twig',
                [
                    'name' => $name
                ]
            );
    }
    
}
