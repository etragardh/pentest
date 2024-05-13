<?php

namespace Breakdance\DynamicData;

class AcfPostPageLinkField extends AcfPostField {

    public function slug()
    {
        return 'acf_post_link_' . $this->field['key'];
    }

    public function getPost()
    {
        $value = AcfField::getValue($this->field, false);
        if (is_int($value)) {
            return get_post($value);
        }
        if (is_array($value) && !empty($value)) {
            [$postId] = $value;
            return get_post($postId);
        }
        return false;
    }
}
