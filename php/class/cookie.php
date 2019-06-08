<?php namespace Oxmosys;

abstract class Cookie
{
    /**
     * @param string $name
     * If $name is null gets all cookies returning $_COOKIE array()
     * 
     * @return mixed
     */
    public static function get(string $name = null)
    {
        try {
            if (is_null($name)) {
                return $_COOKIE;
            } else {
                if (!isset($_COOKIE[$name]))
                    return false; //throw new Exception("No Cookie Found", 1);

                return unserialize($_COOKIE[$name]);
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * @param string $name
     * If $name is null gets all cookies returning $_COOKIE array()
     * 
     * @param mixed $value
     * Any data that can be serialized into string
     * 
     * @return bool
     * If the cookie was set returns true, else returns false
     */
    public static function set(string $name, $value, int $time = null)
    {
        $ret = false;
        if (ini_get("session.use_cookies")) {
            $time == null ? $time = time() + 60 * 60 * 24 * 30 : $time;
            $params = session_get_cookie_params();
            $serializedData = serialize($value);
            $ret = setcookie(
                $name,
                $serializedData,
                $time,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        } else {
            throw new Exception("Cookies Not Enabled", 1);
        }

        return $ret;
    }

    public static function unset(string $name)
    {
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                $name,
                '',
                time() - 3600,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        } else {
            throw new Exception("Cookies Not Enabled", 1);
        }
    }
}
