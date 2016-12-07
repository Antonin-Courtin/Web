<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class CommandesModel
{

    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getDetailsCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.aquarium_id', 'a.nom','p.dateAjoutPanier', 'p.prix', 'p.quantite', 'u.login', 'u.adresse','p.commande_id')
            ->from('paniers', 'p')
            ->innerJoin('p', 'users', 'u', 'p.user_id=u.id')
            ->innerJoin('p', 'commandes', 'c', 'p.commande_id=c.id')
            ->innerJoin('p', 'aquariums', 'a', 'p.aquarium_id=a.id')
            ->where('p.commande_id=?')
            ->setParameter(0, $id);
        return $queryBuilder->execute()->fetchAll();

    }

    public function expedieCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('commandes')
            ->set('etat_id','2')
            ->where('id= ?')
            ->setParameter(0,$id);
        return $queryBuilder->execute();

    }

    public function getAllCommandes() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.date_achat', 'u.login', 'p.prix','p.id','t.libelle')
            ->from('commandes', 'p')
            ->innerJoin('p','etats','t','p.etat_id=t.id')
            ->innerJoin('p','users','u','p.user_id=u.id');

        return $queryBuilder->execute()->fetchAll();

    }

    public function valideCommande($prix,$id,$panier){
        $conn=$this->db;
        $conn->beginTransaction();
        $requestSQL=$conn->prepare('insert into commandes(user_id,prix,etat_id) values (?,?,?)');
        $requestSQL->execute([$id,$prix,'1']);
        $lastinsertid=$conn->lastInsertId();

        foreach($panier as $pani){
            $requestSQL=$conn->prepare('update paniers set commande_id=? where id=?');
            $requestSQL->execute([$lastinsertid,$pani['id']]);
        }

        $conn->commit();

        return null;
    }

    public function getCommandesUser($idUser){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('commandes')
            ->where('user_id = ?')
            ->innerJoin('commandes','etats', 't','etat_id=t.id')
            ->setParameter(0, $idUser)
            ->addOrderBy('t.libelle', 'DESC');;

        return $queryBuilder->execute()->fetchAll();

    }

}