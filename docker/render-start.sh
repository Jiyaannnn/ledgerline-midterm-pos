#!/bin/sh
set -eu

port="${PORT:-10000}"
case "$port" in
    ''|*[!0-9]*) echo 'PORT must be numeric' >&2; exit 1 ;;
esac
if [ "$port" -lt 1 ] || [ "$port" -gt 65535 ]; then
    echo 'PORT must be between 1 and 65535' >&2
    exit 1
fi

# Apache reads only the public directory; Render can assign a port at runtime.
printf 'Listen %s\n' "$port" > /etc/apache2/ports.conf
sed "s/@PORT@/$port/g" /etc/apache2/sites-available/000-default.conf.template > /etc/apache2/sites-available/000-default.conf
exec "$@"
