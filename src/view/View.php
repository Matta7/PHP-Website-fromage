<?php

class View {

    private $title;
    private $content;
    protected $router;
    private $feedback;

    public function __construct($title, $content, $router, $feedback) {
        $this->title = $title;
        $this->content = $content;
        $this->router = $router;
        $this->feedback = $feedback;
    }

    public function render() {
        if($this->title === null || $this->content === null) {
            $this->makeTestPage();
        }
        $title = $this->title;
        $content = $this->content;
        $menu = $this->getMenu();
        $feedback = $this->feedback;

        include("template.php");
    }

    //@TODO enlever conditions (compte non connecté ici) et problème avec <nav>
    public function getMenu() {
        $menu = "<nav><ul><li id = 'accueil'> <a href='fromages.php'>Page d'accueil</a></li>\n";
        $menu .= "<li id='listeFromage'><a href='?liste'>Liste des fromages</a></li>\n";
        $menu .= "<li><a id='aPropos' href='?action=" . 'aPropos' . "'>À Propos</a></li></ul></nav>";
        $menu .= "<button onclick=\"window.location.href = '" . $this->router->getLoginURL() . "';\">Se connecter</button>\n";
        $menu .= "<button onclick=\"window.location.href = '" . $this->router->getRegistrationURL() . "';\">S'inscrire</button>\n";

        return $menu;
    }

    public function makeHomePage() {
        $this->title = 'Page d\'accueil';
        $this->content = '<h1> Bienvenue sur le site ! </h1>\n';
    }

    public function makeTestPage() {
        $this->title = "Test";
        $this->content = "Test\n";
    }

    public function makeUnknownCheesePage() {
        $this->title = "Fromage inconnu";
        $this->content = "Fromage inconnu\n";
    }

    public function makeCheesePage($cheese, $id = null) {
        $this->title = $cheese->getName();
        $this->content = '<p>' . $cheese->getName() . ' est un fromage issue de la region ' . $cheese->getRegion() . '. Crée en ' . $cheese->getYear() . "</p>\n";
        $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getCheeseUpdateURL($id) . "';\">Modifier</button>\n";
        $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getCheeseAskDeletionURL($id) . "';\">Supprimer</button>\n";
        //$this->content .= "<a href='" .  ."'> Modifier </a>\n";
        //$this->content .= "<a href='" . $this->router->getCheeseAskDeletionURL($id) ."'> Supprimer </a>\n";
        if($cheese->getImage() != null) {
            $this->content .= "<p><img src='upload/" . $cheese->getImage() . "' alt='" . $cheese->getName() . "'></p>\n";
        }
    }

    public function makeListPage($cheeseTab, $page = 1) {
        $this->title = 'Liste des fromages';

        $this->content = "<form action='" . $this->router->getCheeseResearchURL() . "' method='POST'>\n";
        if(key_exists('search', $_SESSION)) {
            $this->content .= "<p>Rechercher : <input type='text' name='search' value='" . $_SESSION['search'] . "'/></p></form>\n";
        }
        else {
            $this->content .= "<p>Rechercher : <input type='text' name='search'/></p></form>\n";
        }

        if(key_exists('search', $_SESSION)) {
            $this->content .= "<nav>\n<ul>\n";
            foreach($cheeseTab as $key => $value) {
                $this->content .= "<li><a href='" . $this->router->getCheeseURL($key) . "'>" . $value->getName() . "</a></li>\n";
            }
            $this->content .= "</ul>\n</nav>\n";
        }
        else {
            $this->pagination($cheeseTab, $page);
        }
    }

    // Fonction pour le complément Pagination.
    public function pagination($cheeseTab, $page) {

        $nbObjectPerPage = 5;
        $pagination = array_keys($cheeseTab);
        $nbObject = count($pagination);
        $nbPages = ceil($nbObject / $nbObjectPerPage)+1;
        $firstObjet = ($page * $nbObjectPerPage) - $nbObjectPerPage;

        $error = false;
        if($page >= $nbPages) {
            $this->makeListPage($cheeseTab, $page-1);
            $error = true;
        }

        if(!$error) {
            $this->content .= "<nav>\n<ul>\n";
            for ($i = $firstObjet; $i < $firstObjet + $nbObjectPerPage; $i++) {
                if(key_exists($i, $pagination)) {
                    $this->content .= "<li><a href='" . $this->router->getCheeseURL($pagination[$i]) . "'>" . $cheeseTab[$pagination[$i]]->getName() . "</a></li>\n";
                }
            }
            $this->content .= "</ul>\n</nav>\n";

            for ($i = 1; $i < $nbPages; $i++) {
                $this->content .= "<button onclick=\"window.location.href = '" . $this->router->getPageURL($i) . "';\">$i</button>\n";
            }
        }
    }

