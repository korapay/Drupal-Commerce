<?php

namespace Drupal\commerce_korapay_gateway\Plugin\Commerce\PaymentGateway;

use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the Korapay Checkout payment.
 *
 * @CommercePaymentGateway(
 *   id = "korapay",
 *   label = "korapay",
 *   display_label = "korapay",
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_korapay_gateway\PluginForm\KorapayForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "mastercard", "visa", "verve"
 *   },
 * )
 */

 class Korapay extends OffsitePaymentGatewayBase implements KorapayInterface{

      protected $verifyCount = 0;

  const KORAPAY_LIVE_URL = 'https://gateway.korapay.com/merchant';

  const KORAPAY_STAGING_URL = ' https://gateway.koraapi.com/merchant';
/**
   * {@inheritdoc}
   */
  public function getSecretKey() {
    return $this->configuration['secret_key'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPublicKey() {
    return $this->configuration['public_key'];
  }
  
    /**
   * {@inheritdoc}
   */
  public function getPayButtonText() {
    return $this->configuration['pay_button_text'];
  }


   /**
   * {@inheritdoc}
   */
  public function getBaseUrl() {
    // if ($this->getMode() == 'live') {
    //   return self::KORAPAY_LIVE_URL;
    // }
    // else {
      return self::KORAPAY_STAGING_URL;
    // }
  }
  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'public_key' => '',
        'secret_key'=>'',
        'pay_button_text' => '',
      ] + parent::defaultConfiguration();
  }

   /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['public_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Public Key'),
      '#description' => $this->t('Enter your korapay merchant Public key.'),
      '#default_value' => $this->getPublicKey(),
      '#required' => TRUE,
    ];
      $form['secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret key'),
      '#description' => $this->t('Enter your Korapay merchant Secret Key.'),
      '#default_value' => $this->getSecretKey(),
      '#required' => TRUE,
    ];

      $form['pay_button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pay button text'),
      '#description' => $this->t('(Optional) Enter a custom pay button text.'),
      '#default_value' => $this->getPayButtonText(),
      '#required' => FALSE,
    ];
    
    return $form;
  }

   /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);
    // Validate the secret key.
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $pub_key = $values['public_key'];
      if (!is_string($pub_key) || !(substr($pub_key, 0, 3) === 'pk_')) {
        $form_state->setError($form['public_key'], $this->t('A Valid Korapay Public Key must start with \'pk_\'.'));
      }
    }
  }
  

  /**
   * {@inheritdoc}y
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['public_key'] = $values['public_key'];
      $this->configuration['secret_key'] = $values['secret_key'];
       $this->configuration['pay_button_text'] = $values['pay_button_text'];
    }
  }

  public function onReturn(OrderInterface $order, Request $request) {
    $logger = \Drupal::logger('commerce_korapay_gateway');

    $response = urldecode($request->query->get('resp'));
    $merchantTransactionReference = urldecode($request->query->get('reference'));

    

    $logger->info('Korapay returned: ' . $response.' '.$merchantTransactionReference);

    if($response == 'failed'){
         throw new PaymentGatewayException('Payment was not successful.');
    }

    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->create([
      'state' => 'authorization',
      'amount' => $order->getTotalPrice(),
      'payment_gateway' => $this->entityId,
      'order_id' => $order->id(),
      'remote_id' =>'',
      'remote_state' => 'success',
    ]);
    
    $logger->info('Saving Payment information. Transaction reference: ' .      $merchantTransactionReference);
    
    $payment->save();
    drupal_set_message('Payment was processed');
    
    $logger->info('Payment information saved successfully. Transaction reference: ' .      $merchantTransactionReference);

   }
}