<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class PanierModel
{
    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }

    public function getPanierUser($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('SUM(p.quantite) as quantite', 'p.prix*SUM(p.quantite) as prix','p.aquarium_id','p.dateAjoutPanier','p.id')
            ->from('paniers', 'p')
            ->where('p.user_id=?')
            ->andWhere('p.commande_id is NULL')
            ->groupBy('p.aquarium_id')
            ->setParameter(0,$id);
        return $queryBuilder->execute()->fetchAll();
    }

    public function valideCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->insert ('commandes')
            ->values([
                'id' => '?',
                'id_genre' => '?',
                'duree' => '?',
                'dateSortie' => '?',
                'nomProducteur' => '?'

            ])
            ->setParameter(0, $donnees['titre'])
            ->setParameter(1, $donnees['genreFilm_id'])
            ->setParameter(2, $donnees['duree'])
            ->setParameter(3, $donnees['date'])
            ->setParameter(4, $donnees['producteur'])
        ;
        return $queryBuilder->execute();
    }

    public function updatePanierUser($id,$id_user,$quantite){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('paniers','p')
            ->set('p.quantite','? + p.quantite')
            ->where('p.user_id=?')
            ->andWhere('p.aquarium_id=?')
            ->setParameter(0,$quantite)
            ->setParameter(1,$id_user)
            ->setParameter(2,$id);
        return $queryBuilder->execute();

    }
    public function isInPanier($id,$idUser){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select("*")
            ->from('paniers','p')
            ->where('p.user_id=?')
            ->andWhere('p.aquarium_id=?')
            ->andWhere('p.commande_id is NULL')
            ->setParameter(0,$idUser)
            ->setParameter(1,$id);
            $q=$queryBuilder->execute()->fetchAll();
        if(sizeof ($q)==1){
            return $q;
        }else{
            return false;
        }

    }
    public function addToPanier($aquarium,$id_user,$quantite){
        $queryBuilder=new QueryBuilder($this->db);
        $queryBuilder
            ->insert ('paniers')
            ->values([
                'aquarium_id' => '?',
                'prix' => '?',
                'quantite' => '?',
                'user_id' => '?'


            ])
            ->setParameter(0, $aquarium['id'])
            ->setParameter(1, $aquarium['prix'])
            ->setParameter(2,$quantite)
            ->setParameter(3,$id_user);
        return $queryBuilder->execute();
    }

    public function deleteArticle($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('paniers')
            ->where('id=?')
            ->setParameter(0, $id);
        return $queryBuilder->execute();

    }


}
