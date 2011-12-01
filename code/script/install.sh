# This file can be run to have some automatized tasks done before running the software
# TODO: test this file!

BASEDIR=$(dirname $0)

echo "Installing or updating mongodb"
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10

echo "deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen" >> /etc/apt/sources.list
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10
sudo apt-get update 
sudo apt-get install mongodb-10gen

echo "Changin permissions to directories"
chmod -R 777 $BASEDIR/../web/app/tmp
chmod 777 $BASEDIR/../web/app/webroot/uploads

echo "Installation finished"

echo <<<EOF
After registering, set your user as an admin running this command in your mongodb:

db.users.update( { username:"yourusername" }, { $set: {admin:1} } );

EOF
