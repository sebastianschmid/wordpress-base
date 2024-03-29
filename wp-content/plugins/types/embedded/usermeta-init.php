<?php

// Add usermeta and post fileds groups to access.
$usermeta_access = new Usermeta_Access;
$fields_access = new Post_Fields_Access;
//setlocale(LC_ALL, 'nl_NL');

/**
* Add User Fileds menus, need add to wpcf_admin_menu_hook
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/
function wpcf_admin_menu_edit_user_fields_hook() {
    do_action( 'wpcf_admin_page_init' );

    // Group filter
    wp_enqueue_script( 'wpcf-filter-js',
            WPCF_EMBEDDED_RES_RELPATH
            . '/js/custom-fields-form-filter.js', array('jquery'), WPCF_VERSION );
    // Form
    wp_enqueue_script( 'wpcf-form-validation',
            WPCF_EMBEDDED_RES_RELPATH . '/js/'
            . 'jquery-form-validation/jquery.validate.min.js', array('jquery'),
            WPCF_VERSION );
    wp_enqueue_script( 'wpcf-form-validation-additional',
            WPCF_EMBEDDED_RES_RELPATH . '/js/'
            . 'jquery-form-validation/additional-methods.min.js',
            array('jquery'), WPCF_VERSION );
    // Scroll
    wp_enqueue_script( 'wpcf-scrollbar',
            WPCF_EMBEDDED_RELPATH . '/common/visual-editor/res/js/scrollbar.js',
            array('jquery') );
    wp_enqueue_script( 'wpcf-mousewheel',
            WPCF_EMBEDDED_RELPATH . '/common/visual-editor/res/js/mousewheel.js',
            array('wpcf-scrollbar') );
	//Css editor
	wp_enqueue_script( 'wpcf-form-codemirror' , 
		WPCF_RELPATH . '/resources/js/codemirror234/lib/codemirror.js', array('wpcf-js'));	
	wp_enqueue_script( 'wpcf-form-codemirror-css-editor' , 
		WPCF_RELPATH . '/resources/js/codemirror234/mode/css/css.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-html-editor' , 
		WPCF_RELPATH . '/resources/js/codemirror234/mode/xml/xml.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-html-editor2' , 
		WPCF_RELPATH . '/resources/js/codemirror234/mode/htmlmixed/htmlmixed.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-editor-resize' , 
		WPCF_RELPATH . '/resources/js/jquery_ui/jquery.ui.resizable.min.js', array('wpcf-js'));	
	
		
				
	wp_enqueue_style( 'wpcf-css-editor',
            WPCF_RELPATH . '/resources/js/codemirror234/lib/codemirror.css' );
	wp_enqueue_style( 'wpcf-css-editor-resize',
            WPCF_RELPATH . '/resources/js/jquery_ui/jquery.ui.theme.min.css' );
	wp_enqueue_style( 'wpcf-usermeta',
                WPCF_EMBEDDED_RES_RELPATH . '/css/usermeta.css' );
		
							
    /*
     * Enqueue styles
     */
    wp_enqueue_style( 'wpcf-scroll',
            WPCF_EMBEDDED_RELPATH . '/common/visual-editor/res/css/scroll.css' );

    add_action( 'admin_footer', 'wpcf_admin_fields_form_js_validation' );
	require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/usermeta.php';
    require_once WPCF_INC_ABSPATH . '/fields-form.php';
	require_once WPCF_INC_ABSPATH . '/usermeta-form.php';
    $form = wpcf_admin_usermeta_form();
    wpcf_form( 'wpcf_form_fields', $form );

}



