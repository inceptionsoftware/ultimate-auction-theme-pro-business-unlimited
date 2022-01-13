=== Ultimate WooCommerce Auction Pro ===
Contributors: nitesh_singh
Tags: woocommerce auction, woocommerce auction plugin, woocommerce auction theme, woocommerce bidding
Requires at least: 4.6
Tested up to: 5.8.3
Stable tag: 2.2.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Awesome plugin to host auctions on your WooCommerce powered site and sell your products as auctions.

== Description ==

Ultimate WooCommerces Auction plugin allows easy and quick way to add your products as auctions on your site.
Simple and flexible, Lots of features, very configurable.  Easy to setup.  Great support.

= Plugin Features =

    1. Registered User can place bids 
	2. Ajax Admin panel for better management.
    3. Add standard auctions for bidding
    4. Buy Now option    
    5. Show auctions in your timezone        
    6. Set Reserve price for your product
	7. Set Bid incremental value for auctions
	8. Ability to edit, delete & end live auctions
	9. Re-activate Expired auctions
	10. Email notifications to bidders for placing bids
    11. Email notification to Admin for all activity
    12. Email Sent for Payment Alerts
	13. Outbid Email sent to all bidders who has been outbid.
	14. Count Down Timer for auctions.	
	15. Ability to Cancel last bid 
    and Much more...

== Installation ==
= IMPORTANT = 

Please backup your WordPress database before you install/uninstall/activate/deactivate/upgrade Ultimate WooCommerce Auction Plugin

Pre-installation note: You need to install WooCommerce plugin before proceeding further. It is available here - https://wordpress.org/plugins/woocommerce/
Once you have installed kindly refer to their documentation here for full setup - https://docs.woocommerce.com/documentation/plugins/woocommerce/marketplace/

1. 	Upload the folder ultimate-woocommerce-auction with its contents to wp-content/plugins/

2.  Activate the plugin.

3.  After you have setup WooCommerce and activated this plugin, you should add a product. 

4. 	While adding product, choose "product data = Auction Product". Add data to relevant fields. 

