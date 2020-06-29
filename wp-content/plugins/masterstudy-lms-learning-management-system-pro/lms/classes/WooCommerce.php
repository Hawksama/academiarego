<?php

class STM_LMS_Data_Store_CPT extends WC_Product_Data_Store_CPT
{

    public function read(&$product)
    {

        add_filter('woocommerce_is_purchasable', function () {
            return true;
        }, 10, 1);

        $product->set_defaults();

        if (!$product->get_id() || !($post_object = get_post($product->get_id())) || !(('product' === $post_object->post_type) || ('stm-courses' === $post_object->post_type) || ('stm-course-bundles' === $post_object->post_type))) {
            throw new Exception(__('Invalid product.', 'woocommerce'));
        }

        $product->set_props(array(
            'name' => $post_object->post_title,
            'slug' => $post_object->post_name,
            'date_created' => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp($post_object->post_date_gmt) : null,
            'date_modified' => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp($post_object->post_modified_gmt) : null,
            'status' => $post_object->post_status,
            'description' => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id' => $post_object->post_parent,
            'menu_order' => $post_object->menu_order,
            'reviews_allowed' => 'open' === $post_object->comment_status,
        ));

        $this->read_attributes($product);
        $this->read_downloads($product);
        $this->read_visibility($product);
        $this->read_product_data($product);
        $this->read_extra_data($product);
        $product->set_object_read(true);

    }

    public function get_product_type($product_id)
    {

        $post_type = get_post_type($product_id);

        if ('product_variation' === $post_type) {
            return 'variation';
        } elseif ( in_array( $post_type, array( 'stm-courses', 'stm-course-bundles', 'product' ) ) ) {
            $terms = get_the_terms($product_id, 'product_type');
            return !empty($terms) ? sanitize_title(current($terms)->name) : 'simple';
        } else {
            return false;
        }
    }

    /**
	 * Search product data for a term and return ids.
	 *
	 * @param  string     $term Search term.
	 * @param  string     $type Type of product.
	 * @param  bool       $include_variations Include variations in search or not.
	 * @param  bool       $all_statuses Should we search all statuses or limit to published.
	 * @param  null|int   $limit Limit returned results. @since 3.5.0.
	 * @param  null|array $include Keep specific results. @since 3.6.0.
	 * @param  null|array $exclude Discard specific results. @since 3.6.0.
	 * @return array of ids
	 */
	public function search_products( $term, $type = '', $include_variations = false, $all_statuses = false, $limit = null, $include = null, $exclude = null ) {
		global $wpdb;

		$custom_results = apply_filters( 'woocommerce_product_pre_search_products', false, $term, $type, $include_variations, $all_statuses, $limit );

		if ( is_array( $custom_results ) ) {
			return $custom_results;
		}

		$post_types   = $include_variations ? array( 'product', 'product_variation', 'stm-courses' ) : array( 'product' );
		$type_where   = '';
		$status_where = '';
		$limit_query  = '';

		/**
		 * Hook woocommerce_search_products_post_statuses.
		 *
		 * @since 3.7.0
		 * @param array $post_statuses List of post statuses.
		 */
		$post_statuses = apply_filters(
			'woocommerce_search_products_post_statuses',
			current_user_can( 'edit_private_products' ) ? array( 'private', 'publish' ) : array( 'publish' )
		);

		// See if search term contains OR keywords.
		if ( stristr( $term, ' or ' ) ) {
			$term_groups = preg_split( '/\s+or\s+/i', $term );
		} else {
			$term_groups = array( $term );
		}

		$search_where   = '';
		$search_queries = array();

		foreach ( $term_groups as $term_group ) {
			// Parse search terms.
			if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $term_group, $matches ) ) {
				$search_terms = $this->get_valid_search_terms( $matches[0] );
				$count        = count( $search_terms );

				// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
				if ( 9 < $count || 0 === $count ) {
					$search_terms = array( $term_group );
				}
			} else {
				$search_terms = array( $term_group );
			}

			$term_group_query = '';
			$searchand        = '';

			foreach ( $search_terms as $search_term ) {
				$like              = '%' . $wpdb->esc_like( $search_term ) . '%';
				$term_group_query .= $wpdb->prepare( " {$searchand} ( ( posts.post_title LIKE %s) OR ( posts.post_excerpt LIKE %s) OR ( posts.post_content LIKE %s ) OR ( wc_product_meta_lookup.sku LIKE %s ) )", $like, $like, $like, $like ); // @codingStandardsIgnoreLine.
				$searchand         = ' AND ';
			}

