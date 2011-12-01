# This file can be run to have some automatized tasks done before running the software

BASEDIR=$(dirname $0)
chmod -R 777 $BASEDIR/../web/app/tmp
chmod 777 $BASEDIR/../web/app/webroot/uploads
