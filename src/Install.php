<?php
namespace Vipkwd\WebmanMiddleware;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * @var array
     */
    protected static $pathRelation = array();

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        self::putFile( config_path() . '/throttle.php', 'config/throttle.php');
        self::putFile( app_path() . '/middleware/Throttle.php', 'middleware.tpl');
        static::installByRelation();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        self::removeFile( config_path() . '/throttle.php', 'config/throttle.php');
        self::removeFile( app_path() . '/middleware/Throttle.php', 'middleware.tpl');
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path().'/'.substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            //symlink(__DIR__ . "/$source", base_path()."/$dest");
            copy_dir(__DIR__ . "/$source", base_path()."/$dest");
            echo "Create $dest";
        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path()."/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest";
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
        }
    }

    private static function putFile(string $targetFile, string $originFile){
        $code = file_get_contents(__DIR__.'/'.$originFile);
        if(is_file($targetFile)){
            //?????????????????????,????????????
            if( md5(self::formatText($targetFile)) != md5(self::formatText($code)) ){
                @copy($targetFile, $targetFile .'_'.time() .'.bak');
            }
        }
        file_put_contents($targetFile, $code);
    }
    private static function removeFile(string $targetFile, string $originFile){
        if(!is_file($targetFile)){
            //?????????????????????,???????????????
            if( md5(self::formatText($targetFile)) == md5(self::formatText(__DIR__.'/'.$originFile)) ){
                unlink($targetFile);
            }
        }
    }

    private static function formatText($code){
        is_file($code) && $code = file_get_contents($code);
        return preg_replace("/(.+\r\n|\s*)+/", '', $code);
    }
}