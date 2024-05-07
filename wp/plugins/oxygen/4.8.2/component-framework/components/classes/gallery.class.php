<?php

/**
 * Comment Form Component Class
 * 
 * @since 2.0
 * @author Louis
 */

class Oxygen_VSB_Gallery extends CT_Component {

    public $param_array;
    public $query;
    public $action_name = "oxy_render_gallery";
    public $template_file = "gallery.php";

    function __construct($options) {

        // run initialization
        $this->init( $options );

        add_shortcode('oxy_gallery', array($this, 'shortcode'));
        add_oxygen_element('oxy_gallery', array($this, 'shortcode'));

        // change component button place
        remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
        add_action("oxygen_helpers_components_dynamic", array( $this, "component_button" ) );

        // add specific options to Basic Styles tab
        add_action("ct_toolbar_component_settings", array( $this, "settings"), 9 );

        // render preveiew with AJAX
        add_filter("template_include", array( $this, "single_template"), 100 );

        add_filter("oxy_allowed_empty_options_list", array( $this, "allowed_empty_options") );
    }

    /**
     * Generate CSS for arrays parameters only
     * 
     * @since 2.0
     * @author Ilya
     */

    function allowed_empty_options($options) {

        $options_to_add = array(
            //"layout",
            "images_per_row",
            "space_around_image",
            "image_min_width",
            "image_aspect_ratio",
            "gallery_source",
            "image_ids",
            "acf_field",
            "metabox_field",
            "woo_product_id",
            "gallery_captions",
            "gallery_captions_only_hover",
            "caption_color",
            "caption_background_color",
            "hide_captions_below",
            "image_opacity",
            "image_opacity_hover",
            "transition_duration",
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }


    /**
     * Get IDs of ACF gallery images
     * 
     * @since 2.0
     * @author Louis & Ilya
     */

    function get_ids_from_acf($fieldname) {

        if (function_exists('get_field')) {

            $ids = "";

            $images = get_field($fieldname); // defaults to current post... but will that work inside Oxygen? otherwise pass the post ID: https://www.advancedcustomfields.com/resources/get_field/
            if (!$images) $images = get_field($fieldname, 'option'); // look settings pages if there any
            if (is_array($images)){
                foreach($images as $image) {
                    $ids .= $image['id'].",";
                }
            }
            else {
                _e("No ACF gallery field data found", "oxygen");
            }

            return $ids;
        } 
        else {
            return "";
        }
    }

    function get_ids_from_metabox($fieldname) {

        if (function_exists('rwmb_meta')) {

            $ids = "";

            global $meta_box_current_group_fields;
            if ( isset($meta_box_current_group_fields[$fieldname]) ) {
                $ids = implode(",", $meta_box_current_group_fields[$fieldname]);
            }
            else { 
                $images = rwmb_meta($fieldname);
                if (is_array($images)){
                    foreach($images as $image) {
                        $ids .= $image['ID'].",";
                    }
                }
            }

            // look in Meta Box Settings Pages in case of any
            if (!$ids) {
                if (strpos($fieldname, '/')) {

                    $path = explode( '/', $fieldname );
                    $possible_settings_page = $path[0];
                    $field_id = str_replace($possible_settings_page."/", "", $fieldname);

                    $field = OxygenMetaBoxIntegration::get_field_by_path($field_id, $possible_settings_page);

                    if ($field['value']) {
                        $ids = implode(",", $field['value']);
                    }
                }
            }
                
            
            if (!$ids) {
                _e("No Meta Box gallery field data found", "oxygen");
            }

            return $ids;
        } 
        else {
            return "";
        }
    }
    
    /**
     * Get IDs of WooCommerce gallery images
     * 
     * @since 2.0
     * @author Louis & Ilya
     */
    
    function get_ids_from_woocommerce() {

        $product_id = ($this->param_array['woo_product_id']!="") ? $this->param_array['woo_product_id'] : get_the_ID();

        $product = wc_get_product($product_id);

        if (@method_exists($product, 'get_gallery_attachment_ids')) {
            $attachment_ids = $product->get_gallery_attachment_ids();

            foreach( $attachment_ids as $attachment_id ) {
                $ids .= $attachment_id.",";
            }

            return $ids;
        } 
        else {
            _e("No WooCommerce product found", "oxygen");
            
            return "";
        }

        // this works on the frontend but not inside oxygen on a template... presumably becauseglobal $product isn't set?
        // this just won't work if it's not a woocommerce product... so maybe we should grey out the "woocommerce" option unless it's a templat that applies to a woocommerce product
    }

    
    /**
     * Get IDs of default gallery images
     * 
     * @since 2.0
     * @author Louis
     */

    function get_from_media_library($ids,$thumb_size) {

            $ids = explode(",", $ids);

            $attachments = array();

            foreach ($ids as $id) {

                if ($id != '') {
                    $data = wp_get_attachment_metadata($id);

                    if (!$data) {
                        continue;
                    }

                    $attachments[] = array(
                        'url' => esc_attr(wp_get_attachment_url($id)),
                        'thumb_url' => esc_attr(wp_get_attachment_image_src($id,$thumb_size)[0]),
                        'width' => $data['width'],
                        'height' => $data['height'],
                        'caption' => esc_attr(wp_get_attachment_caption($id)),
                        'alt' => esc_attr(get_post_meta($id, '_wp_attachment_image_alt', true))
                    );
                }
            }

            return $attachments;
    }


    /**
     * Generate CSS
     * 
     * @since 2.0
     * @author Louis
     */

    function generate_css($selector) {

        global $media_queries_list;

        ob_start();

        echo "<style data-element-id=\"$selector\">";

        if ($this->param_array['layout'] == 'flex') {

            $width = number_format((100 / $this->param_array['images_per_row']), 3)."%";

            if ($this->param_array['space_around_image'] != '') {
                $width = "calc(".$width." - ".$this->param_array['space_around_image']."px - ".$this->param_array['space_around_image']."px)";
            }

            $aspect_ratio = explode(":", $this->param_array['image_aspect_ratio']);
            $aspect_ratio = ($aspect_ratio[1] / $aspect_ratio[0]);
            $aspect_ratio = number_format($aspect_ratio * 100, 2)."%"; 

            ?>
            <?php echo $selector; ?>.oxy-gallery-flex .oxy-gallery-item {
                width: <?php echo $width; ?>;
                margin: <?php echo $this->param_array['space_around_image']; ?>px;
                min-width: <?php echo $this->param_array['image_min_width']; ?>px;
                overflow: hidden;
                flex: auto;
            }

            <?php echo $selector; ?>.oxy-gallery-flex .oxy-gallery-item-sizer {
                padding-bottom: <?php echo $aspect_ratio; ?>;
                position: relative;
            }
        <?php } 
        else if ($this->param_array['layout'] == 'masonry') { ?>
                <?php echo $selector; ?>.oxy-gallery.oxy-gallery-masonry {
                    column-width: <?php echo $this->param_array['image_min_width']; ?>px;
                    column-count: <?php echo $this->param_array['images_per_row']; ?>;
                    column-gap: <?php echo $this->param_array['space_around_image']; ?>px;
                }

                <?php echo $selector; ?>.oxy-gallery-masonry .oxy-gallery-item {
                    margin-bottom: <?php echo $this->param_array['space_around_image']; ?>px;
                }
        <?php } ?>

            <?php echo $selector; ?>.oxy-gallery-captions .oxy-gallery-item .oxy-gallery-item-contents figcaption:not(:empty) {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background-color: <?php echo oxygen_vsb_get_global_color_value($this->param_array['caption_background_color']); ?>; /* caption background color */
                padding: 1em;
                color: <?php echo oxygen_vsb_get_global_color_value($this->param_array['caption_color']); ?>;  /* caption text color */
                font-weight: bold;
                -webkit-font-smoothing: antialiased;
                font-size: 1em;
                text-align: center;
                line-height: var(--oxy-small-line-height);
                /*pointer-events: none;*/
                transition: <?php echo $this->param_array['transition_duration']; ?>s ease-in-out opacity;
                display: block;
            }

        <?php 

        if ($this->param_array['gallery_captions_only_hover'] == 'yes') { ?>
        
            <?php echo $selector; ?>.oxy-gallery-captions .oxy-gallery-item .oxy-gallery-item-contents figcaption:not(:empty) {
                opacity: 0;
            }
            <?php echo $selector; ?>.oxy-gallery-captions .oxy-gallery-item:hover .oxy-gallery-item-contents figcaption {
                opacity: 1;
            }

        <?php }

        else { ?>

            <?php echo $selector; ?>.oxy-gallery-captions .oxy-gallery-item .oxy-gallery-item-contents figcaption:not(:empty) {
                opacity: 1;
            }

        <?php }

        if ( isset($this->param_array['hide_captions_below'])
             && !empty($this->param_array['hide_captions_below'])
             && $this->param_array['hide_captions_below'] != 'never') { ?>

            @media (max-width: <?php echo $media_queries_list[$this->param_array['hide_captions_below']]['maxSize']; ?>) {
              <?php echo $selector; ?>.oxy-gallery-captions .oxy-gallery-item .oxy-gallery-item-contents figcaption:not(:empty) {
                display: none;
              }
            }

        <?php } ?>
            
            /* hover effects */
            <?php echo $selector; ?>.oxy-gallery .oxy-gallery-item {
              opacity: <?php echo $this->param_array['image_opacity']; ?>;
              transition: <?php echo $this->param_array['transition_duration']; ?>s ease-in-out opacity;
            }

            <?php echo $selector; ?>.oxy-gallery .oxy-gallery-item:hover {
              opacity: <?php echo $this->param_array['image_opacity_hover']; ?>;
            }

        <?php 

        if ($this->param_array['display'] == 'grid' && isset($this->param_array['set_image_fit_to_cover']) && $this->param_array['set_image_fit_to_cover']=='true') { ?>

            <?php echo $selector; ?> .oxy-gallery-item .oxy-gallery-item-contents {
                height: 100%;
            }
            <?php echo $selector; ?> .oxy-gallery-item .oxy-gallery-item-contents img {
                height: 100%;
                object-fit: cover;
            }

        <?php 
        }
        
        echo "</style>";

        return ob_get_clean();
    }


    /**
     * Actual shortcode HTML output
     * 
     * @since 2.0
     * @author Louis
     */

    function shortcode($atts, $content = null, $name = null ) {

        if ( !(isset($atts['preview']) && $atts['preview'] == 'true') && ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

        $options = (!isset($atts['preview']) || $atts['preview']!='true') ? $this->set_options( $atts ) : $atts;
		
        $lazy = isset($options['lazy']) && $options['lazy'] ? 'loading="' . $options['lazy'] . '"' : null;

        $this->param_array = shortcode_atts(
            array(
                "layout"                => "flex", // flex | masonry
                "display"               => "",
                "set_image_fit_to_cover"=> "false",
                "images_per_row"        => '4',
                "space_around_image"    => '10', //px only
                "image_min_width"       => '', //px only
                "image_aspect_ratio"    => '16:9', // this option is only available if the layout is set to flex
                "link"                  => 'yes', // whether to link to images 
                "lightbox"              => 'yes', // this option is only available if link is set to yes 
                "gallery_source"        => 'medialibrary', // acf | medialibrary | woocommerce
                "image_ids"             => '', // if gallery_source is medialibrary, the user can choose images with the media browser
                "acf_field"             => '', // if gallery_source is acf, the user can enter in the name of an ACF gallery field
                "metabox_field"             => '',
                "woo_product_id"        => '', // if gallery_source is woocommerce
                "gallery_captions"      => 'yes',
                "gallery_captions_only_hover" => 'yes',
                "caption_color"         => '#ffffff',
                "caption_background_color" => 'rgba(0,0,0,0.75)',
                "hide_captions_below"   => '',
                "image_opacity"         => '',
                "image_opacity_hover"   => '',
                "transition_duration"   => '0.3',
                "gallery_thumbnail_size" => '',
            ), $options, $this->options['tag'] );

        $photoswipecode = "";

        if (isset($atts['preview']) && $atts['preview']=='true') {            
            $photoswipecode = 
            '<script type="text/javascript">
                jQuery(".pswp").remove();
            </script>';
        } 

        // this should only be included on a page if the gallery element is present and lightbox is enabled
        if ($this->param_array['lightbox'] == 'yes' && $this->param_array['link'] == 'yes') { 
            add_action( 'wp_footer', array( $this, 'output_js' ) );
        }

        if (!$this->in_repeater_cycle()) {
            $defaultcss = "<style>".file_get_contents(plugin_dir_path(__FILE__)."gallery/gallery.css")."</style>";
        }
        $optioncss = $this->generate_css('#'.$options['selector']);

        if ($this->param_array['gallery_source'] =='medialibrary') {
            $image_ids = $this->param_array['image_ids'];
        } 
        else if ($this->param_array['gallery_source'] =='acf') {
            $image_ids = $this->get_ids_from_acf($this->param_array['acf_field']);
        }
        else if ($this->param_array['gallery_source'] =='metabox') {
            $image_ids = $this->get_ids_from_metabox($this->param_array['metabox_field']);
        } 
        else if ($this->param_array['gallery_source'] =='woocommerce') {
            $image_ids = $this->get_ids_from_woocommerce();
        }

        $gallery_images = $this->get_from_media_library($image_ids, $this->param_array['gallery_thumbnail_size']);
        if (!isset($options['classes'])) {
            $options['classes'] = "";
        }
        
        if ($this->param_array['gallery_captions'] == 'yes') {
            $options['classes'] .= " oxy-gallery-captions";
        }

        if ($this->param_array['layout'] == 'masonry') {
            $options['classes'] .= " oxy-gallery-masonry";
        }
        else if ($this->param_array['display'] == 'grid') {
            $options['classes'] .= " oxy-gallery-grid";
        }
        else {
            $options['classes'] .= " oxy-gallery-flex";
        }

        ob_start();

        if (!isset($atts['preview']) || $atts['preview']!='true') : ?>
        <div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
        <?php endif; 

        if (sizeof($gallery_images)==0) {
            echo '<div class="oxygen-empty-gallery"></div>';
        }

        if ($this->param_array['layout'] == 'flex') { ?>

                <?php foreach ($gallery_images as $gallery_image) {

                    if ($this->param_array['link'] == 'yes') {
                        $linktag = 'a';
                        if( empty( $_GET['oxygen_gutenberg_script'] ) ) {
                            $href = " href='".$gallery_image["url"]."' ";
                        }
                        else {
                            $href = " href='#'";
                        }
                    } else {
                        $linktag = 'div';
                        $href = '';
                    } ?>

                    <?php echo "<".$linktag.$href; ?> class='oxy-gallery-item'>
                        <div class='oxy-gallery-item-sizer'>

                            <figure class='oxy-gallery-item-contents' style='background-image: url(<?php echo $gallery_image["thumb_url"]; ?>);'>

                                <img <?php echo $lazy; ?> src="<?php echo $gallery_image["thumb_url"]; ?>" data-original-src="<?php echo $gallery_image["url"]; ?>" data-original-src-width="<?php echo $gallery_image["width"]; ?>" data-original-src-height="<?php echo $gallery_image["height"]; ?>" alt="<?php echo $gallery_image["alt"]; ?>">
                                <figcaption><?php echo $gallery_image["caption"]; ?></figcaption>

                            </figure>

                        </div>
                    <?php echo "</".$linktag; ?>>

                <?php } ?>

        <?php } 
        else if ($this->param_array['layout'] == 'masonry') { ?>

                <?php foreach ($gallery_images as $gallery_image) {

                    if ($this->param_array['link'] == 'yes') {
                        $linktag = 'a';
                        if( empty( $_GET['oxygen_gutenberg_script'] ) ) {
                            $href = " href='".$gallery_image["url"]."' ";
                        }
                        else {
                            $href = " href='#'";
                        }
                    } else {
                        $linktag = 'div';
                        $href = '';
                    } ?>

                    <?php echo "<".$linktag.$href; ?> class='oxy-gallery-item'>

                        <figure class='oxy-gallery-item-contents'>

                            <img <?php echo $lazy; ?> src="<?php echo $gallery_image["thumb_url"]; ?>" data-original-src="<?php echo $gallery_image["url"]; ?>" data-original-src-width="<?php echo $gallery_image["width"]; ?>" data-original-src-height="<?php echo $gallery_image["height"]; ?>" alt="<?php echo $gallery_image["alt"]; ?>">
                            <figcaption><?php echo $gallery_image["caption"]; ?></figcaption>

                        </figure>

                    <?php echo "</".$linktag; ?>>

                <?php } ?>

        <?php } 

        else if ($this->param_array['display'] == 'grid') { ?>

            <?php foreach ($gallery_images as $gallery_image) {

                if ($this->param_array['link'] == 'yes') {
                    $linktag = 'a';
                    if( empty( $_GET['oxygen_gutenberg_script'] ) ) {
                        $href = " href='".$gallery_image["url"]."' ";
                    }
                    else {
                        $href = " href='#'";
                    }
                } else {
                    $linktag = 'div';
                    $href = '';
                } ?>

                <?php echo "<".$linktag.$href; ?> class='oxy-gallery-item'>

                    <figure class='oxy-gallery-item-contents'>

                        <img <?php echo $lazy; ?> src="<?php echo $gallery_image["thumb_url"]; ?>" data-original-src="<?php echo $gallery_image["url"]; ?>" data-original-src-width="<?php echo $gallery_image["width"]; ?>" data-original-src-height="<?php echo $gallery_image["height"]; ?>" alt="<?php echo $gallery_image["alt"]; ?>">
                        <figcaption><?php echo $gallery_image["caption"]; ?></figcaption>

                    </figure>
                    
                <?php echo "</".$linktag; ?>>

            <?php } ?>

        <?php } 

        if (!isset($atts['preview']) || $atts['preview']!='true') : ?>
        </div>
        <?php endif;

        $hashSelector = $this->get_corrected_element_selector($options['selector']);

        // frontend 
        if (!$this->in_repeater_cycle()) {
            if ($this->param_array['link'] == 'yes' && $this->param_array['lightbox'] == 'yes' && (!isset($atts['preview']) || $atts['preview'] != 'true') && !wp_doing_ajax()) { ?>
                <script type="text/javascript">
                    document.addEventListener("oxygenVSBInitGalleryJs<?php echo esc_attr($options['selector']); ?>",function(){
                        if(jQuery('<?php echo $hashSelector; ?>').photoSwipe) {
                            jQuery('<?php echo $hashSelector; ?>').photoSwipe('.oxy-gallery-item-contents');
                        }
                    },false);
                    jQuery(document).ready(function() {
                        let event = new Event('oxygenVSBInitGalleryJs<?php echo esc_attr($options['selector']); ?>');
                        document.dispatchEvent(event);
                    });
                </script>
            <?php } 
        }

        $html = ob_get_clean();

        $outputContent = $photoswipecode.$defaultcss.$optioncss.$html;

        $outputContent = apply_filters('oxygen_vsb_after_component_render', $outputContent, $this->options, $name);

        return $outputContent;
    }


    /**
     * Basic Styles settings
     *
     * @since 3.1
     * @author Ilya K.
     */

    function output_js() {

        wp_enqueue_style( 'photoswipe-css', CT_FW_URI . '/components/classes/gallery/photoswipe/photoswipe.css' );
        wp_enqueue_style( 'photoswipe-default-skin-css', CT_FW_URI . '/components/classes/gallery/photoswipe/default-skin/default-skin.css' );

        wp_enqueue_script( 'photoswipe-global-js', CT_FW_URI . '/components/classes/gallery/photoswipe/jquery.photoswipe-global.js', '', '', true );

    }


    /**
     * Basic Styles settings
     *
     * @since 2.0
     * @author Ilya K.
     */

    function settings () { 

        global $oxygen_toolbar; ?>

        <div class="oxygen-sidebar-flex-panel"
            ng-hide="!isActiveName('oxy_gallery')">

            <div ng-show="!hasOpenTabs('gallery')">

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Gallery Source","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('gallery_source','medialibrary', __('Media Library','oxygen')); ?>
                                <?php $oxygen_toolbar->button_list_button('gallery_source','acf', __('ACF','oxygen')); ?>
                                <?php $oxygen_toolbar->button_list_button('gallery_source','metabox', __('Meta Box','oxygen')); ?>
                                <?php $oxygen_toolbar->button_list_button('gallery_source','woocommerce', __('WooCommerce','oxygen')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.getOption('gallery_source')=='acf'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("ACF Field", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['acf_field']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_gallery','acf_field');iframeScope.renderComponentWithAJAX('oxy_render_gallery')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.getOption('gallery_source')=='metabox'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Meta Box Field", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'>
                                <input type="text" spellcheck="false"
                                    ng-model="iframeScope.component.options[iframeScope.component.active.id]['model']['metabox_field']"
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_gallery','metabox_field');iframeScope.renderComponentWithAJAX('oxy_render_gallery')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.getOption('gallery_source')=='medialibrary'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Image IDs", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-file-input'
                                ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'image_ids')}">
                                <input type="text" spellcheck="false"
                                    <?php $oxygen_toolbar->ng_attributes('image_ids','model') ?>
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_gallery','image_ids');iframeScope.renderComponentWithAJAX('oxy_render_gallery')">
                                <div class="oxygen-file-input-browse"
                                    data-mediaTitle="Select Images" 
                                    data-mediaButton="Select Images"
                                    data-mediaMultiple="true"
                                    data-mediaProperty="image_ids"
                                    data-mediaType="gallery"><?php _e("browse","oxygen"); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.getOption('gallery_source')=='woocommerce'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Woo Product ID", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'
                                ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'woo_product_id')}">
                                <input type="text" spellcheck="false"
                                    <?php $oxygen_toolbar->ng_attributes('woo_product_id','model') ?>
                                    ng-change="iframeScope.setOption(iframeScope.component.active.id,'oxy_gallery','woo_product_id');iframeScope.renderComponentWithAJAX('oxy_render_gallery')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Link Images","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('link','yes'); ?>
                                <?php $oxygen_toolbar->button_list_button('link','no'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'
                    ng-show="iframeScope.getOption('link')=='yes'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Add Lightbox","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('lightbox','yes'); ?>
                                <?php $oxygen_toolbar->button_list_button('lightbox','no'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Thumbnail Resolution","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class="oxygen-select oxygen-select-box-wrapper">
                                <div class="oxygen-select-box">
                                    <div class="oxygen-select-box-current">{{iframeScope.getOption('gallery_thumbnail_size')}}</div>
                                    <div class="oxygen-select-box-dropdown"></div>
                                </div>
                                <div class="oxygen-select-box-options">
                                    <?php $sizes = get_intermediate_image_sizes(); ?>
                                    <?php foreach ($sizes as $key => $value) : ?>
                                    <div class="oxygen-select-box-option"
                                        ng-click="iframeScope.setOptionModel('gallery_thumbnail_size','<?php echo $value; ?>');">
                                        <?php echo $value; ?>
                                    </div>
                                    <?php endforeach; ?>
                                    <div class="oxygen-select-box-option"
                                        ng-click="iframeScope.setOptionModel('gallery_thumbnail_size','full');">
                                        full
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-show="iframeScope.getOption('layout')!='flex'">
                    <?php $oxygen_toolbar->checkbox('lazy', __("Lazy Load", "oxygen"), "lazy", "") ?>
                </div>

            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('gallery', 'layout')" 
                ng-show="!hasOpenTabs('gallery')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Layout", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('gallery', 'layout')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.gallery=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.gallery=[]"><?php _e("Gallery","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Layout","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Layout","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('layout','flex'); ?>
                                <?php $oxygen_toolbar->button_list_button('layout','masonry'); ?>
                                <?php $oxygen_toolbar->button_list_button('display','grid'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-show="iframeScope.getOption('display')=='grid'">
                    <?php $oxygen_toolbar->checkbox('set_image_fit_to_cover', __("Set Image Fit To Cover", "oxygen"), "true", "false") ?>
                </div>

                <div class="oxygen-control-row">
                    <div class="oxygen-control-wrapper">
                        <div class="oxygen-control">
                            <?php 
                            $param = [];
                            $param['param_name'] = 'flex-direction';
                            $tag = $this->options['tag'];
                            ?>
                            <?php include( CT_FW_PATH . '/toolbar/views/position/position.flex-layout.view.php');?>
                        </div>
                    </div>
                </div>

                <div
                    ng-hide="iframeScope.getOption('display')=='grid'">
                <div class='oxygen-control-row'
                    ng-show="iframeScope.getOption('layout')=='flex'">
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Image Aspect Ratio", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'
                                ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'image_aspect_ratio')}">
                                <input type="text" spellcheck="false"
                                    <?php $oxygen_toolbar->ng_attributes('image_aspect_ratio') ?>>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Images per row", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-input'
                                ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'images_per_row')}">
                                <input type="text" spellcheck="false"
                                    <?php $oxygen_toolbar->ng_attributes('images_per_row') ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('space_around_image',__('Space Around Image','oxygen'), "px"); ?>
                </div>

