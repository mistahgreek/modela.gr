#!/usr/bin/env bash
set -euo pipefail

APP_DOMAIN="${APP_DOMAIN:-modela.local}"
APP_URL="${APP_URL:-https://${APP_DOMAIN}}"
APP_DIR_DEFAULT="/home/cloudpanel/htdocs/${APP_DOMAIN}"
APP_OWNER="${APP_OWNER:-${SUDO_USER:-$USER}}"
if [[ -d "${APP_DIR_DEFAULT}/htdocs" ]]; then
  APP_DIR="${APP_DIR:-${APP_DIR_DEFAULT}/htdocs}"
elif [[ -d "${APP_DIR_DEFAULT}/public" ]]; then
  APP_DIR="${APP_DIR:-${APP_DIR_DEFAULT}/public}"
else
  APP_DIR="${APP_DIR:-${APP_DIR_DEFAULT}}"
fi
REPO_URL="${REPO_URL:-https://github.com/mistahgreek/modela.gr.git}"
DB_NAME="${DB_NAME:-modela}"
DB_USER="${DB_USER:-modela}"
DB_PASS="${DB_PASS:-$(openssl rand -hex 18)}"
PHP_VERSION="${PHP_VERSION:-8.3}"
MYSQL_ROOT_USER="${MYSQL_ROOT_USER:-root}"
MYSQL_ROOT_PASS="${MYSQL_ROOT_PASS:-}"

if [[ "${EUID}" -ne 0 ]]; then
  SUDO="sudo"
else
  SUDO=""
fi

export DEBIAN_FRONTEND=noninteractive

log() {
  printf "\n\033[1;32m[modela.gr]\033[0m %s\n" "$1"
}

