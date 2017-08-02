<?php


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'nom' => 'intro'
    ));
})
->bind('homepage')
;

// $app->get() permet de créer une nouvelle route accessible en GET uniquement.
$app->get('/test', function() use ($app){
    return $app['twig']->render("test.html.twig");
})
->bind("route_de_test") // bind sert à donner un nom à la route
;

// $app->match() crée une nouvelle route accessible en GET et en POST
// $app->post() crée une nouvelle route accessible en POST uniquement
$app->match('/twig', function() use($app) {
    return $app['twig']->render(
        'twig.html.twig', // Nom de la vue à rendre
        // tableau des paramètres passés à la vue
        [
            'var' => 'Une variable',
            'now' => new DateTime()
        ]
    );
})
->bind('twig')
;

/*
 * On appelle la méthode helloWorldAction de la classe Controller\DemoController
 * au lieu d'une fonction anonyme
 * Cele nécessite la déclaration de la classe en service dans app.php
 */
$app
    ->get("/helloworld", 'demo.controller:helloWorldAction')
    ->bind("hello_world")
;

/*
 * Route contenant une variable, ici name
 * Toutes les url en hello/quelquechose vont matcher cette route
 */
$app
    ->get("/hello/{name}", 'demo.controller:helloAction')
    ->bind("hello")
;

$app
    ->get("/abonnes", 'bibliotheque.controller:abonnesAction')
    ->bind("abonnes")
;

$app
    ->get("/abonne/{id}", 'bibliotheque.controller:abonneDetailAction')
    // , id en 1er arg renvoie à l'argument à analyser, \d+ permet de vérifier si id est un nombre dans l'URL 
    // assert permet de matcher la route uniquement si id est un nombre dans l'URL 
    ->assert('id', '\d+')
    ->bind('abonne_detail')
;

$app
    ->match("/abonne/ajout", 'bibliotheque.controller:abonneAjoutAction')
    ->bind('abonne_ajout')
;

$app
    ->match("/abonne/modif/{id}", 'bibliotheque.controller:abonneModifAction')
    ->assert('id', '\d+')
    ->bind('abonne_modif')
;

$app
    ->get("/abonne/suppr/{id}", 'bibliotheque.controller:abonneSupprimerAction')
    ->assert('id', '\d+')
    ->bind('abonne_suppression')
;

$app
    ->get("/emprunts", 'bibliotheque.controller:listeEmpruntAction')
    ->bind('emprunts')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
