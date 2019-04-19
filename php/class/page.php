<?php namespace Oxmosys;

use Exception;
use Oxmosys\QueryBuilder;
use PDO;
use Oxmosys\AppConfig;
use Oxmosys\Cookie;

class Page
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

    private function getPage($pnum)
    {
        switch ($pnum) {
            case -1:
                $this->compiledPage = $this->login();
                break;
            case 0:
                $this->compiledPage = $this->dbconfig();
                break;
            case 1:
                $this->compiledPage = $this->home($pnum);
                break;
            case 2:
                $this->compiledPage = $this->warehouse_item($pnum);
                break;
            case 4:
                $this->compiledPage = $this->supplier($pnum);
                break;
            case 5:
                $this->compiledPage = $this->lista_fornitori($pnum);
                break;
            case 6:
                $this->compiledPage = $this->lista_utenti($pnum);
                break;
            case 7:
                $this->compiledPage = $this->register($pnum);
                break;
            case 8:
                $this->compiledPage = $this->warehouse_items_list($pnum);
                break;
            case 9:
                $this->compiledPage = $this->warehouse_reports($pnum);
                break;
        }
    }

    public function dbconfig()
    {
        $_components = $this->_components;
        $_templates = $this->_templates;

        $page = ''
            . $_templates->header("Configura Database")
            . $_templates->body()
            . $_components->button("Configura Database", 'primary', null, null, 'btn-config-db')
            . $_components->javaScriptLink('configdb')
            . $_templates->footer();

        return $page;
    }

    public function login()
    {
        $_components = $this->_components;
        $_templates = $this->_templates;

        $page = ''
            . $_templates->header("Login")
            . $_templates->body()
            . '<br>'
            . $_components->htmlFromFile('login')
            . $_components->javaScriptFromFile('login')
            . $_templates->footer();

        return $page;
    }


    public function supplier($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;



        isset($_GET["ID"]) ? $rowId = $_GET["ID"] : $rowId = null;

        $isObsoleto = json_decode($_components->valueFromQuery("SELECT obsolete FROM app_suppliers WHERE id = " . $rowId), true)[0]["obsolete"];

        if ($isObsoleto == 1) {
            $_components->isObsolete = true;
        } else {
            $isObsoleto = 0;
            $_components->isObsolete = false;
        }

        if (!isset($_GET['ID']) || $_GET['ID'] == '') {
            $gridForm_btn = [
                $_components->button(
                    'Crea Fornitore',
                    'primary',
                    5,
                    'ajax-action',
                    'btn-insert',
                    "php/actions/send_data_dml.php",
                    "I",
                    "f-supplier"
                )
            ];
        } else {
            $gridForm_btn = [
                $_components->button(
                    'Salva',
                    'success',
                    5,
                    'ajax-action',
                    'btn-update-supplier',
                    "php/actions/send_data_dml.php",
                    "U",
                    "f-supplier"
                ),
                // Visualizzo il bottone elimina solo se admin
                ($this->user["IS_ADMIN"]  == 1 ?
                    $_components->button(
                        'Rendi Obsoleto',
                        'danger',
                        5,
                        'ajax-action',
                        'btn-delete-supplier',
                        "php/actions/send_data_dml.php",
                        "D",
                        "f-supplier"
                    ) : null)
            ];
        }

        $form_items = [
            $_components->vGridRow([
                // $_components->hGridRow([
                //     $_components->itemFromColumn('app_suppliers', 'code', 'hidden', null, null, '0'),
                // ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_suppliers', 'buname', 'text', "Ragione Sociale", "Ragione Sociale"),
                    $_components->itemFromColumn('app_suppliers', 'vatid', 'text', "P.IVA/C.Fiscale", "P.IVA/C.Fiscale"),
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_suppliers', 'email', 'email', "Email", "Email"),
                    $_components->hGridRow([
                        $_components->itemFromColumn('app_suppliers', 'address', 'text', "Indirizzo", "Indirizzo"),
                        $_components->itemFromColumn('app_suppliers', 'telephone', 'text', "Telefono", "Telefono")
                    ])
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_suppliers', 'notes', 'textarea', "Note", "Note")
                ]), ($isObsoleto == 1 ? 'Articolo Obsoleto<br><br>' : '')
            ], 'f_register_items'), ($isObsoleto == 0 ? $_components->hGridRow($gridForm_btn) : ''),

            //Hidden Elements
            $_components->itemFromColumn('app_suppliers', 'code', 'hidden', null, null, 'N/A'),
            $_components->itemFromColumn('app_suppliers', 'id', 'hidden', null, null, $rowId),
            $_components->itemFromColumn('app_suppliers', 'userreg', 'hidden', null, null, $this->user["USER_ID"]),
            $_components->itemFromColumn('app_suppliers', 'userupdate', 'hidden', null, null, $this->user["USER_ID"])
        ];

        $jsObsoleto = "
            $(document).ready(function(){
                $isObsoleto == 1 ? bootbox.alert('Attenzione! Articolo Obsoleto attivo solo in consultazione') : null;
            });
        ";

        $page = ''
            . $_templates->header("Fornitore")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->form($form_items, 'f-supplier')
            . $_components->hGridRow(['<span id="dettWareItem"></span>'])
            . $_components->javaScript($jsObsoleto)
            //. $_components->javaScriptLink('suppliers')
            //. $_components->javaScriptLink('slidemenu')
            . $_templates->footer();

        return $page;
    }

    public function warehouse_items_list($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;

        $gridRow_btn = [
            $_components->button("Nuovo Articolo", "primary", "2")
        ];

        $page = ''
            . $_templates->header("Lista Articoli")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->hGridRow($gridRow_btn, 'btnNav')
            . $_components->tableFromQuery('report/report_articoli', 'table_articoli', 'tbContainer', 'Lista Articoli')
            . $_templates->footer();

        return $page;
    }

    public function warehouse_item($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;

        isset($_GET["ID"]) ? $rowId = $_GET["ID"] : $rowId = null;

        $itemsVals = json_decode($_components->valueFromQuery("SELECT a.obsolete AS obsolete, a.code AS code, b.taglia AS taglia
                                                                FROM app_warehouse_items a
                                                                JOIN app_custom_warehouse_items b ON
                                                                    a.id = b.app_warehouse_items_id
                                                                WHERE id = " . $rowId), true)[0];

        $isObsoleto = $itemsVals["obsolete"];
        $isObsoleto == 1 ? $isObsoleto = 1 : $isObsoleto = 0;

        if (!isset($_GET['ID']) || $_GET['ID'] == '') {
            $gridForm_btn = [
                $_components->button(
                    'Crea Articolo',
                    'primary',
                    false,
                    'ajax-action',
                    'btn-insert',
                    "php/actions/send_data_dml.php",
                    "I",
                    "f-warehouse-item"
                )
            ];
        } else {
            $qtaGiac = $this->queryBuilder->run('SELECT COALESCE(SUM(quantity), 0) AS "Tot Giac"
                                                    FROM app_warehouse_movements a
                                                    WHERE a.app_warehouse_items_id = ' . $_GET['ID']);

            $gridForm_btn = [
                $_components->button(
                    'Salva',
                    'success',
                    false,
                    'ajax-action',
                    'btn-update',
                    "php/actions/send_data_dml.php",
                    "U",
                    "f-warehouse-item"
                ),
                // Visualizzo il bottone elimina solo se admin
                ($this->user["IS_ADMIN"] == 1 && $qtaGiac[0][0] == 0 ?
                    $_components->button(
                        'Rendi Obsoleto',
                        'danger',
                        8,
                        'ajax-action',
                        'btn-delete',
                        "php/actions/send_data_dml.php",
                        "D",
                        "f-warehouse-item"
                    ) : null)
            ];            // prendo i valori dalla query delle tabelle custom
            $app_custom_warehouse_items = $_components->valueFromQueryPhp("SELECT TIPO, MODELLO, COLORE, TAGLIA, GENERE, IMAGEPATH FROM APP_CUSTOM_WAREHOUSE_ITEMS WHERE APP_WAREHOUSE_ITEMS_ID = " . $_GET['ID']);
        }

        // $footer_objs = [
        //     // generazione Menu (codice in variabile pubblica di classe)
        //     $_components->javaScript($this->menuJs),
        //     // colorazione menu attivo
        //     //$_components->javaScript('$("#m-p' . $pnum . '").addClass("active")')
        // ];

        $form_items = [
            $_components->vGridRow([
                $_components->hGridRow([
                    $_components->selectFromQuery('lov/lov_suppliers', 'app_warehouse_items', 'app_suppliers_id', 'classic', 'Fornitore', "", "NO", null, "Fornitore"),
                    $_components->selectFromQuery('lov/lov_generi', 'app_custom_warehouse_items', 'genere', 'classic', 'Genere', "", "NO", (isset($app_custom_warehouse_items) ? $app_custom_warehouse_items["GENERE"] : "DONNA"), "Genere")
                ]),
                $_components->hGridRow(['<br>']),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_custom_warehouse_items', 'tipo', 'autocomplete', "Tipo", "Tipo", (isset($app_custom_warehouse_items) ? $app_custom_warehouse_items["TIPO"] : null)),
                    $_components->itemFromColumn('app_custom_warehouse_items', 'modello', 'autocomplete', "Modello", "Modello", (isset($app_custom_warehouse_items) ? $app_custom_warehouse_items["MODELLO"] : null)),
                    $_components->itemFromColumn('app_custom_warehouse_items', 'colore', 'autocomplete', "Colore", "Colore", (isset($app_custom_warehouse_items) ? $app_custom_warehouse_items["COLORE"] : null)),
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_warehouse_items', 'code', 'text', 'Codice', "Codice"),
                    $_components->selectFromQuery('lov/lov_taglie', 'app_custom_warehouse_items', 'taglia', 'classic', 'Taglia', "", "NO", (isset($app_custom_warehouse_items) ? $app_custom_warehouse_items["TAGLIA"] : "U"), 'Taglia'),
                    $_components->itemFromColumn('app_warehouse_items', 'unitprice', 'number', "Prezzo Unitario", "Prezzo Unitario", "0", null, '', '', 0.01)
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_warehouse_items', 'descri', 'textarea', "Descrizione", 'Descrizione'),
                    $_components->itemFromColumn('app_warehouse_items', 'notes', 'textarea', "Note", "Note")
                ]),
                ($isObsoleto == 1 ? 'Articolo Obsoleto<br><br>' : '')
            ], 'f_warehouse_items'), ($isObsoleto == 0 ? $_components->hGridRow($gridForm_btn) : ''),

            // Caricamento immagine articolo
            $_components->hGridRow(['<br>']),
            $rowId ?
                $_components->hGridRow([
                    '
                    <label class="btn btn-primary">
                        Seleziona Immagine&hellip; <input type="file" id="uploadImage" accept="assets/img/upd/*" name="image" style="display: none;">
                    </label><br><br>' . ($rowId ? '<div id="preview"><img src="' . $app_custom_warehouse_items["IMAGEPATH"] . '" /></div>' : '')
                ]) : '',

            //Hidden Elements
            $_components->itemFromColumn('app_warehouse_items', 'app_measure_units_id', 'hidden', null, null, 1),
            $_components->itemFromColumn('app_warehouse_items', 'id', 'hidden', $rowId),
            $_components->itemFromColumn('app_warehouse_items', 'app_warehouses_id', 'hidden', null, null, 1),
            $_components->itemFromColumn('app_warehouse_items', 'userreg', 'hidden', null, null, $this->user["USER_ID"]),
            $_components->itemFromColumn('app_warehouse_items', 'userupdate', 'hidden', null, null, $this->user["USER_ID"])
        ];

        $jsObsoleto = "
            $(document).ready(function(){
                $isObsoleto == 1 ? bootbox.alert('Attenzione! Articolo Obsoleto attivo solo in consultazione') : null;
            });
        ";

        $page = ''
            . $_templates->header("Art. #" . $itemsVals["code"] . "-" . $itemsVals["taglia"])
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->form($form_items, 'f-warehouse-item')
            . $_components->hGridRow(['<span id="dettWareItem"></span>'])
            . $_components->javaScript($jsObsoleto)
            //. $_components->javaScriptLink('slidemenu')
            . $_templates->footer();

        return $page;
    }

    public function register($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;

        isset($_GET["ID"]) ? $rowId = $_GET["ID"] : $rowId = null;

        $isObsoleto = json_decode($_components->valueFromQuery("SELECT obsolete FROM app_users WHERE id = " . $rowId), true)[0]["obsolete"];

        $isObsoleto == 1 ? $isObsoleto = 1 : $isObsoleto = 0;

        if (!isset($_GET['ID']) || $_GET['ID'] == '') {
            $gridForm_btn = [
                $_components->button(
                    'Crea Utente',
                    'primary',
                    6,
                    'ajax-action',
                    'btn-insert',
                    "php/actions/send_data_dml.php",
                    "I",
                    "f-register"
                )
            ];
        } else {
            $gridForm_btn = [
                $_components->button(
                    'Salva',
                    'success',
                    6,
                    'ajax-action',
                    'btn-update',
                    "php/actions/send_data_dml.php",
                    "U",
                    "f-register"
                ),
                // Visualizzo il bottone elimina solo se admin
                ($this->user["IS_ADMIN"]  == 1 ?
                    $_components->button(
                        'Rendi Obsoleto',
                        'danger',
                        6,
                        'ajax-action',
                        'btn-delete',
                        "php/actions/send_data_dml.php",
                        "D",
                        "f-register"
                    ) : null)
            ];
        }

        $form_items = [
            $_components->vGridRow([
                $_components->hGridRow([
                    $_components->itemFromColumn('app_users', 'name', 'text', "Nome"),
                    $_components->itemFromColumn('app_users', 'surname', 'text', "Cognome"),
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_users', 'username', 'text', "Username"),
                    $_components->itemFromColumn('app_users', 'password', 'password', "Password", null, null, (isset($_GET["ID"]) ? 'disabled' : null)),
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_users', 'email', 'email', "e-Mail"),
                    $_components->selectFromQuery('lov/lov_ruoli', 'app_users', 'app_user_roles_id', 'classic', 'Ruoli', "", "NO", 3),
                ]),
                $_components->hGridRow([
                    $_components->itemFromColumn('app_users', 'notes', 'textarea', "Note")
                ]), ($isObsoleto == 1 ? 'Utente Obsoleto<br><br>' : '')
            ], 'f_register_items'), ($isObsoleto == 0 ? $_components->hGridRow($gridForm_btn) : ''),
            //Hidden Elements
            $_components->itemFromColumn('app_users', 'id', 'hidden', null, null, $rowId),
            $_components->itemFromColumn('app_users', 'userreg', 'hidden', null, null, $this->user["IS_ADMIN"])
        ];

        $jsObsoleto = "
            $(document).ready(function(){
                $isObsoleto == 1 ? bootbox.alert('Attenzione! Utente Obsoleto attivo solo in consultazione') : null;
            });
        ";

        $page = ''
            . $_templates->header("Utente")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->form($form_items, 'f-register')
            . $_components->hGridRow(['<span id="dettRegUser"></span>'])
            . $_components->javaScript($jsObsoleto)
            . $_templates->footer();

        return $page;
    }

    public function home($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;

        $_components = $this->_components;
        $_templates = $this->_templates;

        $nowDate = new \DateTime();

        $page =
            $_templates->header("Homepage")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->vGridRow([
                '<h5 style="text-align:center">Articoli Movimentabili</h5>',
                $_components->hGridRow([
                    $_components->hGridRow([
                        $_components->selectFromQuery('lov/lov_causali', 'app_warehouse_causals', 'id', 'classic', 'Causali', "", "NO", 2, "Causali", null),

                        $_components->hGridRow([
                            $_components->itemFromColumn('app_warehouse_movements', 'datereg', 'date', "Data Mov", "Data Mov", ""),
                            $_components->itemFromColumn('app_warehouse_movements', 'quantity', 'number', "Quantit&agrave;", "Quantit&agrave;", 1, 'min="1"')
                        ])
                    ], "", "margin-top:10px;min-width:350px")
                ])
            ])

            . $_components->tableFromQuery('report/report_homepage', 'table_homepage', 'tbContainer', ' ')
            . $_templates->footer();

        return $page;
    }


    public function lista_utenti($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;

        $page = ''
            . $_templates->header("Lista Utenti")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->hGridRow([
                $_components->button("Nuovo Utente", "Primary", "7")
            ], 'btnNav')
            . $_components->tableFromQuery('report/report_utenti', 'table_utenti', 'tbContainer', 'Lista Utenti')
            . $_templates->footer();

        return $page;
    }

    public function lista_fornitori($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;

        $gridRow_btn = [
            $_components->button("Nuovo Fornitore", "Primary", "4")
        ];

        $page = ''
            . $_templates->header("Lista Fornitori")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->hGridRow($gridRow_btn, 'btnNav')
            . $_components->tableFromQuery('report/report_fornitori', 'table_fornitori', 'tbContainer', 'Lista Fornitori')
            . $_templates->footer();

        return $page;
    }

    public function warehouse_reports($pnum)
    {
        $_SESSION["PAGE"]["ID"] = $pnum;
        $_components = $this->_components;
        $_templates = $this->_templates;

        $page = ''
            . $_templates->header("Report Magazzino")
            . $_templates->slideMenu()
            . $_templates->body()
            . $_components->tableFromQuery('report/report_report_maga', 'table_report_maga', 'tbContainer', 'Lista Report Magazzino')
            . $_templates->footer();

        return $page;
    }
}

