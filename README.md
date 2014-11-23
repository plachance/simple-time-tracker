README
======

Minimalist time tracking software.

Requirements
------------

PHP 5.3 and up.
PostgreSQL 9 and up.

Installation
------------

* Copy `.htaccess.dist` to `.htaccess`. Edit to your setup.
* Copy `/protected/application.default.php` to `/protected/application.php`. Edit to your setup.
* Create a database as you specified in `application.php`.
* Download, extract and run ```php composer.phar install```
* Run ```php console.php orm:schema-tool:create```.
* Create users with ```php console.php stt:user:create```.
