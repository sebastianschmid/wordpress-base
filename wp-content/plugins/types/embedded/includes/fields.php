<?php

/**
 * Gets all groups.
 * Modified by Gen, 13.02.2013
 * TODO Gen When you add modified comment - document what is modified (or remove it)
 * 
 * @global type $wpdb
 * @param string $post_type
 * @param boolean|string $only_active
 * @param boolean|string $add_fields - 'field_active', 'field_all', false (to omitt fields)
 * @return type 
 */
function wpcf_admin_fields_get_groups( $post_type = 'wp-types-group',
        $only_active = false, $add_fields = false ) {
    $groups = get_posts( 'numberposts=-1&post_type=' . $post_type . '&post_status=null' );
    $_groups = array();
    if ( !empty( $groups ) ) {
        foreach ( $groups as $k => $group ) {
            $group = wpcf_admin_fields_adjust_group( $group, $add_fields );
            if ( $only_active && !$group['is_active'] ) {
                continue;
            }
            $_groups[$k] = $group;
        }
    }
    return $_groups;
}

/**
 * Gets group by ID.
 * 
 * Since 1.2 we enabled fetching by post title.
 * Modified by Gen, 13.02.2013
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_fields_get_group( $group_id, $post_type = 'wp-types-group',
        $add_fields = false ) {
    $group = get_post( $group_id );
    if ( empty( $group->ID ) ) {
        $group = get_page_by_title( $group_id, OBJECT, $post_type );
    }
    if ( empty( $group->ID ) ) {
        $group = get_page_by_path( $group_id, OBJECT, 'wp-types-group' );
    }
    if ( empty( $group->ID ) ) {
        return array();
    }
    return wpcf_admin_fields_adjust_group( $group, $add_fields );
}

/**
 * Converts post data.
 * 
 * @param type $post
 * @return type 
 */
function wpcf_admin_fields_adjust_group( $post, $add_fields = false ) {
    if ( empty( $post ) ) {
        return false;
    }
    $group = array();
    $group['id'] = $post->ID;
    $group['slug'] = $post->post_name;
    $group['name'] = $post->post_title;
    $group['description'] = $post->post_content;
    $group['meta_box_context'] = 'normal';
    $group['meta_box_priority'] = 'high';
    $group['is_active'] = $post->post_status == 'publish' ? true : false;
    $group['filters_association'] = get_post_meta( $post->ID,
            '_wp_types_group_filters_association', true );

    // Attach fields if required (since 1.3)
    if ( $add_fields ) {
        $active = $add_fields == 'fields_active' ? true : false;
        $group['fields'] = wpcf_admin_fields_get_fields_by_group( $post->ID,
                'slug', $active );
    }

    return $group;
}

/**
 * Gets Fields Admin Styles supported by specific group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @return type 
 */
function wpcf_admin_get_groups_admin_styles_by_group( $group_id ) {
    $admin_styles = get_post_meta( $group_id, '_wp_types_group_admin_styles',
            true );
    return trim( $admin_styles );
}

/**
 * Saves group's admin styles
 * 
 * @global type $wpdb
 * @param type $group_id
 * @param type $padmin_styles 
 */
function wpcf_admin_fields_save_group_admin_styles( $group_id, $admin_styles ) {
	update_post_meta( $group_id, '_wp_types_group_admin_styles', $admin_styles );
}

/**
 * Gets all fields.
 * 
 * @global type $wpdb
 * @return type 
 * added param $use_cache by Gen (used when adding new fields to group)
 */
function wpcf_admin_fields_get_fields( $only_active = false,
        $disabled_by_type = false, $strictly_active = false,
        $option_name = 'wpcf-fields', $use_cache = true, $clear_cache = false) {
	
	static $cache = array();

 	if ($clear_cache) {
        $cache = array();
        }

	$cache_key = md5( $only_active . $disabled_by_type . $strictly_active . $option_name . $use_cache );
	if ( isset( $cache[$cache_key] ) && $use_cache == true ) {
        return $cache[$cache_key];
    }
    $required_data = array('id', 'name', 'type', 'slug');
    $fields = (array) get_option( $option_name, array() );
    foreach ( $fields as $k => $v ) {
        $data = wpcf_fields_type_action( $v['type'] );
        if ( empty( $data ) ) {
            unset( $fields[$k] );
            continue;
        }
        if ( isset( $data['wp_version'] )
                && wpcf_compare_wp_version( $data['wp_version'], '<' ) ) {
            unset( $fields[$k] );
            continue;
        }
        if ( $strictly_active ) {
            if ( !empty( $v['data']['disabled'] ) || !empty( $v['data']['disabled_by_type'] ) ) {
                unset( $fields[$k] );
                continue;
            }
        } else {
            if ( ($only_active && !empty( $v['data']['disabled'] ) ) ) {
                unset( $fields[$k] );
                continue;
            }
            if ( !$disabled_by_type && !empty( $v['data']['disabled_by_type'] ) ) {
                unset( $fields[$k] );
                continue;
            }
        }
        foreach ( $required_data as $required ) {
            if ( !isset( $v[$required] ) ) {
                unset( $fields[$k] );
                continue;
            }
        }
        $fields[$k] = wpcf_sanitize_field( $v );
    }
	$cache[$cache_key] = $fields;
    return $fields;
}

