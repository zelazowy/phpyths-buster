This repo is based on https://github.com/mikechernev/dockerised-php and http://geekyplatypus.com/dockerise-your-php-application-with-nginx-and-php7-fpm blogpost.

It is repo containing config for docker containers with nginx, php7 and mysql, and also nginx conf for **specifically Symfony application**.

### How to start
  * create `symfony-docker` directory wherever you want
  * clone this repo from github: `git clone git@github.com:zelazowy/docker-nginx-php7.git conf`
  * install Symfony Framework in `symfony-docker/code` directory http://symfony.com/doc/current/setup.html
  * comment lines in `app_dev.php`
  ```
  if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
  ) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
  }
  ```
  * add `docker.local` to your local `/etc/hosts` file (you can change it in `site.conf` in line containing `server_name docker.local;`)
  * run `docker-compose up` in `symfony-docker/symfony` dir
  * you're set up!

### Shortcut
If you're lazy (as I am) you can use `setup.sh` script that does almost everything for you: creates directories, clones this repository and install newest symfony.
Download `setup.sh` file and execute it: `sh setup.sh` in directory you want to work.
You only have to comment lines listed in 4th point of standard installation instruction and add `docker.local` host to your `/etc/hosts` file.

If something not works - let me know, I'll be happy to help!

This is a part of my series about creating Symfony app using docker. If you want to learn more visit: http://jonczyk.me/2016/08/02/phpyths-buster-project-environment/ post.
