<?php

namespace Breakdance\Elements\Macros;

use Breakdance\Elements\MacrosController;

add_action('breakdance_register_macros', function() {
    /**
     * @var string
     * @psalm-suppress UndefinedClass
    */
    $wooPresetInputsTemplate = \EssentialElements\Woopresetinputs::cssTemplate();

    ob_start();

    ?>
    {% macro wooPresetInputsDesign(inputs) %}
        <?php echo $wooPresetInputsTemplate; ?>
    {% endmacro %}
    <?php


    $macro = ob_get_clean();
    MacrosController::getInstance()->register($macro, 'preset_inputs', '');
});
