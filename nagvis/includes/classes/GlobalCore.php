<?php
/*****************************************************************************
 *
 * GlobalCore.php - The core of NagVis pages
 *
 * Copyright (c) 2004-2008 NagVis Project (Contact: michael_luebben@web.de)
 *
 * License:
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 *****************************************************************************/

/**
 * class GlobalCore
 *
 * @author  Lars Michelsen <lars@vertical-visions.de
 */
class GlobalCore {
	public $MAINCFG;
	public $LANG;
	
	private $iconsetTypeCache;

	/**
	 * Class Constructor
	 *
	 * @author Lars Michelsen <lars@vertical-visions.de>
	 */
	public function __construct(&$MAINCFG = NULL, &$LANG = NULL) {
		$this->iconsetTypeCache = Array();
		
		if($MAINCFG == NULL) {
			// Load the main configuration
			$this->MAINCFG = new GlobalMainCfg(CONST_MAINCFG);
		} else {
			$this->MAINCFG = &$MAINCFG;
		}
		
		if($LANG == NULL) {
			// Initialize language
			$this->LANG = new GlobalLanguage($this->MAINCFG);
		} else {
			$this->LANG = &$LANG;
		}
	}
	
	/* Here are some methods defined which get used all over NagVis and have
	 * no other special place where they could be located */
  
	/**
	 * Check if GD-Libs installed, when GD-Libs are enabled
	 *
	 * @param	Boolean $printErr
	 * @return	Boolean	Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function checkGd($printErr) {
    if(!extension_loaded('gd')) {
      if($printErr) {
        new GlobalFrontendMessage('WARNING', $this->LANG->getText('gdLibNotFound'));
      }
      return FALSE;
    } else {
      return TRUE;
    }
	}
	
	/**
	 * Reads all defined Backend-ids from the main configuration
	 *
	 * @return	Array Backend-IDs
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	public function getDefinedBackends() {
		$ret = Array();
		foreach($this->MAINCFG->config AS $sec => $var) {
			if(preg_match("/^backend_/i", $sec)) {
				$ret[] = $var['backendid'];
			}
		}
		
		return $ret;
	}
	
	/**
	 * Gets all rotation pools
	 *
	 * @return	Array pools
	 * @author Lars Michelsen <lars@vertical-visions.de>
	 */
	function getDefinedRotationPools() {
		$ret = Array();
		
		foreach($this->MAINCFG->config AS $sec => &$var) {
			if(preg_match('/^rotation_/i', $sec)) {
				$ret[] = $var['rotationid'];
			}
		}
		
		return $ret;
	}
	