			if ( $term_group_query ) {
				$search_queries[] = $term_group_query;
			}
		}

		if ( ! empty( $search_queries ) ) {
			$search_where = ' AND (' . implode( ') OR (', $search_queries ) . ') ';
		}

		if ( ! empty( $include ) && is_array( $include ) ) {
			$search_where .= ' AND posts.ID IN(' . implode( ',', array_map( 'absint', $include ) ) . ') ';
		}

		if ( ! empty( $exclude ) && is_array( $exclude ) ) {
			$search_where .= ' AND posts.ID NOT IN(' . implode( ',', array_map( 'absint', $exclude ) ) . ') ';
		}

		if ( 'virtual' === $type ) {
			$type_where = ' AND ( wc_product_meta_lookup.virtual = 1 ) ';
		} elseif ( 'downloadable' === $type ) {
			$type_where = ' AND ( wc_product_meta_lookup.downloadable = 1 ) ';
		}

		if ( ! $all_statuses ) {
			$status_where = " AND posts.post_status IN ('" . implode( "','", $post_statuses ) . "') ";
		}

		if ( $limit ) {
			$limit_query = $wpdb->prepare( ' LIMIT %d ', $limit );
		}

		// phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
		$search_results = $wpdb->get_results(
			// phpcs:disable
			"SELECT DISTINCT posts.ID as product_id, posts.post_parent as parent_id FROM {$wpdb->posts} posts
			 LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON posts.ID = wc_product_meta_lookup.product_id
			WHERE posts.post_type IN ('" . implode( "','", $post_types ) . "')
			$search_where
			$status_where
			$type_where
			ORDER BY posts.post_parent ASC, posts.post_title ASC
			$limit_query
			"
			// phpcs:enable
		);

		$product_ids = wp_parse_id_list( array_merge( wp_list_pluck( $search_results, 'product_id' ), wp_list_pluck( $search_results, 'parent_id' ) ) );

		if ( is_numeric( $term ) ) {
			$post_id   = absint( $term );
			$post_type = get_post_type( $post_id );

			if ( 'product_variation' === $post_type && $include_variations ) {
				$product_ids[] = $post_id;
			} elseif ( 'product' === $post_type ) {
				$product_ids[] = $post_id;
			}

			$product_ids[] = wp_get_post_parent_id( $post_id );
		}

		return wp_parse_id_list( $product_ids );
	}
}

add_filter('woocommerce_data_stores', 'stm_lms_woocommerce_data_stores');

function stm_lms_woocommerce_data_stores($stores)
{
    $stores['product'] = 'STM_LMS_Data_Store_CPT';
    return $stores;
}

add_action('woocommerce_checkout_update_order_meta', 'stm_before_create_order', 200, 1);

function stm_before_create_order($order_id)
{
    $cart = WC()->cart->get_cart();
    $ids = array();
    foreach ($cart as $cart_item) {
        $ids[] = apply_filters('stm_lms_before_create_order', array(
            'item_id' => $cart_item['product_id'],
            'price' => $cart_item['line_total'],
            'quantity' => $cart_item['quantity']
        ), $cart_item);
    }
    update_post_meta($order_id, 'stm_lms_courses', $ids);
}

add_action('woocommerce_order_status_completed', 'stm_lms_woocommerce_order_created');

function stm_lms_woocommerce_order_created($order_id)
{
    $order = new WC_Order($order_id);
    $user_id = $order->get_user_id();

    $courses = get_post_meta($order_id, 'stm_lms_courses', true);

    foreach ($courses as $course) {
        STM_LMS_Course::add_user_course($course['item_id'], $user_id, 0, 0);
        STM_LMS_Course::add_student($course['item_id']);

        do_action('stm_lms_woocommerce_order_approved', $course, $user_id);
    }

}

add_action('woocommerce_order_status_pending', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_failed', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_on-hold', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_processing', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_refunded', 'stm_lms_woocommerce_order_cancelled');
add_action('woocommerce_order_status_cancelled', 'stm_lms_woocommerce_order_cancelled');

function stm_lms_woocommerce_order_cancelled($order_id)
{
    $order = new WC_Order($order_id);
    $user_id = $order->get_user_id();

    $courses = get_post_meta($order_id, 'stm_lms_courses', true);

    foreach ($courses as $course) {
        stm_lms_get_delete_user_course($user_id, $course['item_id']);
        STM_LMS_Course::remove_student($course['item_id']);

        do_action('stm_lms_woocommerce_order_cancelled', $course, $user_id);
    }
}