<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'popupimage/classes/Popup.php';

class PopUpImage extends Module
{
    public function __construct()
    {
        $this->name = 'popupimage';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Doryan Fourrichon';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        
        //récupération du fonctionnement du constructeur de la méthode __construct de Module
        parent::__construct();
        $this->bootstrap = true;

        $this->displayName = $this->l('Pop Up Image');
        $this->description = $this->l('My module');

        $this->confirmUninstall = $this->l('Do you want to delete this module');

    }

    public function install()
    {
        if (!parent::install() ||
        !Configuration::updateValue('POPUPACTIVE',0) ||
        !$this->registerHook('displayHeader') ||
        !$this->createTable() ||
        !$this->installTab('AdminPopUp','Ajout Pop up','AdminCatalog')
        ) {
            return false;
        }
            return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
        !Configuration::deleteByName('POPUPACTIVE') ||
        !$this->unregisterHook('displayHeader') ||
        !$this->deleteTable() ||
        !$this->uninstallTab()
        ) {
            return false;
        }
            return true;
    }

    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    public function renderForm()
    {
        $field_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Setings'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                        'label' => $this->l('Active Pop up'),
                        'name' => 'POPUPACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            )
                        )
                ],
            ],
            'submit' => [
                'title' => $this->l('save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ]
        ];

        $helper = new HelperForm();
        $helper->module  = $this;
        $helper->name_controller = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['POPUPACTIVE'] = Configuration::get('POPUPACTIVE');

        return $helper->generateForm($field_form);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('saving')) {
            if (Validate::isBool(Tools::getValue('POPUPACTIVE'))) {
                Configuration::updateValue('POPUPACTIVE',Tools::getValue('POPUPACTIVE'));
                return $this->displayConfirmation('Bien enregistré !');
            }
                return $this->displayError('un problème est survenue');

                
        }
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        // ajouter un lien vers le controller d'admin
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();

        foreach(Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if($tabParentName){

            $tab->id_parent = Tab::getIdFromClassName($tabParentName);
        } else{
            $tab->id_parent =  10;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $idTab = Tab::getIdFromClassName('AdminParametre');
        $tab =  new Tab($idTab);
        $tab->delete();
    }

    public function createTable()
    {
        return DB::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'popups(
                id_popup INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                popup_name varchar(255) NOT NULL,
                popup_image varchar(255) NOT NULL,
                date_add datetime,
                date_end datetime,
                link varchar(255),
                active BOOLEAN NOT NULL,
                affiche_home BOOLEAN NOT NULL,
                affiche_categories VARCHAR(255) NOT NULL
            )'
        );
    }

    public function deleteTable()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS '._DB_PREFIX_.'popups'
        );
    }

    public function hookDisplayHeader($params)
    {
        $popup = PopUp::getPopUpActive();
        $link = new Link();
        dump(_PS_MODULE_DIR_);
        $imagePath = $link->getBaseLink() . '/modules/' . $this->name . '/views/img/' . $popup[0]["popup_image"];

        if (!file_exists($imagePath)) {
            // Log ou affiche un message d'erreur
            error_log('Le fichier image n\'existe pas : ' . $imagePath);
        }

        $this->smarty->assign(array(
            'popup_image' => $imagePath,
            'popup_name' => $popup[0]["popup_name"]


        ));

        // if (Configuration::get('POPUPACTIVE') == 1 && $popup[0]["active"] == 1) {
        //     $this->context->controller->registerJavascript('js_script_modal','modules/popupimage/views/js/script.js');
        // }
        $this->context->controller->registerJavascript('js_script_modal','modules/popupimage/views/js/script.js');

        return $this->display(__FILE__,'/views/templates/hooks/modal_popup.tpl');
    }
}