<?php
//***************************************
// Montage des controleurs sur le routeur
$app->mount("/", new App\Controller\IndexController($app));
$app->mount("/produit", new App\Controller\AquariumController($app));
$app->mount("/connexion", new App\Controller\UserController($app));
$app->mount("/panier", new App\Controller\PanierController($app));
$app->before(function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $nomRoute=$request->get("_route"); //var_dump($request) pour voir
    if ($app['session']->get('droit') != 'DROITadmin'  && ($nomRoute=="index.pageAdmin" || $nomRoute=="commande.afficheDetailsCommande" || $nomRoute=="commande.valideCommande"
                    || $nomRoute=="aquarium.showCommandes" || $nomRoute=="aquarium.show" || $nomRoute=="aquarium.add" || $nomRoute=="aquarium.validFormAdd" || $nomRoute=="aquarium.delete"
        || $nomRoute=="aquarium.validFormDelete" ||$nomRoute=="aquarium.edit" || $nomRoute=="aquarium.validFormEdit" )) {
        return $app->redirect($app["url_generator"]->generate("index.errorDroit"));
    }if($app['session']->get('droit')!='DROITclient' && (($nomRoute=="panier.showCommandes" || $nomRoute=='panier.show' || $nomRoute=='panier.add' || $nomRoute=='panier.deleteArticle'
            || $nomRoute=='panier.valide'))){
        return $app->redirect($app["url_generator"]->generate("index.errorDroit"));

    }

});