class Component
{

    public $isObsolete, $app, $pageNumber, $queryBuilder, $user;

    public function __construct(AppConfig $app, int $pageNumber, QueryBuilder $queryBuilder = null)
    {
        $this->app = $app;
        is_null($queryBuilder) ?
            $this->queryBuilder = $queryBuilder : $this->queryBuilder = new QueryBuilder($this->app::$dbConn);
        $this->pageNumber = $pageNumber;
        $this->user = Cookie::get("USER");
    }

    public function tableFromQuery($queryName, $id, $class = '', $title = '')
    {


        $query = file_get_contents($this->app::$config["paths"]["sql"] . $queryName . '.sql');

        if (isset($query)) {
            $statement = $this->app::$dbConn->prepare($query);

            if ($statement->execute()) {

                $statement->setFetchMode(PDO::FETCH_ASSOC);
                // get table column names
                $fieldnr = $statement->columnCount();

                $tableData = $statement->fetchAll(PDO::FETCH_ASSOC);

                if (sizeof($tableData) > 0) {
                    $tableCols = array_keys($tableData[0]);
                } else {
                    $tableCols = [];
                }

                $tableHeaders = '<thead class="thead-light"><tr>';

                foreach ($tableCols as &$value) {
                    $colName = str_replace('_', ' ', $value);
                    $tableHeaders = $tableHeaders . '<th>' . $colName . '</th>';
                }
                $tableHeaders = $tableHeaders . '</tr></thead>';

                // get table data
                //$tableRows = $result->fetch_array();
                $tableBody = '<tbody class="table-striped">';



                // while ($tableRows = $statement->fetch(PDO::FETCH_ASSOC)) {
                foreach ($tableData as $key => $value) {
                    # code...
                    $headerIdx = 0;

                    $tableBody .= '<tr>';
                    foreach ($tableData[$key] as &$value) {
                        if (is_array($tableCols[$headerIdx])) {
                            $last_value = end($tableCols[$headerIdx]);
                            $last_key = key($tableCols[$headerIdx]);
                            $tableBody .= '<td headers="' . $last_key . '">' . $value . '</td>';
                        } else {
                            $tableBody .= '<td headers="' . $tableCols[$headerIdx] . '">' . $value . '</td>';
                        }
                        $headerIdx++;
                    }
                    $tableBody .= '</tr>';
                }
                $tableBody .= "</tbody>";

                $table = '';

                if ($title != '') {
                    $table = $table . '
                    <div class="row ' . $class . '">
                        <div class="col">
                        <h2>' . $title . '</h2>
                    ';
                }

                $table = $table . '
                        <table id="' . $id . '" class="table table-striped table-bordered table-light" style="width:100%">
                            ' . $tableHeaders . '
                            ' . $tableBody . '
                        </table>
                        </div>
                    </div>
                        ';

                return $table;
            }
        } else {
            return "ERROR: No query defined";
        }
    }

