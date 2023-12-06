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

        $this->fieldImageSettings = [];
        $this->fieldImageSettings['popup_image'] = array(
            'name' => 'popup_image',
            'dir' => _PS_MODULE_DIR_.'popupimage/views/img/'
        );


        parent::__construct();

        $this->fields_list = [
            'popup_name' => [
                'title' => 'Nom',
                'search' => true
            ],
            'popup_image' => [
                'title' => 'Image',
                'search' => true,
                'options' => [
                    'src_field' => 'popup_image'
                ]
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
            Configuration::get('category')
        );

        if (!is_array($selected_cat)) {
            $selected_cat = array($selected_cat);
        }

        $tree = array(
            'selected_categories' => $selected_cat,
            'use_checkbox' => true,
            'id' => 'category',
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
                    'required' => true
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
                'class' => 'btn btn-primary',
                'name' => 'submitAddpopup'
            ]
        ];

        

        return parent::renderForm();
    }

    public function renderImage($path)
    {
        if (file_exists($path)){
            return '<img src="'.$path.'">';
        } else {
            return;
        }    
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddpopup')) {
            if($_FILES['popup_image']['error'] == UPLOAD_ERR_OK)
            {
                $targetDir = _PS_MODULE_DIR_. $this->module->name .'/views/img/';

                if(!file_exists($targetDir))
                {
                    mkdir($targetDir, 0755, true);
                }

                $targetFile = $targetDir.basename($_FILES["popup_image"]["name"]);

                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                if($imageFileType == 'jpg' || $imageFileType == 'png')
                {
                    move_uploaded_file($_FILES["popup_image"]["tmp_name"], $targetFile);

                    $imageName = basename($_FILES["popup_image"]["name"]);
                }
                else
                {
                    $this->errors[] = $this->l('Seuls les fichiers JPG et PNG sont autorisés.');
                }
            }
            else
            {
                $this->errors[] = $this->l('Erreur lors de l\'upload du fichier.');
            }

            $id_popup = (int)Tools::getValue('id_popup');

            if ($id_popup > 0) {
                // Si id_popup existe, effectue une mise à jour
                $selectedCategories = Tools::getValue('affiche_categories');
                $selectedCategoriesJson = json_encode($selectedCategories);
    
                $sql = 'UPDATE ' . _DB_PREFIX_ . 'popups 
                        SET 
                            popup_name = "' . Tools::getValue('popup_name') . '",
                            popup_image = "' . $imageName . '",
                            date_add = "' . Tools::getValue('date_add') . '",
                            date_end = "' . Tools::getValue('date_end') . '",
                            link = "' . Tools::getValue('link') . '",
                            active = "' . Tools::getValue('active') . '",
                            affiche_home = "' . Tools::getValue('affiche_home') . '",
                            affiche_categories = \'' . $selectedCategoriesJson . '\'
                        WHERE id_popup = ' . $id_popup;
    
                Db::getInstance()->execute($sql);
            } else {
                // Sinon, effectue une insertion
                $selectedCategories = Tools::getValue('affiche_categories');
                $selectedCategoriesJson = json_encode($selectedCategories);
    
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'popups 
                        (popup_name, popup_image, date_add, date_end, link, active, affiche_home, affiche_categories) 
                        VALUES 
                        ("' . Tools::getValue('popup_name') . '", "' . $imageName . '", "' . Tools::getValue('date_add') . '", "' . Tools::getValue('date_end') . '", "' . Tools::getValue('link') . '", "' . Tools::getValue('active') . '", "' . Tools::getValue('affiche_home') . '", \'' . $selectedCategoriesJson . '\')';
    
                Db::getInstance()->execute($sql);
            }


        }

        return parent::postProcess();
    }
}