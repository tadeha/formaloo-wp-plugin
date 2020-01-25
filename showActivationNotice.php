<?php


/**
 * Runs only when the plugin is activated.
 * @since 0.1.0
 */
function formaloo_admin_notice_activation_hook() {

    /* Create transient data */
    set_transient( 'formaloo-admin-notice-activation', true, 5 );
}

/* Add admin notice */
add_action( 'admin_notices', 'formaloo_admin_notice_activation_notice' );

/**
 * Admin Notice on Activation.
 * @since 0.1.0
 */
function formaloo_admin_notice_activation_notice(){

    /* Check transient, if available display notice */
    if( get_transient( 'formaloo-admin-notice-activation' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using this plugin! <a href="<?php echo admin_url( "admin.php?page=formaloo" ) ?>"><strong>Please Activate Formaloo plugin</strong></a>.</p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'formaloo-admin-notice-activation' );
    }
}