/**
* Add/Edit usermeta fields group
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/
function wpcf_admin_menu_edit_user_fields() {
    if ( isset( $_GET['group_id'] ) ) {
        $title = __( 'Edit Usermeta Group', 'wpcf' );
    } else {
        $title = __( 'Add New Usermeta Group', 'wpcf' );
    }
    echo wpcf_add_admin_header( $title );

   $form = wpcf_form( 'wpcf_form_fields' );
    echo '<br /><form method="post" action="" class="wpcf-fields-form '
    . 'wpcf-form-validate" onsubmit="';
    echo 'if (jQuery(\'#wpcf-group-name\').val() == \'' . __( 'Enter group title',
            'wpcf' ) . '\') { jQuery(\'#wpcf-group-name\').val(\'\'); }';
    echo 'if (jQuery(\'#wpcf-group-description\').val() == \'' . __( 'Enter a description for this group',
            'wpcf' ) . '\') { jQuery(\'#wpcf-group-description\').val(\'\'); }';
    echo 'jQuery(\'.wpcf-forms-set-legend\').each(function(){
        if (jQuery(this).val() == \'' . __( 'Enter field name',
            'wpcf' ) . '\') {
            jQuery(this).val(\'\');
        }
        if (jQuery(this).next().val() == \'' . __( 'Enter field slug',
            'wpcf' ) . '\') {
            jQuery(this).next().val(\'\');
        }
        if (jQuery(this).next().next().val() == \'' . __( 'Describe this field',
            'wpcf' ) . '\') {
            jQuery(this).next().next().val(\'\');
        }
	});';
    echo '">';
    echo $form->renderForm();
    echo '</form>';
    echo wpcf_add_admin_footer();
}

/**
* Add Usermeta Fields manager page.
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/
function wpcf_admin_menu_user_fields_control_hook() {
	do_action( 'wpcf_admin_page_init' );
	
    add_action( 'admin_head', 'wpcf_admin_user_fields_control_js' );
    add_thickbox();
    require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/fields-control.php';
	require_once WPCF_INC_ABSPATH . '/usermeta-control.php';

    if ( isset( $_REQUEST['_wpnonce'] )
            && wp_verify_nonce( $_REQUEST['_wpnonce'],
                    'user_fields_control_bulk' )
            && (isset( $_POST['action'] ) || isset( $_POST['action2'] )) && !empty( $_POST['fields'] ) ) {
        $action = $_POST['action'] == '-1' ? $_POST['action2'] : $_POST['action'];
        wpcf_admin_user_fields_control_bulk_actions( $action );
    }

    global $wpcf_control_table;
    $wpcf_control_table = new WPCF_User_Fields_Control_Table( array(
                'ajax' => true,
                'singular' => __( 'User Field', 'wpcf' ),
                'plural' => __( 'User Fields', 'wpcf' ),
                    ) );
    $wpcf_control_table->prepare_items();
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_user_fields_control() {
    global $wpcf_control_table;
    echo wpcf_add_admin_header( __( 'User Fields Control', 'wpcf' ) );
    echo '<br /><form method="post" action="" id="wpcf-custom-fields-control-form" class="wpcf-custom-fields-control-form '
    . 'wpcf-form-validate" enctype="multipart/form-data">';
    echo wpcf_admin_custom_fields_control_form( $wpcf_control_table );
    wp_nonce_field( 'user_fields_control_bulk' );
    echo '</form>';
    echo wpcf_add_admin_footer();
}

/**
* Usermeta groups listing
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/
function wpcf_usermeta_summary() {
    echo wpcf_add_admin_header( __( 'User Fields', 'wpcf' ) );
	require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/usermeta.php';
    require_once WPCF_INC_ABSPATH . '/usermeta-list.php';
    $to_display = wpcf_admin_fields_get_fields();
    if ( !empty( $to_display ) ) {
        add_action( 'wpcf_groups_list_table_after',
               'wpcf_admin_promotional_text' );
    }
    wpcf_admin_usermeta_list();
    echo wpcf_add_admin_footer();
}







//Add usermeta hook when user profile loaded 
add_action( 'show_user_profile', 'wpcf_admin_user_profile_load_hook' );
add_action( 'edit_user_profile', 'wpcf_admin_user_profile_load_hook' );
	
//Save usermeta hook
add_action( 'personal_options_update', 'wpcf_admin_user_profile_save_hook' );
add_action( 'edit_user_profile_update', 'wpcf_admin_user_profile_save_hook' );



/**
	 * Add usermeta groups to post editor
*/
add_filter( 'editor_addon_menus_types',
                'wpcf_admin_post_add_usermeta_to_editor_js' );


add_action( 'init', '__wpcf_usermeta_test', 999999999999999999999 );
add_filter( 'wpcf_post_groups', '__wpcf_usermeta_test_groups' );

function __wpcf_usermeta_test() {
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
	$field['id'] = md5('date'.mktime());
    $here = array( basename($_SERVER['REQUEST_URI']), basename($_SERVER['SCRIPT_FILENAME']) );
	global $post;
	// Get post_type
    if ( $post ) {
        $post_type = get_post_type( $post );
    }
	else if ( !empty($_GET['post'])){
		 $post_type = get_post_type( $_GET['post'] );	
	}
	if( ( $here[0] == ('index.php' || 'wp-admin')) && ( $here[1] != 'index.php') ) {
		if ( isset($post_type) && in_array( $post_type, array('view', 'view-template') ) ) {
			return;	
		}
		wpcf_admin_post_add_to_editor( $field );
	}
}

function __wpcf_usermeta_test_groups( $groups ) {
    if ( !empty( $groups ) ) {
        return $groups;
    }
    $groups = wpcf_admin_fields_get_groups('wp-types-user-group');
	$check = false;
	if ( !empty( $groups ) ) {
		
		foreach ( $groups as $group_id => $group ) {

            // Mark meta box as hidden
            $groups[$group_id]['__show_meta_box'] = false;

            if ( empty( $group['is_active'] ) ) {
				continue;	
			}
			$fields = wpcf_admin_fields_get_fields_by_group( $group['id'],
                    'slug', true, false, true, 'wp-types-user-group', 'wpcf-usermeta' );
			if ( empty( $fields ) ) {
				continue;
			}
			$check = true;
		}
	}
	if ( !$check ){
		remove_action( 'admin_enqueue_scripts', 'wpcf_admin_post_add_to_editor_js' );
	}
	else{
		wpcf_enqueue_scripts();
	}
    return $groups;
}

