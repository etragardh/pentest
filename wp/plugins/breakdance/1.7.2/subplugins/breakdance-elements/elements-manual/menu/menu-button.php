<?php

namespace EssentialElements;

class MenuButton extends \EssentialElements\Button
{

    static function tag()
    {
        return 'li';
    }

    static function name()
    {
        return 'Menu Button';
    }

    static function additionalClasses()
    {
        return [['name' => 'breakdance-menu-item', 'template' => 'yes']];
    }

    static function slug()
    {
       return get_class();
    }

    static function nestingRule()
    {
        return ["type" => "final", "restrictedToBeADirectChildOf" => ['EssentialElements\MenuBuilder'] ];
    }

    static function category()
    {
        return 'site';
    }

}
