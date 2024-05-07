<?php

namespace Breakdance\DesignLibrary;

function getDesignLibraryAppLoader()
{
    ?>

    <style>
        .design-library-spa-wrapper iframe {
            display: block;
            width: 100%;
            height: 100vh;
        }

        .wrap {
            margin: 0;
        }

        #wpbody-content,
        #wpcontent {
            padding: 0;
        }

        #wpfooter {
            display: none;
        }
    </style>

    <div class="wrap">
        <div class="design-library-spa-wrapper">
            <iframe id="design-library-spa-wrapper-iframe" width="100%" frameborder="0"
                    src="<?= site_url("?breakdance=design_library") ?>">
            </iframe>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo BREAKDANCE_PLUGIN_URL; ?>plugin/lib/iframe-resizer@4/iframeResizer.min.js"></script>
    <script>
      iFrameResize({
        onInit: (element) => {
          const sidebar = document.querySelector('#adminmenuwrap');
          element.style.minHeight = `${sidebar.offsetHeight}px`;
        },
        onResized: () => {
          jQuery(document).trigger('wp-pin-menu');
        }
      }, "#design-library-spa-wrapper-iframe");
    </script>

    <?php
}