if ( !isset( $_GET['post_type'] ) && isset( $_GET['post'] )) {
	$post_type = get_post_type( $_GET['post'] );	
}
else if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'],
                        get_post_types( array('show_ui' => true) ) ) ) {
    $post_type = $_GET['post_type'];
} 

		 
if ( isset( $post_type ) && in_array( $post_type, array('view', 'view-template') ) ) {
	    add_filter( 'editor_addon_menus_wpv-views',
                'wpcf_admin_post_add_usermeta_to_editor_js' );
        add_action( 'admin_footer', 'wpcf_admin_post_js_validation' );
        //wpcf_enqueue_scripts();
}

/**
* Get current logged user ID
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/	
function wpcf_usermeta_get_user($method = ''){
	if ( empty($method)){
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;	
	}
	
	return $user_id;
}


/**
* Add User Fields to editor
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/
function wpcf_admin_post_add_usermeta_to_editor_js($items){
	global $wpcf;

	$groups = wpcf_admin_fields_get_groups('wp-types-user-group');
	$user_id = wpcf_usermeta_get_user();
	$add = array();
	if ( !empty( $groups ) ) {
		$item_styles = array();
		foreach ( $groups as $group_id => $group ) {
			if ( empty( $group['is_active'] ) ) {
				continue;	
			}
			$group['name'] .= ' (User meta fields)';
			$fields = wpcf_admin_fields_get_fields_by_group( $group['id'],
                    'slug', true, false, true, 'wp-types-user-group', 'wpcf-usermeta' );
					
			if ( !empty( $fields ) ) {
				foreach ( $fields as $field_id => $field ) {
                    // Use field class
                    $wpcf->usermeta_field->set( $user_id, $field );
					
                    // Get field data
                    $data = (array) $wpcf->usermeta_field->config;
					
                    // Get inherited field
                    if ( isset( $data['inherited_field_type'] ) ) {
						$inherited_field_data = wpcf_fields_type_action( $data['inherited_field_type'] );
                    }

                    $callback = '';
                    if ( isset( $data['editor_callback'] ) ) {
						$data['editor_callback'] = str_replace(')',",'%s')",$data['editor_callback']);
                        $callback = sprintf( $data['editor_callback'],
                                $field['id'], 'usermeta' );
                    } else {
                        // Set callback if function exists
                        $function = 'wpcf_fields_' . $field['type'] . '_editor_callback';
                        $callback = function_exists( $function ) ? 'wpcfFieldsEditorCallback(\'' . $field['id'] . '\',\'usermeta\')' : '';
                    }
					
					if ( empty($callback) ){
						$callback = 'wpcfFieldsEditorCallback(\'' . $field['id'] . '\',\'usermeta\',\'nonpopup\')';	
					}
					
                    $add[$group['name']][stripslashes( $field['name'] )] = array(stripslashes( $field['name'] ), trim( wpcf_usermeta_get_shortcode( $field ),
                                '[]' ), $group['name'], $callback);

                    /*
                     * Since Types 1.2
                     * We use field class to enqueue JS and CSS
                     */
                    $wpcf->usermeta_field->enqueue_script();
                    $wpcf->usermeta_field->enqueue_style();
                }	
			}
			
		}
	}
	
	$items = $items + $add;
	return $items;
		
}

/**
 * Returns shortcode for specified usermeta field.
 * 
 * @param type $field
 * @param type $add Additional attributes
 */
function wpcf_usermeta_get_shortcode( $field, $add = '' ) {
    $shortcode = '[';
    $shortcode .= 'types usermeta="' . $field['slug'] . '"' . $add;
    if ( in_array( $field['type'], array('textfield', 'textarea', 'wysiwyg') ) ) {
        $shortcode .= ' class="" style=""';
    }
	
	// If repetitive add separator
    if ( wpcf_admin_is_repetitive( $field ) ) {
        $shortcode .= ' separator=", "';
    }
	
    $shortcode .= '][/types]';
    $shortcode = apply_filters( 'wpcf_fields_shortcode', $shortcode, $field );
    $shortcode = apply_filters( 'wpcf_fields_shortcode_type_' . $field['type'],
            $shortcode, $field );
    $shortcode = apply_filters( 'wpcf_fields_shortcode_slug_' . $field['slug'],
            $shortcode, $field );
    return $shortcode;
}


/**
 * Calls view function for specific usermeta field type.
 * 
 * @param type $field
 * @param type $atts (additional attributes: user_id, user_name, user_is_author, user_current)
 * @return type 
 */