    public function selectFromQuery($queryName, $tableName, $colName, $type, $nullDisplay = null, $nullValue = "", $nullable = "NO", $itemDefault = null, $label = null, $search = false, $attrib = null, $class = '', $title = '')
    {
        isset($_GET['ID']) ? $rowid = $_GET['ID'] : $rowid = null;

        // $type ['classic', 'search']


        if ($rowid != null) {
            $query = "SELECT $colName FROM $tableName WHERE ID = $rowid";
        }

        // Fetch column data by id

        if (isset($query)) {
            if ($result = $this->app::$dbConn->query($query)) {
                while ($tableRows = $result->fetch(PDO::FETCH_ASSOC)) {
                    foreach ($tableRows as &$value) {
                        $itemVal = $value;
                    }
                }
                //$result->close();
            }
        }

        // Fetch column properties

        $query = file_get_contents($this->app::$config["paths"]["sql"] . $queryName . '.sql');

        if (isset($query)) {

            // get table data
            $select = '';
            $lovValues = [];
            $lovReturn = [];
            $lovDisplay = [];




            switch ($type) {
                case 'classic':
                    if ($label != null) {
                        $select = '<label for="' . strtoupper('LOV-' . $tableName . '-' . $colName) . '">' . $label . '</label>';
                    }
                    $select .= '<select ' . ($this->isObsolete ? ' disabled' : '') . ' class="custom-select" aria-nullable="' . $nullable . '" name="' . strtoupper($tableName . '-' . $colName) . '" id="' . strtoupper('LOV-' . $tableName . '-' . $colName) . '" ' . $attrib . '>';

                    break;
                case 'search':
                    if ($label != null) {
                        $select = '<label style="margin-bottom: 14px;" for="' . strtoupper('LOV-' . $tableName . '-' . $colName) . '">' . $label . '</label>';
                    }
                    $select .= '<br><select ' . ($this->isObsolete ? ' disabled' : '') . ' class="selectpicker" aria-nullable="' . $nullable . '" name="' . strtoupper($tableName . '-' . $colName) . '" data-live-search="true" id="' . strtoupper('LOV-' . $tableName . '-' . $colName) . '" ' . $attrib . '>';
                    break;
            }

            if ($result = $this->app::$dbConn->query($query)) {


                while ($tableRows = $result->fetch(PDO::FETCH_NUM)) {
                    array_push($lovDisplay, $tableRows[0]);
                    array_push($lovReturn, $tableRows[1]);
                }

                $lovValues[0] = $lovDisplay;
                $lovValues[1] = $lovReturn;
            }

            if (isset($itemVal)) {
                $itemDefault = $itemVal;
            }

            if ($nullDisplay != null) {
                $itemDefault != null ? $selected = "" : $selected = " selected";
            }

            $select = $select . '<option value="' . $nullValue . '"' . $selected . ' disabled>' . $nullDisplay . '</option>';

            if ($result) {
                for ($i = 0; $i < count($lovValues[0]); $i++) {
                    $display = $lovValues[0][$i];
                    $return = $lovValues[1][$i];
                    $itemDefault == $return ? $selected = " selected" : $selected = "";
                    $select = $select . '<option value="' . $return . '"' . $selected . '>' . $display . '</option>';
                }
            }

            $select = $select . '</select>';

            if ($result) {
                //$result->close();
            }

            return $select;
        } else {
            return "ERROR: No query defined";
        }
    }

