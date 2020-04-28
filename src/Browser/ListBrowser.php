<?php

namespace BrowserBotPHP\Browser\Browser;



class ListBrowser
{

    public static $ListBrowser  = array();



    public static function AddNewBrowser(BrowserBase &$browser)
    {
        ListBrowser::$ListBrowser[] = &$browser;
    }


    public static function RunAllBrowsers()
    {

        foreach (ListBrowser::$ListBrowser as $browserInList) {
            $browserInList->run();
        }
    }

    public static function UpdateBrwoser()
    {
        /// update here from https://symfony.com/doc/current/components/process.html#reference-process-signal
        foreach (ListBrowser::$ListBrowser as $key =>  $browserInList) {
            if (!$browserInList->_process->isRunning()) {

                unset(ListBrowser::$ListBrowser[$key]);
            } elseif (!$browserInList->IsTimeOut()) {
                unset(ListBrowser::$ListBrowser[$key]);
            }
        }
    }
}