/**
 * Gets field by ID.
 * Modified by Gen, 13.02.2013
 * 
 * @global type $wpdb
 * @param type $field_id
 * @param type $only_active
 * @return type 
 */
function wpcf_admin_fields_get_field( $field_id, $only_active = false,
        $disabled_by_type = false, $strictly_active = false,
        $option_name = 'wpcf-fields' ) {
    $fields = wpcf_admin_fields_get_fields( $only_active, $disabled_by_type,
            $strictly_active, $option_name );
    if ( !empty( $fields[$field_id] ) ) {
        $data = wpcf_fields_type_action( $fields[$field_id]['type'] );
        if ( isset( $data['wp_version'] )
                && wpcf_compare_wp_version( $data['wp_version'], '<' ) ) {
            return array();
        }
        $fields[$field_id]['id'] = $field_id;
        return wpcf_sanitize_field( $fields[$field_id] );
    }
    return array();
}

/**
 * Gets field by slug.
 * Modified by Gen, 13.02.2013
 * 
 * @global type $wpdb
 * @param type $slug
 * @return type 
 */
function wpcf_fields_get_field_by_slug( $slug, $meta_name = 'wpcf-fields' ) {
    return wpcf_admin_fields_get_field( $slug, false, false, false, $meta_name );
}

/**
 * Gets all fields that belong to specific group.
 * 
 * @global type $wpdb
 * @param type $group_id
 * @param type $key
 * @param type $only_active
 * @param type $post_type
 * @param type $meta_name
 * @return type 
 */
function wpcf_admin_fields_get_fields_by_group( $group_id, $key = 'slug',
        $only_active = false, $disabled_by_type = false,
        $strictly_active = false, $post_type = 'wp-types-group',
        $meta_name = 'wpcf-fields' ) {
    static $cache = array();
    $cache_key = md5( $group_id . $key . $only_active . $disabled_by_type . $strictly_active . $post_type . $meta_name );
    if ( isset( $cache[$cache_key] ) ) {
        return $cache[$cache_key];
    }
    $group_fields = get_post_meta( $group_id, '_wp_types_group_fields', true );
    if ( empty( $group_fields ) ) {
        return array();
    }
    $group_fields = explode( ',', trim( $group_fields, ',' ) );
    $fields = wpcf_admin_fields_get_fields( $only_active, $disabled_by_type,
            $strictly_active, $meta_name );
    $results = array();
    foreach ( $group_fields as $field_id ) {
        if ( !isset( $fields[$field_id] ) ) {
            continue;
        }
        $field = wpcf_admin_fields_get_field( $field_id, false, false, false,
                $meta_name );
        if ( !empty( $field ) ) {
            $results[$field_id] = $field;
        }
    }
    $cache[$cache_key] = $results;
    return $results;
}

/**
 * Gets groups that have specific term.
 * 
 * @global type $wpdb
 * @param type $term_id
 * @param type $fetch_empty
 * @param type $only_active
 * @return type 
 */
function wpcf_admin_fields_get_groups_by_term( $term_id = false,
        $fetch_empty = true, $post_type = false, $only_active = true ) {
    $args = array();
    $args['post_type'] = 'wp-types-group';
    $args['numberposts'] = -1;
    // Active
    if ( $only_active ) {
        $args['post_status'] = 'publish';
    }
    // Fetch empty
    if ( $fetch_empty ) {
        if ( $term_id ) {
            $args['meta_query']['relation'] = 'OR';
            $args['meta_query'][] = array(
                'key' => '_wp_types_group_terms',
                'value' => ',' . $term_id . ',',
                'compare' => 'LIKE',
            );
        }
        $args['meta_query'][] = array(
            'key' => '_wp_types_group_terms',
            'value' => 'all',
            'compare' => '=',
        );
    } else if ( $term_id ) {
        $args['meta_query'] = array(
            array(
                'key' => '_wp_types_group_terms',
                'value' => ',' . $term_id . ',',
                'compare' => 'LIKE',
            ),
        );
    } else {
        return array();
    }
    $groups = get_posts( $args );
    foreach ( $groups as $k => $post ) {
        $temp = get_post_meta( $post->ID, '_wp_types_group_post_types', true );
        if ( $fetch_empty && $temp == 'all' ) {
            $groups[$k] = wpcf_admin_fields_adjust_group( $post );
        } else if ( strpos( $temp, ',' . $post_type . ',' ) !== false ) {
            $groups[$k] = wpcf_admin_fields_adjust_group( $post );
        } else {
            unset( $groups[$k] );
        }
    }
    return $groups;
}