    public function makeDebugPage($variable) {
        $this->title = 'Debug';
        $this->content = '<pre>'.htmlspecialchars(var_export($variable, true)) . "</pre>\n";
    }

    public function makeCheeseCreationPage($cheeseBuilder = null) {
        if ($cheeseBuilder === null) {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getCheeseSaveURL() . "' method='POST'>\n
            <p>Nom du fromage : <input type='text' name='name' /></p>\n
            <p>Région du fromage : <input type='text' name='region' /></p>\n
            <p>Année de creation du fromage : <input type='text' name='year' /></p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'></p>\n
            <p><input type='submit' value='Créer'></p>\n
            </form>\n";
        } else {
            $cheese = $cheeseBuilder->createCheese();
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getCheeseSaveURL() . "' method='POST'>\n
            <p>Nom du fromage : <input type='text' name='name' value='" . $cheese->getName() . "' />" . $cheeseBuilder->getError()['name'] . "</p>\n
            <p>Region du fromage : <input type='text' name='region' value='" . $cheese->getRegion() . "' />" . $cheeseBuilder->getError()['region'] . "</p>\n
            <p>Année de creation du fromage : <input type='text' name='year' value='" . $cheese->getYear() . "' />" . $cheeseBuilder->getError()['year'] . "</p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'>" . $cheeseBuilder->getError()['image'] . "</p>\n
            <p><input type='submit' value='Créer'></p>\n
            </form>\n";
        }
    }

    public function makeCheeseDeletionPage($id) {
        $this->title = 'Supprimer ?';
        $this->content = "<h1>Voulez vous vraiment supprimer ce fromage ?</h1>\n<form action='".$this->router->getCheeseDeletionURL($id)."' method='POST'>\n
        <button>Supprimer</button>\n
        </form>\n";
    }

    public function makeCheeseUpdatePage($id, $cheeseBuilder = null) {
        $cheese = $cheeseBuilder->createCheese();
        if($cheeseBuilder->getError() != null) {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getCheeseUpdatedURL($id) . "' method='POST'>\n
            <p>Nom du fromage : <input type='text' name='name' value='" . $cheese->getName() . "' />" . $cheeseBuilder->getError()['name'] . "</p>\n
            <p>Region du fromage : <input type='text' name='region' value='" . $cheese->getRegion() . "' />" . $cheeseBuilder->getError()['region'] . "</p>\n
            <p>Année de creation du fromage : <input type='text' name='year' value='" . $cheese->getYear() . "' />" . $cheeseBuilder->getError()['year'] . "</p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'>" . $cheeseBuilder->getError()['image'] . "</p>\n
            <p><input type='submit' value='Modifier'></p>\n
            </form>\n";
        }

        else {
            $this->content = "<form enctype='multipart/form-data' action='" . $this->router->getCheeseUpdatedURL($id) . "' method='POST'>\n
            <p>Nom du fromage : <input type='text' name='name' value='" . $cheese->getName() . "' /></p>\n
            <p>Region du fromage : <input type='text' name='region' value='" . $cheese->getRegion() . "' /></p>\n
            <p>Année de creation du fromage : <input type='text' name='year' value='" . $cheese->getYear() . "' /></p>\n
            <p>Insérer une image correspondant (optionnel) : <input type='file' name='image'></p>\n
            <p><input type='submit' value='Modifier'></p>\n
            </form>\n";
        }

    }

    public function makeLoginFormPage() {
        $this->title = 'Connexion';
        $this->content = "<form action='" . $this->router->getAuthenticationURL() . "' method='POST'>\n
        <p>Nom d'utilisateur : <input type='text' name='login'/></p>\n
        <p>Mot de passe : <input type='password' name='password'/></p>\n
        <p><input type='submit' value='Se connecter'></p>\n
        </form>\n";
    }

    public function makeRegistrationFormPage() {
        $this->title = 'Inscription';
        $this->content = "<form action='" . $this->router->getRegisteredURL() . "' method='POST'>\n
        <p>Nom : <input type='text' name='name'/></p>\n
        <p>Nom d'utilisateur : <input type='text' name='login'/></p>\n
        <p>Mot de passe : <input type='password' name='password'/></p>\n
        <p>Confirmer mot de passe : <input type='password' name='confirmPassword'/></p>\n
        <p><input type='submit' value='Se connecter'></p>\n
        </form>\n";
    }

