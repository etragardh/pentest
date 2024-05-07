<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;

class StoreSubmission extends Action {

    /**
     * Get the displayable label of the action.
     * @return string
     */
    public static function name()
    {
        return 'Store Submission';
    }

    /**
     * Get the URL friendly slug of the action.
     * @return string
     */
    public static function slug()
    {
        return 'store_submission';
    }

    /**
     * Get controls for the builder
     * @return array
     */
    public function controls()
    {
        return [
            control('submission_title', 'Submission Title', [
                'type' => 'text',
                'layout' => 'vertical',
                'variableOptions' => [
                    'enabled' => true,
                    'populate' => [
                        'path' => 'content.form.fields',
                        'text' => 'label',
                        'value' => 'advanced.id'
                    ]
                ]
            ]),
            control('store_files', 'Store uploaded files', [
                'type' => 'toggle',
                'layout' => 'inline',
            ]),
            control('store_files_as_attachment', 'Add uploaded files to WordPress media library', [
                'type' => 'toggle',
                'layout' => 'inline',
                'condition' => [
                    'path' => 'content.actions.store_submission.store_files',
                    'operand' => 'is set',
                    'value' => ''
                ]
            ]),
            control('restrict_file_access', 'Restrict uploaded file access to admin users', [
                'type' => 'toggle',
                'layout' => 'inline',
                'condition' => [
                    'path' => 'content.actions.store_submission.restrict_file_access',
                    'operand' => 'is set',
                    'value' => ''
                ]
            ]),
        ];
    }

    /**
     * Does something on form submission
     * @param FormData $form
     * @param FormSettings $settings
     * @param FormExtra $extra
     * @return ActionSuccess|ActionError|array<array-key, ActionSuccess|ActionError>
     */
    public function run($form, $settings, $extra)
    {
        $id = wp_insert_post([
            'post_title'  => $this->renderData($form, $settings['actions']['store_submission']['submission_title'] ?? 'Submission'),
            'post_type'   => 'breakdance_form_res',
            'post_status' => 'unread'
        ]);

        if (is_wp_error($id)) {
            return [
                'type' => 'error',
                'message' => $id
            ];
        }

        $fieldsToAdd = [
            '_breakdance_fields'   => 'fields',
            '_breakdance_form_id' => 'formId',
            '_breakdance_post_id' => 'postId',
            '_breakdance_ip'      => 'ip',
            '_breakdance_referer' => 'referer',
            '_breakdance_user_agent' => 'userAgent',
            '_breakdance_user_id' => 'userId',
        ];

        $storeUploadedFiles = $settings['actions']['store_submission']['store_files'] ?? false;
        /** @var FormFile[] $files */
        $files = array_merge([], ...array_values($extra['files']));
        if ($storeUploadedFiles) {
            $storeFilesAsAttachment = $settings['actions']['store_submission']['store_files_as_attachment'] ?? false;
            // Flatten files array
            if ($storeFilesAsAttachment) {
                foreach ($files as $file) {
                    $attachmentId = wp_insert_attachment([
                        'post_mime_type' => $file['type'],
                        'post_title' => sanitize_file_name(basename($file['url'])),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    ], $file['file'], (int)$id);
                    add_post_meta((int) $attachmentId, '_breakdance_file', $file, true);
                }
            } else {
                $extra['uploads'] = $files;
                $fieldsToAdd['_breakdance_uploads'] = 'uploads';
            }
        } else {
            foreach ($files as $file) {
                wp_delete_file($file['file']);
            }
        }


        foreach ($fieldsToAdd as $key => $value) {
            add_post_meta((int) $id, $key, $extra[$value], true);
        }

        add_post_meta((int) $id, '_breakdance_settings', $settings);

        return [
            'type' => 'success',
            'id' => $id
        ];
    }

    /**
     * @return bool
     */
    static public function proOnly(){
        return false;
    }
}