function types_render_usermeta( $field_id, $params, $content = null, $code = '' ) {

    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
	
    global $wpcf, $post, $wpdb;
    // HTML var holds actual output
    $html = '';
	$current_user = wpcf_usermeta_get_user();
	
    // Set post ID
	// user_id, user_name, user_is_author, user_current
    $post_id = $post->ID;
    if ( isset( $params['post_id'] ) && !empty( $params['post_id'] ) ) {
        $post_id = $params['post_id'];
    }
	//print_r($params);exit;
	//Get user By ID
	if ( isset( $params['user_id'] ) ){
		$user_id = $params['user_id'];
	}
	else if ( isset( $params['user_name'] ) ){	//Get user by login
		 $user_id = $wpdb->get_var("SELECT * FROM " . $wpdb->prefix . "users WHERE user_login = '". $params['user_name'] ."'",0,0); 
	}
	else if ( isset( $params['user_is_author'] ) ){	//Get Post author
		$user_id = $post->post_author;
	}
	else if ( isset( $params['user_current'] ) ){//Get current logged user
		$user_id = wpcf_usermeta_get_user();	
	}
	else{	//If empty get post author, if no post, return empty
		if ( !empty( $post_id )){
			$user_id = $post->post_author;
		}
		else{
			return;
		}
	}
	
	if ( empty($user_id) ){
		return;	
	}
    // Get field
    $field = wpcf_fields_get_field_by_slug( $field_id, 'wpcf-usermeta' );
	
	//If Access plugin activated
	if (function_exists('wpcf_access_register_caps')){ 
		require_once WPCF_ABSPATH . '/includes/fields.php';
		$field_groups = wpcf_admin_fields_get_groups_by_field( $field_id, 'wp-types-user-group' );
		if (!empty($field_groups)) {
			foreach ($field_groups as $field_group) {
				if ($user_id == $current_user){
					if (!current_user_can('view_own_on_site_' . $field_group['slug'])){
						return;
					}
				}
				else{
					if (!current_user_can('view_others_on_site_' . $field_group['slug'])){
						return;
					}	
				}
			}
		}
	}
    // If field not found return empty string
    if ( empty( $field ) ) {

        // Log
        if ( !function_exists( 'wplogger' ) ) {
            require_once WPCF_EMBEDDED_ABSPATH . '/common/wplogger.php';
        }
        global $wplogger;
        $wplogger->log( 'types_render_field call for missing field \''
                . $field_id . '\'', WPLOG_DEBUG );

        return '';
    }
	
    // See if repetitive
    if ( wpcf_admin_is_repetitive( $field ) ) {
		
        $wpcf->usermeta_repeater->set( $user_id, $field );
        $_meta = $wpcf->usermeta_repeater->_get_meta();
        $meta = '';
		if ( isset($_meta['custom_order']) ){
			$meta = $_meta['custom_order'];
		}
		
        if ( (count( $meta ) == 1 ) ) {
            $meta_id = key( $meta );
            $_temp = array_shift( $meta );
            if ( strval( $_temp ) == '' ) {
                return '';
            } else {
                $params['field_value'] = $_temp;
                return types_render_field_single( $field, $params, $content,
                                $code, $meta_id );
            }
        } else if ( !empty( $meta ) ) {
            $output = '';

            if ( isset( $params['index'] ) ) {
                $index = $params['index'];
            } else {
                $index = '';
            }
			
            // Allow wpv-for-each shortcode to set the index
            $index = apply_filters( 'wpv-for-each-index', $index );

            if ( $index === '' ) {
				$output = array();
                foreach ( $meta as $temp_key => $temp_value ) {
                    $params['field_value'] = $temp_value;
                    $temp_output = types_render_field_single( $field, $params,
                            $content, $code, $temp_key );
                    if ( !empty( $temp_output ) ) {
                        $output[] = $temp_output;
                    }
                }
                if ( !empty( $output ) && isset( $params['separator'] ) ) {
                    $output = implode( $params['separator'], $output );
                } else if ( !empty( $output ) ) {
                    $output = implode( '', $output );
                } else {
                    return '';
                }
            } else{
				// Make sure indexed right
                $_index = 0;
                foreach ( $meta as $temp_key => $temp_value ) {
                    if ( $_index == $index ) {
                        $params['field_value'] = $temp_value;
                        $output = types_render_field_single( $field, $params,
                                        $content, $code, $temp_key );
                    }
                    $_index++;
                }
            }
            $html = $output;
        } else {
            return '';
        }
    } else {
		
		$params['field_value'] = get_user_meta( $user_id,
                wpcf_types_get_meta_prefix( $field ) . $field['slug'], true );
		if ( $params['field_value'] == '' && $field['type'] != 'checkbox' ) {
          return '';
        }
		$html = types_render_field_single( $field, $params, $content, $code );
		//print_r($params);
		//print "$html <br><br>";
		/*if ( !empty($params['field_value']) && $field['type'] != 'date' ){
			$html = types_render_field_single( $field, $params, $content, $code );
		}
		if ( $field['type'] == 'date' && !empty($params['field_value']['timestamp']) ){
				$html = types_render_field_single( $field, $params );
		}*/
    }

    // API filter
    $wpcf->usermeta_field->set( $user_id, $field );
    return $wpcf->usermeta_field->html( $html, $params );
}



