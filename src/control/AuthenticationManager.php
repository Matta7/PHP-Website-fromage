<?php

class AuthenticationManager {

    protected $accountsTab;

    public function __construct($accountsTab) {
        $this->accountsTab = $accountsTab;
    }

    public function connectUser($login, $password) {
        $account = $this->accountsTab->checkAuth($login, $password);
        if($account != null) {
            $_SESSION['user'] = $account;
        }
    }

    public function isUserConnected() {
        if(key_exists('user', $_SESSION)) {
            return true;
        }
        return false;
    }

    public function isAdminConnected() {
        if(key_exists('user', $_SESSION)) {
            if($_SESSION['user']->getStatus() == 'admin') {
                return true;
            }
        }
        return false;
    }

    public function getUserName() {
        return $_SESSION['user']->getName();
    }

    public function disconnectUser() {
        unset($_SESSION['user']);
    }

    public function registration($name, $login, $password) {
        return $this->accountsTab->registration($name, $login, $password);
    }
}