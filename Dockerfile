FROM webdevops/php-nginx
MAINTAINER Mahdi Sheikh Hosseini <mah454>

RUN mkdir /var/log/robot/ ; touch /var/log/robot/application.log ; chown -R www-data. /var/log/robot/ ; chmod -R 777 /var/log/robot/
COPY src/*.php /app/
COPY robot /etc/logrotate.d/