    public function itemFromColumn($tableName, $colName, $itemType, $itemName = null, $itemLabel = false, $itemDefault = null, $attrib = null, $class = '', $title = '', $step = 1)
    {
        isset($_GET['ID']) ? $rowid = $_GET['ID'] : $rowid = null;

        if ($itemDefault == null) {
            $itemVal = '';
        }



        if ($rowid != null) {
            $query = "SELECT $colName FROM $tableName WHERE ID = $rowid";
        }

        // Fetch column data by id

        if (isset($query)) {
            if ($result = $this->app::$dbConn->query($query)) {
                while ($tableRows = $result->fetch(PDO::FETCH_ASSOC)) {
                    foreach ($tableRows as &$value) {
                        $itemVal = $value;
                    }
                }
                //$result->close();
            }
        }
        if ($itemType == 'date' || $itemType == 'DATE') {
            $itemVal = substr($itemVal, 0, 10);
        }

        // Fetch column properties

        $query = "SHOW FIELDS FROM $tableName where upper(field) = upper('$colName')";

        if (isset($query)) {
            if ($result = $this->app::$dbConn->query($query)) {

                // get item data

                $itemData = $result->fetch(PDO::FETCH_ASSOC);

                //$result->close();

                $type = $itemData['Type'];
                preg_match('#\((.*?)\)#', $type, $maxLength);

                if (!isset($maxLength[1])) {
                    $maxLength[1] = '999';
                }

                $item = '';

                if ($itemLabel && $itemName != null) {
                    $item = '<label  for="' . strtoupper($tableName . '-' . $itemData['Field']) . '">' . str_replace('_', ' ', $itemName) . '</label>';
                }

                if ($itemType != "password") {
                    if ($itemDefault == null) {
                        if ($itemData['Default'] == 'CURRENT_TIMESTAMP') {
                            $itemDefault = date("Y-m-j");
                        } else {
                            $itemDefault = $itemData['Default'];
                        }
                    }

                    if (isset($itemVal)) {
                        $itemDefault = $itemVal;
                    }
                } else {
                    $itemDefault = null;
                }

                switch ($itemType) {
                    case 'textarea':
                        $item = $item
                            . '<textarea maxlength="' . $maxLength[1]
                            . ($this->isObsolete ? '" disabled' : '"')
                            . ' class="form-control" table="' . strtoupper($tableName) . '" name="' . strtoupper($tableName . '-' . $itemData['Field'])
                            . '" id="' . strtoupper($itemType . '-' . $tableName . '-' . $itemData['Field'])
                            . '" placeholder="' . ($itemName != null ? str_replace('_', ' ', $itemName) : str_replace('_', ' ', $itemData['Field']))
                            . '" aria-nullable="' . $itemData['Null'] . '" '
                            . $attrib
                            . '>'
                            . $itemDefault
                            . '</textarea>';
                        break;
                    default:
                        $item = $item
                            . '<input maxlength="' . $maxLength[1]
                            . ($this->isObsolete ? '" disabled' : '"')
                            . ' value="' . $itemDefault
                            . '" type="' . $itemType
                            . '' . ($itemType == 'number' ? '" step="' . $step . '"' : '')
                            . '" class="form-control" table="' . strtoupper($tableName) . '" name="' . strtoupper($tableName . '-' . $itemData['Field'])
                            . '" id="' . strtoupper(str_replace("-", "_", $itemType) . '-' . $tableName . '-' . $itemData['Field'])
                            . '" placeholder="' . ($itemType == 'date' ? '' : ($itemName != null ? str_replace('_', ' ', $itemName) : str_replace('_', ' ', $itemData['Field'])))
                            . '" aria-nullable="' . $itemData['Null'] . '" '
                            . $attrib
                            . ' />';
                }


                $item = $item . '<br/>';

                return $item;
            }
        } else {
            return "ERROR: No query defined";
        }
    }

