<?php

namespace Drupal\commerce_korapay_gateway\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface;

/**
 * Provides the interface for the Korapay payment gateway.
 */
interface KorapayInterface extends OffsitePaymentGatewayInterface {

  /**
   * Get the Korapay API Secret key set for the payment gateway.
   *
   * @return string
   *   The Korapay API Secret key.
   */
  public function getSecretKey();


   /**
   * Get the Korapay API Public key set for the payment gateway.
   *
   * @return string
   *   The Korapay API Public key.
   */
  public function getPublicKey();

  /**
   * Get the configured Korapay Payment Button Text.
   *
   * @return string
   *   The Korapay Payment button text.
   */
  public function getPayButtonText();

    /**
   * Get the Korapay base url to use for API requests based on the mode.
   *
   * @return string
   *   The Korapay Base URL.
   */
  public function getBaseUrl();
  
}
