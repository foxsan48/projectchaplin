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
class Chaplin_Config_Servers
    extends Chaplin_Config_Abstract
{
    const CONFIG_TYPE = 'Ini';

    protected function _getConfigType()
    {
        return self::CONFIG_TYPE;
    }

    protected function _getConfigFile()
    {
        return APPLICATION_PATH.'/../config/chaplin.ini';
    }

    public function getRedisSettings()
    {
        return $this->_zendConfig->phpredis;
    }

    public function getAmqpSettings()
    {
        return $this->_getValue(
            $this->_zendConfig->amqp,
            'amqp'
        )->toArray();
    }

    public function getConfigConnectionRead()
    {
        return $this->_getValue(
            $this->_zendConfig->amqp->servers->read,
            'amqp.servers.read'
        )->toArray();
    }

    public function getConfigConnectionWrite()
    {
        return $this->_getValue(
            $this->_zendConfig->amqp->servers->write,
            'amqp.servers.write'
        )->toArray();
    }

    public function getSmtpSettings()
    {
        $smtp = $this->_getValue(
            $this->_zendConfig->smtp,
            'smtp'
        )->toArray();
        // Zend will not accept a blank SSL option
        if (isset($smtp['server']) &&
            isset($smtp['server']['options']) &&
            isset($smtp['server']['options']['ssl']) &&
            empty($smtp['server']['options']['ssl'])) {
            unset($smtp['server']['options']['ssl']);
        }
        if (isset($smtp['server']) &&
            isset($smtp['server']['options']) &&
            isset($smtp['server']['options']['auth']) &&
            isset($smtp['server']['options']['username']) &&
            isset($smtp['server']['options']['password']) &&
            (
                empty($smtp['server']['options']['username']) ||
                empty($smtp['server']['options']['password'])
            )
        ) {
            unset($smtp['server']['options']['auth']);
            unset($smtp['server']['options']['username']);
            unset($smtp['server']['options']['password']);
        }
        return $smtp;
    }

    public function getSqlSettings()
    {
        return $this->_getValue(
            $this->_zendConfig->sql,
            'sql'
        );
    }

    public function getMongoSettings()
    {
        return $this->_getValue(
            $this->_zendConfig->sql,
            'sql'
        );
    }

    /**
     * Gets the Virtual Host setting
     * Useful for sending email when
     * we don't know the request URL
     *
     * @return string
     * @author Dan Dart
     **/
    public function getVhost()
    {
        return $this->_getValue(
            $this->_zendConfig->vhost,
            'vhost'
        );
    }

    /**
     * Gets the Shortened Virtual Host setting
     * Useful for sending video URLs
     *
     * @return string
     * @author Dan Dart
     **/
    public function getShort()
    {
        return $this->_getValue(
            $this->_zendConfig->short,
            'short'
        );
    }
}