ensure_packages() {
  log "Installing base packages"
  ${SUDO} apt-get update -y
  ${SUDO} apt-get install -y software-properties-common ca-certificates curl git lsb-release unzip

  if ! grep -q "packages.sury.org/php" /etc/apt/sources.list /etc/apt/sources.list.d/* 2>/dev/null; then
    log "Adding PHP repository"
    ${SUDO} curl -fsSL https://packages.sury.org/php/apt.gpg | ${SUDO} gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-php.gpg
    . /etc/os-release
    OS_CODENAME="${VERSION_CODENAME:-$(lsb_release -cs)}"
    echo "deb https://packages.sury.org/php/ ${OS_CODENAME:-jammy} main" | ${SUDO} tee /etc/apt/sources.list.d/sury-php.list >/dev/null
  fi

  log "Installing PHP ${PHP_VERSION}, MySQL, Redis, and build tools"
  ${SUDO} apt-get update -y
  ${SUDO} apt-get install -y "php${PHP_VERSION}-cli" "php${PHP_VERSION}-fpm" "php${PHP_VERSION}-mysql" \
    "php${PHP_VERSION}-mbstring" "php${PHP_VERSION}-xml" "php${PHP_VERSION}-gd" "php${PHP_VERSION}-bcmath" \
    "php${PHP_VERSION}-redis" "php${PHP_VERSION}-curl" "php${PHP_VERSION}-zip" \
    nginx redis-server mysql-server

  if ! command -v composer >/dev/null 2>&1; then
    log "Installing Composer"
    EXPECTED_SIGNATURE="$(curl -fsSL https://composer.github.io/installer.sig)"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '${EXPECTED_SIGNATURE}') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); exit(1); }"
    ${SUDO} php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    ${SUDO} rm composer-setup.php
  fi

  if ! command -v node >/dev/null 2>&1 || ! node -v | grep -q "v20"; then
    log "Installing Node.js 20 (verified apt repo)"
    . /etc/os-release
    OS_CODENAME="${VERSION_CODENAME:-$(lsb_release -cs)}"
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | ${SUDO} gpg --dearmor -o /usr/share/keyrings/nodesource.gpg
    echo "deb [signed-by=/usr/share/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x ${OS_CODENAME} main" | ${SUDO} tee /etc/apt/sources.list.d/nodesource.list >/dev/null
    ${SUDO} apt-get update -y
    ${SUDO} apt-get install -y nodejs
  fi
}

configure_database() {
  log "Provisioning MySQL database"
  ${SUDO} systemctl enable --now mysql
  local mysql_flags="-u${MYSQL_ROOT_USER}"
  if [[ -n "${MYSQL_ROOT_PASS}" ]]; then
    mysql_flags="${mysql_flags} -p${MYSQL_ROOT_PASS}"
  fi
  ${SUDO} mysql ${mysql_flags} -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
  ${SUDO} mysql ${mysql_flags} -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
  ${SUDO} mysql ${mysql_flags} -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"
}

clone_repository() {
  log "Fetching source code into ${APP_DIR}"
  ${SUDO} mkdir -p "${APP_DIR}"
  ${SUDO} chown -R "${APP_OWNER}:${APP_OWNER}" "${APP_DIR}"

  if [[ ! -d "${APP_DIR}/.git" ]]; then
    ${SUDO} -u "${APP_OWNER}" git clone "${REPO_URL}" "${APP_DIR}"
  else
    (cd "${APP_DIR}" && ${SUDO} -u "${APP_OWNER}" git pull --ff-only)
  fi
}

set_env_value() {
  local key="$1"
  local value="$2"
  local file="$3"
  local escaped
  escaped="$(printf '%s' "${value}" | sed -e 's/[\\/&|]/\\&/g')"

  if grep -q "^${key}=" "${file}"; then
    sed -i "s|^${key}=.*|${key}=${escaped}|" "${file}"
  else
    echo "${key}=${escaped}" >>"${file}"
  fi
}

configure_application() {
  log "Configuring environment"
  cd "${APP_DIR}"

  if [[ ! -f ".env" ]]; then
    cp .env.example .env
  fi

  set_env_value "APP_URL" "${APP_URL}" ".env"
  set_env_value "DB_CONNECTION" "mysql" ".env"
  set_env_value "DB_HOST" "localhost" ".env"
  set_env_value "DB_DATABASE" "${DB_NAME}" ".env"
  set_env_value "DB_USERNAME" "${DB_USER}" ".env"
  set_env_value "DB_PASSWORD" "${DB_PASS}" ".env"
  set_env_value "REDIS_HOST" "127.0.0.1" ".env"
  set_env_value "SESSION_DRIVER" "redis" ".env"
  set_env_value "CACHE_STORE" "redis" ".env"
}

install_application() {
  log "Installing PHP dependencies"
  cd "${APP_DIR}"
  composer install --no-dev --optimize-autoloader

  log "Installing Node dependencies"
  if command -v npm >/dev/null 2>&1; then
    npm ci --no-progress || npm install --no-progress
    npm run build
  fi

  if ! grep -q "^APP_KEY=.\\+" .env; then
    php artisan key:generate --force
  fi

  log "Running migrations and seeders"
  php artisan migrate --force --seed
  php artisan storage:link
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache

  ${SUDO} chown -R www-data:www-data storage bootstrap/cache
}

configure_nginx() {
  local cp_conf="/etc/nginx/sites-enabled/${APP_DOMAIN}.conf"
  local cp_conf_vhost="/etc/nginx/sites-enabled/${APP_DOMAIN}.vhost"
  if [[ -f "${cp_conf}" || -f "${cp_conf_vhost}" ]]; then
    log "Detected CloudPanel-managed nginx vhost; skipping custom config"
    ${SUDO} nginx -t && ${SUDO} systemctl reload nginx
    return
  fi

  log "Configuring Nginx (standalone)"
  local nginx_conf="/etc/nginx/sites-available/${APP_DOMAIN}.conf"

  cat <<EOF | ${SUDO} tee "${nginx_conf}" >/dev/null
server {
    listen 80;
    server_name ${APP_DOMAIN};
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;
    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

  ${SUDO} ln -sf "${nginx_conf}" "/etc/nginx/sites-enabled/${APP_DOMAIN}.conf"
  ${SUDO} nginx -t
  ${SUDO} systemctl enable --now nginx
  ${SUDO} systemctl reload nginx
}

configure_services() {
  log "Enabling Redis and PHP-FPM"
  ${SUDO} systemctl enable --now "php${PHP_VERSION}-fpm" redis-server

  log "Setting up queue worker"
  cat <<EOF | ${SUDO} tee /etc/systemd/system/modela-queue.service >/dev/null
[Unit]
Description=Modela Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php ${APP_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

  ${SUDO} systemctl daemon-reload
  ${SUDO} systemctl enable --now modela-queue.service

  log "Adding scheduler cron"
  local cron_line="* * * * * cd ${APP_DIR} && /usr/bin/php artisan schedule:run >> /dev/null 2>&1"
  (crontab -l 2>/dev/null | grep -v "artisan schedule:run" || true; echo "${cron_line}") | crontab -
}

welcome() {
  cat <<EOF

========================================================
  Modela.gr is ready! 🚀
========================================================
URL:        ${APP_URL}
Directory:  ${APP_DIR}
Admin user: admin@modela.gr / password
Demo user:  demo@modela.gr / password
Database:   ${DB_NAME} (user: ${DB_USER})
Services:   nginx, mysql, redis, php${PHP_VERSION}-fpm, modela-queue

Next steps:
 - Point your DNS for ${APP_DOMAIN} to this server.
 - Access the app and log in with the admin credentials above.
 - Adjust Stripe keys and mail settings in ${APP_DIR}/.env if needed.

Enjoy your fully automated CloudPanel deployment!
========================================================
EOF
}

log "Starting unattended installation for ${APP_DOMAIN}"
ensure_packages
configure_database
clone_repository
configure_application
install_application
configure_nginx
configure_services
welcome
