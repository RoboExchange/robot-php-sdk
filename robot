/var/log/robot/*.log {
        daily
        missingok
        rotate 5
        compress
        delaycompress
        notifempty
        create 0640 www-data adm
}
