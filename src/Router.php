<?php

require_once("view/View.php");
require_once("view/PrivateView.php");
require_once("control/Controller.php");

class Router {

    public function main($cheese, $accountTab) {

        session_name("cheeseID");
        session_start();

        $view = $this->creationView();
        $controller = new Controller($view, $cheese, $accountTab);

        $accessTab = $this->creationAccessTab($cheese);


        if(key_exists('id', $_GET)) {
            $id = $_GET['id'];
            $controller->showInformation($id);
        }

        if(key_exists('liste', $_GET)) {
            if($_GET['liste'] == 'rechercher') {
                $controller->research($_POST);
            }

            else if($_GET['liste'] > 0) {
                $controller->showList($_GET['liste']);
            }

            else {
                $controller->showList();
            }
        }

        if(key_exists('action', $_GET)) {

            if($_GET['action'] === 'aPropos' && in_array('aPropos', $accessTab)) {
                $controller->aPropos();
            }

            else if($_GET['action'] === 'nouveau' && in_array('nouveau', $accessTab)) {
                $controller->newCheese();
            }

            else if($_GET['action'] === 'sauverNouveau' && in_array('sauverNouveau', $accessTab)) {
                $controller->saveNewCheese($_POST);
            }

            else if($_GET['action'] === 'connexion' && in_array('connexion', $accessTab)) {
                $controller->login();
            }

            else if($_GET['action'] === 'authentification' && in_array('authentification', $accessTab)) {
                $controller->connected($_POST);
            }

            else if($_GET['action'] === 'inscription' && in_array('inscription', $accessTab)) {
                $controller->register();
            }

            else if($_GET['action'] === 'inscrit' && in_array('inscrit', $accessTab)) {
                $controller->registered($_POST);
            }

            else if($_GET['action'] === 'deconnexion' && in_array('deconnexion', $accessTab)) {
                $controller->disconnection();
            }

            else if(key_exists('id', $_GET)) {
                if ($_GET['action'] === 'supprimerConfirmation'&& in_array('supprimerConfirmation', $accessTab)) {
                    $controller->askCheeseDeletion($_GET['id']);
                }

                else if ($_GET['action'] === 'supprimer'&& in_array('supprimer', $accessTab)) {
                    $controller->deleteCheese($_GET['id']);
                    $view->makeHomePage();
                }
                else if($_GET['action'] === 'modification'&& in_array('modification', $accessTab)) {
                    $controller->updateCheese($_GET['id']);
                }
                else if($_GET['action'] === 'sauverModification'&& in_array('sauverModification', $accessTab)) {
                    $controller->updatedCheese($_POST,$_GET['id']);
                }
                else {
                    $this->POSTredirect('fromages.php', 'Vous n\'avez pas les droits');
                }
            }
            else {
                $this->POSTredirect('fromages.php', 'Vous n\'avez pas les droits');
            }
        }

        $view->render();
    }

    public function creationView() {
        if(key_exists('feedback', $_SESSION)) {
            if(key_exists('user', $_SESSION)) {
                $view = new PrivateView('Page d\'accueil', '<h1> Bienvenue ' . $_SESSION['user']->getName() . ' ! </h1>', $this, $_SESSION['feedback'], $_SESSION['user']);
                unset($_SESSION['feedback']);

            }

            else {
                $view = new View('Page d\'accueil', '<h1> Bienvenue sur le site ! </h1>', $this, $_SESSION['feedback']);
                unset($_SESSION['feedback']);
            }
        }
        else {
            if(key_exists('user', $_SESSION)) {
                $view = new PrivateView('Page d\'accueil', '<h1> Bienvenue ' . $_SESSION['user']->getName() . ' ! </h1>', $this, null, $_SESSION['user']);
                unset($_SESSION['feedback']);
            }

            else {
                $view = new View('Page d\'accueil', '<h1> Bienvenue sur le site ! </h1>', $this, null);
                unset($_SESSION['feedback']);
            }
        }
        return $view;
    }

    public function creationAccessTab($cheese) {

        if(!key_exists('user',$_SESSION)) {
            return array('aPropos', 'connexion', 'authentification', 'inscription', 'inscrit');
        }

        else {
            if ($_SESSION['user']->getStatus() === "admin") {
                return array('aPropos', 'deconnexion', 'nouveau', 'sauverNouveau', 'modification', 'sauverModification', 'supprimer', 'supprimerConfirmation');
            }
            else {
                $accessTab = array('aPropos', 'deconnexion', 'nouveau', 'sauverNouveau');
                if(key_exists('id', $_GET)) {
                    if ($_SESSION['user']->getName() === $cheese->read($_GET['id'])->getCreator()) {
                        $accessTab = array_merge($accessTab, array('modification', 'sauverModification', 'supprimer', 'supprimerConfirmation'));
                    }
                }
                return $accessTab;
            }
        }
    }


    public function getHomePageURL() {
        return 'fromages.php';
    }

    public function getCheeseURL($id) {
        return "?id=$id";
    }

    public function getCheeseCreationURL() {
        return 'fromages.php?action=nouveau';
    }

    public function getCheeseSaveURL() {
        return 'fromages.php?action=sauverNouveau';
    }

    public function getCheeseAskDeletionURL($id) {
        return "fromages.php?action=supprimerConfirmation&id=$id";
    }

    public function getCheeseDeletionURL($id) {
        return "fromages.php?action=supprimer&id=$id";
    }

    public function getCheeseUpdateURL($id){
        return "fromages.php?action=modification&id=$id";
    }

    public function getCheeseUpdatedURL($id) {
        return "fromages.php?action=sauverModification&id=$id";
    }

    public function getCheeseResearchURL() {
        return "fromages.php?liste=rechercher";
    }

    public function getLoginURL() {
        return "fromages.php?action=connexion";
    }

    public function getAuthenticationURL() {
        return "fromages.php?action=authentification";
    }

    public function getDisconnectionURL() {
        return "fromages.php?action=deconnexion";
    }

    public function getAProposURL(){
        return "fromages.php?action=aPropos";
    }

    public function getRegistrationURL() {
        return "fromages.php?action=inscription";
    }

    public function getRegisteredURL() {
        return "fromages.php?action=inscrit";
    }

    public function getPageURL($page) {
        return "fromages.php?liste=$page";
    }

    public function POSTredirect($url, $feedback) {
        $_SESSION['feedback'] = $feedback;
        header("Location: ".htmlspecialchars_decode($url), true, 303);
        die;
    }
}
