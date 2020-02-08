<?php
/**
 * Add Custom field to Product category
 */
add_action('product_cat_add_form_fields', 'pl_add_located_field', 10, 1);
add_action('product_cat_edit_form_fields', 'pl_edit_located_field', 10, 1);
add_action('edited_product_cat', 'pl_save_located_field', 10, 1);
add_action('create_product_cat', 'pl_save_located_field', 10, 1);

//Product Cat create page
function pl_add_located_field() {
    ?>
    <div class="form-field">
        <label for="pl_located"><?php _e('Located'); ?></label>
        <select name="pl_located" id="pl_located">
            <option value="individual">Individual Warehouse</option>
            <option value="vendor">Vendor Warehouse</option>
        </select>
    </div>
    <?php
}
//Product Cat edit page
function pl_edit_located_field($term) {
    $term_id = $term->term_id;
    $pl_located = get_term_meta($term_id, 'pl_located', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="pl_located"><?php _e('Located'); ?></label></th>
        <td>
            <select name="pl_located" id="pl_located">
                <option value="individual" <?= esc_attr($pl_located) == 'individual' ? 'selected' : '' ?> >Individual Warehouse</option>
                <option value="vendor" <?= esc_attr($pl_located) == 'vendor' ? 'selected' : '' ?> >Vendor Warehouse</option>
            </select>
        </td>
    </tr>
    <?php
}
// Save custom field
function pl_save_located_field($term_id) {
    $pl_located = filter_input(INPUT_POST, 'pl_located');
    update_term_meta($term_id, 'pl_located', $pl_located);
}

//After purchased
add_action( 'woocommerce_checkout_order_processed', 'pl_check_located',  1, 1  );
function pl_check_located ($order_id) {
    $order = wc_get_order($order_id);
    $order_data = $order->get_data();
    $vendor_items = [];
    $total_sub_order = 0;
    foreach ($order->get_items() as $item) {
        $item_data = $item->get_data();
        $product = $item->get_product();
        $cats = get_the_terms($item_data['product_id'], 'product_cat');
        $is_vendor = false;
        foreach ($cats as $cat) {
            $pl_located = get_term_meta($cat->term_id, 'pl_located', true);
            if ($pl_located === 'vendor') {
                $is_vendor = true;
                break;
            }
        }

        if ($is_vendor) {
            $vendor_items[] = [
                'id' => $item_data['product_id'],
                'name' => $item_data['name'],
                'price' => $product->get_price(),
                'quantity' => $item_data['quantity'],
                'total' => $item_data['total']
            ];
            $total_sub_order += $item_data['total'];
        }
    }

    if (!empty($vendor_items)) {
        $sub_order = [
            'id' => $order_data['id'],
            'status' => $order_data['status'],
            'currency' => $order_data['currency'],
            'created_at' => $order_data['date_created']->date('Y-m-d H:i:s'),
            'total' => $total_sub_order,
            'billing' => $order_data['billing'],
            'shipping' => $order_data['shipping'],
            'payment_method_title' => $order_data['payment_method_title'],
            'items' => $vendor_items
        ];
        $api = new VendorApi();
        $result = $api->createOrder($sub_order);
        if (!$result) {
            // catch error
        }
    }
}

//Change order status
add_action( 'woocommerce_order_status_changed', 'action_woocommerce_order_status_changed', 10, 4 );
function action_woocommerce_order_status_changed( $order_id, $this_status_transition_from, $this_status_transition_to, $instance ) {
    $api = new VendorApi();
    $result = $api->updateOrderStatus($order_id, $this_status_transition_to);
    if (!$result) {
        // catch error
    }
};
