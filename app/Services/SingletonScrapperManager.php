<?php
namespace App\Services;


use Phpfastcache\CacheManager;

class SingletonScrapperManager {
   protected static $instance = null;

  /** call this method to get instance */
   public static function instance(){
      if (static::$instance === null){
         static::$instance = CacheManager::getInstance('Files', null);
      }
      return static::$instance;
  }

  /** protected to prevent instantiation from outside of the class */
  private function __construct(){
  }
}