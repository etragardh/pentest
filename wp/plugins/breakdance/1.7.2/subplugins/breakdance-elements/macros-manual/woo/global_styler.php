<?php

namespace Breakdance\Elements\Macros;

use Breakdance\Elements\MacrosController;

add_action('breakdance_register_macros', function () {
    /**
     * @var string
     * @psalm-suppress UndefinedClass
     */
    $wooGlobalStylerTemplate = \EssentialElements\Wooglobalstyler::cssTemplate();
    $wooGlobalStylerTemplate = str_replace("macros", "_self", $wooGlobalStylerTemplate);

    ob_start();

    ?>
    {% macro wooGlobalStyler(design, rootSelector, breakpoint, globalSettings) %}
        <?php echo $wooGlobalStylerTemplate; ?>
    {% endmacro %}
    <?php

    $macro = ob_get_clean();
    MacrosController::getInstance()->register($macro, 'global_styler', '');
});
