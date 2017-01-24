<?php
/**
 * This file is part of Project Chaplin.
 *
 * Project Chaplin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Project Chaplin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Project Chaplin. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Project Chaplin
 * @author     Dan Dart
 * @copyright  2012-2013 Project Chaplin
 * @license    http://www.gnu.org/licenses/agpl-3.0.html GNU AGPL 3.0
 * @version    git
 * @link       https://github.com/dandart/projectchaplin
**/
class Chaplin_Service
{
    const LIFETIME_SECS = 1800;
    
    public static function inject(Chaplin_Service $service)
    {
        self::$_instance = $service;
    }

    private static $_instance;

    public static function getInstance()
    {
        if (is_null(self::$_instance))
            self::$_instance   = new Chaplin_Service();
        return self::$_instance;
    }

    private function __construct() {}
    private function __clone() {}

    private $_zendCache;
    private function getCache()
    {
        if (is_null($this->_zendCache)) {
            //@TODO - probably put this in a config file
            $frontendOptions = [
                'lifetime' => self::LIFETIME_SECS,
                'automatic_serialization' => true
            ];
            if (Zend_Registry::isRegistered(
                    Chaplin_Dao_PhpRedis_Abstract::DEFAULT_REGISTRY_KEY
            )){
                $backendOptions = [
                    'phpredis' => Zend_Registry::get(
                        Chaplin_Dao_PhpRedis_Abstract::DEFAULT_REGISTRY_KEY
                    )
                ];
                $backendType = new Chaplin_Cache_Backend_PhpRedis($backendOptions);
            } else {
                $backendOptions = [
                    'cache_dir' => APPLICATION_PATH.'/../temp/'
                ];
                $backendType = 'File';
            }

            $this->_zendCache = Zend_Cache::factory(
                'Core',
                $backendType,
                $frontendOptions,
                $backendOptions
            );
        }
        return $this->_zendCache;
    }

    public function setCache(Zend_Cache $zendCache)
    {
        $this->_zendCache   = $zendCache;
    }

    public function getHttpClient()
    {
        $objClient = new Chaplin_Http_Client();
        $objCache  = new Chaplin_Cache_Http_Client($objClient, $this->getCache());
        return new Chaplin_Service_Http_Client($objCache);
    }

    public function getEncoder()
    {
        return new Chaplin_Service_Encoder_API();
    }

    public function getYouTube()
    {
        return new Chaplin_Service_YouTube_API();
    }

    public function getVimeo()
    {
        return new Chaplin_Service_Vimeo_API();
    }
}
