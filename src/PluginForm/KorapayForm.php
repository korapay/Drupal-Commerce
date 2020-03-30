<?php

namespace Drupal\commerce_korapay_gateway\PluginForm;

use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

class KorapayForm extends BasePaymentOffsiteForm {
  
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
   
 $form = parent::buildConfigurationForm($form, $form_state);
    
    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    /** @var \Drupal\commerce_korapay\Plugin\Commerce\PaymentGateway\KorapayInterface $plugin */
    $plugin = $payment->getPaymentGateway()->getPlugin();

    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $payment->getOrder();

    // Adds information about the billing profile.
    $billing_address = $order->getBillingProfile()->get('address')->first();
    // Get total order price.
    $amount = $payment->getAmount();

    //The transaction data to be sent to the modal
    $transactionData = [
      'public_key'=>$plugin->getPublicKey(),
      'reference' => $payment->getOrderId(),
      'amount' => $amount->getNumber(),
      'email' => $order->getEmail(),
      'first_name'=>$billing_address->getGivenName(),
      'last_name'=>$billing_address->getFamilyName(),
      'redirect_url' => $form['#return_url'],
      'cancel_action' => $form['#cancel_url'],
    ];
    
    //Attach korapay libraries and dependencies to the form
    $form['#attached']['library'][] = 'commerce_korapay_gateway/korapay_live';
    $form['#attached']['library'][] = 'commerce_korapay_gateway/korapay';

    //Build a redirect form where the client can wait for his transaction to be initialize or do it manually
   $form = $this->buildRedirectForm($form, $form_state, '', $transactionData, '');

   //Attach the transactionData to the form
    $form['#attached']['drupalSettings']['korapay']['transactionData'] = json_encode($transactionData);;

    return $form;
  }

  
  /**
   * {@inheritdoc}
   */
  public function buildRedirectForm(array $form, FormStateInterface $form_state, $redirect_url, array $data, $redirect_method = BasePaymentOffsiteForm::REDIRECT_GET) {


    //The message to be shown while waiting for the payment gateway(Korapay) to load
      $helpMessage = t('Please wait while the payment server loads. If nothing happens within 10 seconds, please click on the button below.');


    $form['commerce_message'] = [
      '#markup' => '<div class="checkout-help">' . $helpMessage . '</div>',
      '#weight' => -10,
      // Plugin forms are embedded using #process, so it's too late to attach
      // another #process to $form itself, it must be on a sub-element.
      '#process' => [
        [get_class($this), 'processRedirectForm'],
      ],
    ];

    return $form;
  }

  
  /**
   * {@inheritdoc}
   */
  public static function processRedirectForm(array $element, FormStateInterface $form_state, array &$complete_form) {
    $complete_form['#attributes']['class'][] = 'payment-redirect-form';
    unset($element['#action']);
    // The form actions are hidden by default, but needed in this case.
    $complete_form['actions']['#access'] = TRUE;
    foreach (Element::children($complete_form['actions']) as $element_name) {
      $complete_form['actions'][$element_name]['#access'] = TRUE;
    }

    return $element;
  }


  
}
