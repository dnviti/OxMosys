<?php namespace Oxmosys;

use Oxmosys\AppConfig;
use Exception;

class User
{
    public $username;
    public $user_id;
    public $app, $queryBuilder;

    public function __construct()
    {
        $app = new AppConfig();
        $this->dbConn = $app::$dbConn;
        $this->appConfig = $app::$config;
        $this->queryBuilder = $app::$qb;
    }

    public function register($params)
    {
        try {
            $this->queryBuilder
                ->table('app_users')
                ->insert($params)
                ->run();
        } catch (\Throwable $th) {
            return "Error: " . $th->getMessage();
        }
    }

    public function update($id, $params)
    {
        try {
            $this->queryBuilder
                ->table('app_users')
                ->where('ID', '=', $id)
                ->update($params)
                ->run();
        } catch (\Throwable $th) {
            return "Error: " . $th->getMessage();
        }
    }

    public function delete($tbname, $id)
    {
        if ($id != 1) {
            try {
                $res = $this->queryBuilder
                    ->table($tbname)
                    ->where('ID', '=', $id)
                    ->delete()
                    ->run();

                if (!($res > 0 ? true : false)) {
                    throw new Exception("DML (DELETE)<br>TABLE (" . $tbname . ")<br>ID (" . $id . ")");
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        } else {
            throw new Exception("DML (DELETE)<br>TABLE (" . $tbname . ")<br>ID (" . $id . ")");
        }
    }

    public function recoverPass($params)
    {
        $newPass = $params[2];
        $username = $params[0];

        //var_dump($params);
        $sql = "UPDATE app_users set password = '$newPass' where upper(username) = upper('$username')";
        if ($this->dbConn->query($sql)->execute() === true) {
            return true;
        } else {
            return "Error: " . implode($this->dbConn->errorInfo());
        }
    }

    public function login($user, $pass)
    {
        try {
            // $qb = new QueryBuilder($this->dbConn);
            // $sql = "SELECT password, id FROM app_users WHERE upper(username) = upper('$user') and obsolete = 0";

            $result = $this->queryBuilder
                ->table("app_users")
                ->select(array("password", "id"))
                ->where("username", "=", $user)
                ->where("obsolete", "=", "0")
                ->run();

            if (sizeof($result) > 0) {
                $queryRes = $result[0];
                $passHash = $queryRes[0];

                $userid = $queryRes[1];

                $pass = password_verify($pass, $passHash);
                //var_dump($pass);
                if ($pass) {
                    $this->username = $user;
                    $this->user_id = $userid;

                    return true;
                } else {
                    return false;
                }
            } else {
                throw new Exception("L'utente scelto non esiste", 0);
            }
        } catch (\Throwable $th) {
            throw $th;
        }


        //$result = $this->dbConn->query($sql);

        // if ($result->num_rows > 0) {
        //     $queryRes = $result->fetch_array();
        //     $passHash = $queryRes[0];

        //     $userid = $queryRes[1];

        //     $pass = password_verify($pass, $passHash);
        //     //var_dump($pass);
        //     if ($pass) {
        //         $this->username = $user;
        //         $this->user_id = $userid;

        //         return true;
        //     } else {
        //         return false;
        //     }
        // } else {
        //     return false;
        // }
    }

    public function isValid()
    {
        $sql = " SELECT 1 FROM app_users WHERE upper(username) = upper('$this->usern ame')";
        $result = $this->dbConn->query($sql)->fetchAll();
        if (sizeof($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        // $sql = "SELECT 1 FROM app_users WHERE upper(username) = upper('$this->username') and app_user_role_id = 0";
        // $result = $this->dbConn->query($sql);

        $result = $this->queryBuilder
            ->table("app_users")
            ->where("username", "=", $this->username)
            ->where("app_user_roles_id", "=", 0)
            ->run();

        if (sizeof($result) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getProperties()
    {
        return array(
            "USERNAME" => $this->username,
            "USER_ID"  => (int)$this->user_id,
            "IS_ADMIN" => $this->isAdmin($this->dbConn)
        );
    }
}
