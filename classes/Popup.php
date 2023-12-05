<?php

class PopUp extends ObjectModel
{
    public $id_popup;
    public $popup_name;
    public $popup_image;
    public $date_add;
    public $date_end;
    public $link;
    public $active;
    public $affiche_home;
    public $affiche_category;

    public static $definition = [
        'table' => 'popups',
        'primary' => 'id_popup',
        'fields' => [
            'popup_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'popup_image' => ['type' => self::TYPE_STRING, 'validate' => 'isFileName', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'date_end' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'link' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => false],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'affiche_home' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'affiche_categories' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false]
        ]
    ];

    public static function getPopUpActive()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'popups WHERE active = 1 ORDER BY id_popup ASC LIMIT 1';

        return DB::getInstance()->executeS($sql);
    }
}