/**
 * Gets groups that have specific post_type.
 * 
 * @global type $wpdb
 * @param type $post_type
 * @param type $fetch_empty
 * @param type $only_active
 * @return type 
 */
function wpcf_admin_get_groups_by_post_type( $post_type, $fetch_empty = true,
        $terms = null, $only_active = true ) {
    $args = array();
    $args['post_type'] = 'wp-types-group';
    $args['numberposts'] = -1;
    // Active
    if ( $only_active ) {
        $args['post_status'] = 'publish';
    }
    // Fetch empty
    if ( $fetch_empty ) {
        $args['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key' => '_wp_types_group_post_types',
                'value' => ',' . $post_type . ',',
                'compare' => 'LIKE',
            ),
            array(
                'key' => '_wp_types_group_post_types',
                'value' => 'all',
                'compare' => '=',
            ),
        );
    } else {
        $args['meta_query'] = array(
            array(
                'key' => '_wp_types_group_post_types',
                'value' => ',' . $post_type . ',',
                'compare' => 'LIKE',
            ),
        );
    }

    $results_by_post_type = array();
    $results_by_terms = array();

    // Get posts by post type
    $groups = get_posts( $args );
    if ( !empty( $groups ) ) {
        foreach ( $groups as $key => $group ) {
            $group = wpcf_admin_fields_adjust_group( $group );
            $results_by_post_type[$group['id']] = $group;
        }
    }

    // Distinct terms
    if ( !is_null( $terms ) ) {
        if ( !empty( $terms ) ) {
//            $args['meta_query'] = array('relation' => 'OR');
            $terms_sql = array();
            $add = '';
            if ( $fetch_empty ) {
                $add = " OR m.meta_value LIKE 'all'";
            }
            foreach ( $terms as $term ) {
                $terms_sql[] = $term;
            }
            $terms_sql = "AND (m.meta_value LIKE '%%," . implode( ",%%' OR m.meta_value LIKE '%%,",
                            $terms ) . ",%%' $add)";
            global $wpdb;
            $terms_sql = "SELECT * FROM $wpdb->posts p
                    JOIN $wpdb->postmeta m
                    WHERE p.post_type='wp-types-group' AND p.post_status='publish'
                    AND p.ID = m.post_id AND m.meta_key='_wp_types_group_terms'
                    $terms_sql";
            $groups = $wpdb->get_results( $terms_sql );
            if ( !empty( $groups ) ) {
                foreach ( $groups as $key => $group ) {
                    $group = wpcf_admin_fields_adjust_group( $group );
                    $results_by_terms[$group['id']] = $group;
                }
            }
            foreach ( $results_by_post_type as $key => $value ) {
                if ( !array_key_exists( $key, $results_by_terms ) ) {
                    unset( $results_by_post_type[$key] );
                }
            }
        }
    }

    return $results_by_post_type;
}

/**
 * Gets groups that have specific template.
 * 
 * @global type $wpdb
 * @param type $post_type
 * @param type $fetch_empty
 * @param type $only_active
 * @return type 
 */
function wpcf_admin_get_groups_by_template( $templates = array('default'),
        $fetch_empty = true, $only_active = true ) {
    $args = array();
    $args['post_type'] = 'wp-types-group';
    $args['numberposts'] = -1;
    $meta_query = array();
    // Active
    if ( $only_active ) {
        $args['post_status'] = 'publish';
    }

    // Fetch empty
    if ( $fetch_empty ) {
        $args['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key' => '_wp_types_group_templates',
                'value' => 'all',
                'compare' => '=',
            ),
        );
    } else {
        $args['meta_query'] = array(
            'relation' => 'OR');
    }
    foreach ( $templates as $template ) {
        $args['meta_query'][] = array(
            'key' => '_wp_types_group_templates',
            'value' => ',' . $template . ',',
            'compare' => 'LIKE',
        );
    }

    $results_by_template = array();

    // Get posts by template
    $groups = get_posts( $args );
    if ( !empty( $groups ) ) {
        foreach ( $groups as $key => $group ) {
            $group = wpcf_admin_fields_adjust_group( $group );
            $results_by_template[$group['id']] = $group;
        }
    }

    return $results_by_template;
}

