<?php

namespace BrowserBotPHP\Browser\Browser;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BrowserBase
{

    const PathNodeJsApp = "/NodeApp/app.js";

    protected  $_timestart = 0;


    protected $_jsPassInfo = null;
    protected $_jsPassCode = null;
    protected $_maxTimeout; // sec
    protected $_proxy;
    protected $_isStarted = false;
    protected $_command = array();
    protected $_process;
    protected $_cookies = array();
    protected $_url;

    public function __construct($MaxTimeout = 300, Proxy &$proxy = null, $CookiesArray = null, $url = null)
    {
        $this->_maxTimeout =  $MaxTimeout;

        if ($proxy != null) {
            $this->_proxy = &$proxy;
        } elseif (Proxy::$DefaultProxy != null) {

            $this->_proxy = &Proxy::$DefaultProxy;
        }

        ListBrowser::AddNewBrowser($this);

        if (is_array($CookiesArray))
            $this->SetCookies($CookiesArray);

        $this->SetUrl($url);
    }
    public function SetJsinfoPath($jsPassInfo)
    {
        $this->_jsPassInfo = $jsPassInfo;
    }
    public function SetUrl($url)
    {
        $this->_url = $url;
    }
    public function SetJsCodePath($jsPassCode)
    {
        $this->_jsPassCode = $jsPassCode;
    }

    public function SetCookie(Cookie $cookies)
    {
        $_cookies[] = $cookies;
    }
    public function SetCookies($CookiesArray, $ResetCookiesArray = true)
    {
        if (!$ResetCookiesArray) {
            foreach ($CookiesArray as  $cookie) {
                $_cookies[] = $cookie;
            }
        } else
            $_cookies = $CookiesArray;
    }

    public function IsStarted()
    {
        return $this->_isStarted;
    }
    public function GetTimeStart()
    {
        return $this->_timestart;
    }
    //overwrite
    public function Before($object = null)
    {
        # code...
    }
    //overwrite
    public function After($object = null)
    {
        # code...
    }

    protected function start()
    {
        $this->_isStarted = true;
        $this->_timestart = time();
    }

    public function IsTimeOut()
    {
        if (($this->_maxTimeout +  $this->_timestart) <= time()) {
            return true;
        }

        return false;
    }

    public function Run()
    {

        if ($this->IsStarted()) //if started befor
            return false;

        $this->_before();

        $this->_commandBuilding();

        ///https://symfony.com/doc/current/components/process.html#reference-process-signal
        ///https://symfony.com/doc/current/components/process.html#stopping-a-process
        ////////////////////////////////////////////////////
        $this->_process = new Process($this->_command);
        $this->_process->start();


        // ... do other things
        $x = 1;
        while ($this->_process->isRunning()) {
            sleep(1);
            $x++;
            echo $x . " am wait here" . PHP_EOL;
            echo $this->_process->getOutput() . PHP_EOL;
        }
        // executes after the command finishes
        if (!$this->_process->isSuccessful()) {
            throw new ProcessFailedException($this->_process);
        } else {

            echo $this->_process->getOutput();
        }
        /////////////////////////

        $this->_after();


        return true;
    }

    protected function _commandBuilding()
    {
        $this->_command = array();
        $this->_command[] = "node";
        $this->_command[] = __DIR__ . self::PathNodeJsApp;
    }

    protected function _before()
    {
        $this->start();
        # code...
        $this->Before();
    }
    protected function _after()
    {

        # code...
        $this->After();
    }
}
