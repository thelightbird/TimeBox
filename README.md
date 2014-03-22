TimeBox
=======

TimeBox is an online file storage with version control developed with the Symfony2 framework.


## Install on Windows

1. Download and install [Wamp Server](http://www.wampserver.com/)

2. Edit following files:
  * C:\wamp\bin\php\php5.4.12\php.ini
  * C:\wamp\bin\apache\Apache2.4.4\bin\php.ini

  Uncomment theses lines:
  ```
extension=php_curl.dll
extension=php_intl.dll
extension=php_mbstring.dll
extension=php_openssl.dll
  ```
3. Restart Wamp: left click on the wamp icon → Restart All Services

4. <kbd>Windows</kbd><kbd>Pause</kbd> → Advanced settings → Environment variables →	System Variables → Add to PATH :
  `;C:\wamp\bin\php\php5.4.12`

5. Install [Git Preview](http://msysgit.github.io/)

6. Launch Git Bash

  ```bash
cd "C:\wamp\www"
git clone https://github.com/thelightbird/TimeBox
cd TimeBox/source/TimeBox-website
cp app/config/parameters.yml.dist app/config/parameters.yml
  ```
7. Edit file app/config/parameters.yml:

  ```bash
database_name: timebox
database_user: root
database_password: <password from step 1.>
  ```
  Tip: if your password contains a #, it must be put between quotes.
8. Type the following commands:

  ```bash
mkdir app/cache
mkdir app/logs
mkdir vendor
php composer.phar install
  ```

9. Install Database



## Install on Linux (Ubuntu)

1. Type the following commands:

  ```bash
sudo apt-get install apache2 php5 mysql-server libapache2-mod-php5 php5-mysql php5-intl phpmyadmin
  ```
  Tips:
  * Server to reconfigure: apache2 (Press <kbd>Space</kbd><kbd>Tab</kbd><kbd>Enter</kbd>)
  * Reconfigure database ? → Yes

2. Type the following commands:

  ```bash
cd /var/www
git clone https://github.com/thelightbird/TimeBox
cd TimeBox/source/TimeBox-website
mkdir app/cache
mkdir app/logs
setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs
cp app/config/parameters.yml.dist app/config/parameters.yml
  ```

3. Edit file app/config/parameters.yml:

  ```bash
database_name: timebox
database_user: root
database_password: <password from step 1.>
  ```
  Tip: if your password contains a #, it must be put between quotes.

4. Type the following commands:

  ```bash
mkdir vendor
php composer.phar install
  ```

5. Install Database



## Install Database

1. Go on [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

2. Connect and create `timebox` database

3. Open a terminal:

  ```bash
cd TimeBox/source/TimeBox-website
php app/console doctrine:schema:update --force
  ```


    
