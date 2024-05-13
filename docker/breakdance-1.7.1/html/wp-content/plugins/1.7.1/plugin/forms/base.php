<?php


/*

HOW FORMS WORK:

Users can create custom forms in Breakdance by adding a Form element to
their page. They can add form fields and set various form options. A number
of preset forms such as login, logout, etc. are also available.

The form HTML is rendered on the server side. There is a render function that
takes the form options/fields and returns the HTML for the form. In the case of
a preset form, the fields are hard-coded. In the case of a custom form, the fields
are created in a repeater in Breakdance.

On the frontend, the form relies on JS for validation, handling submissions via
AJAX, redirecting after submit, captchas, etc. The form’s options (i.e. where to
redirect after submit, success message, AJAX endpoint slug, etc.) need to be available
to this JS. These are stored as JSON in a data-options attribute on the form.

All forms have a handler that runs on the backend after submission. The form data is
passed to this handler by the JS that runs on the frontend.

For the submission handler to know what the options of the form are, the form data has
to be extracted from Breakdance somehow. So we call getNodeById, which gets the tree for
the post ID that the form is in, and then uses the _lookupTable to find the form.

The submission handler gets the options of the form and then handles the submission
as appropriate. The submission is stored as a Submission post type in the WP database.

*/




include __DIR__ . "/forms.php";
include __DIR__ . "/ajax.php";
include __DIR__ . "/files.php";
include __DIR__ . "/utils.php";

// Custom
include __DIR__ . "/custom/base.php";

// Actions
include __DIR__ . "/actions/base.php";
include __DIR__ . "/actions/api-action.php";
include __DIR__ . "/actions/provider.php";

include __DIR__ . "/actions/email.php";
include __DIR__ . "/actions/discord.php";
include __DIR__ . "/actions/slack.php";
include __DIR__ . "/actions/mailchimp.php";
include __DIR__ . "/actions/mailerlite.php";
include __DIR__ . "/actions/convertkit.php";
include __DIR__ . "/actions/getresponse.php";
include __DIR__ . "/actions/drip.php";
include __DIR__ . "/actions/activecampaign.php";
include __DIR__ . "/actions/webhook.php";
include __DIR__ . "/actions/store-submission.php";
include __DIR__ . "/actions/custom-javascript.php";
include __DIR__ . "/actions/popup.php";

