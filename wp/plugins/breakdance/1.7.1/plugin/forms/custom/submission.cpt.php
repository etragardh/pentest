<?php

namespace Breakdance\Forms\Submission;

use Breakdance\Forms\Actions\ActionProvider;
use function Breakdance\Forms\getSecureFileUrl;
use function Breakdance\Forms\getIdFromField;
use function Breakdance\Forms\getSubmissionPanelFieldHandler;
use function Breakdance\Forms\getFormData;
use function Breakdance\Forms\getFormSettings;
use function Breakdance\Subscription\appendProToLabelInFreeMode;

/**
 * Register Submission Custom Post Type
 * @return void
 */
function registerPostType()
{
    register_post_type(
        'breakdance_form_res',
        [
            'labels' => [
                'name' => 'Form Submissions',
                'singular_name' => 'Form Submission',
                'edit' => 'View',
                'edit_item' => 'Edit Submission',
                'item_updated' => 'Submission updated.'
            ],
            'show_ui' => true,
            'show_in_admin_bar' => true,
            'show_in_menu' => false,
            'supports' => ['title'],
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => false, // Disable "add new"
            ],
            'map_meta_cap' => true,
            'public' => false
        ]
    );
}
add_action('init', '\Breakdance\Forms\Submission\registerPostType');

/**
 * @param string $hook
 */
function enqueueStyles($hook)
{
    global $post;

    /** @var \WP_Post */
    $post = $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'breakdance_form_res' === $post->post_type ) {
            $url = BREAKDANCE_PLUGIN_URL . 'plugin/forms/custom/css/submission.css';
            wp_enqueue_style('breakdance-submission', $url);
        }
    }
}
add_action('admin_enqueue_scripts', '\Breakdance\Forms\Submission\enqueueStyles', 10, 1);

/**
 * @param array<string, string> $columns
 * @return array<string, string>
 */
function setColumnNames($columns)
{
    unset($columns['date']);

    $columns['title'] = __('Email');
    $columns['form']  = __('Form');
    $columns['post']  = __('Post');
    $columns['submitted_on'] = __('Submitted on');

    return $columns;
}
add_filter('manage_breakdance_form_res_posts_columns', '\Breakdance\Forms\Submission\setColumnNames');

/**
 * Get submission meta data
 * @param int $id
 * @return FormSubmissionMeta
 */
function getMeta($id)
{
    $formId = (int) get_post_meta($id, '_breakdance_form_id', true);
    $postId = (int) get_post_meta($id, '_breakdance_post_id', true);
    /** @var FormUserSubmittedContents $fields */
    $fields = get_post_meta($id, '_breakdance_fields', true) ?: [];
    /** @var FormSettings|null $settings */
    $settings = get_post_meta($id, '_breakdance_settings', true) ?: null;

    $ip = (string) get_post_meta($id, '_breakdance_ip', true);
    $referer = (string) get_post_meta($id, '_breakdance_referer', true);
    $userAgent = (string) get_post_meta($id, '_breakdance_user_agent', true);
    $userId = (int) get_post_meta($id, '_breakdance_user_id', true);

    $user = false;
    if ($userId) {
        $user = get_user_by( 'id', $userId );
    }

    $form = getFormData($settings ? $settings['form']['fields'] : [], $fields);

    $formPostTitle = get_the_title($postId);
    $formName = $settings ? $settings['form']['form_name'] : 'Unknown form';
    $status = get_post_status($id);

    $date = get_the_time('F j, Y \a\t h:m a', $id);
    $modified = get_the_modified_date('F j, Y \a\t h:m a', $id);
    $editUrl = get_edit_post_link($postId);
    $postType = get_post_type($postId);
    $postTypeLabel = 'Post';

    if ($postType) {
        $postTypeObject = get_post_type_object($postType);
        $postTypeLabel = $postTypeObject ? (string) $postTypeObject->labels->singular_name : ucwords($postType);
    }

    $builderUrl = \Breakdance\Admin\get_builder_loader_url((string) $postId);
    /** @var string[] $allEditablePostTypes */
    $allEditablePostTypes = BREAKDANCE_ALL_EDITABLE_POST_TYPES;
    if (!$editUrl && in_array($postType, $allEditablePostTypes)) {
        $editUrl = $builderUrl;
    }

    return  [
        'formId' => $formId,
        'postId' => $postId,

        'form' => $form,
        'fields' => $fields,
        'settings' => $settings,

        'formName'  => $formName,
        'postTitle' => $formPostTitle,
        'postType' => $postType,
        'postTypeLabel' => $postTypeLabel,

        'status' => $status,
        'date' => $date,
        'modified' => $modified,

        // WP
        'editUrl'    => $editUrl,
        'builderUrl' => $builderUrl,

        // Other
        'ip'      => $ip,
        'referer' => $referer,
        'userAgent' => $userAgent,
        'user' => $user,
    ];
}

