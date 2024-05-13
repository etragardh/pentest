<?php

namespace EssentialElements;

class MenuLink extends \EssentialElements\TextLink
{

    static function tag()
    {
        return 'li';
    }

    static function name()
    {
        return 'Menu Link';
    }

    static function className()
    {
        return 'breakdance-menu-item';
    }

    static function slug()
    {
       return get_class();
    }

    static function nestingRule()
    {
        return ["type" => "final", "restrictedToBeADirectChildOf" => ['EssentialElements\MenuBuilder'] ];
    }

    static function designControls() {
        return [];
    }

    static function category()
    {
        return 'site';
    }

    static function template()
    {
        $template = parent::template();

        $start = "{{ macros.linkStart(content.content.link, 'breakdance-menu-link', '', false, 'content.content.text') }}";
        $end = "{{ macros.linkEnd() }}";

        return $start . $template . $end;
    }

    static function attributes()
    {
        return [];
    }

    static function additionalClasses()
    {
        return [];
    }

}
