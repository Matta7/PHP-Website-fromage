<?php

/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");

/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
require_once("model/CheeseStorageMySQL.php");
require_once("model/AccountStorageMySQL.php");
require_once("/users/21910887/private/mysql_config.php");
/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */

$dns = $MYSQL_DNS;
$user = $MYSQL_USER;
$pass = $MYSQL_PASSWORD;


/*
$dsn = 'mysql:host=mysql.info.unicaen.fr;port=3306;dbname=21910887_bd;charset=utf8mb4';
$user = '21910887';
$pass = 'lie6So3eijohJei5';
*/

$db = new PDO($dns, $user, $pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$router = new Router();
$router->main(new CheeseStorageMySQL($db), new AccountStorageMySQL($db));