<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request
use App\Model\PanierModel;
use App\Model\AquariumModel;
use App\Model\TypeAquariumModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class PanierController implements ControllerProviderInterface
{

    private $panierModel;
    private $aquariumModel;
    public function index(Application $app) {
        return $this->show($app);
    }

    public function show(Application $app){
        if( $app['session']->get('droit')=="DROITclient"){
            $this->panierModel = new PanierModel($app);
            $panier = $this->panierModel->getPanierUser($app['session']->get('user_id'));
            $this->aquariumModel=new AquariumModel($app);
            $aquarium=$this->aquariumModel->getAllAquariums();
            $total=0;
            foreach ($panier as $p){
                $total=$p['prix']+$total;
            }
            return $app["twig"]->render('frontOff\frontOFFICE.html.twig',['data'=>$aquarium,'panier'=>$panier,'total'=>$total]);


        }
    }

    public function validerCommande($app){
        if(isset($_POST['total'])){
            $this->panierModel=new PanierModel($app);
            $produit=$this->panierModel->getPanierUser($app['session']->get('user_id'));
            $this->panierModel->valideCommande($_POST['total']);
        }

    }

    public function addPanier(Application $app, $id)
    {
        if ($app['session']->get('droit') == "DROITclient" ) {
            $this->panierModel = new PanierModel($app);
            $this->aquariumModel = new AquariumModel($app);
            if($this->panierModel->isInPanier($id,$app['session']->get('user_id'))!=NULL){
                $this->panierModel->updatePanierUser($id,$app['session']->get('user_id'),$_POST['quantite']);
            }else{
                $aquarium = $this->aquariumModel->getAquarium($id);
                $this->panierModel->addToPanier($aquarium, $app['session']->get('user_id'),$_POST['quantite']);

            }

        }
        if ($app['session']->get('droit') == "DROITclient") {
            return $app->redirect($app["url_generator"]->generate("panier.show"));


        }else{
            return $app->redirect($app["url_generator"]->generate("aquarium.index"));
        }
    }

    public function deleteArticlePanier(Application $app,$id){
        if($app['session']->get('droit') == "DROITclient"){
            $this->panierModel=new PanierModel($app);
            $this->panierModel->deleteArticle($id);
            return $app->redirect($app["url_generator"]->generate("panier.show"));

        }

    }







    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', 'App\Controller\PanierController::index')->bind('panier.index');
        $controllers->get('/show', 'App\Controller\PanierController::show')->bind('panier.show');
        $controllers->post('/add/{id}', 'App\Controller\PanierController::addPanier')->bind('panier.add');
        $controllers->get('/delete/{id}', 'App\Controller\PanierController::deleteArticlePanier')->bind('panier.deleteArticle');
        return $controllers;
    }
}