/**
 * Calls view function for specific field type.
 * 
 * @param type $field
 * @param type $atts
 * @return type 
 */
function types_render_usermeta_field( $field_id, $params, $content = null, $code = '' ) {

    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
    global $wpcf, $post;

    // HTML var holds actual output
    $html = '';

    // Set post ID
    $post_id = $post->ID;
    if ( isset( $params['post_id'] ) && !empty( $params['post_id'] ) ) {
        $post_id = $params['post_id'];
    }

    // Get field
    $field = wpcf_fields_get_field_by_slug( $field_id, 'wpcf-usermeta' );
	
    // If field not found return empty string
    if ( empty( $field ) ) {

        // Log
        if ( !function_exists( 'wplogger' ) ) {
            require_once WPCF_EMBEDDED_ABSPATH . '/common/wplogger.php';
        }
        global $wplogger;
        $wplogger->log( 'types_render_usermeta_field call for missing field \''
                . $field_id . '\'', WPLOG_DEBUG );

        return '';
    }
	
	//Get user By ID
	if ( isset( $params['user_id'] ) ){
		$user_id = $params['user_id'];
	}
	else if ( isset( $params['user_name'] ) ){	//Get user by login
		 $user_id = $wpdb->get_var("SELECT * FROM " . $wpdb->prefix . "users WHERE user_login = '". $params['user_name'] ."'",0,0); 
	}
	else if ( isset( $params['user_is_author'] ) ){	//Get Post author
		$user_id = $post->post_author;
	}
	else if ( isset( $params['user_current'] ) ){//Get current logged user
		$user_id = wpcf_usermeta_get_user();	
	}
	else{	//If empty get post author, if no post, return empty
		if ( !empty( $post_id )){
			$user_id = $post->post_author;
		}
		else{
			return;
		}
	}
	
	if ( empty($user_id) ){
		return;	
	}

	
    // See if repetitive
    if ( wpcf_admin_is_repetitive( $field ) ) {
        $wpcf->usermeta_repeater->set( $user_id, $field );
        $_meta = $wpcf->usermeta_repeater->_get_meta();
        $meta = $_meta['custom_order'];
//        $meta = get_post_meta( $post_id,
//                wpcf_types_get_meta_prefix( $field ) . $field['slug'], false );
        // Sometimes if meta is empty - array(0 => '') is returned
        if ( (count( $meta ) == 1 ) ) {
            $meta_id = key( $meta );
            $_temp = array_shift( $meta );
            if ( strval( $_temp ) == '' ) {
                return '';
            } else {

                $params['field_value'] = $_temp;
                return types_render_field_single( $field, $params, $content,
                                $code, $meta_id );
            }
        } else if ( !empty( $meta ) ) {
            $output = '';

            if ( isset( $params['index'] ) ) {
                $index = $params['index'];
            } else {
                $index = '';
            }

            // Allow wpv-for-each shortcode to set the index
            $index = apply_filters( 'wpv-for-each-index', $index );

            if ( $index === '' ) {
                $output = array();
                foreach ( $meta as $temp_key => $temp_value ) {
                    $params['field_value'] = $temp_value;
                    $temp_output = types_render_field_single( $field, $params,
                            $content, $code, $temp_key );
                    if ( !empty( $temp_output ) ) {
                        $output[] = $temp_output;
                    }
                }
                if ( !empty( $output ) && isset( $params['separator'] ) ) {
                    $output = implode( $params['separator'], $output );
                } else if ( !empty( $output ) ) {
                    $output = implode( '', $output );
                } else {
                    return '';
                }
            } else {
                // Make sure indexed right
                $_index = 0;
                foreach ( $meta as $temp_key => $temp_value ) {
                    if ( $_index == $index ) {
                        $params['field_value'] = $temp_value;
                        return types_render_field_single( $field, $params,
                                        $content, $code, $temp_key );
                    }
                    $_index++;
                }
                // If missed index
                return '';
            }
            $html = $output;
        } else {
            return '';
        }
    } else {
        $params['field_value'] = get_user_meta( $user_id,
                wpcf_types_get_meta_prefix( $field ) . $field['slug'], true );
        if ( $params['field_value'] == '' && $field['type'] != 'checkbox' ) {
            return '';
        }
        $html = types_render_field_single( $field, $params, $content, $code );
    }

    // API filter
    $wpcf->usermeta_field->set( $user_id, $field );
    return $wpcf->usermeta_field->html( $html );
}





/**
	 * Add fields to user profile
*/	
function wpcf_admin_user_profile_load_hook($user){
	if ( !current_user_can( 'edit_user', $user->ID ) )
		return false;
		
	require_once WPCF_INC_ABSPATH . '/usermeta.php';	
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta.php';
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta-post.php';
	add_action( 'admin_footer', 'wpcf_admin_fields_usermeta_styles' );
	

	wpcf_admin_userprofile_init($user);	
}


