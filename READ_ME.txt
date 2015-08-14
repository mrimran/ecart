1. NOTICE

It is strongly recommended to backup your current Magento Target Store before migration. We are not responsible for any data loss or damage caused by incorrect usage of the tool.
Backup of Source Store is not necessary and not required.

2. SETUP INSTRUCTIONS AND USAGE GUIDE

http://litextension.com/docs/cart_migration_user_guide.pdf

3. CUSTOMER PASSWORD MIGRATION GUIDE

The tool can migrate passwords with "Customer Password Plugin". This is a special plugin which adds the ability to read passwords encrypted by source stores to Magento. All passwords are migrated over and remains encrypted, customers can login to the new shop right away without the need of resetting passwords. "Zencart to Magento" and "Magento to Magento" tools natively support migrating passwords and do not need this plugin.

4. SEO PLUGIN GUIDE

"Products and Categories SEO Urls Plugin" helps migrate Products and Categories urls. Old urls will be saved in Target Magento Store and will be maintained to keep all current SEO ranking you have built up for years.

For us to create the plugin, please provide your source cart url, we will diagnose and create the plugin to send to you within 24 hours. There are cases which we also require FTP of your source cart.

5. CUSTOM FIELDS PLUGIN GUIDE

"Custom Fields Plugin" helps migrate custom fields from Source Store to Target Magento Store. This is useful in case customers have customized their store, for example: adding fields to database tables, and also want to migrate these fields into Magento Store.

For us to create the plugin, please provide FTP of your source cart, we will diagnose and create the plugin to send to you within 24 hours.

6. CONTACT & SUPPORT & PLUGIN REQUEST

For any question, bug report, plugin request, please drop us a message with your details info at: http://litextension.com/contacts, or email us: contact@litextension.com
We are striving to get your issue solved within 24 hours.

######################################################################
# CHANGES LOG
######################################################################

Version 2.3.1
- Support migrate related product
- Support migrate product tags
- Support migrate customer group
- Support "Inventory Updater" plugin, which can quickly update all product cost and quantity within minutes.

Version 2.2.0
- Support password migration plugins
- Bug fixes and improvements

Version 2.1.0
- Add license management

Version 2.0.0
- Code refactored and optimized
- Completely new data transfer algorithm, dramatically reduce requests between Target Cart and Source Cart
- Huge improvement in migration speed, UP TO 3-10 TIMES FASTER THAN VERSION 1.x
- Improve stability
- Add new free option: Ignore product inventory for out-of-stock products

Version 1.3.0
- Added new Advanced option: Migrate recent data ( new data only in comparison with current data).
- Import product options and shipment in Orders.
- Preserve customers password, customers can use the same password in Magento site ( Zen Cart only )
- Improve stability
- Add new free options: Ignore product inventory for out-of-stock products

Version 1.2.0
- Migration speed improvement
- Fix cp1252 encoding issue
- Fix bug on Order and Customer import
- Correct customer created date
- Add auto-resume when migration stops due to network errors, fix timeout bug
- Fix order import, accept no-product orders.
- Considerably improve customer import speed
- Fix order history time

Version 1.1.0
- Bug fixes and improvements.
- New advanced option added: Preserve customer and order id.

Version 1.0.0
- Initial release
- Support migrate: products, categories, manufacturers, customers, orders, product reviews, taxes.
- Support additional options:
+ Clear current store data.
+ Migrate product and category SEF urls.
+ Migrate images in product description to target store.