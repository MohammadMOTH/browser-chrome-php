<?php

namespace BrowserBotPHP\Browser\Browser;

class Proxy
{


    public static $DefaultProxy = null;

    public static $ProxyList;

    protected  $_ip;
    protected  $_port;
    protected  $_username;
    protected  $_password;


    function __construct($Ip, $Port, $Username = "", $passport = "", Bool $SetAsDefaultProxy = false)
    {

        $this->_ip = $Ip;
        $this->_port = $Port;

        $this->_username = $Username;
        $this->_password = $passport;

        if ($SetAsDefaultProxy)
            self::$DefaultProxy = &$this; // ref

        self::$ProxyList[] = &$this;
    }

    public function returnIpPort()
    {
        return $this->_ip . ":" . $this->_port;
    }

    public function returnUsername()
    {
        return $this->_username;
    }
    public function returnPassword()
    {
        return $this->_password;
    }
}