Your auction product should now be ready and displayed under "Shop" page. If you have problems please visit the [support](http://docs.auctionplugin.net) for technical questions, documentation and FAQs.

== Screenshots ==

1. Admin: Create auction product
2. Admin: Create auction product with data
3. Admin: Plugin Settings
4. Frontend: Shop Page
5. Frontend: Single product page example


== Changelog ==

= 2.2.1 =

1. New Feature:

	We have added the functionality to auction products for immediate auto-relist. Now, the admin can automatically relist auction products with zero hours.

	We have added retain bid functionality for unlogged users. If an unlogged/unregistered user tried to place a bid with a specific value. "Login/Register" notice displayed on the product page. When the user login/registers on the product, the specific bid value is auto-filled in the bid input box of the auction product.

2. Improvement:

	We added a category parameter in the shortcode of the auction plugin. Now, the admin can display auction products with a specific category.

3. Fixes:

	We have fixed the issue of the malicious code pattern in the EDD_SL_Plugin_Updater.php file.

	We have fixed the multiple SMS issue in the auction plugin. We have changed the hook for the Won SMS Message.

	We have fixed the timing issue of the auto relist auction product. When the auction product is relisted, the timer displays the correct time.

	We have fixed the tax issue on the checkout page. If the full bid amount of the won auction product is auto-debited, tax and other charges will display on the checkout page to pay.

	We have removed the "Current Bid" sorting from the shop page. We will implement the sorting functionality for auction products on the default price sorting of the woocommerce plugin. 

	We have fixed warning and errors with PHP8.

= 2.2.0 =

1. Improvement:

	Won Notification Email was under rare case was sent to wrong winner. This problem was extremely rare and we could not reproduce the scenario but we decided to have different email settings/templates for admin and bidder. This approach will elimniate any possibily to show different usernames and the auction product names in the email header and body. 

2. Fixes:
	
	Correct Float Value will now be displayed once "Auto Debit Via Stripe" is debited on checkout page. If the float value is auto debited by the auction plugin, the correct value will be displayed in "Auto Debit Via Stripe" on checkout page.
	
	We have fixed "Your payment is already done!! please place the order" notice issue on checkout page. This notice will be only displayed on checkout page when the value of the auction product has been auto debited.
		
	"Email Heading" was not modifying for "Ultimate Auction - Auction Won" email notification - When admin add custom text in "Email Heading" field from woocommerce -> settings -> Emails -> Ultimate Auction - Auction Won -> Manage, it will change the heading of bid won email notification.

	"Checkout All" button issue - When the "Checkout All" button is clicked from my-account -> Auctions -> Bids Won, it will checkout all winning auction product at a time.

	Timer issue on chrome browser with "Finland" language - The countdown timer will display correctly in the Chrome browser when the "Finland" language is selected on the site.

	CSS conflict issue with the user registration plugin - The data picker will display properly in the auction plugin when the user registration plugin is active on site.

	We have fixed the SKU filter conflict issue with auction plugin. Now, "In stock" and "Out of Stock" filter will work properly in product menu.

	Pagination issue in WP Admin > Auctions > Auction > future auctions - Now all future auction products will display with pagination under the auctions menu.


= 2.1.9 =

1. New Feature:

	Export Expired auctions CSV - Admin can export the CSV file for all expired auctions.

2. Improvement:

	Bidding Notification Email - Admin can set separate subject for admin email and bidder email.

	We have sanitized, validated, and escaped all functions using POST/GET/REQUEST/FILE calls for meeting security guidelines of WordPress.org.
	
	Proxy Bidding: Plugin now automatically proxy bids the "Reserve Price" on behalf of user if his max bid is above reserve price. 
	
	The stripe library has been updated for compatibility with php8.
	
	Credit Card Auto Debit Addon:
	
		Added more checks to ensure under no condition multiple debit can be possible when auction is expired.

	
3. Fixes:

	Added missing string translation for auction plugin in admin dashboard.

	Implemented AJAX functionality for "username is winning" text on shop page,product page and widgets.
	
	Credit Card Auto Debit Addon:

		Fixed Autodebit issue with relisted auction product.

		Fixed Autodebit issue with duplicate auction product.

	Fixed timer issue on shop page when auction product is extended. When the auction product is extended, the countdown timer will be updated on the product page and shop page without refresh.

	Fixed buyer's premium calculation issue - When the admin deletes the highest bid in the auction product, the auction plugin will calculate the buyer's premium according to the second highest bid.

	Fixed "Total Auto Debit Via Stripe" display issue on checkout page. Value of "Total Auto Debit Via Stripe" will be displayed correct on checkout page when the include tax is selected from the woocommerce setting.
	
	We have fixed below issue with PHP8.
	-> Fixed warning and errors with PHP8.
	-> Fixed AJAX bid place issue with PHP8.

= 2.1.8 =

1. Fix:
	
	There was a warning appearing, it has now been fixed. Warning: Trying to access array offset on value of type bool in /home/willimda/public_html/stage/wp-content/plugins/ultimate-woocommerce-auction-pro/includes/admin/class-uwa-admin.php on line 2403

	We had observed that plugin was not installing properly on PHP 8. This has been resolved now.


= 2.1.7 =

1. Fix: 

	NOTICE was being displayed inside WP Dashboard. We have attended and fixed it.
	
= 2.1.6 =

1. New Feature:
	Email: Watchlist Notification
		We have added a new email notification which will send email to the user who has added that product to his watchlist if any user places a bid on that product.

2. Improvement:
	Proxy Bid cases have been updated
		Plugin now automatically proxy bids when auction ends for user if user's "maximum bid price" is more than reserve price to make him a winner. Previously, the auction ended without winner if the actual bid is not more than reserve price no matter what maximum bid would be.
		Plugin will now automatically proxy bid and jump the bid of User 1 if User 2 matches "maximum bid" of User 1. It will place bid for User 1 of amount same as maximum bid.

3. Improvement:
	Separate Start and End Date columns have been added inside Manage Auction > Expired Auction and inside My Account page.

4. Fix:
	Error occured when admin was converting auction product to simple product. This has been fixed.


= 2.1.5 =

1. Fix

	Wordpress Errors were displayed while adding "Simple Products". This has been rectified.



= 2.1.4 = 
1. Fixes
 
	Countdown Timer - There was an issue of display on safari browser and for browsers on IPhone. This has been fixed.


= 2.1.3 =

1. New Feature

	Direct Bid displays a new drop down with bid values. These bid values are in accordance with the increment value. 
	
	Hide or Display Custom/Direct Bid portions - New settings have been introduced for hiding or displaying these fields.
	
	Bid Increment can be now set globally - New global setting has been introduced.
	
	Global setting for blocking/unblocking users for bidding.
	
2. Improvement

	Winner Information can be viewed in shop, detailpage and widgets
	
	Winning and Losing labels can be hidden/shown and set custom labels for auction products on shop page.
	

3. Fixes

	Auto Debit of Winning bid and Buyer's Premium was not working when admin was ending an auction product. This has been fixed.
	
	Related Product for Auction Products were showing wrong products. We have changed this query.


= 2.1.2 = 

1. Improvement

	Credit Card Auto Debit: We observed a case where when "Aajx Bidding" setting has been turned on then sometimes duplicate calls were made to Stripe API for automatic Debit. We have rectified this and addressed this issue.
	
2. Fixes

	Auction Entries inside Auctions > Live Auction or Expired Auction was sometimes not showing properly. This has been fixed. 

= 2.1.1 = 

1. Fixes

	Countdown Timer - There was an issue of display on safari browser and for browsers on IPhone. This has been fixed.

= 2.1.0 = 

1. New Feature:

	Buyer's Premium feature is now available at product level too.

2. Improvement

	Ending Soon Email - New option has been added in this email to send this email to users who have this product in their watchlist. 

3. Fixes:

	Countdown Timer will now show time based on the timezone selected on the website. Previously, it was showing local computer time of the user which was causing confusion.

	Winner email's "Pay Now" button was earlier redirecting to Wordpress default login Page. It has been rectified and is now redirecting to "My Account Page".

	We had found an issue with checkout process while using "WooCommerce Deposit" plugin. This has been fixed.
	
	Users can now search by SKU numbers for auction products too. 


= 2.0.9 = 

1. Fix:

	We have fixed following Notice which was appearing after previous version was released.
	
		Notice: Undefined index: ua-auction-cron in /home/runcloud/webapps/33-forever/wp-content/plugins/ultimate-woocommerce-auction-pro/ultimate-woocommerce-auction-pro.php on line 308

2. Improvement

	New plugin notification to set your server cron is now displayed instead of four notices. And we have documented the process to set server cron in this article - https://docs.auctionplugin.net/article/123-set-your-auction-cron-job


= 2.0.8 =

1. New Feature:
	
	Server Cron Job: We have developed four background cron settings using which auction status, email, sms will be triggered even when there are no traffic on the website. These background cron jobs will ensure that events associated with auction products happen at exact time. You can go through this article to see how to set this in your hosting panel - https://docs.auctionplugin.net/article/68-installing-ultimate-woocommerce-auction-pro-plugin
	
	New Addon: Currency Switcher With Aelia: We have developed a new addon which works with Aelia Currency Switcher plugin to show auction prices in multiple currencies.

2. Fix:

	Auction products were not being added in the checkout page when visitor was clicking "paynow" or "getitem" button in Winner Email. This issue has now been fixed.
	
	Addon: Credit Card Auto Debit: State Field was missing inside WooCommerce My Account Registration form. This has now been added.

3. Improvement:

	Error messages shown by our plugin were being displayed on top of screen which were going un-noticed by visitors. We have now added a slide feature which will slide the page automatically to the top so that visitor does not miss the error messages.


= 2.0.7 =

1. Improvement:

	Addon: Credit Card Auto debit
		We have improved the code and introduced a flag which would prevent to send multiple API requests to Stripe to auto debit winning amount if for any reason the API response was unsuccessful. Hence plugin would send this request only once whether the response is successful or unsuccessful. This will ensure that multiple amount is not detected.
	
= 2.0.6 = 

1. Fixes 

	Addon: Credit Card Auto debit
		For amounts having decimals, plugin was receiving "Invalid integer" error from Stripe API. We have now fixed this issue and it works for decimal values with two places.
		
	Multiple Email
		We had received an issue from customer that multiple winner emails, sms and "auto debit" requests were being sent out to winners. Though after multiple tests we were not able to reproduce the problem but we have implemented few code which will ensure that recursive calls to emails, Twilio and Stripe API are not made.
	
2.	Improvement:	

	Addon: Credit Card Auto debit
	
		Added Payment status and date inside "Edit Product" screen when debit has been successful.
	
	Logging
	
		WooCommerce Log - We have added Auction ID for logs generated for auction products.
		
= 2.0.5 = 

1. Fixes

	For "Credit Card Auto Debit" Addon, since we use base configuration of "WooCommerce Stripe Payment Gateway" plugin, this plugin has added prefix to Customer ID stored in DB due to which user's payment method was not stored properly and transaction for auto debit were not happening properly. We have now updated our plugin to match the prefix and fixed this issue.
	
	If anyone has installed Elementor plugin on their website and will make a WooCommerce product page (for auction) using it, then they can choose "add to cart" button to show bidding portion. This is a temporary fix until we develop full compatibility for Elementor.
	
	
= 2.0.4 =

1. New Features

	Plugin is now compatible with WooCommerce Product Table (WCPMT) plugin and we have added a new shortcode which you can use with WCPT plugin to show count-down timer on table's column. Here is the shortcode - [countdown id="%product_id%"] 
 
2. Improvement
	
	We have added a new bid button - "Directly Bid" on auction product detail page. You can enable this setting from "Auctions > Settings > Display > "Enable Specific Fields" > Direct Bid Button. If it is already active then save it so that its value can be saved to database. So now, if you enable it then your users will get two options to bid "Directly Bid" and "Custom Bid".
	
	We have changed name of "Place Bid" button to "Custom Bid". Please update your translation file accordingly.
	
	Auction sorting options are added to default product sorting options so user can set any auction option as default.
	
	Bid Increment for Fixed value now supports values less than 1 (e.g. 0.2, 0.7) but 0 is not allowed for both admin and vendor auctions

3. Fixes
	
	WooCommerce's Product Category Widget and shop page was showing wrong auction product count for categories. This has been fixed now.
	
	Auction Product Detail Page was not displaying properly for DIVI Themes. This has been fixed now.
	
	Shop manager User role can view bidder names when masking is on.
	
	There were few issues found when our plugin was working with WPML plugin. These have been fixed.
	
	
= 2.0.3 = 

1. Fixes

	Anti Sniping was not working properly after we had used new Wordpress Timezone function. This has been fixed.
	
	Ending Date displayed on auction product detail page was incorrect. This has been fixed. 
	
	
= 2.0.2 = 

1. New Features

	New Addon: Offline Dealing of Buyer & Seller - This Addon will share contact details of buyer and seller with each other so that they can do offline dealing.

2. Improvement

	Plugin now uses latest WP functions which were introduced in WP 5.3 - https://make.wordpress.org/core/2019/09/23/date-time-improvements-wp-5-3/
	
	SMS Notification Addon: We have added a provision for old users to update their phone numbers and country inside their My Account page. Old users will get a message at the time of placing bid - "Please enter Phone Number and Country details before placing the bid"
	
	Credit Card Auto Debit Addon: Credit Card form has been modified so that it clearly displays all the relevant fields of credit card. 
	
	New configuration to show reserve price on auction detail page. 
	
3. Fixes

	Categories were showing wrong "auction product" count for shop page. This has been fixed now.
	
	WCFM Front End Manager Dashboard > Add auction had few Javascript and CSS enhancements for Wordpress 2020 Theme.
	
	Add/Edit Auction Product was not showing "General" Tab option. This has been fixed now.
	
	Notices and Warning were being displayed in few screens and this has been fixed now.


= 2.0.1 = 

1. New Features

	Assign a new winner for an expired auction by deleting bid of the existing winner. Admin can now do this if they have not received payment from their existing winner. Once they delete existing winner, then highest (normal bidding) or lowest (reverse bidding) most bidder next in line will be the winner and email notification will be sent to that person. 
	
	Choose your own winner. Auction owners can now choose their own winners. They get an option to do this inside "Edit Product".
	
	New Email notification - "Ultimate Auction - Auction Lost" has been added for proxy and silent bidding. This email notification once enabled will send notification to all bidders who lost in bidding once auction expires. 
	
	New configuration has been added for admin to allow admin and "auction owners" to bid on their own auction. By default both can bid. This has been added inside "WP Admin > Auctions > Settings > Auction".
	   

2. Improvement

	Block/Un-block Users to place bid now can now be done in bulk. We have added this option.
	
	Resend button for sending winning email has been provided inside "Auctions" and "Bids" screens. 
	
	Email "Ultimate Auction - Auction Won" now has new checkbox to send this email notification to Admin and Auction Owners or Sellers. 	
	
	Under WP Admin > Auctions > Settings > Display > Auction Detail Page, we have provided many configuration to enable/disable various texts displayed on "Auction Product Detail Page". This will help admins to choose what they want to show and what to hide.
	
	We now display currency sign right before the bid text field on Auction Product Detail page.
	
	Silent Auction now has a new configuration to enable/disable outbid email notifications.
	
	When any auction product has only "buy now" price enabled and no "bidding" then we have shown buy now price on auction list page. Previously it showed "Place Bid" text.

3. Fixes

	Outbid email was being sent for users who were outbiding their own bids. This has been fixed now. 
	
	Javascript error occurred due to single quote which has ben fixed by adding addslashes function. 
	
	There was an issue caught while imputing variable bid increment field during Import. This has now been fixed.
	

= 2.0.0 =

1. New Addons added

	Collect Credit Card and automatically debit Bid Amount
	
	SMS Notification using Twilio
	
	Buyer's Premium
	
= 1.1.4 =

1. Fix

	My Account > Auctions page was throwing 404 Error when Permalink Setting was set to Plain and other values. this has been fixed.

2. Improvement

	Redirection to Auction Detail page - When any visitor (without logged in) used to visit auction product detail page, they were prompted to login/Register which upon click opens "My Account" page. And then after login or registration, it did not redirect back to Auction Product Detail page. We have now included that feature.

	HTTP POST request was being called with each page load to check expiration status. This was redundant and not required and thus have been removed.


= 1.1.3 =

1. New:

	PRO Version shortcodes will now have pagination option.

2. Fix:
	
	WooCommerce Product Search Widget was not displaying auction products. This has been fixed.

	Bids Menu was not displaying bids for "All" options. This has been fixed.

	When you will convert "Future Auction" to "Live Auction" then it would happen without any error.

3. Improvement

	When user or any auction is deleted then associated auction data for user or auction will also be deleted.

	English Sentences for few emails were edited.


= 1.1.2 = 

1. New:
	
	Bulk Import feature for auction products - https://docs.auctionplugin.net/article/91-5-how-to-bulk-import-auction-products

	WPML Compatible

2. Fix:
	
	Deleting auction was causing type E_ERROR. This has been fixed.
	
	WCFM Ultimate version had a conflict which we have resolved now.


= 1.1.1 = 

1. New:
	Automatic Relisting: This feature will enable auctioneers to relist their auctions automatically based on conditions they choose like
		If Winner has not paid
		If Auction expired without bids
		If Reserve Price was not met

	Variable Increment: This feature will allow auctioneers to mention different incremental value for different bid amounts. Previously, auctioneer were only able to mention a fixed increment value based on which subsequent bids were placed. This will help auctioneers to get good bidding amount for their products.

	Block/Un-Block User to place bid: Admin can now block any registered user to place their bid. If blocked then user will not be able to place bid and will get an alert message. This will help auctioneers to block users who win but dont pay final bid amount.

	Instant Bidding: Admin gets a configuration under Auction Settings which when enabled will place bid instantly using AJAX based requests without page refresh. 

	Show Countdown timer on shop page: Admin gets a new configuration to enable timer for auction products on shop page. Configuration is under: Auction->Settings->Display Settings Tab -> Shop Page Setting -> Enable/Disable countdown on Shop(Product loop) Page)

	Hide "Product Condition" field on Auction detail page: New configuration has been added under Settings > Display Settings to hide this field. 

