README file for Commerce Korapay Gateway

## CONTENTS OF THIS FILE

- Introduction
- Requirements
- Installation
- Configuration
- How it works
- Troubleshooting
- Maintainers

## INTRODUCTION

This project integrates Korapay payment Gateway into the Drupal Commerce payment and checkout systems. It currently supports standard workflow from Korapay.
https://korapay.com/developers

## REQUIREMENTS

This module requires the following modules:

- Submodules of Drupal Commerce package (https://drupal.org/project/commerce)
  - Commerce core
  - Commerce Payment (and its dependencies)
- [Korapay account](https://korapay.com)

## INSTALLATION

- Install as you would normally install a contributed drupal module.
  See: Installing modules (Drupal 8) [documentation page](https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules) for further information.

## CONFIGURATION

- Permissions: There are no specific permissions for this module. The Payments permissions are to be used for configurations.
- Enable the Korapay payment methods on the [Payment gateways page](/admin/commerce/config/payment-gateways).
- Avaliable payments flow:
  - Korapay Standard (Off-site). Configure Korapay Standard"payment.
    - Transaction mode: either if a test/development store or a production one.
      - Available options: "Live" and "Test";
    - Public Key: Merchant public key for the payment gateway.

## HOW IT WORKS

- General considerations:
  - Shop owner must have a [Korapay account](https://korapay.com)
  - Customer should have a valid credit card/bank account
- Customer/Checkout workflows:
  - It follows the Drupal Commerce Credit Card workflow.
    The customer should enter his/her credit card data or bank account info.

* The Korapay modal uses the site information to configure the modal automatically

- Payment Terminal:
  - The store owner can view the Korapay payments.

## Credits

---

This project has been developed by Korapay Development team
