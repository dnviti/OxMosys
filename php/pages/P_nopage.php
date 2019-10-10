<?php

namespace Oxmosys;

use Exception;
use Oxmosys\QueryBuilder;
use PDO;
use Oxmosys\AppConfig;
use Oxmosys\Cookie;

class NoPage
{
    public
        $app,
        $queryBuilder,
        $pageNumber,
        $_assets,
        $_components,
        $_templates,
        $compiledPage,
        $user;

    public function __construct(int $pageNumber)
    {
        $this->app = new AppConfig();
        $this->queryBuilder = $this->app::$qb;

        if ($this->user = Cookie::get("USER")) {
            $this->pageNumber = $pageNumber;
        } else {
            // $dbExists = $this->queryBuilder->run("SELECT upper(SCHEMA_NAME) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->app::$config["db"]["database"] . "'");
            // $dbExists = strtoupper($dbExists[0][0]);
            // $dbCorrect = strtoupper($this->app::$config["db"]["database"]);
            // if ($dbExists == $dbCorrect && (bool)$this->app::$config["db"]["isinit"])
            if ($this->app->dbExists())
                // Se il DB esiste vado al login
                $this->pageNumber = -1;
            else
                // Altrimenti inizio la configurazione applicativa
                $this->pageNumber = 0;
        }

        // Instanzio gli asset, i componenti e i template
        $this->_components = new Component($this->app, $this->pageNumber, $this->queryBuilder);
        $this->_templates = new Template($this->app, $this->pageNumber, $this->queryBuilder, $this->_components);

        $_SESSION["PAGE"]["ID"] = $this->pageNumber;
        // stampo la pagina compilata
        $this->getPage($this->pageNumber);
        echo $this->compiledPage;
    }

    public function compilePage()
    {
        $_components = $this->_components;
        $_templates = $this->_templates;

        $page = ''
            . $_templates->header("404")
            . $_templates->slideMenu()
            . $_templates->body()
            . "<h1 style='text-align:center'>Pagina non Trovata</h1>"
            . $_components->button("Torna alla Home", "primary", 1)
            . $_templates->footer();

        return $page;
    }
}