/**
	 * Add styles to admin fields groups
*/	
function wpcf_admin_fields_usermeta_styles(){
			
	require_once WPCF_INC_ABSPATH . '/usermeta.php';	
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta.php';
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta-post.php';
	
	$groups = wpcf_admin_fields_get_groups('wp-types-user-group');
	echo '<style type="text/css">';
	if (!empty($groups)) {
		foreach ($groups as $group) {
			echo str_replace("}","}\n",wpcf_admin_get_groups_admin_styles_by_group($group['id']));	
			
		}
		
	}	
	echo '</style>';	
}

/**
	 * Add fields to user profile
*/	
function wpcf_admin_user_profile_save_hook($user_id){
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	require_once WPCF_INC_ABSPATH . '/usermeta.php';	
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta.php';
	require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields-post.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/usermeta-post.php';
	wpcf_admin_userprofilesave_init($user_id);	
}


/*
*  Register Usermeta Groups in Types Access
*
*
*/
class Usermeta_Access{
	
	/**
	 * Initialize plugin enviroment
	 */ 

	public function __construct() {
	// setup custom capabilities
	  //If access plugin installed	
      if (function_exists('wpcf_access_register_caps')) // integrate with Types Access
        {	
			$fields_groups = wpcf_admin_fields_get_groups('wp-types-user-group');
			if ( !empty($fields_groups) ) {
				//Add Usermeta Fields area
				add_filter('types-access-area', array('Usermeta_Access','register_access_usermeta_area'), 10, 2);
				//Add Usermeta Fields groups
				add_filter('types-access-group', array('Usermeta_Access','register_access_usermeta_groups'), 10, 2);
				//Add Usermeta Fields caps to groups
				add_filter('types-access-cap', array('Usermeta_Access','register_access_usermeta_caps'), 10, 3);
			}
        }
	}
	
	// register custom CRED Frontend capabilities specific to each group
	public static function register_access_usermeta_caps($caps, $area_id, $group_id)
    {
        $USERMETA_ACCESS_AREA_NAME= __ ('User Meta Fields Frontend Access','wpcf');
		$USERMETA_ACCESS_AREA_ID = '__USERMETA_FIELDS';
        $default_role='guest'; //'administrator';
		
		//List of caps with default permissions
		$usermeta_caps = array(
			array('view_own_on_site',$default_role,__('View own fields on site','wpcf')),
			array('view_others_on_site',$default_role,__('View others fields on site','wpcf')),
			array('view_own_in_profile',$default_role,__('View own fields in profile','wpcf')),
			array('modify_own',$default_role,__('Modify own fields','wpcf')),
			/*
			array('view_others_in_profile',$default_role,__('View others fields in profile','wpcf')),
			array('modify_others_','administrator',__('Modify others fields','wpcf')),*/
		);
		if ( $area_id == $USERMETA_ACCESS_AREA_ID ){
			$fields_groups = wpcf_admin_fields_get_groups('wp-types-user-group');
			if ( !empty($fields_groups) ) {
				foreach ($fields_groups as $group) {
					$USERMETA_ACCESS_GROUP_NAME = $group['name'] . ' Access Group';
					$USERMETA_ACCESS_GROUP_ID = '__USERMETA_FIELDS_GROUP_'.$group['slug'];
					if ( $group_id == $USERMETA_ACCESS_GROUP_ID ){
						for ($i=0; $i<count($usermeta_caps); $i++){
							$caps[$usermeta_caps[$i][0].'_'.$group['slug']] = array(
							'cap_id' => $usermeta_caps[$i][0].'_'.$group['slug'],
							'title' =>  $usermeta_caps[$i][2],
							'default_role' =>  $usermeta_caps[$i][1]
							);
						}
						
					}
				}
			}
		
		}
		
        return $caps;
    }
	
	// register a new Types Access Group within Area for Usermeta Fields Groups Frontend capabilities
    public static function register_access_usermeta_groups($groups, $id)
    {	
		$USERMETA_ACCESS_AREA_NAME=__('User Meta Fields Frontend Access','wpcf');
		$USERMETA_ACCESS_AREA_ID = '__USERMETA_FIELDS';
		
        if ($id == $USERMETA_ACCESS_AREA_ID){
			$fields_groups = wpcf_admin_fields_get_groups('wp-types-user-group');
			if ( !empty($fields_groups) ) {
				foreach ($fields_groups as $group) {
					$USERMETA_ACCESS_GROUP_NAME = $group['name'];
					//. ' User Meta Fields Access Group'
					$USERMETA_ACCESS_GROUP_ID = '__USERMETA_FIELDS_GROUP_'.$group['slug'];
					$groups[] = array('id' => $USERMETA_ACCESS_GROUP_ID, 'name' => '' . $USERMETA_ACCESS_GROUP_NAME);
				}
			}
		}
        return $groups;
    }
	
	// register a new Types Access Area for Usermeta Fields Groups Frontend capabilities
    public static function register_access_usermeta_area($areas, $area_type = 'usermeta')
    {
        $USERMETA_ACCESS_AREA_NAME = __('User Meta Fields Access','wpcf');
        $USERMETA_ACCESS_AREA_ID = '__USERMETA_FIELDS';
        $areas[] = array('id' => $USERMETA_ACCESS_AREA_ID, 'name' => $USERMETA_ACCESS_AREA_NAME);
        return $areas;
    }
}





