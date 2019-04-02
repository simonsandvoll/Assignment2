<?php

require_once 'serverClass.php';

class Session extends Server {
    public function __start () {
        session_start();
    }
    public function __setSession ($type, $content) {
        // set session 
        $_SESSION[$type] = $content;
    }

    public function __getSession ($type) {
        // get data from session 
        $sessionData = null;
        if (isset($_SESSION[$type])) {
            $sessionData = $_SESSION[$type];
        } 
        return $sessionData;
    }

    // reset session data
    public function __unsetSession ($type) {
        unset($_SESSION[$type]);
    }

    public function __destroy() {
        session_destroy();
    }
}

?>