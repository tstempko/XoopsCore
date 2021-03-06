<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       The XUUPS Project http://sourceforge.net/projects/xuups/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 */

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

class UserconfigsMenusPlugin extends Xoops_Module_Plugin_Abstract implements MenusPluginInterface
{
    /**
     * expects an array of array containing:
     * name,      Name of the submenu
     * url,       Url of the submenu relative to the module
     * ex: return array(0 => array(
     *      'name' => _MI_PUBLISHER_SUB_SMNAME3;
     *      'url' => "search.php";
     *    ));
     *
     * @return array
     */
    public function subMenus()
    {
        $ret = array();
        $xoops = Xoops::getInstance();
        if ($plugins = Xoops_Module_Plugin::getPlugins('userconfigs')) {
            foreach (array_keys($plugins) as $dirname) {
                $mHelper = $xoops->getModuleHelper($dirname);
                $ret[$dirname]['name'] = $mHelper->getModule()->getVar('name');
                $ret[$dirname]['url'] = 'index.php?modid=' . $mHelper->getModule()->getVar('modid');
            }
        }

        return $ret;
    }
}