/*
*  Register Post Fields Groups in Types Access
*
* @author Gen gen.i@icanlocalize.com
* @since Types 1.3
*/
class Post_Fields_Access{
	
	/**
	 * Initialize plugin enviroment
	 */ 

	public function __construct() {
	// setup custom capabilities
	  //If access plugin installed	
      if (function_exists('wpcf_access_register_caps')) // integrate with Types Access
        {	
			$fields_groups = wpcf_admin_fields_get_groups();
			if ( !empty($fields_groups) ){
				//Add Fields area
				add_filter('types-access-area', array('Post_Fields_Access','register_access_fields_area'), 10, 2);
				//Add Fields groups
				add_filter('types-access-group', array('Post_Fields_Access','register_access_fields_groups'), 10, 2);
				//Add Fields caps to groups
				add_filter('types-access-cap', array('Post_Fields_Access','register_access_fields_caps'), 10, 3);
			}
        }
	}
	
	// register custom CRED Frontend capabilities specific to each group
	public static function register_access_fields_caps($caps, $area_id, $group_id)
    {
        $FIELDS_ACCESS_AREA_NAME= __ ('Post Custom Fields Frontend Access','wpcf');
		$FIELDS_ACCESS_AREA_ID = '__FIELDS';
        $default_role='guest'; //'administrator';
		
		//List of caps with default permissions
		$fields_caps = array(
			array('view_fields_on_site',$default_role,__('View Fields On Site','wpcf')),
			array('view_fields_in_edit_page',$default_role,__('View Fields In Edit Page','wpcf')),
			array('modify_fields_in_edit_page','author',__('Modify Fields In Edit Page','wpcf')),
		);
		if ( $area_id == $FIELDS_ACCESS_AREA_ID ){
			$fields_groups = wpcf_admin_fields_get_groups();
			if ( !empty($fields_groups) ){
				foreach ($fields_groups as $group) {
					$FIELDS_ACCESS_GROUP_NAME = $group['name'] . ' Access Group';
					$FIELDS_ACCESS_GROUP_ID = '__FIELDS_GROUP_'.$group['slug'];
					if ( $group_id == $FIELDS_ACCESS_GROUP_ID ){
						for ($i=0; $i<count($fields_caps); $i++){
							$caps[$fields_caps[$i][0].'_'.$group['slug']] = array(
							'cap_id' => $fields_caps[$i][0].'_'.$group['slug'],
							'title' =>  $fields_caps[$i][2],
							'default_role' =>  $fields_caps[$i][1]
							);
						}
						
					}
				}
			}
		
		}
		
        return $caps;
    }
	
	// register a new Types Access Group within Area for Post Fields Groups Frontend capabilities
    public static function register_access_fields_groups($groups, $id)
    {	
		$FIELDS_ACCESS_AREA_NAME=__('Post Fields Frontend Access','wpcf');
		$FIELDS_ACCESS_AREA_ID = '__FIELDS';
		
        if ($id == $FIELDS_ACCESS_AREA_ID){
			$fields_groups = wpcf_admin_fields_get_groups();
			if ( !empty($fields_groups) ){
				foreach ($fields_groups as $group) {
					$FIELDS_ACCESS_GROUP_NAME = $group['name'];
					//. ' User Meta Fields Access Group'
					$FIELDS_ACCESS_GROUP_ID = '__FIELDS_GROUP_'.$group['slug'];
					$groups[] = array('id' => $FIELDS_ACCESS_GROUP_ID, 'name' => '' . $FIELDS_ACCESS_GROUP_NAME);
				}
			}
		}
        return $groups;
    }
	
	// register a new Types Access Area for Post Fields Groups Frontend capabilities
    public static function register_access_fields_area($areas, $area_type = 'usermeta')
    {
        $FIELDS_ACCESS_AREA_NAME = __('Post Meta Fields Access','wpcf');
        $FIELDS_ACCESS_AREA_ID = '__FIELDS';
        $areas[] = array('id' => $FIELDS_ACCESS_AREA_ID, 'name' => $FIELDS_ACCESS_AREA_NAME);
        return $areas;
    }
}


add_action('wp_ajax_wpcf_types_suggest_user',
        'wpcf_access_wpcf_types_suggest_user_ajax');
/**
 * Suggest user AJAX. 
 */
