# erp

A framework to facilitate ERP (Enterprise Resource Planning) solutions

This framework is an in-house tool built to create and design ERP solutions. It also consist of a web portal to handle administration of MySQL server which is almost equivalent to MyPHPAdmin.

## Install

### On Windows

To host a PHP server and a MySQL database on Windows, one simple method would be to install [WAMP server](https://en.wikipedia.org/wiki/WampServer) ([from Bitnami here](https://bitnami.com/stack/wamp/installer) on your computer. During the installation, you will be asked to provide the root `username` and `password`, be sure to store them somewhere safe as this framework will need use it while starting up.

### On MacOS

PHP and Apache are installed in MacOS by default. To enable them, some configurations are needed([See here](https://editrocket.com/articles/php_apache_mac.html) or [here](https://websitebeaver.com/set-up-localhost-on-macos-high-sierra-apache-mysql-and-php-7-with-sslhttps) for High Sierra or above). After that, you'll need to install MySQL which can downloaded [here](https://www.mysql.com/downloads/). During the installation, you will be asked to provide the root `username` and `password`, be sure to store them somewhere safe as this framework will need use it while starting up.

## Get started

### Setup `config.php`

After placing this framework into your apache server folder, you will need to create a file name `config.php` within the `includes/php` folder.
You will need to provide the following content for the framework to pick these configurations and operate properly:

```php
define("ROOT_URL", "<your apache server root URL e.g.: 'http://localhost/'>");

define("MYSQL_HOST", "<your MySQL host e.g.: '127.0.0.1>'");
define("MYSQL_USER", "<your MySQL username>");
define("MYSQL_PASSWORD", "<your MySQL password>");

define("TEMP_DIRECTORY", "<your apache server's temporary folder e.g.: '/tmp/'>");

date_default_timezone_set("your preferred timezone e.g.: 'Asia/Taipei'");
```

At this stage, you should be able to access the main page (`<your apache server root URL>/erp`) and the administrator page (`<your apache server root URL>/erp/admin`)

## Create a new system

To create a ERP solution, simply create a folder with the pathname `systems/<your system name>`, and add a file with the pathname `systems/<your system name>/settings.json`.
For simplicity, do bear in mind that the corresponding database shares the same name of the system.
To define the database tables, create your sql files with create commands within the folder with the pathname `systems/<your system name>/tables`. For instance, `systems/<your system name>/tables/users.sql` which consist of a command `CREATE TABLE user (...);`
You will then be able to reset the existing database tables with these settings in the administrator page.