    public function valueFromQuery($query)
    {


        //$query = file_get_contents($this->app::$config["paths"]["sql"] . $queryName . '.sql');

        if (isset($query)) {
            if ($result = $this->app::$dbConn->query($query)) {


                // get table data
                $valueArray = [];

                while ($tableRows = $result->fetch(PDO::FETCH_ASSOC)) {
                    array_push($valueArray, $tableRows);
                }

                //$result->close();

                $jsonData = json_encode($valueArray);
                //var_dump($jsonData);
                return $jsonData;
            }
        } else {
            return "ERROR: No query defined";
        }
    }


    public function valueFromQueryFile($filepath, $params = [])
    {
        //$query = file_get_contents($this->app::$config["paths"]["sql"] . $queryName . '.sql');

        if (isset($filepath)) {
            $query = file_get_contents($this->app::$config["paths"]["sql"] . $filepath);

            if ($query) {
                $this->app::$qb->setFetchMode(PDO::FETCH_ASSOC);
                $result = $this->app::$qb->run($query, $params);
                // get table data
                // $valueArray = [];

                // while ($tableRows = $result->fetch(PDO::FETCH_ASSOC)) {
                //     array_push($valueArray, $tableRows);
                // }

                //$result->close();

                $jsonData = json_encode($result);
                //var_dump($jsonData);
                return $jsonData;
            }
        } else {
            return "ERROR: No query defined";
        }
    }