    public function makeAProposPage(){
        $this->title = 'À propos';
        $this->content="<p>Numéro de groupe : Groupe 64</p>\n";
        $this->content.="<p>Numéros étudiants des membres du groupe : 21910887 & 21908377</p>\n";
        $this->content.="<p>Nous avons réalisé toutes les fonctionnalité de base, c'est à dire, la création d'objet ainsi que l'authentification d'un compte, tout deux répertoriés dans une base de donnée MySQL.</p>\n";
        $this->content.="<p>Un utilisateur connecté peut ainsi ajouter des objets, modifier et supprimer l'objet qu'il crée, sauf dans le cas où son statue est admin.</p>\n";
        $this->content.="<p>Nous avons aussi implémenter la création de compte, un visiteur lambda peut ainsi se créer un compte.</p>\n";
        $this->content.="<p>Parmi les compléments suggérés, nous avons réaliser :</p>\n";
        $this->content.="<ul>\n<li>(*) Une recherche d'objets, nous pouvons ainsi rechercher un objet à partir d'une chaine de caractère. La recherche d'objet est accessible depuis la liste des fromages.</li>\n<li>(*) Associer des images aux obets, un objet peut être illustré par zéro ou une image modifiable par le créateur de l'objet ou l'admin.</li>\n<li>En troisième et dernier complément, nous avons implémenter le système de pagination. Il s'applique lorsque la liste des objets est affiché, n'est pas utilisé lors de la recherche mais le pourrait.</li></ul>\n";
        $this->content.="<p>Répartition des tâches : nous nous sommes répartis les tâches surtout au sein du model. Nous avons ainsi globalement fait la vue et le controller ensemble.</p>\n";
        $this->content.="<p>WILLENBUCHER Gurvan s'est occupé de la création d'objets et de saisir les URL pour la vue, ainsi que le CSS.</p>\n";
        $this->content.="<p>VALLÉE Mathieu s'est occupé de la partie authentification des comptes et création, et aussi la gestion de la base de donnée.</p>\n";
        $this->content.="<p>Pour ce qui est des principaux choix en matière de design, nous avons suivi l'ordre des TP des séances 12 à 17. Nous avons ainsi une structure MVCR comme demandée dans l'énoncé.</p>\n";
        $this->content.="<p>Le model représente toutes les données du site, intéractions avec la base de donnée comprises, la vue va afficher, sans modifier le model toutes les pages, le controller va nous permettre d'intéragir entre le model et la vue et le routeur nous permet de gérer les URL, créer la vue et le controller ainsi qu'à gérer les droits des utilisateurs.</p>\n";
        $this->content.="<p>Note : nous avions commencé à implémenter le responsive, que nous avons abandonné depuis pour un autre complément. Ainsi nous avions surement laissé des traces.</p>\n";
    }
    public function displayCheeseCreationSuccess($id){
        $this->router->POSTredirect('?id='. $id, "Le fromage a été crée avec succès.");
    }

    public function displayCheeseCreationFailure() {
        $this->router->POSTredirect('?action=nouveau', "Un champ est invalide.");
    }

    public function displayCheeseDeletionSuccess() {
        $this->router->POSTredirect('?liste', "Le fromage a été supprimé avec succès.");
    }

    public function displayCheeseDeletionFailure($id) {
        $this->router->POSTredirect("?id=$id", "L'action a échoué.");
    }

    public function displayCheeseUpdatedSuccess($id) {
        $this->router->POSTredirect("?id=$id", "Le fromage a été modifié.");
    }

    public function displayCheeseUpdatedFailure($id) {
        $this->router->POSTredirect("?action=modification&id=$id", "Un champ est invalide.");
    }

    public function displayCheeseResearchListSuccess($search) {
        $this->router->POSTredirect("?liste=$search", "Fromages commençant par $search :");
    }

    public function displayCheeseResearchListFailure() {
        $this->router->POSTredirect("?liste", "Recherche invalide");
    }

    public function displayCheeseAuthenticationSuccess($name) {
        $this->router->POSTredirect($this->router->getHomePageURL(), "Vous êtes connecté $name.");
    }

    public function displayCheeseAuthenticationFailure() {
        $this->router->POSTredirect('?action=connexion', "Nom d'utilisateur ou mot de passe erroné.");
    }

    public function displayCheeseDisconnectionFailure() {
        $this->router->POSTredirect($this->router->getHomePageURL(), "Vous êtes déconnecté.");
    }

    public function displayCheeseRegistrationSuccess($name) {
        $this->router->POSTredirect($this->router->getHomePageURL(), "Inscription réussie. Vous êtes connecté $name");
    }

    public function displayCheeseRegistrationFailure() {
        $this->router->POSTredirect($this->router->getRegistrationURL(), "Un champ est invalide.");
    }
}
