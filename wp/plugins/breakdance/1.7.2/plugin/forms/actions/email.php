<?php

namespace Breakdance\Forms\Actions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

class Email extends Action {

    /**
     * Get the displayable label of the action.
     * @return string
     */
    public static function name()
    {
        return 'Email';
    }

    /**
     * Get the URL friendly slug of the action.
     * @return string
     */
    public static function slug()
    {
        return 'email';
    }

    /**
     * Get controls for the builder
     * @return array
     */
    public function controls()
    {
        return [
            repeaterControl('emails', 'Emails',
                [
                    control('subject', 'Subject', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                                'condition' => [
                                    'path' => 'type',
                                    'operand' => 'is none of',
                                    'value' => ['file', 'html']
                                ]
                            ]
                        ]
                    ]),
                    control('to', 'To Email', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                                'condition' => [
                                    'path' => 'type',
                                    'operand' => 'is one of',
                                    'value' => ['email']
                                ]
                            ]
                        ]
                    ]),
                    control('from', 'From Email', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                                'condition' => [
                                    'path' => 'type',
                                    'operand' => 'is one of',
                                    'value' => ['email']
                                ]
                            ]
                        ]
                    ]),
                    control('from_name', 'From Name', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                                'condition' => [
                                    'path' => 'type',
                                    'operand' => 'is none of',
                                    'value' => ['file', 'html']
                                ]
                            ]
                        ]
                    ]),
                    control('reply_to', 'Reply To', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                                'condition' => [
                                    'path' => 'type',
                                    'operand' => 'is one of',
                                    'value' => ['email']
                                ]
                            ],
                        ]
                    ]),
                    control('message', 'Message', [
                        'type' => 'richtext',
                        'layout' => 'vertical',
                        'variableOptions' => [
                            'enabled' => true,
                            'populate' => [
                                'path' => 'content.form.fields',
                                'text' => 'label',
                                'value' => 'advanced.id',
                                'condition' => [
                                    'path' => 'type',
                                    'operand' => 'is none of',
                                    'value' => ['file', 'html']
                                ]
                            ]
                        ],
                        'variableItems' => [
                            ['text' => 'All Fields', 'value' => 'all_fields'],
                        ]
                    ]),
                    control('attach_files', 'Attach uploaded files', [
                        'type' => 'toggle',
                        'layout' => 'inline',
                    ]),
                ],
                [
                    'repeaterOptions' => [
                        'titleTemplate' => '{subject}',
                        'defaultTitle' => 'Email',
                        'buttonName' => 'Add email',
                        'defaultNewValue' => [
                            'subject' => 'New contact form message',
                            'message' => '{all_fields}'
                        ]
                    ]
                ]
            )
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
        add_action( 'wp_mail_failed',
            /**
             * @param \WP_Error $wp_error
             */
            function($wp_error) {
            $this->addContext('Errors', [
                'message' => $wp_error->get_error_message(),
                'data' => $wp_error->get_error_data()
            ]);
        });
        $emailsSent = array_map(
            function ($email) use ($extra, $form) {
                return $this->submit($email, $form, $extra['files']);
            },
            $settings['actions']['email']['emails']
        );

        $anyEmailFailed = in_array(false, $emailsSent, true);

        if ($anyEmailFailed) {
            // It's "unknown" because wp_mail doesn't return any errors, only a boolean
            return [
                'type' => 'error',
                'message' => 'An unknown error occurred while sending the email message.'
            ];
        }

        return ['type' => 'success'];
    }

    /**
     * Sends an email
     * @param FormEmail $email
     * @param FormData $form
     * @param FormFile[] $files
     * @return bool
     */
    public function submit($email, $form, $files = [])
    {
        $defaultSubject = 'New contact form message';
        $defaultMessage = $this->getDefaultMessage();

        $to = $this->getEmails($this->renderData($form, $email['to']));

        if (!$to) {
            return false; // TODO: Expose error to the user
        }

        $fromEmail = $this->renderData($form, $email['from']);
        $fromName  = $this->renderData($form, $email['from_name']);
        $replyTo   = $this->renderData($form, $email['reply_to']);
        $subject   = $this->renderData($form, $email['subject'] ?: $defaultSubject);
        $message   = $this->renderData($form, $email['message'] ?: $defaultMessage, true);

        $isHTMLEnabled = true; // TODO: Emails could either plain text or HTML.

        $headers = ["From: {$fromName} <{$fromEmail}>"];

        if ($replyTo) {
            $headers[] = "Reply-To: $replyTo";
        }

        /** @psalm-suppress RedundantCondition */
        if ($isHTMLEnabled) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $message = nl2br($message);
        }

        $attachUploadedFiles = $email['attach_files'] ?? false;
        $attachments = [];
        if ($attachUploadedFiles) {
            $attachments = $this->getAttachments($files);
        }

        /** @var string[] $attachments */
        $attachments = apply_filters('breakdance_email_attachments', $attachments);
        /** @var string $message */
        $message = apply_filters('breakdance_email_message', $message);
        /** @var string[] $headers */
        $headers = apply_filters('breakdance_email_headers', $headers);

        $headersAsKeyValueList = [];
        foreach ($headers as $header) {
            [$headerKey, $headerValue] = explode(':', $header);
            $headersAsKeyValueList[$headerKey] = $headerValue;
        }

        $this->addContext('Email Headers', $headersAsKeyValueList);
        $this->addContext('Email Body', [
            'from' => "$fromName <$fromEmail>",
            'to' => implode(', ', $to),
            'subject' => $subject,
            'message' => $message,
            'attachments' => $attachments,
        ]);
        $sent = wp_mail($to, $subject, $message, $headers, $attachments);

        return !!$sent;
    }

    /**
     * @param FormFile[] $files
     * @return array
     */
    function getAttachments($files)
    {
        // Flatten files array
        /** @var FormFile[] $attachments */
        $attachments = array_merge([], ...array_values($files));

        return array_map(
          /**
           * @param FormFile $file
           * @return string
           */
          function ($file) {
            return $file['file'];
          },
          $attachments
        );
    }

    /**
     * This message is used as fallback if the message field is empty
     * @return string
     */
    public function getDefaultMessage()
    {
        return '{all_fields}';
    }

    /**
     * Turn comma-separated string of email addresses into an array
     * Removes invalid emails from the list
     * @param string|null $value
     * @return string[]
     */
    public function getEmails($value)
    {
        if (!$value) {
            return [];
        }

        $emails = explode(',', $value);
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails, 'is_email');
        return array_values($emails);
    }

}
