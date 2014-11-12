<?php
/**
* @author Gaëtan Masson <gaetanmdev@gmail.com>
*/
class ImageCacher
{
    private $path;
    private $rule;
    private $rootURL;
    private $cacheDataFile;

    public function __construct($rule, $rootURL, $path, $cacheDataFile)
    {
        $this->path          = $path;
        $this->rule          = $rule;
        $this->rootURL       = $rootURL;
        $this->cacheDataFile = $this->path.'/imagecacher-data';
    }

    private function dowloadImage($imgURL, $nameWithPath, $remoteImgDate)
    {
        $ch = curl_init($imgURL);
        $fp = fopen($nameWithPath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        touch($nameWithPath, $remoteImgDate);
        $fp = fopen($this->cacheDataFile, 'w');
        fwrite($fp, time().'\n');
        fclose($fp);
    }

    private function getRemoteFileModificationDate($imgURL)
    {
        $ch = curl_init($imgURL);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        curl_exec($ch);
        $info = curl_getinfo($ch);
        return $info['filetime'];
    }

    public function getImage($imgURL)
    {
        $explURL       = explode("/", $imgURL);
        $imgName       = end($explURL);
        $nameWithPath  = $this->path.'/'.$imgName;

        if (file_exists($nameWithPath))
        {
            $fp        = fopen($this->cacheDataFile, 'r');
            $cacheTime = trim(fgets($fp));
            fclose($fp);
            if ($cacheTime > strtotime($this->rule))
                return $this->rootURL.'/'.$nameWithPath;

            $remoteImgDate = $this->getRemoteFileModificationDate($imgURL);
            $cachedImgDate  = filemtime($nameWithPath);
            if ($cachedImgDate != $remoteImgDate)
                $this->dowloadImage($imgURL, $nameWithPath, $remoteImgDate);
            return $this->rootURL.'/'.$nameWithPath;
        }
        $this->dowloadImage($imgURL, $nameWithPath, $this->getRemoteFileModificationDate($imgURL));
        return $this->rootURL.'/'.$nameWithPath;
    }

    public function setPath($path){$this->path = $path;}

    public function setRule($rule){$this->rule = $rule;}

    public function setRootURL($rootURL){$this->rootURL = $rootURL;}

    public function getPath(){return $this->path;}

    public function getRule(){return $this->rule;}

    public function getRootURL(){return $this->rootURL;}
}