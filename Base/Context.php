<?php
namespace Base;

use Base\Dispatch\Dispatcher;

class Context
{
    private $_request;

    private $_dispatcher;

    private $_user;

    private $_dbConnection;

    private static $_instance;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @return \Base\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @return \Base\Dispatch\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    /**
     * @param mixed $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @return DbConnection
     */
    public function getDbConnection()
    {
        return $this->_dbConnection;
    }

    /**
     * @param mixed $dbConnection
     */
    public function setDbConnection(DbConnection $dbConnection): void
    {
        $this->_dbConnection = $dbConnection;
    }


}
