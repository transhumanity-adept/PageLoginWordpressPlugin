<?php

/**
 * Plugin Name: authorization
 * Author:      bondarenko
 * Version:     1.0
 * Requires PHP: 8.0
 */

add_filter( 'template_include', 'my_template' );
function my_template( $template ) {
    if( is_page('lmslogin') ){
        $template = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/authorization' . '/lmslogin_template.php';
    }
    return $template;
};
function add_custom_roles(): void
{
    add_role(
        'user',
        'Пользователь',
        [
            'read' => true
        ]
    );
}
function remove_custom_roles(): void
{
    remove_role('user');
}
register_activation_hook(__FILE__, function() {
    if (!post_exists(title: 'lmslogin', type: 'page', status: 'publish')) {
        wp_insert_post(array(
            'post_type'	=> 'page',
            'post_title' => 'lmslogin',
            'post_status' => 'publish'
        ));
    }
    add_custom_roles();
});

register_deactivation_hook(__FILE__, function () {
    if (!empty($GLOBALS['$page_id'])) wp_delete_post($GLOBALS['$page_id']);
    remove_custom_roles();
});