function wpcf_access_wpcf_types_suggest_user_ajax() {
    global $wpdb;
   	/* $users = array();
    $q = $wpdb->escape(trim($_POST['q']));
    $q = like_escape($q);
    $found = $wpdb->get_results("SELECT ID, display_name, user_login FROM $wpdb->users WHERE user_nicename LIKE '%%$q%%' OR user_login LIKE '%%$q%%' OR display_name LIKE '%%$q%%' OR user_email LIKE '%%$q%%' LIMIT 10");
    if (!empty($found)) {
        foreach ($found as $user) {
            $users[$user->user_login] = $user->display_name . ' (' . $user->user_login . ')';
        }
    }*/
	$users = '';
	$q = $wpdb->escape(trim($_GET['q']));
    $q = like_escape($q);
    $found = $wpdb->get_results("SELECT ID, display_name, user_login FROM $wpdb->users WHERE user_nicename LIKE '%%$q%%' OR user_login LIKE '%%$q%%' OR display_name LIKE '%%$q%%' OR user_email LIKE '%%$q%%' LIMIT 10");
	
    if (!empty($found)) {
        foreach ($found as $user) {
            //$users[$user->user_login] = $user->display_name . ' (' . $user->user_login . ')';
			$users .= '<li>'.$user->user_login.'</li>';
        }
    }
    echo $users;
    die();
}	

/*
 * Callback sumit form usermeta addon
*/
function wpcf_get_usermeta_form_addon_submit(){
	$add = '';
	if ( !empty($_POST['is_usermeta']) ){
		if ( $_POST['display_username_for'] == 'post_autor'){
			$add .= ' user_is_author="true"';	
		}
		elseif( $_POST['display_username_for'] == 'current_user' ){
			$add .= ' user_current="true"';		
		}
		else{
			if ( $_POST['display_username_for_suser_selector'] == 'specific_user_by_id' ){
				$add .= ' user_id="'. $_POST['display_username_for_suser_id_value'] .'"';		
			}
			else{
				$add .= ' user_name="'. $_POST['display_username_for_suser_username_value'] .'"';	
			}
		}
	}
	return $add;
}
/*
 * Usermeta fields addon. 
 * Add form user users
*/

function wpcf_get_usermeta_form_addon(){
	global $wpdb;
	$form = array();
	$users = $wpdb->get_results("SELECT ID, user_login, display_name FROM $wpdb->users LIMIT 5");
	$form[] = array(
		'#type' => 'hidden',
		'#value' => 'true',
        '#name' => 'is_usermeta',
	);
	$form[] = array(
		'#type' => 'radio',
		'#suffix' => '<br />',
		'#value' => 'post_autor',
		'#title' => 'Author of this post',
        '#name' => 'display_username_for',
		'#default_value' => '',
		'#before' => '<h3>' . __( 'Display the field for this user:', 'wpcf' ) . '</h3>',
        '#inline' => true,
		'#attributes' => array('onclick' => 'wpcf_showmore(false)','checked'=>'checked')
	);
	$form[] = array(
		'#type' => 'radio',
		'#suffix' => '<br />',
		'#value' => 'current_user',
		'#title' => 'The current logged in user',
        '#name' => 'display_username_for',
		'#default_value' => '',
        '#inline' => true,
		'#attributes' => array('onclick' => 'wpcf_showmore(false)')
	);
	$form[] = array(
		'#type' => 'radio',
		'#title' => 'A specific user',
		'#value' => 'pecific_user',
		'#id' => 'display_username_for_suser',
        '#name' => 'display_username_for',
		'#default_value' => '',
		'#after' => '<br><br>',
        '#inline' => true,
		'#attributes' => array('onclick' => 'wpcf_showmore(true)')
	);
	
	$form[] = array(
		'#type' => 'radio',
		'#title' => 'User ID',
		'#value' => 'specific_user_by_id',
		'#id' => 'display_username_for_suser_id',
        '#name' => 'display_username_for_suser_selector',
		'#before' => '<div id="specific_user_div" style="display:none; margin-top:0px; margin-left:15px;">',
		'#after' => '<input type="text" class="wpcf-form-textfield form-textfield textfield" name="display_username_for_suser_id_value"'.
		' id="display_username_for_suser_id_value">',
		'#default_value' => '',
        '#inline' => true,
		'#attributes' => array('onclick' => 'hideControls(\'display_username_for_suser_username_value\',\'display_username_for_suser_id_value\')','checked'=>'checked')
	);
	$dropdown_users = '';
	foreach ($users as $u) {
        $dropdown_users .= '<option value="' . $u->user_login . '">' . $u->display_name . ' (' . $u->user_login . ')' . '</option>';
    }
	$form[] = array(
		'#type' => 'radio',
		'#title' => 'User name',
		'#value' => 'specific_user_by_username',
		'#id' => 'display_username_for_suser_username',
        '#name' => 'display_username_for_suser_selector',
		'#before' => '<div class="types-suggest-user types-suggest" id="types-suggest-user">',
		'#after' => '<input type="text" class="input wpcf-form-textfield form-textfield textfield" style="display:none;"'.
		' name="display_username_for_suser_username_value" id="display_username_for_suser_username_value"><br><br></div></div>',
		'#default_value' => '',
        '#inline' => true,
		'#attributes' => array('onclick' => 'hideControls(\'display_username_for_suser_id_value\',\'display_username_for_suser_username_value\')')
	);
	
	return $form;
}