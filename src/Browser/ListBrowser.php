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

        if (self::GetCount() == 0)
            return false;

        return true;
    }
    public static function GetCount()
    {
        $count = 0;
        foreach (ListBrowser::$ListBrowser as $key =>  $browserInList) {
            if (!(!$browserInList->IsRun() || $browserInList->IsTimeOut())) {

                $count++;
            }
        }
        return $count;
    }
}
