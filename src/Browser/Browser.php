<?php

namespace BrowserBotPHP\Browser\Browser;

use BrowserBotPHP\STemplates\STemplates;
use stdClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Run Browser headless
 * Class BrowserBase
 */
class BrowserBase
{

    const PathNodeJsApp = "/NodeApp/app.js";
    const Tagout = "o$$&ut";
    const FileOutputJs = "/fileoutput.js";

    const OutputEverySecand = 1;
    const OutputJustWhenFinish = 0;

    protected  $_timestart = 0;

    protected $_typeOutput = 0;

    /**
     * @var string Path info file js
     */
    protected $_jsPassInfo = null;
    /**
     * @var array Array of path files js
     */
    protected $_jsPassCode = array();
    /**
     * @var int Max time to run browser
     */
    protected $_maxTimeout; // sec
    /**
     * @var Proxy Proxy run Browser
     */
    protected $_proxy;
    /**
     * @var array Command Args for Node.js App
     */
    protected $_command = array();
    /**
     * @var Process Process is class manger run commad shell
     */
    protected $_process;
    /**
     * @var array Array of Cookies object
     */
    protected $_cookies = array();
    /**
     * @var String Url
     */
    protected $_url = null;
    /**
     * @var string User Agent Browser
     */
    protected $_userAgent = null;

    /**
     * @var string Path to save image file
     */
    protected $_makePhoto = null;

    /**
     * @var bool Force Stop use proxy if loaded $_proxy var
     */
    protected $_useproxy = true;

