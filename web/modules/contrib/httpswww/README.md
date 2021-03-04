CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Troubleshooting
 * FAQ
 * Maintainers


INTRODUCTION
------------

This modules lets you easily set your preferred URL with or without the "www"
prefix, and optionally forcing an HTTPS redirect. No code changes required.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/httpswww

 * To submit bug reports and feature suggestions, or track changes:
   https://www.drupal.org/project/issues/httpswww


REQUIREMENTS
------------

This module requires no modules outside of Drupal core, but may require DNS
or server configuration and a valid SSL certificate.


INSTALLATION
------------

Install as you would normally install a contributed Drupal module.
Visit https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

Please note that before changing any configuration you must make sure your site
is accessible for the options you choose. You also need to have a valid SSL
certificate installed for HTTPS to work.

Navigate to Administration » Configuration » System » HTTPS/www settings
to change configuration settings.

Note that if you check the "Enabled redirects" checkbox and don't have the
"Bypass HTTPS/www redirects" permission and if you are on a different URL than
what you have selected, you will be logged out when saving.


Configure the user permissions in Administration » People » Permissions:

 * Administer HTTPS/www redirects
   Users with this permissions will be able to change URL and how users access
   the website.

 * Bypass HTTPS/www redirects
   Users with this permission will be unaffected by the settings and will not
   be redirected.


TROUBLESHOOTING
---------------

 * Users experience an endless redirect.

   - Try to clear the cache. If that doesn't work, see the next point, and
     please report the issue.

 * If you lock yourself out of the site, add the below line to your
   settings.php file and you should have access to your site again using the
   URL you used previously.

       $config['httpswww.settings']['enabled'] = FALSE;


FAQ
---

Q: I got logged out when I saved configuration settings. Why is that?

A: It means you have access to change configuration, but you don't have the
"Bypass HTTPS/www redirects" permission. Additionally, if you don't have the
"Bypass" permission and you are on a different URL than the one you select, you
will get logged out due to the URL being considered as different domain.


MAINTAINERS
-----------

Current maintainers:

 * enzipher - https://www.drupal.org/u/enzipher

This project has been sponsored by:

 * {deuxcode} - https://www.deuxcode.com