/**
 * @param string $column
 * @param int $postId
 */
function renderColumns($column, $postId)
{
    $meta = getMeta($postId);

    switch ($column) {
        case 'form':
            if ($meta['postId'] && $meta['builderUrl']) {
                echo sprintf('<a href="%s" target="_blank">%s (%s)</a>', $meta['builderUrl'], $meta['formName'], $meta['postId']);
            } else {
                echo '<i>Deleted Form</i>';
            }
            break;

        case 'post':
            if ($meta['formId'] && $meta['editUrl']) {
                echo sprintf('<a href="%s" target="_blank">%s</a>', $meta['editUrl'], $meta['postTitle']);
            } else {
                echo '<i>Deleted Post</i>';
            }
            break;

        case 'submitted_on':
            echo $meta['date'];
            break;
    }
}
add_action('manage_breakdance_form_res_posts_custom_column', '\Breakdance\Forms\Submission\renderColumns', 10, 2);

function renderMetaBox()
{
    global $post;
    /**
     * @psalm-suppress MixedPropertyFetch
     * @psalm-suppress MixedArgument
     */
    $meta = getMeta($post->ID);

    /**
     * @psalm-suppress MixedArgument
     */
    markAsRead($post->ID);
    add_thickbox();
    ?>
    <table class="form-table">
       <tbody>
        <?php
        foreach ($meta['form'] as $field) {
            $fieldId = getIdFromField($field);
            $id = sprintf("fields[%s]", $fieldId);

            if ($field['type'] === 'file') {
                continue;
            }

            $fieldValue = '';
            if (isset($meta['fields'][$fieldId])) {
                /** @var string|string[] $fieldValue */
                $fieldValue = $meta['fields'][$fieldId];
            }

            $fieldHandler = getSubmissionPanelFieldHandler($field, $fieldValue);
            if (empty($fieldHandler)) {
                continue;
            }
        ?>
          <tr>
             <th>
                 <label for="<?php echo $id; ?>"><?php echo $field['label']; ?></label>
             </th>
             <td>
                 <?php echo $fieldHandler; ?>
             </td>
          </tr>
        <?php } ?>
       </tbody>
    </table>
    <?php
}

