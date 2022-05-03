<?php

require_once('model/Account.php');
require_once('model/AccountStorage.php');

class AccountStorageMySQL implements AccountStorage {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function checkAuth($login, $password) {

        $requete = "SELECT * FROM accounts WHERE accounts . login = :login";
        $stmt = $this->db->prepare($requete);
        $data = array(':login' => $login);

        $stmt->execute($data);
        $resultatRequete = $stmt->fetch();

        if($resultatRequete != null) {
            if(password_verify($password, $resultatRequete['password'])) {
                return new Account($resultatRequete['name'], $login, $password, $resultatRequete['status']);
            }
        }
        return null;
    }

    public function registration($name, $login, $password) {

        if(strip_tags($name) !== $name) {
            return false;
        }


        $requete = "SELECT * FROM accounts";
        $resultatRequete = $this->db->query($requete)->fetchAll();

        foreach($resultatRequete as $key => $value) {
            if($value['login'] === $login) {
                return false;
            }
        }

        $requete = "INSERT INTO accounts (name, login, password) VALUES (:name, :login, :password) ";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $name,
            ':login' => $login,
            ':password' => password_hash($password, PASSWORD_BCRYPT)
        );
        $stmt->execute($data);

        return true;
    }
}