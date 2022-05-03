<?php

require_once('view/View.php');
require_once('model/Cheese.php');
require_once('model/CheeseBuilder.php');
require_once('control/AuthenticationManager.php');

class Controller {

    private $view;
    private $cheeseTab;
    private $authenticationManager;

    public function __construct($view, $cheeseTab, $accountTab) {
        $this->view = $view;
        $this->cheeseTab = $cheeseTab;
        $this->authenticationManager = new AuthenticationManager($accountTab);
    }

    public function aPropos() {
        $this->view->makeAProposPage();
    }

    public function showInformation($id) {
        if(!key_exists($id, $this->cheeseTab->readAll())) {
            $this->view->makeUnknownCheesePage();
        }
        else {
            $this->view->makeCheesePage($this->cheeseTab->read($id), $id);
        }
    }

    public function showList($page = 1) {
        unset($_SESSION['search']);
        unset($_SESSION['currentUpdateCheese']);

        $this->view->makeListPage($this->cheeseTab->readAll(), $page);
    }

    public function newCheese() {
        if(key_exists('currentNewCheese', $_SESSION)) {
            $this->view->makeCheeseCreationPage($_SESSION['currentNewCheese']);
        }
        else {
            $this->view->makeCheeseCreationPage();
        }
    }

    public function saveNewCheese(array $data) {
        $cheeseBuilder = new cheeseBuilder($data);

        if($cheeseBuilder->isValid()) {
            $error = false;
            unset($_SESSION['currentNewCheese']);
            $a = $cheeseBuilder->createCheese();

            $id = $this->cheeseTab->create($a);

            if(key_exists('image', $_FILES)) {
                if ($_FILES['image']['error'] == 0) {
                    if ($cheeseBuilder->isImageValid($_FILES['image'], $id)) {

                        if(file_exists("upload/$id." . str_replace('image/', '', $_FILES['image']['type']))) {
                            unlink('upload/' . "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                        }
                        move_uploaded_file($_FILES['image']['tmp_name'], "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                        $this->cheeseTab->addImage($id, $_FILES['image']);
                    }
                    else {
                        $error = true;
                        $this->cheeseTab->delete($id);
                        $_SESSION['currentNewCheese'] = $cheeseBuilder;
                        $this->view->displayCheeseCreationFailure();
                    }
                }
            }

            if(!$error) {
                $this->view->displayCheeseCreationSuccess($id);
            }
        }

        else {
            $_SESSION['currentNewCheese'] = $cheeseBuilder;
            $this->view->displayCheeseCreationFailure();
        }
    }

    public function askCheeseDeletion($id) {
        if($this->cheeseTab->read($id) != null) {
            $this->view->makeCheeseDeletionPage($id);
        }

        else {
            $this->view->makeUnknownCheesePage();
        }
    }

    public function deleteCheese($id) {
        if($this->cheeseTab->read($id) != null) {
            $this->cheeseTab->delete($id);
            $this->view->displayCheeseDeletionSuccess();
        }
        else {
            $this->view->displayCheeseDeletionFailure();
        }
    }

    public function updateCheese($id) {
        if(key_exists('currentUpdateCheese', $_SESSION)) {
            $this->view->makeCheeseUpdatePage($id, $_SESSION['currentUpdateCheese']);
        }
        else {
            $a = $this->cheeseTab->read($id);
            $data = array('name' => $a->getName(), 'region' => $a->getRegion(), 'year' => $a->getYear());
            $this->view->makeCheeseUpdatePage($id, new CheeseBuilder($data));
        }
    }

    public function updatedCheese(array $data, $id) {
        $cheeseBuilder = new CheeseBuilder($data);
        $image = null;
        $error = false;
        if($cheeseBuilder->isValid()) {

            if(key_exists('image', $_FILES)) {
                if ($_FILES['image']['error'] == 0) {
                    if ($cheeseBuilder->isImageValid($_FILES['image'], $id)) {
                        $oldImage = $this->cheeseTab->read($id)->getImage();
                        if($oldImage != null) {
                            unlink('upload/' . $oldImage);
                        }
                        move_uploaded_file($_FILES['image']['tmp_name'], "upload/$id." . str_replace('image/', '', $_FILES['image']['type']));
                    }
                    else {
                        $error = true;
                        $_SESSION['currentUpdateCheese'] = $cheeseBuilder;
                        $this->view->displayCheeseUpdatedFailure($id);
                    }
                }
            }

            if(!$error) {
                unset($_SESSION['currentUpdateCheese']);
                $this->cheeseTab->update($id, $cheeseBuilder->createCheese(), $image);
                $this->view->displayCheeseUpdatedSuccess($id);
            }
        }

        else {
            $_SESSION['currentUpdateCheese'] = $cheeseBuilder;
            $this->view->displayCheeseUpdatedFailure($id);
        }
    }

    // Fonction pour le complément de la recherche d'objet.
    public function research($data) {
        unset($_SESSION['search']);
        if($data['search'] === strip_tags($data['search']) && $data['search'] !== '') {
            $_SESSION['search'] = $data['search'];
            $this->view->makeListPage($this->cheeseTab->research($data['search']));
        }

        else {
            $this->view->displayCheeseResearchListFailure();
        }
    }

    public function login() {
        $this->view->makeLoginFormPage();
    }

    public function connected($data) {
        if($this->authenticationManager->isUserConnected()) {
            $this->authenticationManager->disconnectUser();
        }

        $this->authenticationManager->connectUser($data['login'], $data['password']);
        if($this->authenticationManager->isUserConnected()) {
            $this->view->displayCheeseAuthenticationSuccess($this->authenticationManager->getUserName());
        }

        else {
            $this->view->displayCheeseAuthenticationFailure();
        }
    }

    public function disconnection() {
        $this->authenticationManager->disconnectUser();
        $this->view->displayCheeseDisconnectionFailure();
    }

    public function register() {
        $this->view->makeRegistrationFormPage();
    }

    public function registered($data) {
        if($data['password'] === $data['confirmPassword']) {
            if($this->authenticationManager->registration($data['name'], $data['login'], $data['password'])) {
                $this->authenticationManager->connectUser($data['login'], $data['password']);
                $this->view->displayCheeseRegistrationSuccess($data['name']);
            }
            else {
                $this->view->displayCheeseRegistrationFailure();
            }
        }
        else {
            $this->view->displayCheeseRegistrationFailure();
        }
    }
}