function renderActionsMetaBox()
{
    global $post;
    /**
     * @psalm-suppress MixedPropertyFetch
     * @psalm-suppress MixedArgument
     * @var array<string, array{type: "error"|"success"|"admin_error", message: string, executed_at: string, context: ActionContext[]}>
     */
    $actions = get_post_meta($post->ID, '_breakdance_form_actions', true) ?: [];
    $actionProvider = ActionProvider::getInstance();
    $successMessage = 'Action completed successfully';

    ?>
    <div class="breakdance-actions">
        <?php if (empty($actions)) { ?>
            <p class="breakdance-actions-empty">No form actions.</p>
        <?php } ?>
        <?php
        foreach ($actions as $slug => $action) {
            $instance = $actionProvider->getActionBySlug((string) $slug);
            $hasError = $action['type'] !== 'success';
            $name     = $instance ? $instance->name() : $slug; // Fallback to slug if the action is undefined.
            $message  = $hasError ? $action['message'] : $successMessage;
            $executionDate = '';
            if (isset($action['executed_at'])) {
                $executionDate = (new \DateTime($action['executed_at']))->format("D, d M Y H:i:s");
            }
        ?>
            <div id="inspect-action-<?php echo $slug; ?>" style="display:none;">
                <div>
                    <?php foreach ($action['context'] as $context) {
                        if (empty($context['data'])) { continue; } ?>
                        <h3><?php echo $context['section']; ?></h3>
                        <dl class="breakdance-action-context-list">
                            <?php
                            /**
                             * @psalm-suppress MixedAssignment
                             */
                            foreach ($context['data'] as $contextKey => $contextValue) {
                                ?>
                                <dt class="breakdance-action-context-list__title"><?php echo (string) $contextKey; ?></dt>
                                <dd class="breakdance-action-context-list__details"><?php  echo !is_string($contextValue) ? '<pre>' . print_r($contextValue, true) . '</pre>' : $contextValue; ?></dd>
                            <?php } ?>
                        </dl>
                    <?php } ?>
                </div>
            </div>

            <div class="breakdance-action-item breakdance-action-item--<?php echo $hasError ? 'error' : 'success'; ?>">
                <header class="breakdance-action-item-header">
                    <h3 class="breakdance-action-item__name"><?php echo $name; ?></h3>
                    <span class="breakdance-action-item__icon dashicons <?php echo $hasError ? 'dashicons-no-alt' : 'dashicons-yes'; ?>"></span>
                    <span class="breakdance-action-item__time"><?php echo (string) $executionDate; ?></span>
                    <?php if (!empty($action['context'])) { ?>
                        <span class="breakdance-action-item__view">
                            <a href="#TB_inline?&width=800&height=800&inlineId=inspect-action-<?php echo $slug; ?>" class="thickbox" name="<?php echo $name; ?>">Details</a>
                        </span>
                    <?php } ?>
                </header>

                <p class="breakdance-action-item__message notice <?php echo $hasError ? 'notice-error error' : 'notice-success' ?> ">
                    <?php echo $message; ?>
                </p>
            </div>
        <?php } ?>
    </div>
    <?php
}

function renderSidebar()
{
    global $post;
    /**
     * @psalm-suppress MixedPropertyFetch
     * @psalm-suppress MixedArgument
     */
    $meta = getMeta($post->ID);
    ?>
    <div id="minor-publishing">
        <ul>
            <li>ID: <strong><?php echo (string) $post->ID; ?></strong></li>
            <li>Form: <strong><?php echo sprintf('<a href="%s" target="_blank">%s (%s)</a>', $meta['builderUrl'], $meta['formName'], $meta['formId']); ?></strong></li>
            <?php if ($meta['editUrl']) { ?>
                <li><?php echo $meta['postTypeLabel'];?>: <strong><?php echo sprintf('<a href="%s" target="_blank">%s</a>', $meta['editUrl'], $meta['postTitle']); ?></strong></li>
            <?php } ?>
            <?php if ($meta['user']) { ?>
                <li>User: <strong><?php echo esc_html($meta['user']->nickname); ?></strong></li>
            <?php } ?>
            <li>User IP: <strong><?php echo $meta['ip']; ?></strong></li>
            <li>Referer: <strong><?php echo esc_html($meta['referer']); ?></strong></li>
            <li>User Agent: <strong><?php echo esc_html($meta['userAgent']); ?></strong></li>
            <li>Submitted on: <strong><?php echo $meta['date']; ?></strong></li>
            <li>Updated on: <strong><?php echo $meta['modified']; ?></strong></li>
        </ul>
    </div>

    <div id="major-publishing-actions" class="breakdance-sidebar-actions">
        <div id="delete-action">
            <a class="submitdelete deletion" href="<?php echo get_delete_post_link( (int) $post->ID ); ?>">Move to Trash</a>
        </div>

        <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="Update">
    </div>
    <?php
}

