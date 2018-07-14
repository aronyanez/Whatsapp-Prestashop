<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class MarkContactWhatsapp extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'markcontactwhatsapp';
        $this->tab = 'social_networks';
        $this->version = '1.1.0';
        $this->author = 'Arón Yáñez';
        $this->need_instance = 0;
        $this->module_key = '36f7c4d71872c26600f166261c81ae48';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Whatsapp Contact');
        $this->description = $this->l('Add Whatsapp Contact');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayFooterBefore')
        && Configuration::updateValue('Whats_Number', '524434395115')
        && Configuration::updateValue('Whats_Background', '#20b038')
        && Configuration::updateValue('Whats_Fontcolor', '#ffffff')
        && Configuration::updateValue('Whats_Message', $this->l('I want information'));
    }


    public function uninstall()
    {
        return parent::uninstall()
        && $this->unregisterHook('displayHeader')
        && $this->unregisterHook('displayFooterBefore')
        && Configuration::deleteByName('Whats_Number')
        && Configuration::deleteByName('Whats_Background')
        && Configuration::deleteByName('Whats_Fontcolor')
        && Configuration::deleteByName('Whats_Message');
    }

    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit'.$this->name)) {
            $Whats_Number= (string)Tools::getValue('Whats_Number');
            $Whats_Message= (string)Tools::getValue('Whats_Message');
            $Whats_Background= (string)Tools::getValue('Whats_Background');
            $Whats_Fontcolor= (string)Tools::getValue('Whats_Fontcolor');
            if (!Validate::isPhoneNumber($Whats_Number)) {
                $output .= $this->displayError($this->l('Error: : Phone Number field is invalid.
                	Must be a numeric value.'));
            } elseif (!$Whats_Number || empty($Whats_Number)) {
                 $output .= $this->displayError($this->l('Error: Phone Number field is invalid.
                 	Value can\'t be empty.'));
            } elseif (!Validate::isMessage($Whats_Message)) {
                $output .= $this->displayError($this->l('Error: Message field is invalid.
                	Must be a alphanumeric value without special characters.'));
            } elseif (!$Whats_Message || empty($Whats_Message)) {
                $output .= $this->displayError($this->l('Error: Message field is invalid.
                	Value can\'t be empty.'));
            } elseif (!Validate::isColor($Whats_Background)) {
                $output .= $this->displayError($this->l('Error: Background field is invalid.
                	Must be a Hex value.'));
            } elseif (!Validate::isColor($Whats_Fontcolor)) {
                $output .= $this->displayError($this->l('Error: FontColor field is invalid.
                	Must be a Hex value.'));
            } elseif (!$Whats_Background || empty($Whats_Background)) {
                $output .= $this->displayError($this->l('Error: Background field is invalid.
                	Value can\'t be empty.'));
            } elseif (!$Whats_Fontcolor || empty($Whats_Fontcolor)) {
                $output .= $this->displayError($this->l('Error: FontColor field is invalid.
                	Value can\'t be empty.'));
            } else {
                Configuration::updateValue('Whats_Number', $Whats_Number);
                Configuration::updateValue('Whats_Message', $Whats_Message);
                Configuration::updateValue('Whats_Background', $Whats_Background);
                Configuration::updateValue('Whats_Fontcolor', $Whats_Fontcolor);

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm=array();
        // Init Fields form array
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
            ),
            'input' =>  array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone Number'),
                    'desc' => $this->l('Your phone number'),
                    'hint' => $this->l('Format: country code + Phone Number.'),
                    'name' => 'Whats_Number',
                    'prefix' => '<i class="icon icon-whatsapp"></i>',
                    'size' => 10,
                    'maxlength' => 15,
                    'required' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Message'),
                    'desc' => $this->l('Your initial Message'),
                    'name' => 'Whats_Message',
                    'size' => 100,
                    'required' => true,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background'),
                    'name' => 'Whats_Background',
                    'required' => true,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('FontColor'),
                    'name' => 'Whats_Fontcolor',
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
        $helper = new HelperForm();
        // Module, Token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        // title and Toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
                'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
            // Load current value
        $helper->fields_value['Whats_Number'] = Configuration::get('Whats_Number');
        $helper->fields_value['Whats_Message'] = Configuration::get('Whats_Message');
        $helper->fields_value['Whats_Background'] = Configuration::get('Whats_Background');
        $helper->fields_value['Whats_Fontcolor'] = Configuration::get('Whats_Fontcolor');
        return $helper->generateForm($fieldsForm);
    }



    public function hookDisplayHeader()
    {
        /* Place your code here. */
        $this->context->controller->registerStylesheet(
            'modules-whatsapp-style',
            'modules/'.$this->name.'/views/css/front.css',
            array(
                'media' => 'all',
                'priority' => 162,
            )
        );
        $this->context->controller->registerStylesheet(
            'modules-whatsapp-icon',
            'https://use.fontawesome.com/releases/v5.0.13/css/all.css',
            array(
                'server' => 'remote',
                'position' => 'head',
                'media' => 'all',
                'priority' => 163,
            )
        );
    }

    public function hookdisplayFooterBefore()
    {
        return $this->getWhats();
    }

    public function getWhats()
    {
        /* Place your code here. */
        $this ->context->smarty-> assign(array(
            'Whats_Number' => Configuration::get('Whats_Number'),
            'Whats_Message' => Configuration::get('Whats_Message'),
            'Whats_Background' => Configuration::get('Whats_Background'),
            'Whats_Fontcolor' => Configuration::get('Whats_Fontcolor')
        ));
        return $this->display(__FILE__, 'views/templates/hook/Whatsapphook.tpl');
    }
}
