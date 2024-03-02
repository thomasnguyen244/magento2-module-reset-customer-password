# Magento 2 Reset Customer Password - Send Welcome Emails to Customers Programe
``workwiththomas/module-manage-customer-password``

Magento 2 Customer Password, Reset Customer Password in Admin, Send Reset Password Email to Customers

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)

## Donation

If this project help you reduce time to develop, you can give me a cup of coffee :) 
[![Buy Me A Coffee](https://raw.githubusercontent.com/thomasnguyen244/resume/update-resume-info/assets/buy-me-a-coffee.png)](https://www.buymeacoffee.com/workwiththomas)

## Main Functionalities
- Change customer password when edit/add customer in backend
- Bulk reset password for customers in admin grid
- Command line tool reset password and send emails to customers
- Logging customer password changes.

## Installation

### Type 1: Zip file

 - Unzip the zip file in `app/code/Thomas`
 - Enable the module by running `php bin/magento module:enable Thomas_CustomerPassword`
 - Apply database updates by running `php bin/magento setup:upgrade --keep-generated`
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require workwiththomas/module-manage-customer-password`
 - enable the module by running `php bin/magento module:enable Thomas_CustomerPassword`
 - apply database updates by running `php bin/magento setup:upgrade --keep-generated`
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration


## Specifications


## Attributes


## How to work

1. Console command tools:

- Send welcome with reset password emails: 

    + Run send emails to all customer per limit:

    ``bin/magento thomas:customer-password:reset-password --limit=100``

    + Run send emails to special customer email address:

    ``bin/magento thomas:customer-password:reset-password --emails="customer1@gmail.com,customer2@gmail.com"``