2. Improvement:

	Plugin Menu: We have organized plugin menu in better way so that it is more convenient for admin to access all settings and information in it.

	Anti-Sniping/Soft Close: We have added two options for this feature. One option will recursively extend expiration time if bids are placed during a specific time interval. Second option will extend expiration only once and will send email to all bidders intimating this extension. Admin can now choose what behavior they want.

	Add Auction Product form now has options to choose bidding, buy now or both.

	Multiple checkout option for users: If any user has won multiple auction products, he/she had to individually click "Pay Now" link under their My Accounts > Auctions > Won Bids to checkout that product and pay. Now, plugin offers a multiple check out option which will add all their won products to their WooCommerce's checkout page and they can pay for them together. 

	We have now renamed "Scheduled", "Pending" words to "Future" so that it is easier for admin to understand future auctions.

3. Fix:

	Shop page was not listing auctions if admin edited any auction. This bug appeared due to wrong query implementation but is now fixed.

	Multiple warnings with the plugin has been fixed.
	
	
= 1.1.0 =

1. New:

	PRO version will now let users add auction using a popular multi-vendor plugin - "WCFM Multi-Vendor Marketplace Plugin by WC Lovers". We proudly announce that PRO version now integrates with this plugin.
	
	Admins who want their users to add auction can refer this url - https://docs.auctionplugin.net/article/89-how-your-users-can-add-auction
	
	Article about WCFM Multi-Vendor Marketplace is here - https://docs.auctionplugin.net/article/90-wcfm-marketplace-by-wc-lovers

