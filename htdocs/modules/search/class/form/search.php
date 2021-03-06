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
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id$
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');

class SearchSearchForm extends XoopsThemeForm
{
    /**
     * We are not using this for objects but we need to override the constructor
     * @param null $obj
     */
    public function __construct($obj = null)
    {
    }

    public function getSearchFrom($andor, $queries,$mids, $mid)
    {
        $xoops = Xoops::getInstance();
        $search = Search::getInstance();
        // create form
        parent::__construct(_MD_SEARCH, 'search', 'index.php', 'get');

        // create form elements
        $this->addElement(new XoopsFormText(_MD_SEARCH_KEYWORDS, 'query', 30, 255, htmlspecialchars(stripslashes(implode(' ', $queries)), ENT_QUOTES)), true);
        $type_select = new XoopsFormSelect(_MD_SEARCH_TYPE, 'andor', $andor);
        $type_select->addOptionArray(array(
            'AND' => _MD_SEARCH_ALL, 'OR' => _MD_SEARCH_ANY, 'exact' => _MD_SEARCH_EXACT
        ));
        $this->addElement($type_select);
        if (!empty($mids)) {
            $mods_checkbox = new XoopsFormCheckBox(_MD_SEARCH_SEARCHIN, 'mids[]', $mids);
        } else {
            $mods_checkbox = new XoopsFormCheckBox(_MD_SEARCH_SEARCHIN, 'mids[]', $mid);
        }
        if (empty($modules)) {
            $gperm_handler = $xoops->getHandlerGroupperm();
            $available_modules = $gperm_handler->getItemIds('module_read', $search->getUserGroups());
            $available_plugins = Xoops_Module_Plugin::getPlugins('search');

            //todo, would be nice to have the module ids availabe also
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('dirname', "('" . implode("','", array_keys($available_plugins)) . "')", 'IN'));
            if (isset($available_modules) && !empty($available_modules)) {
                $criteria->add(new Criteria('mid', '(' . implode(',', $available_modules) . ')', 'IN'));
            }
            $module_handler = $xoops->getHandlerModule();
            $mods_checkbox->addOptionArray($module_handler->getNameList($criteria));
        } else {
            /* @var $module XoopsModule */
            $module_array = array();
            foreach ($modules as $mid => $module) {
                $module_array[$mid] = $module->getVar('name');
            }
            $mods_checkbox->addOptionArray($module_array);
        }
        $this->addElement($mods_checkbox);
        if ($search->getConfig('keyword_min') > 0) {
            $this->addElement(new XoopsFormLabel(_MD_SEARCH_SEARCHRULE, sprintf(_MD_SEARCH_KEYIGNORE, $search->getConfig('keyword_min'))));
        }
        $this->addElement(new XoopsFormHidden('action', 'results'));
        $this->addElement(new XoopsFormHiddenToken('id'));
        $this->addElement(new XoopsFormButton('', 'submit', _MD_SEARCH, 'submit'));
        return $this;
    }
}