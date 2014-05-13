<?php
/** mdocs_browser_compatibility */
/**
This file is part of Memphis API.

Memphis Documents Library is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Memphis Documents Library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Memphis Documents Library.  If not, see <http://www.gnu.org/licenses/>.
@package memphis-documents-library
@subpackage mdocs-browser-compatibility
@author Ian Howatson <ian@howatson.net>
*/
class mdocs_browser_compatibility {
/**
 * Gets the users browser information
 * @return array The users browser information string=>userAgent, string=>name, string=>version, string=>platform, string=> pattern
 */
    function get_browser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
		//$u_agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9) AppleWebKit/537.71 (KHTML, like Gecko) Version/7.0 Safari/537.71";
		$bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } else {
			$bname = 'Unknow'; 
            $ub = "Unknow"; 
		}
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
               if(count($matches[0]) == 0 ) $version= '?';
			   else $version= $matches['version'][0];
            }
            else {
				if(count($matches[1]) == 0 ) $version= '?';
				else $version= $matches['version'][1];
            }
        } else {
			if(count($matches[0]) == 0 )  $version= '?';
			else $version= $matches['version'][0];
        }
        
        // check if we have a number
		if ($u_agent==null || $u_agent=="") {$u_agent="?";}
		if ($bname==null || $bname=="") {$bname="?";}
        if ($version==null || $version=="") {$version="?";}
		if ($platform==null || $platform=="") {$platform="?";}
		if ($pattern==null || $pattern=="") {$pattern="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
}
?>