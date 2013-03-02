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
class Chaplin_Service_YouTube_API
{
    const LOCATION = '/../external/youtube-dl';

    private $_strURL;
    
    public function __construct($strURL)
    {
        $this->_strURL = $strURL;        
    }

    public function getDownloadURL()
    {
        $strCommandLine = APPLICATION_PATH.self::LOCATION.' --prefer-free-formats -g '.$this->_strURL;
        return exec($strCommandLine);
    }
    
    public function downloadVideo($strPathToSave)
    {
        $strCommandLine = APPLICATION_PATH.self::LOCATION.
            " --prefer-free-formats -o '".
            $strPathToSave."/%(title)s.%(ext)s' ".'"'.$this->_strURL.'"';
        echo $strCommandLine.PHP_EOL;
        ob_flush();
        flush();
        system($strCommandLine);
    }

    public function downloadThumbnail($strPathToSave)
    {
        $yt = new Zend_Gdata_YouTube();
        $entryVideo = $yt->getVideoEntry($this->_strURL);

        $strFilename = $strPathToSave.'/'.$entryVideo->getVideoTitle().'.webm.png';

        $arrThumbnails = $entryVideo->getVideoThumbnails();
        if (!isset($arrThumbnails[0])) {
            throw new Exception('No thumbnails?');
        }
        $strURL = $arrThumbnails[0]['url'];

        $strImage = file_get_contents($strURL);
        file_put_contents($strFilename, $strImage);

        return '/uploads/'.basename($strFilename);
    }
}