2. Improvement

	Negative bid values wont be accepted.
	
	Buy now can be disabled if user enters ZERO value for it.

3. Fix:

	Alert confirmation while placing bid had issues with page reload due to which "Cancel" button was not cancelling the bid. This has been fixed.		


= 1.0.4 =

1. Proxy Bidding: Outbid email is sent to bidder when bid from other users is higher or lower than  max/min bidding amount depending on normal or reverse auction type.
 

2. Silent Auction:Incorrect bidding value was being displayed to the logged in user when bid was being placed. This has now been fixed.
 

3. Private Message notifications: Recipients input field when had {seller} placeholder, then this email notification was not working. This has now been fixed.


= 1.0.3 =

1. Improvement:

		Private Message notifications: Email notification now has a new placeholder: {seller} that can be entered inside “Recipients” field for sellers to receive this notification from users.

2. Fix:

	Silent Auction
		Removed “Winning” and “Losing” labels as these were silent auctions.
		
	Wrong bidding value in email for subsequent bidders were shown. This has been fixed.
	
	
= 1.0.2 =

1. New:

Silent auction: New admin configuration added under Auctions > Settings > Auction Settings > Silent Auctions > “Restrict users to bid only one time”. This when activated will allow user to bid only one time.
 

New admin configuration under Auctions > Settings > Auction Settings > Extra Settings > Enable an alert box. This will show an alert box when user will place a bid.
 

