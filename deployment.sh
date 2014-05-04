#!/bin/bash
# create user/hostname
sudo useradd -r test.xombo.com -d /home/test.xombo.com/ -G www-data
sudo mkdir /home/test.xombo.com
sudo chown -R test.xombo.com:www-data /home/test.xombo.com
sudo -u test.xombo.com chmod -R 710 /home/test.xombo.com
# pull deployment key into ~/.ssh
sudo -u test.xombo.com mkdir /home/test.xombo.com/.ssh
sudo -u test.xombo.com chmod 700 /home/test.xombo.com/.ssh
sudo -u test.xombo.com touch /home/test.xombo.com/.ssh/id_rsa
sudo -u test.xombo.com chmod 400 /home/test.xombo.com/.ssh/id_rsa
sudo -u test.xombo.com chmod 500 /home/test.xombo.com/.ssh
# clone deployment URL into ~/www # git clone GITURL /home/test.xombo.com/www
sudo -u test.xombo.com mkdir /home/test.xombo.com/www
sudo -u test.xombo.com touch /home/test.xombo.com/www/index.html
sudo chown -R test.xombo.com:www-data /home/test.xombo.com/www
sudo chmod -R 750 /home/test.xombo.com/www
# setup crontab
sudo touch /home/test.xombo.com/crontab
sudo chmod 600 /home/test.xombo.com/crontab
sudo chown test.xombo.com:crontab /home/test.xombo.com/crontab
sudo crontab -u test.xombo.com /home/test.xombo.com/crontab
