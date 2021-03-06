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
 * page module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         page
 * @since           2.6.0
 * @author          Mage Gr�gory (AKA Mage)
 * @version         $Id$
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');

class PagePage_contentForm extends XoopsThemeForm //XoopsSimpleForm
{
    /**
     * @param PagePage_content|XoopsObject $obj
     */
    public function __construct(PagePage_content &$obj)
    {
        $helper = Page::getInstance();
        $xoops = $helper->xoops();
        $xoops->theme()->addStylesheet('modules/page/css/styles.css');

        $title = $obj->isNew() ? PageLocale::A_ADD_CONTENT : PageLocale::A_EDIT_CONTENT;
        parent::__construct($title, 'form', 'content.php', 'post', true, 'raw');
        $tabtray = new XoopsFormTabTray('', 'uniqueid');

        /**
         * Main
         */
        $tab1 = new XoopsFormTab(PageLocale::TAB_MAIN, 'main');

        //Author
//        if ($helper->isUserAdmin()) {            $content_author = $obj->isNew() ? $xoops->user->getVar('uid') : $obj->getVar('content_author');
            $tab1->addElement(new XoopsFormSelectUser(XoopsLocale::AUTHOR, 'content_author', true, $content_author, 1, false), true);
//        }
        //date
        $tab1->addElement(new XoopsFormDateTime(XoopsLocale::DATE, 'content_create', 2, $obj->getVar('content_create'), false));
        //title
        $tab1->addElement(new XoopsFormText(XoopsLocale::TITLE, 'content_title', 12, 255, $obj->getVar('content_title'), ''), true);
        // editor
        $editor_configs=array();
        $editor_configs['editor'] = $helper->getConfig('page_editor');
        $editor_configs['rows'] = 12;
        $editor_configs['cols'] = 12;

        //short text
        $editor_configs['name'] = 'content_shorttext';
        $editor_configs['value'] = $obj->getVar('content_shorttext', 'e');
        $tab1->addElement(new XoopsFormEditor(XoopsLocale::SHORT_TEXT, 'content_shorttext', $editor_configs), true);

        //text
        $editor_configs['name'] = 'content_text';
        $editor_configs['value'] = $obj->getVar('content_text', 'e');
        $text = new XoopsFormEditor(XoopsLocale::TEXT, 'content_text', $editor_configs);
        $text->setDescription(PageLocale::CONTENT_TEXT_DESC);
        $tab1->addElement($text, false);

        //Weight
        $weight = new XoopsFormText(XoopsLocale::WEIGHT, 'content_weight', 1, 5, $obj->getVar('content_weight'), '');
        $weight->setPattern('^\d+$', PageLocale::E_WEIGHT);
        $tab1->addElement($weight, true);

        $tabtray->addElement($tab1);

        /**
         * Metas
         */
        $tab2 = new XoopsFormTab(PageLocale::TAB_METAS, 'metas');
        //content_mkeyword
        $tab2->addElement(new XoopsFormTextArea(PageLocale::CONTENT_META_KEYWORDS, 'content_mkeyword', $obj->getVar('content_mkeyword'), 1, 11, PageLocale::CONTENT_META_KEYWORDS_DSC));
        //content_mdescription
        $tab2->addElement(new XoopsFormTextArea(PageLocale::CONTENT_META_DESCRIPTION, 'content_mdescription', $obj->getVar('content_mdescription'), 5, 11));

        $tabtray->addElement($tab2);

        /**
         * Options
         */
        $tab3 = new XoopsFormTab(PageLocale::TAB_OPTIONS, 'options');
        //Options
        $content_option = $obj->getOptions();
        $checkbox = new XoopsFormCheckbox(XoopsLocale::OPTIONS, 'content_option', $content_option, false);
        $checkbox->setDescription(PageLocale::CONTENT_OPTIONS_DSC);
        foreach ($obj->options as $option) {            $checkbox->addOption($option, Xoops_Locale::translate('L_CONTENT_DO' . strtoupper($option), 'page'));
        }
        $tab3->addElement($checkbox);
        //maindisplay
        $tab3->addElement(new XoopsFormRadioYN(PageLocale::Q_ON_MAIN_PAGE, 'content_maindisplay', $obj->getVar('content_maindisplay')));
        //active
        $tab3->addElement(new XoopsFormRadioYN(XoopsLocale::ACTIVE, 'content_status', $obj->getVar('content_status')));

        $tabtray->addElement($tab3);

        /**
         * Permissions
         */
        if ($helper->isUserAdmin()) {
            $tab4 = new XoopsFormTab(PageLocale::TAB_PERMISSIONS, 'permissions');
            //permissions
            $group_list = $xoops->getHandler('member')->getGroupList();
            $full_list = array_keys($group_list);
            if(!$obj->isNew()) {
                $module_id = $helper->getModule()->getVar('mid', 'n');
                $groups_ids_view = $helper->getGrouppermHandler()->getGroupIds('page_view_item', $obj->getVar('content_id'), $module_id);
                $groups_ids_view = array_values($groups_ids_view);
                $groups_can_view_checkbox = new XoopsFormCheckBox(PageLocale::CONTENT_SELECT_GROUPS, 'groups_view_item[]', $groups_ids_view, false);
            } else {
                $groups_can_view_checkbox = new XoopsFormCheckBox(PageLocale::CONTENT_SELECT_GROUPS, 'groups_view_item[]', $full_list, false);
            }
            $groups_can_view_checkbox->addOptionArray($group_list);
            $tab4->addElement($groups_can_view_checkbox);

            $tabtray->addElement($tab4);
        }

        $this->addElement($tabtray);

        $this->addElement(new XoopsFormHidden('content_id', $obj->getVar('content_id')));

        /**
         * Buttons
         */
        $button_tray = new XoopsFormElementTray('', '');
        $button_tray->addElement(new XoopsFormHidden('op', 'save'));

        $button = new XoopsFormButton('', 'submit', XoopsLocale::A_SUBMIT, 'submit');
        $button->setClass('btn btn-success');
        $button_tray->addElement($button);

        $button_2 = new XoopsFormButton('', 'reset', XoopsLocale::A_RESET, 'reset');
        $button_2->setClass('btn btn-warning');
        $button_tray->addElement($button_2);

        $button_3 = new XoopsFormButton('', 'cancel', XoopsLocale::A_CANCEL, 'button');
        $button_3->setExtra("onclick='javascript:history.go(-1);'");
        $button_3->setClass('btn btn-danger');
        $button_tray->addElement($button_3);

        $this->addElement($button_tray);
    }
}