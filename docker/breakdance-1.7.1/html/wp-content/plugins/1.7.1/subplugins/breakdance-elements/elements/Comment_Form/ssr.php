<?php

$req      = get_option( 'require_name_email' );
$commenter = wp_get_current_commenter();
$consent = (isset( $_POST['wp-comment-cookies-consent']));
$aria_req = ( $req ? " aria-required='true'" : '' );

$comment_form_args = array(
    'title_reply_before' => '<h5 id="reply-title" class="comment-reply-title">',
    'title_reply_after' => '</h5>',
    'class_form' => 'breakdance-form breakdance-form--comments',
    'class_submit' => 'comment-form__submit button-atom button-atom--primary breakdance-form-button',
    'comment_field' => '<div class="breakdance-form-field breakdance-form-field--textarea"><label class="breakdance-form-field__label" for="comment">' . _x('Comment', 'noun') . '</label><textarea class="breakdance-form-field__input" id="comment" name="comment" aria-required="true"></textarea></div>',
    'submit_field' => '<div class="breakdance-form-field breakdance-form-footer">%1$s %2$s</div>',
    'fields' => apply_filters('comment_form_default_fields', array(


        'author' =>
        '<div class="breakdance-form-field">' .
        '<label class="breakdance-form-field__label" for="author">' . __('Name') .
        ($req ? '<span class="breakdance-form-field__required">*</span>' : '') .
        '</label> ' .
        '<input id="author" class="breakdance-form-field__input" name="author" type="text" value="' . esc_attr($commenter['comment_author']) .
        '" size="30"' . $aria_req . ' /></div>',

        'email' =>
        '<div class="breakdance-form-field"><label class="breakdance-form-field__label"class="breakdance-form-field__label" for="email">' . __('Email') .
        ($req ? '<span class="breakdance-form-field__required">*</span>' : '') .
        '</label> ' .
        '<input id="email" class="breakdance-form-field__input" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) .
        '" size="30"' . $aria_req . ' /></div>',

        'url' =>
        '<div class="breakdance-form-field"><label class="breakdance-form-field__label" for="url">' .
        __('Website') . '</label>' .
        '<input id="url" class="breakdance-form-field__input" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) .
        '" size="30" /></div>',

        'cookies' => '<div class="breakdance-form-field"><div class="breakdance-form-checkbox"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' /><label class="breakdance-form-checkbox__text" for="wp-comment-cookies-consent">'. __( 'Save my name, email, and website in this browser for the next time I comment.' ) .'</label></div></div>',

    ),

    ),
);

$postId = get_the_ID();

if (comments_open($postId)) {
    if (get_post_status($postId) === 'draft' && \Breakdance\isRequestFromBuilderSsr()){
        echo '<div class="breakdance-form-message breakdance-form-message--error comments-form__closed"><p>' . __('Draft posts can\'t have comments', 'breakdance') . '</p></div>';
    }

    comment_form($comment_form_args);
} else {
    echo '<div class="breakdance-form-message breakdance-form-message--error comments-form__closed"><p>' . __('Comments are closed') . '</p></div>';
}