/**
 * @global \WP_Post | null $post
 *
 * @return void
 */
function addMetaBoxes()
{
    global $post;

    /** @var \WP_Screen $currentScreen */
    $currentScreen = get_current_screen();
    if ($currentScreen->id !== 'breakdance_form_res'){
        return;
    }

    remove_meta_box( 'submitdiv', 'breakdance_form_res', 'side' );

    add_meta_box(
        'breakdance_form_res_metabox',
        'Submission',
        'Breakdance\Forms\Submission\renderMetaBox',
        'breakdance_form_res'
    );

    add_meta_box(
        'breakdance_form_res_actions',
        'Actions',
        'Breakdance\Forms\Submission\renderActionsMetaBox',
        'breakdance_form_res'
    );

    add_meta_box(
        'breakdance_form_res_sidebar',
        'Info',
        'Breakdance\Forms\Submission\renderSidebar',
        'breakdance_form_res',
            'side'
    );

    if ($post !== null) {

        /** @var FormFile[]|string|false $uploads */
        $uploads = get_post_meta($post->ID, '_breakdance_uploads', true);
        if ($uploads && is_array($uploads)) {
            add_meta_box(
                    'breakdance_form_res_uploads',
                    'Files',
                    'Breakdance\Forms\Submission\renderUploadsMetaBox',
                    'breakdance_form_res',
                    'advanced',
                    'default',
                    compact('post','uploads')
            );
        }

        $attachments = get_posts([
                'numberposts' => - 1,
                'post_type' => 'attachment',
                'post_parent' => $post->ID,
        ]);

        if (!empty($attachments)) {
            add_meta_box(
                    'breakdance_form_res_attachments',
                    'Files',
                    'Breakdance\Forms\Submission\renderAttachmentsMetaBox',
                    'breakdance_form_res',
                    'advanced',
                    'default',
                    compact('post', 'attachments')
            );
        }
    }

}
add_action('add_meta_boxes', '\Breakdance\Forms\Submission\addMetaBoxes');

/**
 * Remove post status from the post title
 * @param array $states
 * @return array
 */
function removePostState($states)
{
    if (get_post_type() === 'breakdance_form_res') {
        return [];
    }
    return $states;
}
add_filter('display_post_states', '\Breakdance\Forms\Submission\removePostState');

/**
 * @param array<string, string> $actions
 * @return array<string, string>
 */