    public function valueFromQueryPhp($query)
    {


        //$query = file_get_contents($this->app::$config["paths"]["sql"] . $queryName . '.sql');

        if (isset($query)) {
            if ($result = $this->app::$dbConn->query($query)) {


                // get table data
                $valueArray = [];

                while ($tableRows = $result->fetch(PDO::FETCH_ASSOC)) {
                    array_push($valueArray, $tableRows);
                }

                //$result->close();

                return $valueArray[0];
            }
        } else {
            return "ERROR: No query defined";
        }
    }

    public function hGridRow($contentarr = [], $classRow = '', $styleCols = '')
    {
        $hGridRow = '<div class="row ' . $classRow . '">';
        foreach ($contentarr as &$content) {
            $hGridRow = $hGridRow . '
            <div class="col" style="' . $styleCols . '">
              ' . $content . '
            </div>';
        }
        $hGridRow = $hGridRow . '</div>';

        return $hGridRow;
    }

    public function vGridRow($contentarr = [], $class = '')
    {
        $vGridRow = '';
        foreach ($contentarr as &$content) {
            $vGridRow = $vGridRow . '
            <div class="row">
                <div class="col">
                ' . $content . '
                </div>
            </div>';
        }

        return $vGridRow;
    }

    public function logo($filename)
    {
        $logo = '
        <div class="row">
            <div class="col">
                <img src="' . $this->app::$config["paths"]["img"] . $filename . '" class="img-fluid">
            </div>
        </div>
        ';

        return $logo;
    }

    public function button(
        $text,
        $type = 'primary',
        $page = null,
        $class = '',
        $id = '',
        $ajaxAction = null,
        $ajaxActionType = null,
        $ajaxForm = null
    ) {
        if (!is_null($page) && is_null($ajaxAction)) {
            $onclick = ' onclick="javascript:event.preventDefault();location.href=\'?p=' . $page . '\'"';
        } else {
            $onclick = ' onclick="javascript:event.preventDefault()" ajax-topage="' . $page . '"';
        }

        if (!is_null($ajaxAction) && !is_null($ajaxActionType)) {
            $ajaxAction = !is_null($ajaxAction) ? ' ajax-action="' . $ajaxAction . '"' : '';
            $ajaxActionType = !is_null($ajaxActionType) ? ' ajax-action-type="' . $ajaxActionType . '"' : '';
            $ajaxForm = !is_null($ajaxForm) ? ' ajax-form="' . $ajaxForm . '"' : '';
        } else {
            $ajaxAction = "";
            $ajaxActionType = "";
            $ajaxForm = "";
        }

        $button = '<button type="button"'
            . $onclick
            . ' id="' . $id
            . ($this->isObsolete ? '" disabled' : '"')
            . ' class="btn btn-' . strtolower($type) . '  btn-block ' . $class . '"'
            . $ajaxAction
            . $ajaxActionType
            . $ajaxForm
            . '>' . $text . '</button>';

        return $button;
    }

    public function javaScript($js)
    {
        $scriptJs = '<script>' . $js . '</script>';
        return $scriptJs;
    }

    public function javaScriptFromFile($jsPath)
    {
        $js = file_get_contents($this->app::$config["paths"]["js"] . $jsPath . '.js');
        $scriptJs = '<script>' . $js . '</script>';
        return $scriptJs;
    }

    public function javaScriptLink($jsPath)
    {
        $jsPath = $this->app::$config["paths"]["js"] . $jsPath . '.js';
        $scriptJs = '<script type="text/javascript" src="' . $jsPath . '"></script>';
        return $scriptJs;
    }

    public function javaScriptLinkToMerge($jsFolderToMerge, $topText = null, $bottomText = null, $minify = false)
    {
        if (isset($this->user["USER_ID"])) {
            // Denomino il file con l'uid utente
            $jsPath = $this->app::$config["paths"]["js"] . $jsFolderToMerge;
            $outFileName = $jsPath . "/" . $jsFolderToMerge . "_merged_uuid" . $this->user["USER_ID"] . ".js";
            // Se il file esiste lo elimino e lo rigenero (per id utente)
            if (file_exists($outFileName)) unlink($outFileName);
            // if (!file_exists($outFileName)) return null;

            // Inizio la creazione del file Merge
            $files = array_diff(scandir($jsPath), array('.', '..'));

            $jsMerge = fopen($outFileName, "w");

            // Scrivi il testo all'inizio
            if (!is_null($topText)) {
                $topText .= "\r\n";
                fwrite($jsMerge, $topText);
            }

            foreach ($files as $file) {
                $fullFilePath = $jsPath . "/" . $file;
                $topFileComment = "\n/**\r\n * Source Folder: \"" . $jsPath . "/\" \r\n * Source File Name: \"" . $file . "\"\r\n */\r\n\r\n";
                $jsContent = file_get_contents($fullFilePath);
                fwrite($jsMerge, $topFileComment . $jsContent);
            }

            // Scrivi il testo alla fine
            // Scrivi il testo all'inizio
            if (!is_null($bottomText)) {
                $bottomText = "\r\n" . $bottomText . "\r\n";
                fwrite($jsMerge, $bottomText);
            }

            fclose($jsMerge);

            // Dopo averlo generato creo l'html necessario per incorporarlo
            $scriptJs = '<script type="text/javascript" src="' . $outFileName . '"></script>';
            return $scriptJs;
        } else {
            return null;
        }
    }