    /** Construct Bots
     * @param integer $MaxTimeout Max Time Run
     * @param Array $CookiesArray Array of Cookies
     * @param String $url Url
     * @param Proxy $proxy Proxy
     *
     * @return void
     */
    public function __construct($MaxTimeout = 300, $CookiesArray = null, string $url = null, Proxy &$proxy = null)
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
        if ($url != null)
            $this->SetUrl($url);
    }
    /**
     * set up path file js , to injection js
     * @param mixed $jsPassInfo Path file
     *
     * @return $this
     */
    public function SetJsinfoPath($jsPassInfo)
    {
        $this->_jsPassInfo = $jsPassInfo;
        return $this;
    }


    /**
     *
     * Save photo After End
     * @param string $path Path Save photo
     *
     * @return $this
     */
    public function SavePhotoPath(string $path)
    {
        $this->_makePhoto = $path;

        return $this;
    }
    /**
     *
     * Set if have proxy var or defulat proxy  : on off
     * @param string $path Path Save photo
     *
     * @return $this
     */
    public function SetUseProxy(bool $UseProxyIfHave)
    {
        $this->_useproxy = $UseProxyIfHave;

        return $this;
    }

    /** لتحديد نوع الخرج اثناء عمل البوت
     * OutputEverySecandUpdate من اجل تحديث الخرج كل ثانية
     * OutputNramal عند انتهاء الخرج يقوم بتحديث
     * @param int $SetType
     *
     * @return int
     */
    public function TypeOutput(int $SetType = null)
    {
        if ($SetType  == null)
            return $this->_typeOutput;

        return $this->_typeOutput = $SetType;
    }


    /**
     * Set Url Page
     * @param string $url Url
     *
     * @return $this
     */
    public function SetUrl(string $url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * set up path files js , to injection js
     * @param string $jsPassCode Path Js file
     *
     * @return $this
     */
    public function SetJsCodePath(string $jsPassCode)
    {
        $this->_jsPassCode[] = $jsPassCode;
        return $this;
    }


    /**
     *
     * Setup Cookies
     * @param Cookie $cookies Setup One cookie
     * @param bool $removeAll Remove All cookies (reset cookies)
     *
     * @return $this
     */
    public function SetCookie(Cookie $cookies, bool $removeAll = false)
    {
        if ($removeAll)
            $this->_cookies = array();

        $this->_cookies[] = $cookies;

        return $this;
    }

    /**
     * Setup Cookies Group From Array
     * @param array $CookiesArray Array of cookies Object
     * @param boolean $ResetCookiesArray Clear Array Befor Run
     *
     * @return $this
     */
    public function SetCookies(array $CookiesArray, bool $ResetCookiesArray = true)
    {
        if (!$ResetCookiesArray) {
            foreach ($CookiesArray as  $cookie) {
                $_cookies[] = $cookie;
            }
        } else
            $_cookies = $CookiesArray;

        return $this;
    }

    /**
     * Set User Agent
     * @param string $stringUserAgent UserAgent name
     *
     * @return $this
     */
    public function SetUserAgent(string $stringUserAgent)
    {
        $this->_userAgent =  $stringUserAgent;

        return $this;
    }



    /**
     * Get Time Start as Timestamp
     * @return int
     */
    public function GetTimeStart()
    {
        return $this->_timestart;
    }

    /** For overwrite
     * @param null $object
     *
     * @return void
     */
    public function Before($object = null)
    {
        # code...
    }

    /** For overwrite
     * @param null $object
     *
     * @return void
     */
    public function After($object = null)
    {
        # code...
    }



    /**
     * return true if timeout
     * return false if not timeout
     * @return bool
     */
    public function IsTimeOut()
    {
        if (!$this->IsRun())
            return true;

        return $this->_cheakTimeout();
    }

    /**
     * return true if is Running
     * return false if is not Running
     * @return bool
     */
    public function IsRun()
    {
        $this->_cheakTimeout();

        if ($this->_process != null)
            return $this->_process->isRunning();
        else
            return false;
    }
    /**
     * run and wait to exit
     * @return $this
     */
    public function RunAndWait()
    {
        $this->Run(); // run

        ////waiting code
        while ($this->IsRun()) {

            try {
                $this->_process->checkTimeout();
            } catch (\Throwable $th) {
                $this->_process->stop();
                break;
            }
            sleep(1);
        }

        return $this;
    }

    /**
     * @return void
     */
    public function ClearOutput()
    {

        $this->_process->clearOutput();
        $this->_process->clearErrorOutput();
    }

    public function ResetJsCodePath()
    {

        $this->_jsPassCode = array();
    }

    /**
     * return false if not have yet output varible
     * return object output form js file
     * @return object|bool
     */
    public function Output($OnRun = false)
    {
        if (!$OnRun) {
            if (!$this->IsRun()) {
                $output =  $this->DebugOutput();
                $x =     explode("<" . self::Tagout . ">", $output);
                if (count($x) < 2)
                    return false;




                return  json_decode(explode("</" . self::Tagout . ">", $x[count($x) - 1])[0]);
            }
        } else {
            $output =  $this->DebugOutput();
            $x =     explode("<" . self::Tagout . ">", $output);
            if (count($x) < 2)
                return false;

            if ($this->_typeOutput == self::OutputEverySecand)
                $this->ClearOutput();
            return  json_decode(explode("</" . self::Tagout . ">", $x[1])[0]);
        }
        return false;
    }
    /**
     * Get all output as string
     * @return string
     */
    public function DebugOutput()
    {
        return $this->_process->getOutput() . $this->_process->getErrorOutput();
    }
    /**
     * Start Browser as async
     *
     * @return $this
     */
    public function Run()
    {

        if ($this->IsRun()) //if started befor
            return false;

        $this->_before();

        $this->_commandBuilding();

        ///https://symfony.com/doc/current/components/process.html#reference-process-signal
        ///https://symfony.com/doc/current/components/process.html#stopping-a-process
        ////////////////////////////////////////////////////
        $this->_process = new Process($this->_command);
        //print_r($this->_command);
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


        return $this;
    }
    /** Start Methode Before run
     * @return $this
     */
    protected function start()
    {
        $this->_timestart = time();
        return $this;
    }
    /**
     *  true is timeout
     *  false is not
     * @return bool
     */
    private function _cheakTimeout()
    {
        if ($this->_process == null)
            return false;

        try {
            $this->_process->checkTimeout();
        } catch (\Throwable $th) {
            $this->_process->stop();
            return true;
        }
        return false;
    }


    /**
     * Command Building For Start node js App
     * @return $this
     */
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


        $data->outputprint =  $this->TypeOutput();


        $data->Cookies =  $this->_cookies;
        if (Proxy::$DefaultProxy != null && $this->_proxy == null) {

            $this->_proxy = &Proxy::$DefaultProxy;
        }
        if (!$this->_useproxy)
            $this->_proxy = null;

        $data->Proxy =  $this->_proxy;

        $data->Url =  $this->_url;
        $data->JsInfoPath =  $this->_jsPassInfo;
        $data->JsCodePath =  $this->_jsPassCode;
        $Stemp = new STemplates();

        if ($this->_makePhoto != null)
            $data->SaveImage = $Stemp->Process($this->_makePhoto)->GetOutput();


        $data->FileOutputJs = __DIR__ . self::FileOutputJs;
        $data->TypeOutput = $this->TypeOutput();

        $this->_command[] = json_encode($data);

        return $this;
    }

    /**
     * run this methode before run
     * @return void
     */
    protected function _before()
    {
        $this->start();
        # code...
        $this->Before();
    }
    /**
     * run this methode after run
     * @return void
     */
    protected function _after()
    {

        # code...
        $this->After();
    }
}