function removeQuickEdit($actions)
{
    if (get_post_type() === 'breakdance_form_res') {
        unset($actions['view']);
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions', '\Breakdance\Forms\Submission\removeQuickEdit', 10, 1);

/**
 * Does something when the submission is saved/updated
 * @param int $postId
 * @param \WP_Post $post
 * @param boolean $update
 */
function onSubmissionSaved($postId, $post, $update)
{
    // initial submissions are handled in
    // handleSubmission in custom.php
    if (!$update) {
        return;
    }
    /** @var array<string, string>|null|false $fields */
    $fields = filter_input(INPUT_POST, 'fields', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (!$fields) {
        return;
    }

    update_post_meta($postId, '_breakdance_fields', $fields);
}
add_action('save_post_breakdance_form_res', '\Breakdance\Forms\Submission\onSubmissionSaved', 10, 3);

/**
 * Add 'Unread' and 'Read' post statuses.
 * @return void
 */
function registerStatuses()
{
    register_post_status('unread', [
        'label'                     => 'Unread',
        'post_type'                 => ['breakdance_form_res'],
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>'),
    ]);

    register_post_status( 'read', [
        'label'                     => 'Read',
        'post_type'                 => ['breakdance_form_res'],
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Read <span class="count">(%s)</span>', 'Read <span class="count">(%s)</span>'),
    ]);
}
add_action('init', '\Breakdance\Forms\Submission\registerStatuses');

/**
 * @param \WP_Query $query
 * @return void
 */
function parseFormFilter($query)
{
    $formId = getFormIdFromRequest();
    if (!$formId) {
        return;
    }

    $postType = (string) filter_input(INPUT_GET, 'post_type', FILTER_UNSAFE_RAW);
    if ($postType !== 'breakdance_form_res' || !is_admin()) {
        return;
    }

    if (!array_key_exists('meta_query', $query->query_vars) || !is_array($query->query_vars['meta_query'])) {
        $query->query_vars['meta_query'] = [];
    }

    $query->query_vars['meta_query'][] = [
        ['key' => '_breakdance_post_id', 'value' => $formId['postId']],
        ['key' => '_breakdance_form_id', 'value' => $formId['formId']],
    ];
}
add_filter('parse_query', '\Breakdance\Forms\Submission\parseFormFilter');

/**
 * @return array{postId: int, formId: int}|false
 */
function getFormIdFromRequest() {
    $postIdAndFormIdSeparatedByUnderscore = (string) filter_input(INPUT_GET, 'form_id', FILTER_UNSAFE_RAW);
    if (!$postIdAndFormIdSeparatedByUnderscore || strpos($postIdAndFormIdSeparatedByUnderscore, '_') === false) {
        return false;
    }

    $formIdAndPostIdAsArray = explode('_', $postIdAndFormIdSeparatedByUnderscore, 2);
    if (!isset($formIdAndPostIdAsArray[0], $formIdAndPostIdAsArray[1]) || empty($formIdAndPostIdAsArray[0]) || empty($formIdAndPostIdAsArray[1])) {
        return false;
    }

    return [
        'postId' => (int) $formIdAndPostIdAsArray[0],
        'formId' => (int) $formIdAndPostIdAsArray[1]
    ];
}

/**
 * Add extra dropdowns to the List Tables
 * @param string $postType The Post Type that is being displayed
 */
function addFormFilter($postType)
{
    if ($postType !== 'breakdance_form_res') {
        return;
    }

    $selected = (string) filter_input(INPUT_GET, 'form_id', FILTER_UNSAFE_RAW);
    $results = getUniqueFormsFromSubmissions();

    if (empty($results)) return;

    $forms = [];
    foreach ($results as $result) {
        $formKey = $result['postId'] . '_' . $result['formId'];
        if (isset($forms[$formKey]) && !empty($forms[$formKey])) {
            // if form ID is present with a form name value there's no need to include it again
            continue;
        }

        $settings = getFormSettings((int) $result['postId'], (int) $result['formId']);
        if (!$settings) {
            // Form has likely been deleted, we can attempt
            // to get the form name from the first submission
            $name = getFormNameFromLatestSubmissionSettings($result['postId'], $result['formId']);
        } else {
            $name = $settings['form']['form_name'];
        }

        $forms[$formKey] = $name;
    }

    $options = array_map(static function ($formName, $formId) use ($selected) {
        $formIdWith = explode('_', $formId, 2)[0];
        if ($formId === $selected) {
            return sprintf('<option value="%1$s" selected>%2$s (%3$s)</option>', $formId, $formName, $formIdWith);
        } else {
            return sprintf('<option value="%1$s">%2$s (%3$s)</option>', $formId, $formName, $formIdWith);
        }
    }, $forms, array_keys($forms));

    ?>
    <select id="form_id" name="form_id">
        <option value="">All Forms</option>
        <?php echo implode("\n", $options); ?>
    </select>
    <?php
}

/**
 * @param int $postId
 * @param int $formId
 * @return string
 */
function getFormNameFromLatestSubmissionSettings($postId, $formId)
{
    $latestSubmissions = wp_get_recent_posts([
            'post_type' => 'breakdance_form_res',
            'post_status' => 'any',
            'posts_per_page' => 1,
            'meta_query' => [
                    ['key' => '_breakdance_form_id', 'value' => $formId],
                    ['key' => '_breakdance_post_id', 'value' => $postId],
            ]
    ], 'OBJECT');
    if ($latestSubmissions && isset($latestSubmissions[0])) {
        /** @var \WP_Post $latestSubmission */
        $latestSubmission = $latestSubmissions[0];
        if ($latestSubmission->ID) {
            /** @var FormSettings|null $settings */
            $settings = get_post_meta($latestSubmission->ID, '_breakdance_settings', true) ?: null;
            return $settings ? $settings['form']['form_name'] : 'Form unknown';
        }
    }
    return 'Form unknown';
}

add_action('restrict_manage_posts', '\Breakdance\Forms\Submission\addFormFilter');

/**
 * @return array{formId: int, postId: int}[]
 */
function getUniqueFormsFromSubmissions() {
    global $wpdb;

    /**
     * @psalm-suppress MixedPropertyFetch
     */
    $query = "
        SELECT DISTINCT
            p1.meta_value as formId,
            p2.meta_value as postId
        FROM $wpdb->postmeta p1
        JOIN $wpdb->postmeta p2
            ON p1.post_id = p2.post_id
           AND p1.meta_key = '_breakdance_form_id'
           AND p2.meta_key = '_breakdance_post_id'";

    $statusFilter = (string) filter_input(INPUT_GET, 'post_status', FILTER_UNSAFE_RAW);
    if ($statusFilter && $statusFilter !== 'all') {
        /**
         * @psalm-suppress MixedPropertyFetch
         */
        $query .= "JOIN $wpdb->posts posts
            ON posts.ID = p1.post_id
        WHERE posts.post_status = '" . $statusFilter . "'";
    }

    $authorFilter = (int) filter_input(INPUT_GET, 'author', FILTER_VALIDATE_INT);
    if ($authorFilter) {
        /**
         * @psalm-suppress MixedPropertyFetch
         */
        $query .= "JOIN $wpdb->posts posts
            ON posts.ID = p1.post_id
        WHERE posts.post_author = " . $authorFilter;
    }

    /**
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedMethodCall
     * @var array{formId: int, postId: int}[] */
    return $wpdb->get_results($query, ARRAY_A);
}

/**
 * Set submission status to read
 * @param int $id
 */
function markAsRead($id)
{
    $status = get_post_status($id);

    if ($status === 'read') {
        return;
    }

    wp_update_post([
        'ID' => $id,
        'post_status' => 'read'
    ]);
}

/**
 * Set submission status to unread
 * @param int $id
 */
function markAsUnread($id)
{
    wp_update_post([
        'ID' => $id,
        'post_status' => 'unread'
    ]);
}

/**
 * @param int $id
 * @param array<string, ActionSuccess|ActionError> $response
 */
function saveActionsLog($id, $response)
{
    add_post_meta((int) $id, '_breakdance_form_actions', $response, true);
}

/**
 * @param \WP_Post $post
 * @param array{id: string, title: string, callback: Callable, args: array{attachments: \WP_Post[]}} $metaBox
 * @return void
 */
function renderAttachmentsMetaBox($post, $metaBox)
{
    $formPostId = (int)get_post_meta($post->ID, '_breakdance_post_id', true);
    $formId = (int)get_post_meta($post->ID, '_breakdance_form_id', true);

    if (!array_key_exists('attachments', $metaBox['args'])) {
        ?>
            <p class="breakdance-actions-empty">No files submitted</p>
        <?php
    } else {
        $attachments = $metaBox['args']['attachments'];
    ?>
        <table class="form-table">
            <tbody>
            <?php
            foreach ($attachments as $attachment) {
                /** @var FormFile $file */
                $file = get_post_meta($attachment->ID, '_breakdance_file', true);
                ?>
                <tr>
                    <td>
                        <span class="breakdance-attachment-item__icon dashicons dashicons-paperclip"></span>
                        <span class="breakdance-attachment-item__attachment"><a target="_blank" href="<?php echo (string) getSecureFileUrl($formPostId, $formId, $file['fieldId'], (string) wp_get_attachment_url($attachment->ID)); ?>"><?php echo (string) $attachment->post_title; ?></a></span>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php

    }
}

/**
 * @param \WP_Post $post
 * @param array{id: string, title: string, callback: Callable, args: array{uploads: FormFile[]}} $metaBox
 * @return void
 */
function renderUploadsMetaBox($post, $metaBox)
{
    $formPostId = (int)get_post_meta($post->ID, '_breakdance_post_id', true);
    $formId = (int)get_post_meta($post->ID, '_breakdance_form_id', true);

    if (!array_key_exists('uploads', $metaBox['args'])) {
        ?>
        <p class="breakdance-actions-empty">No files submitted</p>
        <?php
    } else {
        $uploads = $metaBox['args']['uploads'];
        ?>
        <table class="form-table">
            <tbody>
            <?php
            foreach ($uploads as $file) {
                ?>
                <tr>
                    <td>
                        <span class="breakdance-attachment-item__icon dashicons dashicons-paperclip"></span>
                        <span class="breakdance-attachment-item__attachment"><a target="_blank" href="<?php echo (string) getSecureFileUrl($formPostId, $formId, $file['fieldId'], $file['url']); ?>"><?php echo (string) basename($file['file']); ?></a></span>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php

    }
}

/**
 * @global \WP $wp
 * @global \WP_Post_Type $post_type_object
 *
 * @param string $where
 * @return void
 */
function addCsvExportButton($where)
{
    global $post_type_object;
    global $wp;
    if ($post_type_object->name === 'breakdance_form_res') {
        $wp_nonce = wp_create_nonce('breakdance_export_submissions_to_csv');
        /** @var array $urlArgs */
        $urlArgs = apply_filters('breakdance_add_csv_export_url_args', ['breakdance_action' => 'export_submissions_to_csv', '_wpnonce' => $wp_nonce]);
        $exportUrlArgs = http_build_query(
            array_merge(
                $_GET,
                $urlArgs
            )
        );

        $exportUrl = add_query_arg($exportUrlArgs, '', home_url($wp->request));

        if (\Breakdance\Forms\Submission\hasActiveFilters()) {
            echo '<div class="alignleft actions"><a class="button" href="' . $exportUrl . '">' . appendProToLabelInFreeMode("Export filtered to CSV") . '</a></div>';
        } else {
            echo '<div class="alignleft actions"><a class="button" href="' . $exportUrl . '">' . appendProToLabelInFreeMode("Export all to CSV") . '</a></div>';
        }
    }
}
add_action('manage_posts_extra_tablenav', 'Breakdance\Forms\Submission\addCsvExportButton');

/**
 * @param int $postId
 * @return void
 */
function cleanupUploadedFiles($postId) {
    /** @var \WP_Post|null $post */
    $post = get_post($postId);
    if (!$post) {
        return;
    }
    if ($post->post_type !== 'breakdance_form_res') {
        return;
    }

    /** @var \WP_Post[] $attachments */
    $attachments = get_posts([
            'numberposts' => - 1,
            'post_type' => 'attachment',
            'post_parent' => $post->ID,
    ]);

    foreach($attachments as $attachment) {
        wp_delete_attachment($attachment->ID);
    }

    /** @var FormFile[] $uploads */
    $uploads = get_post_meta($post->ID, '_breakdance_uploads', true) ?? [];
    foreach ($uploads as $file) {
        wp_delete_file($file['file']);
    }
}
add_action('before_delete_post', 'Breakdance\Forms\Submission\cleanupUploadedFiles');