	/**
	 * Reads all languages
	 *
	 * @return	Array list
	 * @author	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAvailableLanguages() {
		$files = Array();
		
		if ($handle = opendir($this->MAINCFG->getValue('paths', 'language'))) {
 			while (false !== ($file = readdir($handle))) {
				if(!preg_match('/^\./', $file)) {
					$files[] = $file;
				}				
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Reads all aviable backends
	 *
	 * @return	Array Html
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAvailableBackends() {
		$files = Array();
		
		if ($handle = opendir($this->MAINCFG->getValue('paths', 'class'))) {
 			while (false !== ($file = readdir($handle))) {
 				if(preg_match('/^class.GlobalBackend-(.+).php/', $file, $arrRet)) {
					$files[] = $arrRet[1];
				}				
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Reads all hover templates in hovertemplate path
	 *
	 * @return	Array hover templates
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	public function getAvailableHoverTemplates() {
		$files = Array();
		
		if($handle = opendir($this->MAINCFG->getValue('paths', 'hovertemplate'))) {
 			while (false !== ($file = readdir($handle))) {
				if(preg_match(MATCH_HTML_TEMPLATE_FILE, $file, $arrRet)) {
					$files[] = $arrRet[1];
				}
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Reads all header templates
	 *
	 * @return	Array list
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAvailableHeaderTemplates() {
		$files = Array();
		
		if ($handle = opendir($this->MAINCFG->getValue('paths', 'headertemplate'))) {
 			while (false !== ($file = readdir($handle))) {
				if(preg_match(MATCH_HTML_TEMPLATE_FILE, $file, $arrRet)) {
					$files[] = $arrRet[1];
				}
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Reads all PNG images in shape path
	 *
	 * @return	Array Shapes
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAvailableShapes() {
		$files = Array();
		
		if ($handle = opendir($this->MAINCFG->getValue('paths', 'shape'))) {
 			while (false !== ($file = readdir($handle))) {
				if(preg_match(MATCH_PNG_GIF_JPG_FILE, $file, $arrRet)) {
					$files[] = $arrRet[0];
				}				
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Reads all iconsets in icon path
	 *
	 * @return	Array iconsets
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAvailableIconsets() {
		$files = Array();
		
		if($handle = opendir($this->MAINCFG->getValue('paths', 'icon'))) {
 			while (false !== ($file = readdir($handle))) {
				if(preg_match('/^(.+)_ok.(png|gif|jpg)$/', $file, $arrRet)) {
					$files[] = $arrRet[1];
				}				
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Returns the filetype of an iconset
	 *
	 * @param   String  Iconset name
	 * @return	String  Iconset file type
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getIconsetFiletype($iconset) {
		$type = '';
		
		if(isset($this->iconsetTypeCache[$iconset])) {
			$type = $this->iconsetTypeCache[$iconset];
		} else {
			if($handle = opendir($this->MAINCFG->getValue('paths', 'icon'))) {
				while (false !== ($file = readdir($handle))) {
					if(preg_match('/^'.$iconset.'_ok.(png|gif|jpg)$/', $file, $arrRet)) {
						$type = $arrRet[1];
					}				
				}
			}
			closedir($handle);
		}
		
		$this->iconsetTypeCache[$iconset] = $type;
		return $type;
	}
	
	/**
	 * Reads all maps in mapcfg path
	 *
	 * @param		String  Regex to match the map name
	 * @return	Array   Array of maps
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	public function getAvailableMaps($strMatch = NULL) {
		$files = Array();
		
		if ($handle = opendir($this->MAINCFG->getValue('paths', 'mapcfg'))) {
 			while (false !== ($file = readdir($handle))) {
				if(preg_match(MATCH_CFG_FILE, $file, $arrRet)) {
					if($strMatch == NULL || ($strMatch != NULL && preg_match($strMatch, $arrRet[1]))) {
							$files[] = $arrRet[1];
					}
				}				
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	/**
	 * Reads all map images in map path
	 *
	 * @return	Array map images
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
     */
	public function getAvailableBackgroundImages() {
		$files = Array();
		
		if($handle = opendir($this->MAINCFG->getValue('paths', 'map'))) {
 			while(false !== ($file = readdir($handle))) {
				if(preg_match(MATCH_PNG_GIF_JPG_FILE, $file)) {
					$files[] = $file;
				}				
			}
			
			if ($files) {
				natcasesort($files);
			}
		}
		closedir($handle);
		
		return $files;
	}
	
	
	/**
	 * Checks for writeable VarFolder
	 *
	 * @param		Boolean 	$printErr
	 * @return	Boolean		Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function checkVarFolderExists($printErr) {
		if(file_exists(substr($this->MAINCFG->getValue('paths', 'var'),0,-1))) {
			return TRUE;
		} else {
			if($printErr == 1) {
				new GlobalFrontendMessage('ERROR', $this->LANG->getText('varFolderNotExists','PATH~'.$this->MAINCFG->getValue('paths', 'var')));
			}
			return FALSE;
		}
	}
	
	/**
	 * Checks for writeable VarFolder
	 *
	 * @param		Boolean 	$printErr
	 * @return	Boolean		Is Successful?
	 * @author 	Lars Michelsen <lars@vertical-visions.de>
	 */
	function checkVarFolderWriteable($printErr) {
		if($this->checkVarFolderExists($printErr) && is_writable(substr($this->MAINCFG->getValue('paths', 'var'),0,-1)) && @file_exists($this->MAINCFG->getValue('paths', 'var').'.')) {
			return TRUE;
		} else {
			if($printErr == 1) {
				new GlobalFrontendMessage('ERROR', $this->LANG->getText('varFolderNotWriteable','PATH~'.$this->MAINCFG->getValue('paths', 'var')));
			}
			return FALSE;
		}
	}
}
?>
