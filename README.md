Setting up for local testing 

These instructions are based on Arch Linux, adjust directions as necessary: 
---

Install apache 
``` sudo pacman -S apache ```
(This will set our http root at ``` /srv/http ```

Update ``` /etc/httpd/conf/httpd.conf ``` to allow .htaccess by changing the AllowOverride value under <Directory "srv/http"> to have the value ``` All ```.

Install php 
``` sudo pacman -S php ``` 
``` sudo pacman -S php-apache ```

Update ``` /etc/httpd/conf/httpd.conf ``` to enable php
See https://wiki.archlinux.org/index.php/Apache_HTTP_Server#php for details

Make sure to install and enable the gd.so plugin

Install Mysql.  MariaDB is fine: 
https://wiki.archlinux.org/index.php/PHP#MySQL.2FMariaDB 

Here's the Boothi.ca-specific part 
---

Set up symbolic links 

These are necessary to allow open-source contributors to run a version of the website on localhost, using the code from boothsite-public-fillers* and for internal Boothi.ca members to do the same, but using non-filler, proprietary code.

\* https://github.com/bradsk88/boothica-public-fillers

In the following script, BASE_DIR is the directory into which you clone the boothica-public and boothica-public-filler repos.  There will be a folder for each of the repos in BASE_DIR.  I have renamed these to "public" and "fillers" for ease of typing.

```
BASE_DIR = /home/bradsk88/boothica_dev 
ln -sfn $BASE_DIR/public/index.php /srv/http/index.php 
ln -sfn $BASE_DIR/public/common/ /srv/http/common 
ln -sfn $BASE_DIR/public/framing/ /srv/http/framing 
ln -sfn $BASE_DIR/public/lib/ /srv/http/lib 
ln -sfn $BASE_DIR/fillers/utils /srv/http/utils 
ln -sfn $BASE_DIR/fillers/common/db_auth.php $BASE_DIR/public/common/db_auth.php 
ln -sfn $BASE_DIR/fillers/common/db_auth.php $BASE_DIR/public/common/boiler.php 
ln -sfn $BASE_DIR/public/css/ css
ln -sfn $BASE_DIR/fillers/media/ media
ln -sfn $BASE_DIR/public/_mobile/ _mobile
ln -sfn $BASE_DIR/public/livefeed/ livefeed
ln -sfn $BASE_DIR/fillers/common/internal_utils.php $BASE_DIR/public/common/internal_utils.php
ln -sfn $BASE_DIR/public/user-registration user-registration
ln -sfn $BASE_DIR/public/comment comment
ln -sfn $BASE_DIR/public/pages pages
```

Explanation:  
``` ln ``` Linux command used for interacting with links  
``` -s ``` Causes ``` ln ``` to create a ``` symbolic link ```  
``` -f ``` Causes ``` ln ``` to overwrite old ``` symbolic link ```s  
``` -n ``` Causes ``` ln ``` to overwrite old ``` symbolic link ```s that point to directories  
