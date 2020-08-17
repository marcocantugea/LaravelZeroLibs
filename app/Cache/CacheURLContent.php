<?php

namespace App\Cache;

use DateInterval;
use DateTime;
use Exception;

class CacheURLContent
{

    private $cacheFilesPath;
    private $fileNewCached;
    private $content = array();
    private $DaysToExprire = 1;
    private $urlRequested = null;

    public function __construct(string $urlpath)
    {
        $this->cacheFilesPath = getcwd() . "\\cache\\";
        $this->parseFilename($urlpath);
        $this->urlRequested = $urlpath;
    }

    /**
     * Get the value of cacheFilesPath
     */
    public function getCacheFilesPath()
    {
        return $this->cacheFilesPath;
    }

    /**
     * Set the value of cacheFilesPath
     *
     * @return  self
     */
    public function setCacheFilesPath($cacheFilesPath)
    {
        $this->cacheFilesPath = $cacheFilesPath;

        return $this;
    }

    protected function createCacheFile()
    {
        //Trata de crear o abrir los archivos
        $streamFile = fopen($this->fileNewCached, "w");

        //revisa si no tengo error en crearlo
        if ($streamFile == false) {
            throw new Exception("No se pudo crear el archivo Bulk Precios");
        }

        fclose($streamFile);

        return $this;
    }

    protected function parseFilename(string $filename)
    {

        $this->fileNewCached = $this->cacheFilesPath . substr(md5($filename), 0, 25) . ".json";
        return $this;
    }

    protected function saveContentToCacheFile()
    {
        try {
            if (!is_null($this->fileNewCached)) {
                if (count($this->content) > 0) {
                    file_put_contents($this->fileNewCached, json_encode($this->content), FILE_APPEND | LOCK_EX);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return $this;
    }

    public function setContent($content)
    {
        if (!is_null($content)) {
            $DateTimeExpire = new DateTime();
            $this->content["DateAdded"] = new DateTime();
            $this->content["Expire"] = $DateTimeExpire->add(new DateInterval("PT" . $this->DaysToExprire . "M"));
            $this->content["url"] = $this->urlRequested;
            $this->content["content"] = $content;
        }
        return $this;
    }

    public function saveCacheFile()
    {
        try {
            $this->createCacheFile()->saveContentToCacheFile();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function ExistChache()
    {
        $exist = false;
        if (file_exists($this->fileNewCached)) {
            $exist = true;
        }
        return $exist;
    }

    public function expire()
    {
        $return = false;
        if (count($this->content) > 0) {
            $nowDate = new DateTime();
            $Expiredate = DateTime::__set_state($this->content['Expire']);
            if ($Expiredate < $nowDate) {
                $return = true;
            }
        }
        return $return;
    }

    public function loadCacheFile()
    {
        if ($this->ExistChache()) {
            $string = file_get_contents($this->fileNewCached);
            $this->content = json_decode($string, true);
            if ($this->expire()) {
                $this->content = array();
                $this->removeExpireCache();
            }
        }

        return $this;
    }

    public function getContent()
    {
        if (count($this->content) <= 0) {
            return null;
        }
        return $this->content["content"];
    }

    public function removeExpireCache()
    {
        if ($this->ExistChache()) {
            unlink($this->fileNewCached);
        }
    }

}
