<?php

require_once _PS_MODULE_DIR_.'popupimage/classes/Popup.php';

class AdminPopUpController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = PopUp::$definition['table'];
        $this->className = PopUp::class;
        $this->module = Module::getInstanceByName('popupimage');
        $this->identifier = PopUp::$definition['primary'];
        $this->_orderBy = PopUp::$definition['primary'];
        $this->bootstrap = true;
        
        parent::__construct();

        $this->fields_list = [
            'popup_name' => [
                'title' => 'Nom',
                'search' => true
            ],
            'popup_image' => [
                'title' => 'Image',
                'search' => true
            ],
            'date_add' => [
                'title' => 'Date début',
                'search' => true
            ],
            'date_end' => [
                'title' => 'Date fin',
                'search' => true
            ],
            'link' => [
                'title' => 'Lien',
                'search' => true
            ],
            'active' => [
                'title' => 'actif',
                'search' => true
            ],
            'affiche_home' => [
                'title' => 'Afficher Accueil',
                'search' => true
            ],
            'affiche_categories' => [
                'title' => 'Afficher Catégories',
                'search' => true
            ]
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('view');

    }

    public function renderForm()
    {
        $selected_cat = json_decode(
            Configuration::get(
                'CODEPROMOCATEGORIES'
            )
        );

        if (!is_array($selected_cat)) {
            $selected_cat = array($selected_cat);
        }

        $tree = array(
            'selected_categories' => $selected_cat,
            'use_search' => true,
            'use_checkbox' => true,
            'id' => 'id_category_tree',
        );

        $this->fields_form = [
            'legend' => [
                'title' => 'Création Pop up'
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'popup_name',
                    'required' => true,
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'popup_image',
                    'required' => true,
                ],
                [
                    'type' => 'date',
                    'name' => 'date_add',
                    'label' => $this->l('Date début'),
                    'required' => true,
                ],
                [
                    'type' => 'date',
                    'name' => 'date_end',
                    'label' => $this->l('Date Fin'),
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Link'),
                    'name' => 'link',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Activer le pop up ?'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' =>'1',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => '0',
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Activer sur la page d\'accueil ?'),
                    'name' => 'affiche_home',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' =>'1',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => '0',
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'categories',
                    'name' => 'affiche_categories',
                    'required' => false,
                    'tree' => $tree
                ],
            ],
            'submit' => [
                'title' => 'Save',
                'class' => 'btn btn-primary'
            ]
        ];

        return parent::renderForm();
    }


}