/**
 * Loads type configuration file and calls action.
 * 
 * @param type $type
 * @param type $action
 * @param type $args 
 */
function wpcf_fields_type_action( $type, $func = '', $args = array() ) {
    static $actions = array();
    $func_in = $func;

    $md5_args = md5( serialize( $args ) );

    if ( !isset( $actions[$type . '-' . $func_in . '-' . $md5_args] ) ) {
        $fields_registered = wpcf_admin_fields_get_available_types();
        if ( isset( $fields_registered[$type] ) && isset( $fields_registered[$type]['path'] ) ) {
            $file = $fields_registered[$type]['path'];
        } else if ( defined( 'WPCF_INC_ABSPATH' ) ) {
            $file = WPCF_INC_ABSPATH . '/fields/' . $type . '.php';
        } else {
            $file = '';
        }
        $file_embedded = WPCF_EMBEDDED_INC_ABSPATH . '/fields/' . $type . '.php';
        if ( file_exists( $file ) || file_exists( $file_embedded ) ) {
            if ( file_exists( $file ) ) {
                require_once $file;
            }
            if ( file_exists( $file_embedded ) ) {
                require_once $file_embedded;
            }
            if ( empty( $func ) ) {
                $func = 'wpcf_fields_' . $type;
            } else {
                $func = 'wpcf_fields_' . $type . '_' . $func;
            }
            if ( function_exists( $func ) ) {
                $actions[$type . '-' . $func_in . '-' . $md5_args] = call_user_func( $func,
                        $args );
            } else {
                $actions[$type . '-' . $func_in . '-' . $md5_args] = false;
            }
        } else {
            $actions[$type . '-' . $func_in . '-' . $md5_args] = false;
        }
    }
    return $actions[$type . '-' . $func_in . '-' . $md5_args];
}

/**
 * Returns shortcode for specified field.
 * 
 * @param type $field
 * @param type $add Additional attributes
 */
function wpcf_fields_get_shortcode( $field, $add = '' ) {
    $shortcode = '[';
    $shortcode .= 'types field="' . $field['slug'] . '"' . $add;
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
 * Saves last field settings when inserting from toolbar.
 * 
 * @param type $field_id
 * @param type $settings 
 */
function wpcf_admin_fields_save_field_last_settings( $field_id, $settings ) {
    $data = get_user_meta( get_current_user_id(), 'wpcf-field-settings', true );
    $data[$field_id] = $settings;
    update_user_meta( get_current_user_id(), 'wpcf-field-settings', $data );
}

/**
 * Gets last field settings when inserting from toolbar.
 * 
 * @param type $field_id
 */
function wpcf_admin_fields_get_field_last_settings( $field_id ) {
    $data = get_user_meta( get_current_user_id(), 'wpcf-field-settings', true );
    if ( isset( $data[$field_id] ) ) {
        return $data[$field_id];
    }
    return array();
}

/**
 * Gets all available types.
 */
function wpcf_admin_fields_get_available_types() {
    static $data = array();
    if ( !empty( $data ) ) {
        return $data;
    }
    foreach ( glob( WPCF_EMBEDDED_INC_ABSPATH . '/fields/*.php' ) as $filename ) {
        require_once $filename;
        if ( function_exists( 'wpcf_fields_' . basename( $filename, '.php' ) ) ) {
            $data_field = call_user_func( 'wpcf_fields_' . basename( $filename,
                            '.php' ) );
            if ( !empty( $data_field['wp_version'] ) ) {
                if ( wpcf_compare_wp_version( $data_field['wp_version'], '>=' ) ) {
                    $data[basename( $filename, '.php' )] = $data_field;
                }
            } else {
                $data[basename( $filename, '.php' )] = $data_field;
            }
        }
    }
    $data = apply_filters( 'types_register_fields', $data );
    return $data;
}

/**
 * Sanitizes field.
 * 
 * @param type $field
 */
function wpcf_sanitize_field( $field ) {
    // Sanitize name
    if ( isset( $field['name'] ) ) {
        $field['name'] = sanitize_text_field( $field['name'] );
    }
    // Sanitize slug
    if ( !empty( $field['slug'] ) ) {
        $field['slug'] = sanitize_title( $field['slug'] );
    } else if ( isset( $field['name'] ) ) {
        $field['slug'] = sanitize_title( $field['name'] );
    }

    return $field;
}