    public function htmlFromFile($htmlPath)
    {
        $html = file_get_contents($this->app::$config["paths"]["html"] . $htmlPath . '.html');
        return $html;
    }

    public function separator($height)
    {
        $separator = '<br style="margin-top:' . $height . 'px">';
        return $separator;
    }
    public function form($contentarr = [], $id, $class = '')
    {
        $form = '<form id="' . $id . '" enctype="multipart/form-data">';
        foreach ($contentarr as &$content) {
            $form = $form . $content;
        }
        $form = $form . '</form>';

        return $form;
    }
}

class Asset
{
    public $appDbConn, $appConfig, $queryBuilder, $pageNumber, $pageComponents, $user;

    public function __construct(AppConfig $app, int $pageNumber, QueryBuilder $queryBuilder, Component $pageComponents)
    {
        $this->app = $app;
        $this->queryBuilder = $queryBuilder;
        $this->pageNumber = $pageNumber;
        $this->pageComponents = $pageComponents;
        $this->user = Cookie::get("USER");
    }

    function getCss($paths)
    {
        $ret = "";
        foreach ($paths as &$value) {
            $ret .= "<link rel=\"stylesheet\" href=\"$value\">\r\n";
        }

        return $ret;
    }
    function getJs($paths)
    {
        $ret = "";
        foreach ($paths as &$value) {
            $ret .= "<script type=\"text/javascript\" src=\"$value\"></script>";
        }
        return $ret;
    }
}

