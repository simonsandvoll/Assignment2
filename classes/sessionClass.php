<?php

require_once 'serverClass.php';

class Session extends Server {

    public function __start () {
        session_start();
    }

    /**
     * Set a session value with a specific content
     * @param { string } $type -> the type session to set.
     * @param { string } $content -> the content of the session.
    */
    public function __setSession ($type, $content) {
        // set session 
        $_SESSION[$type] = $content;
    }

    /**
     * Get a session value if set
     * @param { string } $type -> the type session to get.
     * @return { string } $sessionData -> data from the specific session.
    */
    public function __getSession ($type) {
        // get data from session 
        $sessionData = null;
        if (isset($_SESSION[$type])) {
            $sessionData = $_SESSION[$type];
        } 
        return $sessionData;
    }

    /**
     * Unset a session of specified type
     * @param { string } $type -> the type session to unset.
    */
    public function __unsetSession ($type) {
        unset($_SESSION[$type]);
    }

    /**
     * Exit the session by destroying it. 
    */
    public function __destroy() {
        session_destroy();
    }
}

?>