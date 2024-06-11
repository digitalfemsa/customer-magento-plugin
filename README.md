![alt tag](/view/adminhtml/web/images/logo.png)

Magento 2 Plugin v.1.0.1 (Stable)
========================

Installation for Magento 2.4
-----------

1. First add this repository in your composer config
```bash
composer config repositories.digitalfemsa git https://github.com/digitalfemsa/customer-magento-plugin
```

2. Add composer dependency
```bash
composer require digitalfemsa/digitalfemsa_payments main
```

3. Update Magento
```bash
php bin/magento setup:upgrade
```

4. Compile the component
```bash
php bin/magento setup:di:compile
```

5. Enable plugin
```bash
php bin/magento module:enable DigitalFemsa_Payments 
```

6. Update and/or enable cache
```bash
bin/magento c:f
```

Plugin updates
-----------

1. List all the components
```bash
php bin/magento module:status 
```
2. Verify that the DigitalFemsa_Payments component is listed

3. Disable the module
```bash
php bin/magento module:disable DigitalFemsa_Payments --clear-static-content
```

4. If it exists, delete the generated files in the folder ```<path_magento>/generated/code/DigitalFemsa/```

5. Add composer dependency
```bash
composer require digitalfemsa/digitalfemsa_payments main
```

6. Update Magento
```bash
php bin/magento setup:upgrade
```

7. Compile the component
```bash
php bin/magento setup:di:compile
```

8. Enable plugin
```bash
php bin/magento module:enable DigitalFemsa_Payments 
```

9. Update and/or enable cache
```bash
bin/magento c:f
```

Magento Version Compatibility
-----------------------------
The plugin has been tested in Magento 2.3 and 2.4 
Support is not guaranteed for untested versions.


#development local
```
 composer install --ignore-platform-req=ext-gd --ignore-platform-req=ext-intl --ignore-platform-req=ext-xsl
``
