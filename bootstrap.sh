#!/usr/bin/env bash

# crate swap
sudo /bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
sudo /sbin/mkswap /var/swap.1
sudo /sbin/swapon /var/swap.1

sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get upgrade -y

echo "
   Installing MySql - dbName: kptask | user:root | password:rootpass
***************************************************************"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password rootpass"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password rootpass"

sudo apt-get install -y mysql-common mysql-server mysql-client
mysql -u root -prootpass  -e "CREATE DATABASE kptask;"

echo "
   Installing PHP 7.2...
***************************************************************"
sudo apt-get install -y zip unzip imagemagick
sudo apt-get install -y nginx
sudo apt-get install -y curl git redis-server
sudo apt-get install -y software-properties-common python-software-properties
sudo apt-get install -y php7.2 php7.2-zip php7.2-fpm php7.2-curl php7.2-mysql php7.2-gd php7.2-intl php7.2-xsl php7.2-mbstring php7.2-soap
sudo apt-get install -y php7.1-mcrypt
sudo apt-get install -y php-xdebug xpdf php-redis php-imagick

cd /vagrant/

sudo bash -c "echo '
127.0.0.1       localhost

# The following lines are desirable for IPv6 capab
::1     ip6-localhost   ip6-loopback
fe00::0 ip6-localnet
ff00::0 ip6-mcastprefix
ff02::1 ip6-allnodes
ff02::2 ip6-allrouters
ff02::3 ip6-allhosts
127.0.1.1       ubuntu-xenial   ubuntu-xenial
' > /etc/hosts"

sudo bash -c "echo 'server {
    listen 80;
    sendfile off;
    root /vagrant/kptask/public;
    index index.php index.html index.htm;
    server_name kptask.local;
    location / {
         try_files \$uri \$uri/ /index.php?\$args;
    }
    client_max_body_size 16M;
    client_body_buffer_size 2M;
    proxy_connect_timeout       300;
    proxy_send_timeout          300;
    proxy_read_timeout          300;
    fastcgi_connect_timeout 300;
    fastcgi_send_timeout 300;
    fastcgi_read_timeout 300;

    fastcgi_buffers 16 16k;
    fastcgi_buffer_size 32k;

    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param APPLICATION_ENV development;
    }
}' > /etc/nginx/sites-available/kptask.local"

sudo ln -s /etc/nginx/sites-available/kptask.local /etc/nginx/sites-enabled/kptask.local
sudo service nginx restart

echo "
   Add this line to hosts file :
   192.168.5.28    kptask.local
"