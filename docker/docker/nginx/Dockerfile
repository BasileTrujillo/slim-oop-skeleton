FROM phusion/baseimage

# Define some environment variables
ENV \
  HOME /root \
  DEBIAN_FRONTEND noninteractive

# Install & setup packages
RUN \
  rm -rf /etc/service/sshd /etc/my_init.d/00_regen_ssh_host_keys.sh && \
  locale-gen fr_FR.UTF-8 && \
  export LANG=fr_FR.UTF-8 && \
  add-apt-repository -y ppa:nginx/stable && \
  apt-get update && \
  apt-get dist-upgrade -y && \
  apt-get install -y ca-certificates nginx && \
  echo "\ndaemon off;" >> /etc/nginx/nginx.conf && \
  chown -R www-data:www-data /var/lib/nginx && \
  mkdir /etc/service/nginx && \
  echo "#!/usr/bin/env bash\n\nnginx" > /etc/service/nginx/run && \
  chmod +x /etc/service/nginx/run && \
  apt-get clean && \
  apt-get autoremove -y && \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

VOLUME ["/etc/nginx", "/var/log/nginx", "/home/http"]

WORKDIR /etc/nginx

#EXPOSE 80 443

CMD ["/sbin/my_init"]
