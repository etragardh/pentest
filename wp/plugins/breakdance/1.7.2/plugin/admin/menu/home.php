<?php

namespace Breakdance\Admin;

use function Breakdance\SetupWizard\Onboarding\showNotice;

function homePage()
{
    ?>
    <style>
        #wpcontent {
            padding: 0;
        }

        .breakdance-home__footer {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 14px;
        }

        .breakdance-home__footer {
            color: #D4D4D4;
        }

        .breakdance-home__footer svg path {
            fill: hsl(0deg 0% 80%);
        }

        .breakdance-home__social {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .breakdance-home__video {
            position: relative;
            padding-bottom: 56.25%;
        }

        .breakdance-home__video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .bd-setup-notice--video {
            margin-top: 24px;
        }
    </style>
    <div class='breakdance-home'>

        <?php showNotice(false, false);?>

        <div class="bd-setup-notice bd-setup-notice--video notice">
            <div class="breakdance-home__video">
                <iframe src="https://www.youtube.com/embed/wrpcUw6KIEQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>

        <div class="breakdance-home__footer">
            <div class="breakdance-home__social">
                <a href="https://www.facebook.com/groups/breakdanceofficial" target="_blank">
                    <svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2 5.33333H0V8H2V16H5.33333V8H7.76133L8 5.33333H5.33333V4.222C5.33333 3.58533 5.46133 3.33333 6.07667 3.33333H8V0H5.46133C3.064 0 2 1.05533 2 3.07667V5.33333Z"
                            fill="#E5E5E5" />
                    </svg>
                </a>
                <a href="https://twitter.com/TeamBreakdance" target="_blank">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16 2.03799C15.4113 2.29932 14.7787 2.47532 14.1147 2.55466C14.7927 2.14866 15.3133 1.50532 15.558 0.738657C14.924 1.11466 14.2213 1.38799 13.4733 1.53532C12.8753 0.897324 12.0213 0.498657 11.0773 0.498657C8.958 0.498657 7.40067 2.47599 7.87933 4.52866C5.152 4.39199 2.73333 3.08532 1.114 1.09932C0.254 2.57466 0.668 4.50466 2.12933 5.48199C1.592 5.46466 1.08533 5.31732 0.643333 5.07132C0.607333 6.59199 1.69733 8.01466 3.276 8.33132C2.814 8.45666 2.308 8.48599 1.79333 8.38732C2.21067 9.69132 3.42267 10.64 4.86 10.6667C3.48 11.7487 1.74133 12.232 0 12.0267C1.45267 12.958 3.17867 13.5013 5.032 13.5013C11.1267 13.5013 14.57 8.35399 14.362 3.73732C15.0033 3.27399 15.56 2.69599 16 2.03799Z"
                            fill="#E5E5E5" />
                    </svg>
                </a>

                <a href="https://www.youtube.com/@OfficialBreakdance" target="_blank">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M13.0767 0.122689C10.674 -0.0413109 5.32267 -0.0406442 2.92333 0.122689C0.325333 0.300022 0.0193333 1.86936 0 6.00002C0.0193333 10.1234 0.322667 11.6994 2.92333 11.8774C5.32333 12.0407 10.674 12.0414 13.0767 11.8774C15.6747 11.7 15.9807 10.1307 16 6.00002C15.9807 1.87669 15.6773 0.300689 13.0767 0.122689ZM6 8.66669V3.33336L11.3333 5.99536L6 8.66669Z"
                            fill="#E5E5E5" />
                    </svg>
                </a>
            </div>

            <div class="breakdance-home__copyright">Copyright &copy; Soflyy. All Rights Reserved.</div>
        </div>
    </div>
    <?php
}