class Template extends Asset
{
    public function header($pageTitle = false)
    {
        $_paths = $this->app::$config["paths"];

        $header = '<!DOCTYPE html>
        <html lang="en">
            
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon" sizes="16x16" />
                <!-- CSS -->

                    ';
        $header .= $this->getCss([
            //$_paths["third-part"] . "bootstrap/bootstrap.min.css",
            $_paths["third-part"] . "jquery/jquery-ui.css",
            $_paths["third-part"] . "bootstrap/css/bootstrap.min.css",
            $_paths["css"] . "bsThemeMaterial.css",
            $_paths["third-part"] . "bootstrap-select/bootstrap-select.min.css",
            $_paths["third-part"] . "slideout/slideout.css",
            $_paths["third-part"] . "hamburgers/hamburgers.min.css",
            $_paths["third-part"] . "holdon/HoldOn.min.css",
            $_paths["third-part"] . "DataTables/dataTables.min.css",
            $_paths["third-part"] . "DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css",
            $_paths["third-part"] . "animate/animate.css",
            $_paths["css"] . "style.css"
        ]);

        $header .= '
                <!-- JS -->
                ' . $this->getJs([
            $_paths["third-part"] . "jquery/jquery-3.3.1.min.js",
            $_paths["third-part"] . "jquery/jquery-ui.min.js",
            $_paths["third-part"] . "popper/popper.min.js",
            $_paths["third-part"] . "bootstrap/js/bootstrap.min.js",
            $_paths["third-part"] . "bootstrap-select/bootstrap-select.min.js",
            $_paths["third-part"] . "slideout/slideout.min.js",
            $_paths["third-part"] . "holdon/HoldOn.min.js",
            $_paths["third-part"] . "DataTables/datatables.min.js",
            $_paths["third-part"] . "DataTables/RowReorder-1.2.4/js/dataTables.rowReorder.min.js",
            $_paths["third-part"] . "DataTables/Responsive-2.2.2/js/dataTables.responsive.min.js",
            $_paths["third-part"] . "bootbox/bootbox.all.min.js",
            $_paths["third-part"] . "fontawesome5/js/all.js",
            $_paths["third-part"] . "bootstrap-notify/bootstrap-notify.min.js",
            $_paths["third-part"] . "isjs/is.min.js",
            $_paths["js"] . "main.js"
        ]) . '
            </head>
            <title>OxMosys ' . ($pageTitle ? " - " . $pageTitle : "") . '</title>
        <body>';

        // Main Page items
        // Current User ID
        if (isset($this->user["USER_ID"])) {
            $header .= '<input type="hidden" id="p_user_id" value="' . $this->user["USER_ID"] . '" />';
        }
        // Current Page ID
        if (isset($_SESSION["PAGE"]["ID"])) {
            $header .= '<input type="hidden" id="p_page_id" value="' . $_SESSION["PAGE"]["ID"] . '" />';
        }
        // Current User Name
        if ($this->user["USERNAME"]) {
            $header .= '<input type="hidden" id="p_user_name" value="' . $this->user["USERNAME"] . '" />';
        }
        // Current User Is Admin
        if (isset($this->user["IS_ADMIN"])) {
            $header .= '<input type="hidden" id="p_is_admin" value="' . $this->user["IS_ADMIN"] . '" />';
        }

        return $header;
    }

    public function slideMenu()
    {
        $_components = $this->pageComponents;
        $slidenav = '        <nav id="menu">
        <div class="container-fluid">
            <div class="row">
                <!--<div class="col">-->
                    <h4 style="margin-top: 10px;margin-left: 15px;">Menù</h4>
                <!--</div>-->
            </div>
            <div class="list-group">
                <div class="row">
                <div class="col">
                    <a href="?p=1" id="m-p1" class="list-group-item list-group-item-action">Home</a>
                </div>';

        if ($this->user) {
            $slidenav .=
                '<!--<span class="slideout-spacer"></span>-->
                    <div class="col">
                        <a href="javascript:void(0)" id="slnav-logout" class="list-group-item list-group-item-action">Logout</a>
                    </div>
                </div>';
        }

        $slidenav .= '<div class="row">
        <input type="text" class="form-control" id="menu-search" aria-describedby="menuSearch" placeholder="Cerca Menu">
        </div>';

        $slidenav .=
            '<span class="filterable">
            <h6 data-toggle="collapse" href="#collapse-menu-1"><i class="fas fa-chevron-down"></i> Gestione Magazzino</h6>
                    <div class="row collapse" id="collapse-menu-1">';

        $slidenav .= '<input type="text" class="form-control menu-search-item" id="menu-search-item-1" aria-describedby="menuSearchItem" placeholder="Cerca Funzione">';

        $slidenav .= '<a href="?p=2" id="m-p2" class="list-group-item list-group-item-action filterable-item">Nuovo Articolo</a>
                        <a href="?p=8" id="m-p8" class="list-group-item list-group-item-action filterable-item">Lista Articoli</a>
                    </div>
                    </span>';

        $slidenav .= '<span class="filterable">
                    <h6 data-toggle="collapse" href="#collapse-menu-2"><i class="fas fa-chevron-down"></i> Gestione Fornitori</h6>
                    <div class="row collapse" id="collapse-menu-2">
                    <input type="text" class="form-control menu-search-item" id="menu-search-item-2" aria-describedby="menuSearchItem" placeholder="Cerca Funzione">
                        <a href="?p=5" id="m-p5" class="list-group-item list-group-item-action filterable-item">Lista Fornitori</a>
                        <a href="?p=4" id="m-p4" class="list-group-item list-group-item-action filterable-item">Registrazione Fornitore</a>
                      
                    </div>
                    </span>';
        $slidenav .= '<span class="filterable">
                    <h6 data-toggle="collapse" href="#collapse-menu-4"><i class="fas fa-chevron-down"></i> Report</h6>
                    <div class="row collapse" id="collapse-menu-4">
                    <input type="text" class="form-control menu-search-item" id="menu-search-item-4" aria-describedby="menuSearchItem" placeholder="Cerca Tipi Report">
                        <a href="?p=9" id="m-p9" class="list-group-item list-group-item-action filterable-item">Magazzino</a>
                      
                    </div>
                    </span>';

        if (isset($this->user["IS_ADMIN"])) {
            $slidenav .= '
            <span class="filterable">
                <h6 data-toggle="collapse" href="#collapse-menu-3"><i class="fas fa-chevron-down"></i> Gestione Utenti</h6>
                <div class="row collapse" id="collapse-menu-3">';

            $slidenav .= '<input type="text" class="form-control menu-search-item" id="menu-search-item-3" aria-describedby="menuSearchItem" placeholder="Cerca Funzione">';

            $slidenav .= '
                    <a href="?p=6" id="m-p6" class="list-group-item list-group-item-action filterable-item">Lista Utenti</a>
                    <a href="?p=7" id="m-p7" class="list-group-item list-group-item-action filterable-item">Registrazione Utente</a>
                </div>
            ';
        }

        // $slidenav = $slidenav .
        //     $_components->hGridRow([
        //         $_components->logo(''),
        //         $_components->logo(''),
        //         $_components->logo('')
        //     ]);
        $slidenav = $slidenav . '
              </div>
        </div>
    </nav>';

        return $slidenav;
    }

    public function body()
    {
        $_components = $this->pageComponents;
        $body = '<main id="panel">';
        if ($this->user) {
            $body .= '<button class="toggle-menu hamburger hamburger--slider" type="button"  tabindex="0" aria-label="Menu" role="button" aria-controls="navigation">
                    <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                    </span>
                </button>';
        }
        $body .= '<div class="container-fluid">
        <div class="row">
            <div class="col">'; /*
        if ($this->user) {
            $body = $body . '<button class="toggle-menu hamburger hamburger--slider" type="button"  tabindex="0" aria-label="Menu" role="button" aria-controls="navigation">
                    <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                    </span>
                </button>';
        } else {
            $body = $body . '<br>';
        }*/
        $body .= '</div>
        </div>
        ';

        return $body;
    }

    public function footer($objs = [])
    {
        $_components = $this->pageComponents;
        // $footer = $_components->javaScriptLink("autocomplete");
        //. $_components->javaScriptLink('footer/slideout');
        //$_assets = new Asset();
        $footer = '
            </div>
            </main>
            <div class="overlay"></div>
            ';

        foreach ($objs as &$value) {
            $footer = $footer . $value;
        }

        $footer .=

            //$_components->javaScriptLink('register')
            // $_components->javaScriptLink('slidemenu')
            $_components->javaScriptLinkToMerge("footer")
            . $_components->javaScriptLink('ajax/dml')
            // . $_components->javaScriptLink("ready")
            . $_components->javaScriptLinkToMerge("ready", "$(document).ready(function(){", "});")
            // colorazione menu attivo
            . $_components->javaScript('$("#m-p' . $this->pageNumber . '").addClass("active")');

        $footer .= '
            </body>
            <script>
                $(".list-group-item.list-group-item-action").click(function () {
                    if (this.href.slice(-1) == "#") {
                        bootbox.alert("Funzione in via di sviluppo");
                    }
                });
                $(\'[data-toggle="tooltip"]\').tooltip();
            </script>
            </html>
        ';
        return $footer;
    }
}