Proxy Bidding: New admin configuration added under Auctions > Settings > Auction Settings > Proxy Auction > “Disable Proxy Bidding to happen for amount less than Reserve Price too.”
 

New email notification is sent to bidder when his/her bid is deleted.

2. Improvement:

Bid Sniping: We have implemented this logic to occur only once and send an ending soon email to all bidders when the auction is extended due to it.
 

Relist Email was missing reason for relisting. This has been added.
 

3. Fix:

Silent Auction

Bid notification on the auction detail page was showing the outbid value. Since it is a silent auction, this has been removed.
 
Ending Soon Email was arriving late. There was a timezone conversion issue due to which this email was sent at inappropriate time. This has been fixed.
 

Proxy Bidding

Outbid Email for same users were coming earlier. This issue has been fixed.

When highest bidder will place higher bid then his maximum bid will increase. Previously bid was being placed.

Few text were missing translation. They have now been included.

= 1.0.1 =

1. New Feature - We have added new configuration: "Disable the Buy It Now option once bidding has reached the reserve price". Enjoy this feature.

2. Fix - Few texts inside plugin were missing to be translated. We have tested and included all texts. You can now translate all using LocoTranslate.

3. Fix - Payment link in winning email had a query issue. This has now been fixed and you wont see "Empty Cart" message.

4. Fix - Bid value placed by automated bidding appearing inside Auction Detail page were missing currency format set by WooCommerce. This has now been fixed.

= 1.0 =
Initial Release