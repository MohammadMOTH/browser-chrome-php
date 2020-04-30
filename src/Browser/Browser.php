<?php

namespace BrowserBotPHP\Browser\Browser;

use stdClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BrowserBase
{

    const PathNodeJsApp = "/NodeApp/app.js";
    const Tagout = "o$$&ut";
    const FileOutputJs = "./fileoutput.js";
    protected  $_timestart = 0;


    protected $_jsPassInfo = null;
    protected $_jsPassCode = array();
    protected $_maxTimeout; // sec
    protected $_proxy;
    protected $_isStarted = false;
    protected $_command = array();
    protected $_process;
    protected $_cookies = array();
    protected $_url;
    protected $_userAgent = null;

    protected $_makePhoto = null;


    public function __construct($MaxTimeout = 300, $CookiesArray = null, $url = null, Proxy &$proxy = null)
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
    public function SavePhotoPath($path)
    {
        $this->_makePhoto = $path;
    }
    public function SetUrl($url)
    {
        $this->_url = $url;
    }
    public function SetJsCodePath($jsPassCode)
    {
        $this->_jsPassCode[] = $jsPassCode;
    }

    public function SetCookie(Cookie $cookies)
    {

        $this->_cookies[] = $cookies;
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
        if (!$this->IsRun())
            return true;

        return $this->_cheakTimeout();
    }
    private function _cheakTimeout()
    {
        try {
            $this->_process->checkTimeout();
        } catch (\Throwable $th) {
            $this->_process->stop();
            return true;
        }
    }
    public function IsRun()
    {
        $this->_cheakTimeout();

        return $this->_process->isRunning();
    }
    public function RunAndWait()
    {
        $this->Run();

        while ($this->IsRun()) {

            try {
                $this->_process->checkTimeout();
            } catch (\Throwable $th) {
                $this->_process->stop();
                break;
            }

            sleep(1);
        }
    }
    public function Output()
    {
        if (!$this->IsRun()) {
            $output =  $this->DebugOutput();
            $x =     explode("<" . self::Tagout . ">", $output);
            if (count($x) < 2)
                return false;
            return  json_decode(explode("</" . self::Tagout . ">", $x[1])[0]);
        }
        return false;
    }
    public function DebugOutput()
    {
        return $this->_process->getOutput() . $this->_process->getErrorOutput();
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
        $this->_process->setTimeout($this->_maxTimeout);
        $this->_process->start();
        //  if(defined('SIGINT')) // linux

        //$this->_process->stop($this->_maxTimeout * 100000000);
        //   else

        /*
        // ... do other things
        $x = 1;
        while ($this->_process->isRunning()) {
            sleep(1);
            $x++;

            echo $x . " am wait here" . PHP_EOL;
            echo $this->_process->getOutput() . PHP_EOL;
            echo $this->_process->getErrorOutput() . PHP_EOL;
        }
        // executes after the command finishes
        if (!$this->_process->isSuccessful()) {
            throw new ProcessFailedException($this->_process);
        } else {

            echo $this->_process->getOutput();
        }
        */
        /////////////////////////

        $this->_after();


        return true;
    }

    protected function _commandBuilding()
    {
        $this->_command = array();
        $this->_command[] = "node";
        $this->_command[] = __DIR__ . self::PathNodeJsApp;

        $data = new stdClass();
        if ($this->_userAgent != null)
            $data->UserAgent =  $this->_userAgent;
        else
            $data->UserAgent = null;

        for ($i = 0; $i < count($this->_cookies); $i++) {
            $this->_cookies[$i]->id = $i + 1;
        }

        $data->Cookies =  $this->_cookies;
        $data->Proxy =  $this->_proxy;
        $data->Url =  $this->_url;
        $data->JsInfoPath =  $this->_jsPassInfo;
        $data->JsCodePath =  $this->_jsPassCode;
        $data->SaveImage = $this->_makePhoto;
        $data->FileOutputJs = self::FileOutputJs;
        $this->_command[] = json_encode($data);
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