                <div class='oxygen-control-row'>
                    <?php $oxygen_toolbar->measure_box_with_wrapper('image_min_width',__('Image Min Width','oxygen'), "px"); ?>
                </div>

                </div>

            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('gallery', 'captions')" 
                ng-show="!hasOpenTabs('gallery')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Captions", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('gallery', 'captions')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.gallery=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.gallery=[]"><?php _e("Gallery","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Captions","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Show Captions","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('gallery_captions','yes'); ?>
                                <?php $oxygen_toolbar->button_list_button('gallery_captions','no'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("caption_color", __("Caption Color", "oxygen") ); ?>
                </div>

                <div class="oxygen-control-row">
                    <?php $oxygen_toolbar->colorpicker_with_wrapper("caption_background_color", __("Caption Background Color", "oxygen") ); ?>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Show Captions Only On Hover","oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <div class='oxygen-button-list'>
                                <?php $oxygen_toolbar->button_list_button('gallery_captions_only_hover','yes'); ?>
                                <?php $oxygen_toolbar->button_list_button('gallery_captions_only_hover','no'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $oxygen_toolbar->media_queries_list_with_wrapper("hide_captions_below", __("Hide Captions Below","oxygen"), true); ?>

            </div>

            <div class="oxygen-sidebar-advanced-subtab" 
                ng-click="switchTab('gallery', 'hover')" 
                ng-show="!hasOpenTabs('gallery')">
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/panelsection-icons/general-config.svg">
                    <?php _e("Hover", "oxygen"); ?>
                    <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/open-section.svg">
            </div>

            <div class="oxygen-sidebar-flex-panel"
                ng-if="isShowTab('gallery', 'hover')">

                <div class="oxygen-sidebar-breadcrumb oxygen-sidebar-subtub-breadcrumb">
                    <div class="oxygen-sidebar-breadcrumb-icon" 
                        ng-click="tabs.gallery=[]">
                        <img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/advanced/chevron-left.svg">
                    </div>
                    <div class="oxygen-sidebar-breadcrumb-all-styles" 
                        ng-click="tabs.gallery=[]"><?php _e("Gallery","oxygen"); ?></div>
                    <div class="oxygen-sidebar-breadcrumb-separator">/</div>
                    <div class="oxygen-sidebar-breadcrumb-current"><?php _e("Hover","oxygen"); ?></div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Image Opacity", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->slider_measure_box('image_opacity', "", 0, 1, true, 0.1); ?>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Hover Image Opacity", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->slider_measure_box('image_opacity_hover', "", 0, 1, true, 0.1); ?>
                        </div>
                    </div>
                </div>

                <div class='oxygen-control-row'>
                    <div class='oxygen-control-wrapper'>
                        <label class='oxygen-control-label'><?php _e("Transition Duration", "oxygen"); ?></label>
                        <div class='oxygen-control'>
                            <?php $oxygen_toolbar->slider_measure_box('transition_duration', "sec", 0, 1, true, 0.1); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    <?php }
}

// Create gallery instance
global $oxygen_vsb_components;
$oxygen_vsb_components['gallery'] = new Oxygen_VSB_Gallery(
        array(
            'name'  => __('Gallery','oxygen'),
            'tag'   => 'oxy_gallery',
            'advanced'  => array(
                "other" => array(
                    "values" => array(
                        "layout" => "flex", // flex | masonry
                        "images_per_row" => '4',
                        "space_around_image" => '10', //px only
                        "image_min_width" => '', //px only
                        "image_aspect_ratio" => '16:9', // this option is only available if the layout is set to flex
                        "lazy" => "",
                        "link" => 'yes', // whether to link to images 
                        "lightbox" => 'yes', // this option is only available if link is set to yes 
                        "gallery_source" => 'medialibrary', // acf | medialibrary | woocommerce
                        "image_ids" => '', // if gallery_source is medialibrary, the user can choose images with the media browser
                        "acf_field" => '', // if gallery_source is acf, the user can enter in the name of an ACF gallery field
                        "metabox_field" => '', // if gallery_source is acf, the user can enter in the name of an ACF gallery field
                        "woo_product_id" => '', // if gallery_source is woocommerce
                        "gallery_captions" => 'yes',
                        "gallery_captions_only_hover" => 'yes',
                        "caption_color" => '#ffffff',
                        "caption_background_color" => 'rgba(0,0,0,0.75)',
                        "hide_captions_below" => '',
                        "image_opacity" => '',
                        "image_opacity_hover" => '',
                        "transition_duration" => '0.3'
                    )
                ),
                "positioning" => array(
                        "values"    => array (
                            'width'      => '100',
                            'width-unit' => '%',
                            )
                    ),
                'grid' => array(
						'values' 	=> array (
                            'grid-columns-auto-fit' => '',
							'grid-column-count' => '3',
							'grid-column-min-width' => '200',
							'grid-column-min-width-unit' => 'px',
							'grid-column-max-width' => '1',
							'grid-column-max-width-unit' => 'fr',
							'grid-column-gap' => '20',
							'grid-column-gap-unit' => 'px',

							'grid-row-behavior' => 'Auto',
							'grid-row-count' => '',
							'grid-row-min-height' => '',
							'grid-row-min-height-unit' => 'px',
							'grid-row-max-height' => '',
							'grid-row-max-height-unit' => 'fr',
							'grid-row-gap' => '20',
							'grid-row-gap-unit' => 'px',
                        
                            'grid-match-height-of-tallest-child' => 'true',
							'grid-justify-items' => 'stretch',
							'grid-align-items' => 'stretch',
                            )
					),
            ),
            'not_css_params' => array(
                "lazy",
                "layout",
                "images_per_row",
                "space_around_image",
                "image_min_width",
                "image_aspect_ratio",
                "link",
                "lightbox",
                "gallery_source",
                "image_ids",
                "acf_field",
                "metabox_field",
                "woo_product_id", // if gallery_source is woocommerce
                "gallery_captions",
                "gallery_captions_only_hover",
                "caption_color",
                "caption_background_color",
                "hide_captions_below",
                "image_opacity",
                "image_opacity_hover",
                "transition_duration",
                "gallery_thumbnail_size"
            ),
        ));