<?php namespace Oxmosys;

use Oxmosys\AppConfig;
use Oxmosys\QueryBuilder;

use Exception;

class DML
{
    public $appConfig, $dbConn, $queryBuilder, $tbname, $tbParentname;
    const NO_CASE = 0, UPPER_CASE = 1, LOWER_CASE = 2;

    public $parentTable, $parentIdArr, $childrenTables;

    public function __construct(array $postArray)
    {
        $app = new AppConfig();
        $this->dbConn = $app::$dbConn;
        $this->appConfig = $app::$config;
        $this->queryBuilder = $app::$qb;


        $postRes = array();

        foreach ($postArray as $key => $value) {
            // non considero il valore OPERATION e il valode UPPERCASE
            // che identifica soltanto il tipo di operazione e se i dati sono UPPERCASE!
            if ($key != "OPERATION" && $key != "UPPERCASE") {
                $arrField = explode("-", $key);
                $arr = [
                    "TABLE" => $arrField[0],
                    "COLUMN" => $arrField[1],
                    "VALUE" => $value
                ];
                array_push($postRes, $arr);
            }
        }
        /** Il Risultato dell'array di post quindi:
         * [0] ==> Tabella,
         * [1] ==> Campo,
         * [2] ==> Valore
         */

        // Ordino i risultati per nome tabella
        sort($postRes);

        // Estraggo l'ID per conoscere la tabella padre
        // le altre poi saranno tutte figlie con chiavi esterne
        $idArr = null;
        foreach ($postRes as $key => $value) {
            if (!in_array("ID", $postRes[$key])) {
                continue;
            } else {
                $idArr = array($key, $postRes[$key]);
                // Rimuovo l'id dalla lista poichè mi è utile solo esternamente
                unset($postRes[$key]);
                break;
            }
        }

        // Raggruppo l'array per TABELLA
        $groupByTable = array();

        foreach ($postRes as $value) {
            $groupByTable[$value["TABLE"]][] = $value;
        }

        // Cerco nell'array gruppato la tabella che comanda by $idArr
        // la prendo e la tolgo dall'array, questa andra scritta per PRIMA
        $parentTableArray = $groupByTable[$idArr[1]["TABLE"]];
        unset($groupByTable[$idArr[1]["TABLE"]]);

        /** Ora ho precisamente 3 array:
         *  parentTableArray[NOME TABELLA]  => Contiene la tabella padre che comanda (quella con la chiave primaria)
         *  groupByTable[N]                 => Contiene tutte le tabelle figlie rimanenti
         *  idArr[1]                        => Contiene i dati della chiave primaria della tabella padre
         */

        // Inserisco gli array generati nelle variabili ci classe
        $this->parentTable = $parentTableArray;
        $this->parentIdArr = $idArr;
        $this->childrenTables = $groupByTable;
    }

    public function logdml($type, $table, $params, $lastid = null, $errormess = null)
    {

        $json = json_encode($params);

        $values = array();

        $values["USERREG"] = $params["USERREG"];
        $values["TABLENAME"] = $table;
        $values["OPERATION"] = $type;
        $values["RAWVALUES"] = $json;
        $values["INSERTED_ID"] = $lastid;
        $values["ERRMESS"] = $errormess;

        try {
            $res = $this->queryBuilder
                ->table("app_dml_operation_log")
                ->insert($values)
                ->run();

            if (!($res > 0 ? true : false)) {
                throw new Exception("FATAL: LOG GENERATION ERROR!");
            }
        } catch (\Throwable $th) {
            //throw $th;
            //die();
        }
    }

    public function insert($params, $forceCase = self::NO_CASE)
    {
        switch ($forceCase) {
            case self::UPPER_CASE:
                $params = array_map('strtoupper', $params);
                break;
            case self::LOWER_CASE:
                $params = array_map('strtolower', $params);
                break;
        }

        try {
            $res = $this->queryBuilder
                ->table($this->tbname)
                ->insert($params)
                ->run();

            $lastid = $this->queryBuilder->lastId();

            $this->logdml("INSERT", $this->tbname, $params, $lastid);

            if (!($res > 0 ? true : false)) {
                throw new Exception("DML (INSERT)<br>TABLE (" . $this->tbname . ")");
            }
        } catch (\Throwable $th) {
            $this->logdml("INSERT", $this->tbname, $params, null, $th->getMessage());
            throw $th;
            die();
        }

        return $lastid;
    }

    public function update($key, $params, $forceCase = self::NO_CASE)
    {
        switch ($forceCase) {
            case self::UPPER_CASE:
                $params = array_map('strtoupper', $params);
                break;
            case self::LOWER_CASE:
                $params = array_map('strtolower', $params);
                break;
        }

        // $key = "ID";
        $id = $this->parentIdArr[1]["VALUE"];

        try {
            $res = $this->queryBuilder
                ->table($this->tbname)
                ->where($key, '=', $id)
                ->update($params)
                ->run();

            $this->logdml("INSERT", $this->tbname, $params);

            if (!($res > -1 ? true : false)) {
                throw new Exception("DML (UPDATE)<br>TABLE (" . $this->tbname . ")<br>" . $key . " (" . $id . ")");
            }
        } catch (\Throwable $th) {
            $this->logdml("UPDATE", $this->tbname, $params, null, $th->getMessage());
            throw $th;
            die();
        }

        return $id;
    }

    public function delete($key, $id, $isLogic = true)
    {
        try {
            if ($isLogic) {

                $res = $this->queryBuilder
                    ->table($this->tbname)
                    ->where($key, '=', $id)
                    ->update(array("OBSOLETE" => 1))
                    ->run();

                //$res = $this->update($this->tbname, $id, array("OBSOLETE" => 1));

                if (!($res > 0 ? true : false)) {
                    throw new Exception("DML (UPDATE)<br>TABLE (" . $this->tbname . ")<br>" . $key . " (" . $id . ")<br>ACTION (OBSOLETE)");
                }
            } else {

                $res = $this->queryBuilder
                    ->table($this->tbname)
                    ->where($key, '=', $id)
                    ->delete()
                    ->run();

                if (!($res > 0 ? true : false)) {
                    throw new Exception("DML (DELETE)<br>TABLE (" . $this->tbname . ")<br>ID (" . $id . ")<br>ACTION (OBSOLETE)");
                }
            }
        } catch (\Throwable $th) {
            $this->logdml("UPDATE", $this->tbname, $params, null, $th->getMessage());
            throw $th;
            die();
        }

        return $res;
    }
}
