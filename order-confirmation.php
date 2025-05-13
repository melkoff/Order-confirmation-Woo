<?php

function render_custom_order_confirmation()
{
  if (!isset($_GET['key'])) return '';

  $order_id = wc_get_order_id_by_order_key(sanitize_text_field($_GET['key']));
  $order = wc_get_order($order_id);
  if (!$order) return '';

  ob_start();

  // Get data
  $name = $order->get_formatted_billing_full_name();
  $address = $order->get_formatted_billing_address();
  $phone = $order->get_billing_phone();
  $email = $order->get_billing_email();
  $order_number = $order->get_order_number();
  $order_date = wc_format_datetime($order->get_date_created());
  $shipping_total = wc_price($order->get_shipping_total());
  $tax_total = wc_price($order->get_total_tax());
  $total = $order->get_formatted_order_total();

?>

  <style>
    .order-confirmation {
      font-family: "Noto Sans Arabic";
      line-height: 150%;
      display: flex;
      justify-content: center;
    }

    h2,
    h3,
    p {
      margin: 0px;
    }

    .order-confirmation-container {
      max-width: 1440px;
      margin: auto 50px;
      display: flex;
      gap: 25px;
    }

    @media (max-width: 992px){
      .order-confirmation-container{
        flex-direction: column;
      }
    }

    .container-left,
    .container-right {
      flex: 1;
      min-width: 300px;
    }

    .container-right{
      height: 100%;
    }

    .order-title {
      font-size: 32px;
      font-weight: 600;
      margin: 16px 0;
      line-height: 150%;
    }

    .order-description {
      font-size: 20px;
      font-weight: 500;
      margin: 0px;
    }

    .order-data {
      margin: 48px 0;
      max-width: 380px;
    }

    .billing-container {
      display: flex;
      column-gap: 64px;
      margin-top: 16px;
    }

    .billing-container .billing-field {
      font-size: 18px;
      font-weight: 600;
      width: 72px;
    }

    .billing-container .billing-data {
      font-size: 18px;
      font-weight: 400;
    }

    .section-title {
      font-size: 22px;
      font-weight: 600;
    }

    .track-button {
      display: inline-block;
      width: 100%;
      margin-top: 48px;
      background-color: #0047f1;
      color: white;
      font-weight: 600;
      padding: 16px 10px;
      border: none;
      border-radius: 30px;
      font-size: 20px;
    }

    .order-summary {
      display: flex;
      flex-direction: column;
      gap: 16px;
      max-width: 520px;
      background-color: #F2F5FF;
      padding: 16px 16px 32px 16px;
      border-radius: 12px;
    }

    .summary-header {
      font-size: 18px;
      font-weight: 600;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      font-size: 16px;
    }

    .product {
      display: flex;
      align-items: flex-start;
      gap: 16px;
    }

    .product img {
      width: 110px;
      height: 110px;
      border-radius: 8px;
      object-fit: cover;
    }

    .product-details {
      max-width: 160px;
      font-size: 16px;
      line-height: 150%;
    }

    .product-details .wc-item-meta{
      padding: 0px;
      list-style: none;
      font-size: 12px;
      color: #797B89;
    }

    .product-details .wc-item-meta li{
      display: flex;
    }

    .product-details .wc-item-meta li .wc-item-meta-label{
      margin-right: 5px !important;
      font-weight: 400;
    }

    .product-price {
      margin-left: auto;
      color: #0047ff;
      font-weight: 600;
      font-size: 14px;
    }

    .total-row {
      font-weight: 600;
    }

    .address-data {
      max-width: 180px;
    }
  </style>

  <div class="order-confirmation">
    <div class="order-confirmation-container">
      <div class="container-left">
        <h2 class="order-title">Thank you for your purchase!</h2>
        <p class="order-description">
          Your order will be processed within 24 hours during working days.
          We will notify you by email once your order has been shipped.
        </p>

        <div class="order-data">
          <h3 class="section-title">Billing Address</h3>

          <div class="billing-container">
            <div class="billing-field">Name</div>
            <div class="billing-data"><?php echo esc_html($name); ?></div>
          </div>

          <div class="billing-container">
            <div class="billing-field">Address</div>
            <div class="billing-data address-data"><?php echo wp_kses_post($address); ?></div>
          </div>

          <div class="billing-container">
            <div class="billing-field">Phone</div>
            <div class="billing-data"><?php echo esc_html($phone); ?></div>
          </div>

          <div class="billing-container">
            <div class="billing-field">Email</div>
            <div class="billing-data"><?php echo esc_html($email); ?></div>
          </div>

          <button class="track-button">Track Order</button>
        </div>
      </div>

      <div class="container-right order-summary">
        <h3 class="summary-header">Order Summary</h3>

        <hr style="margin: 0; height:1px; border-width:1;" color="#B9CDFF">

        <div class="summary-row">
          <span>Order Number</span>
          <span><?php echo esc_html($order_number); ?></span>
        </div>

        <div class="summary-row">
          <span>Date</span>
          <span><?php echo esc_html($order_date); ?></span>
        </div>

        <hr style="margin: 0; height:1px; border-width:1;" color="#B9CDFF">

        <?php foreach ($order->get_items() as $item):
          $product = $item->get_product();
          if (! $product) continue;
          $image = wp_get_attachment_image_src($product->get_image_id(), 'thumbnail');
        ?>
          <div class="product">
            <img src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr($product->get_name()); ?>">

            <div class="product-details">
              <strong><?php echo esc_html($item->get_name()); ?></strong><br>
              <?php
              echo wc_display_item_meta($item, array('echo' => false));
              ?>
              <p style="font-size: 12px;"> <?php echo esc_html($item->get_quantity()); ?> Items</p>
            </div>

            <div class="product-price"><?php echo wc_price($item->get_total()); ?></div>
          </div>
          <hr style="margin: 0; height:1px; border-width:1;" color="#B9CDFF">
        <?php endforeach; ?>

        <div class="summary-row">
          <span>Shipping</span>
          <span><?php echo $shipping_total; ?></span>
        </div>

        <div class="summary-row">
          <span>Taxes</span>
          <span><?php echo $tax_total; ?></span>
        </div>

        <hr style="margin: 0; height:1px; border-width:1;" color="#B9CDFF">

        <div class="summary-row total-row">
          <span>Order Total</span>
          <span><?php echo $total; ?></span>
        </div>

      </div>
    </div>
  </div>
<?php

  return ob_get_clean();
}
add_shortcode('order_confirmation', 'render_custom_order_confirmation');
