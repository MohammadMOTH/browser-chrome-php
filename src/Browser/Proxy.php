<?php

namespace BrowserBotPHP\Browser\Browser;

class Proxy
{


    public static $DefaultProxy;

    public static $ProxyList;




    private  $_ip;
    private  $_port;
    private  $_username;
    private  $_password;


    function __construct($Ip, $Port, $Username = "", $passport = "", Bool $SetAsDefaultProxy)
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
