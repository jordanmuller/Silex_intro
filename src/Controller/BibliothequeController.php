<?php

namespace Controller;

use Silex\Application;

class BibliothequeController 
{
    public function abonnesAction(Application $app) 
    {
        // méthode fetchAll() de Doctrine dbal
        // On écrit $app['db'] avec Doctrine sur Silex équivalent à $pdo->
        $abonnes = $app['db']->fetchAll('SELECT * FROM abonne');
        
        return $app['twig']->render(
            'bibliotheque/abonnes.html.twig',
            [
                'abonnes' => $abonnes
            ]
        );
    }
    
    public function abonneDetailAction(Application $app, $id) 
    {
        $abonne = $app['db']->fetchAssoc(
            "SELECT * FROM abonne WHERE id_abonne = :id",
            // Les bindParam s'effectuent toujours dans un tableau avec Doctrine dbal   
            [':id' => $id]
        );
        
        return $app['twig']->render(
            'bibliotheque/abonne.html.twig',
            [
                // Le nom de la valeur du tableau sera la variable passé dans la view bibliotheque/abonne.html.twig
                'abonne' => $abonne
            ]
        );
    }
    
    public function abonneAjoutAction(Application $app) 
    {
        if(!empty($_POST))
        {
            // insert() est une méthode Doctrine dbal
            $app['db']->insert(
                'abonne', // nom de la table
                [
                    'prenom' => $_POST['prenom']
                ] // tableau des valeurs insérés (les clés sont les noms des champs en bdd
            );
            
            // redirect est une méthode raccourcie propre à Silex
            return $app->redirect(
                // l'indice ['url_generator'] est prédéfini dans Silex et Silex Skeleton, on lui attribue toutes les redirections, on passe la valeur de Bind dans generate()
                $app['url_generator']->generate('abonnes')
                // url_generator est un service, dans Silex, les services SONT TOUJOURS des instances de classes
            );
        }
        
        return $app['twig']->render(
        'bibliotheque/abonne_ajout.html.twig'
        );
    }
    
    public function abonneModifAction(Application $app, $id) 
    {
        $abonne = $app['db']->fetchAssoc(
            "SELECT * FROM abonne WHERE id_abonne = :id",
            // Les bindParam s'effectuent toujours dans un tableau avec Doctrine dbal   
            [':id' => $id]
        );
        
        if(empty($abonne))
        {
            // Pour jeter une 404
            $app->abort(404, "Aucun abonné avec l'id $id");
        }
        
        if(!empty($_POST))
        {
            $app['db']->update(
                'abonne', // nom de la table
                [
                    'prenom' => $_POST['prenom'] // tableau des valeurs à modifier (les clés sont les noms des champs en bdd)
                ],
                [
                    'id_abonne' => $id
                ] // tableau pour la classe WHERE ici (WHERE id_abonne = $id)
            );  
            
            return $app->redirect(
                // l'indice ['url_generator'] est prédéfini dans Silex et Silex Skeleton, on lui attribue toutes les redirections, on passe la valeur de Bind dans generate()
                $app['url_generator']->generate('abonnes')
                // url_generator est un service, dans Silex, les services SONT TOUJOURS des instances de classes
            );
        }
        
        return $app['twig']->render(
            'bibliotheque/abonne_modif.html.twig',
            [
                'abonne' => $abonne
            ]
        );
    }
    
    public function abonneSupprimerAction(Application $app, $id) 
    {
        $app['db']->delete(
            'abonne', // nom de la table
            [
                'id_abonne' => $id
            ] //clause WHERE
        );
        
        return $app->redirect(
            // l'indice ['url_generator'] est prédéfini dans Silex et Silex Skeleton, on lui attribue toutes les redirections, on passe la valeur de Bind dans generate()
            $app['url_generator']->generate('abonnes')
            // url_generator est un service, dans Silex, les services SONT TOUJOURS des instances de classes
        );
    }
    
    /*
     * Créer un page qui liste les emprunts avec 
     * id de l'emprunt
     * prénom de l'abonné
     * auteur et titre du livre
     * date sortie et rendue au format fr
     * si non rendu case vide
     */
    
    public function listeEmpruntAction(Application $app) 
    {
        // On auraut pu écrire $emprunts = <<<EOS       EOS; (voir cahier)
        $emprunts = $app['db']->fetchAll(
           "SELECT e.id_emprunt, a.prenom, l.auteur, l.titre, e.date_sortie, e.date_rendu"
                . " FROM emprunt e, abonne a, livre l WHERE e.id_abonne = a.id_abonne "
                . " AND l.id_livre = e.id_livre"
        );
        
        /* $query = <<<EOS
        SELECT *
        FROM emprunt e
        JOIN livre l USING(id_livre)  le USING remplace le ON e.id_livre = l.id_livre
        JOIN abonne a USING(id_abonne)  ON e.id_abonne = a.id_abonne
        ORDER BY date_sortie
EOS;
         * $emprunts = $app['db']->fetchAll($query);
        */
        
        return $app['twig']->render(
            'bibliotheque/emprunts.html.twig',
            [
                'emprunts' => $emprunts
            ]    
        );
    }
}
