FROM phusion/baseimage

# Define some environment variables
ENV \
  HOME /root \
  DEBIAN_FRONTEND noninteractive

# Install packages
RUN \
    rm -rf /etc/service/sshd /etc/my_init.d/00_regen_ssh_host_keys.sh && \
    locale-gen fr_FR.UTF-8 && \
    export LANG=fr_FR.UTF-8 && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get dist-upgrade -y && \
    # PHP Install
    apt-get install -y  php7.0 \
                        php7.0-fpm \
                        php7.0-cli \
                        php7.0-mysqlnd \
                        php7.0-mcrypt \
                        php7.0-gd \
                        php7.0-imagick \
                        php7.0-sybase \
                        php7.0-curl \
                        php7.0-intl \
                        php7.0-imap \
                        php7.0-tidy \
                        php7.0-xml \
                        php-mongodb \
                        # Non-PHP install
                        curl

# Setting up PHP and FPM
RUN \
    # Link Custom xdebug conf
    ln -fs /home/http/docker/config/php/20-xdebug.ini /etc/php/7.0/fpm/conf.d/20-xdebug.ini && \
    ln -fs /home/http/docker/config/php/20-xdebug.ini /etc/php/7.0/cli/conf.d/20-xdebug.ini && \
    # Link Custom PHP Conf
    ln -fs /home/http/docker/config/php/99-custom.ini /etc/php/7.0/fpm/conf.d/99-custom.ini && \
    ln -fs /home/http/docker/config/php/99-custom.ini /etc/php/7.0/cli/conf.d/99-custom.ini && \
    # Setup PHP-FPM
    sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/7.0/fpm/php-fpm.conf && \
    sed -i '/^listen = /clisten = 9070' /etc/php/7.0/fpm/pool.d/www.conf && \
    sed -i '/^listen.allowed_clients/c;listen.allowed_clients =' /etc/php/7.0/fpm/pool.d/www.conf && \
    sed -i '/^;catch_workers_output/ccatch_workers_output = yes' /etc/php/7.0/fpm/pool.d/www.conf && \
    mkdir /etc/service/phpfpm && \
    mkdir /run/php && \
    echo "#!/usr/bin/env bash\n\nphp-fpm7.0 -c /etc/php/7.0/fpm" > /etc/service/phpfpm/run && \
    chmod +x /etc/service/phpfpm/run

# Cleanup
RUN \
    apt-get clean && \
    apt-get autoremove -y && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

VOLUME ["/home/http"]

WORKDIR /home/http

CMD ["/sbin/my_init"]
