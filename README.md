TwmDev
======

ZF2 module to override settings with an additional config file when on development server.

Features
------------
* Defines a DEV constant to check whether or not you're on development
* Allows you to have a development config file which will override every other setting

Installation
------------

1. First we have to make sure your development server is setup properly.
Open **/etc/apache2/envvars** on your development server and add the following line to the bottom of the file:

  ```
  export APACHE_ARGUMENTS="-D dev"
  ```

2. Restart your apache.
3. Add the following line to your composer file:

  ```json
  "require": {
      "thewebmen/twm-dev": "~1.0.0"
  }
  ```
  
  Or use the following command:
  ```sh
  php composer.phar require thewebmen/twm-dev ~1.0.0
  ```
  
4. Add the module to the **end** of your array in application.config.php
  ```php
  return array(
      'modules' => array(
          /*...*/
          'TwmDev',
      ),
  );
  ```
  It is very important this module is at the end, for it to properly override all settings with your development settings.

Usage
------------

Create a **config/autoload/dev.php** and put any settings in there that should be used in development (like different database/credentials or disable caching).

Because PHP CLI has no idea of the apache or httpd envvars it won't automatically recognize you're on a development server. For this I created a flag **--dev** which you can add when using the ZF2 CLI.
That flag will tell TwmDev you're on development and load the development config.

