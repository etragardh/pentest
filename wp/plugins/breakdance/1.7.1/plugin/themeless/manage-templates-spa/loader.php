<?php

namespace Breakdance\Themeless\ManageTemplates;

function getManageBreakdancePostTypesSpaHtml()
{
    ?>

    <style>
        .manage-templates-spa-wrapper iframe {
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

        @media (min-width: 960px ) {
            .manage-templates-spa-wrapper {
                position: fixed;
                width: calc(100% - 160px);
            }
        }
    </style>

    <div class="wrap">
        <div class="manage-templates-spa-wrapper">
            <iframe id="manage-templates-wrapper-iframe" width="100%" frameborder="0"
                    src="<?= site_url("?breakdance=templates") ?>">
            </iframe>
        </div>
    </div>
    <?php
}
