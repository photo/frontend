OpenPhoto / Installation sous FreeBSD + Nginx
=======================
#### OpenPhoto, un service de photo pour les masses

## OS: FreeBSD 9.0+

Ce guide vous explique comment installer sur un serveur OpenPhoto sous FreeBSD avec Nginx

----------------------------------------

### Pré-requis

#### Bases de données et Cloud

##### MySQL
You'll need to provide credentials for a MySql database. If the database doesn't already exist it will be created. If the user doesn't have `CREATE DATABASE` permissions then make sure it's already created.

OpenPhoto necessite une base de donnée. Si la base n'existe pas et que l'utilisateur MySQL n'a pas le droit d'executer la commande `CREATE DATABASE`, assurez vous que la base existe deja

##### Amazon Web Services
Si vous allez utiliser 'Amazon Web Services', vous aurez besoin de vous y identifier.

* http://aws.amazon.com/simpledb/
* http://aws.amazon.com/s3/

#### Configuration de l'environement.
Configuration et compilation de _NGinx_ _PHP-FPM_ et _CURL_ avec quelques modules.

##### _NGinx_

Compiler _NGinx_ avec les options ci-dessous :
HTTP_MODULE
HTTP_CACHE_MODULE
HTTP_GZIP_STATIC_MODULE
HTTP_REWRITE_MODULE
HTTP_UPLOAD_MODULE
HTTP_UPLOAD_PROGRESS

    cd /usr/ports/www/nginx
    make config install clean distclean

##### _PHP-FPM_

Compiler _PHP-FPM_ avec les options ci-dessous :
CLI
FPM
SUHOSIN
MULTIBYTE
MAILHEAD

    cd /usr/port/lang/php5-extentions
    make config install clean distclean

##### Extentions php5

Compiler les extentions _PHP-FPM_ avec les options ci-dessous :
BZ2
CALENDAR
CTYPE
CURL
DOM
FILEINFO
FILTER
GD
HASH
ICONV
JSON
MBSTRING
MCRYPT
OPENSSL
PDF
PHAR
POSIX
SESSION
SIMPLEXML
TOKENIZER
XML
XMLREADER
XMLWRITER
XSL
ZLIB

    cd /usr/port/lang/php5-extentions
    make config install clean distclean

Et si vous utilisez MySQL, compiler `php5-extentions` avec `MYSQL MYSQLI`.

Il y a aussi des paquets optionnels (ImageMagick est fortement conseillé, GD2 pose des problème avec OpenPhoto).

    /usr/ports/net/pecl-oauth
    /usr/ports/graphics/ImageMagick-nox11
    /usr/ports/graphics/pecl-imagick
    /usr/ports/graphics/exiftran

----------------------------------------

### Installer OpenPhoto

Télécharger et installer les sources. Nous vous recommandons d'utiliser le dossier `/usr/local/www/yourdomain.com`.

#### Via git clone

    pkg_add -r git-core
    git clone git://github.com/photo/frontend.git /usr/local/www/yourdomain.com

#### Via wget/tar

    cd /usr/local/www
    wget https://github.com/photo/frontend/tarball/master -O openphoto.tar.gz
    tar -zxvf openphoto.tar.gz
    mv openphoto-frontend-* yourdomain.com

Assuming that this is a development machine you only need to make the config writable by the user Apache runs as. Most likely `www`.
Vous aurez besoin de créer les dossiers suivant et de leur donner le droit d'ecriture.

    mkdir /usr/local/www/yourdomain.com/src/userdata
    mkdir /usr/local/www/yourdomain.com/src/html/photos
    mkdir /usr/local/www/yourdomain.com/src/html/assets/cache
    chown www:www /usr/local/www/yourdomain.com/src/userdata
    chown www:www /usr/local/www/yourdomain.com/src/html/photos
    chown www:www /usr/local/www/yourdomain.com/src/html/assets/cache

----------------------------------------

### Configuration de Nginx et PHP

#### Nginx

Copier le fichier de configuration proposé dans les sources.

    cp /usr/local/www/yourdomain.com/src/configs/openphoto-nginx.conf /usr/local/etc/nginx/sites-enabled/openphoto

Adapter la configuration:
    * Votre domaine (openphoto.domain.ltd)
    * Le dossier d'installation d'OpenPhoto (/usr/local/www/yourdomain.com/src/html)

    /usr/local/etc/nginx/sites-enabled/openphoto

### PHP

Vérifier votre php.ini

    vi /usr/local/etc/php.ini

Les variables ci-dessous doivent etre défini de cette maniere.

    file_uploads = On
    upload_max_filesize = 16M
    post_max_size = 16M

Relancer les services, OpenPhoto est maintenant disponible via votre navigateur.

    /usr/local/etc/rc.d/php-fpm restart
    /usr/local/etc/rc.d/nginx restart

### Ouvrez http://openphoto.domain.ltd dans votre navigateur.

Maintenant vous êtes prêt à lancer votre site OpenPhoto. Acceder a votre site OpenPhoto via votre navigateur sur votre hôte et vous serez redirigé vers un écran de configuration. Vous aurez besoin de vos informations de compte de Cloud pour continuer.

Une fois que vous aurez suivie les 3 étapes OpenPhoto sera en service.
Si pour une raison quelconque vous voulez relancer le _setup_, vous devrez supprimer le fichier de configuration généré et rafraichir votre navigateur.

    rm /usr/local/www/yourdomain.com/src/userdata/configs/yourdomain.com.ini

**ENJOY!**
