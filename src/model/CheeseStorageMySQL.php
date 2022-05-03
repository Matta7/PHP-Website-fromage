<?php

require_once('model/CheeseBuilder.php');
require_once('model/Cheese.php');
require_once('model/CheeseStorage.php');

class CheeseStorageMySQL implements CheeseStorage {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function read($id) {
        $requete = "SELECT * FROM cheese WHERE id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $id);
        $stmt->execute($data);

        $resultatRequete = $stmt->fetch();

        return new Cheese($resultatRequete['name'], $resultatRequete['region'], $resultatRequete['year'], $resultatRequete['creator'], $resultatRequete['image']);
    }

    public function readAll() {
        $requete = "SELECT * FROM cheese";
        $resultatRequete = $this->db->query($requete)->fetchAll();

        $tabRes = array();
        foreach($resultatRequete as $key => $value) {
            $a = new Cheese($value['name'], $value['region'], $value['year'], $value['creator']);
            $tabRes[$value['id']] = $a;
        }
        return $tabRes;
    }

    public function create(Cheese $a) {
        $requete = "INSERT INTO cheese (name, region, year, creator) VALUES (:name, :region, :year, :creator) ";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $a->getName(),
            ':region' => $a->getRegion(),
            ':year' => $a->getYear(),
            ':creator' => $a->getCreator()
        );
        $stmt->execute($data);

        $requete = "SELECT MAX(id) FROM cheese";
        return ($this->db->query($requete)->fetch())['MAX(id)'];
    }

    public function delete($id) {
        $requete = "DELETE FROM cheese WHERE cheese . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':id' => $id);
        $stmt->execute($data);
    }

    public function update($id, $a, $image = null) {
        $requete = "UPDATE cheese SET name = :name, region = :region, year = :year WHERE cheese . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':name' => $a->getName(),
            ':region' => $a->getRegion(),
            ':year' => $a->getYear(),
            ':id' => $id
        );

        $stmt->execute($data);
        if($image != null) {
            $this->addImage($id, $image);
        }
    }

    public function research($search) {
        $requete = "SELECT * FROM cheese WHERE name like :search";
        $stmt = $this->db->prepare($requete);
        $data = array(':search' => "$search%");
        $stmt->execute($data);

        $resultatRequete = $stmt->fetchAll();

        $tabRes = array();
        foreach($resultatRequete as $key => $value) {
            $a = new Cheese($value['name'], $value['region'], $value['year'], $value['creator']);
            $tabRes[$value['id']] = $a;
        }
        return $tabRes;
    }

    // Fonction pour ajouter une image appelée dans la création d'un nouvel fromage ou dans la modification d'un fromage.
    public function addImage($id, $image) {
        $requete = "UPDATE cheese SET image = :image WHERE cheese . id = :id";
        $stmt = $this->db->prepare($requete);
        $data = array(':image' => '' . $id . '.' . str_replace('image/','',$image['type']),
            ':id' => $id
        );

        $stmt->execute($data);
    }
}
