# Magento 2
Oplati payment module for magento 2.X

Manual install
=======

1. Download the Payment Module archive, unpack it and upload its contents to a new folder <root>/app/code/Oplati/Oplati/ of your Magento 2 installation

2. Enable Payment Module

	```bash
	$ php bin/magento module:enable Oplati_Oplati --clear-static-content
	$ php bin/magento setup:upgrade
	 ```
3. Deploy Magento Static Content (Execute If needed)

	```bash
	$ php bin/magento setup:static-content:deploy
	```