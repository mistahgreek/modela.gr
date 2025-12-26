# Modela.gr - 3D Model Printing Platform

A complete Laravel 11 platform for uploading, browsing, and purchasing 3D models with instant quote generation and Stripe payment integration.

## Features

- **User Authentication**: Email/password with verification and password reset
- **3D Model Management**: Upload STL/OBJ/3MF files with automatic processing
- **3D Viewer**: Client-side Three.js viewer for previewing models
- **Instant Quote Engine**: Real-time pricing calculations based on material, size, and print settings
- **E-commerce**: Full Stripe Checkout integration with order management
- **Admin Panel**: Complete backend for managing models, users, pricing, and orders
- **AJAX/SPA-like Experience**: All interactions without page reloads using Alpine.js
- **Professional UI**: Built with Tailwind CSS for responsive design

## Technology Stack

- **Backend**: PHP 8.3, Laravel 11
- **Database**: MySQL 8
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **3D Viewer**: Three.js
- **Payments**: Stripe Checkout
- **Queue**: Redis
- **Email**: Mailpit (dev), SMTP (production)
- **Storage**: Local/Public disk with S3-compatible abstraction

## Prerequisites

### Development
- Docker & Docker Compose
- Git

### Production
- Ubuntu 22.04 or 24.04
- PHP 8.3 with extensions: pdo_mysql, mbstring, xml, gd, bcmath, redis
- MySQL 8.0
- Redis
- Nginx
- Composer
- Node.js 20+ & npm

## Development Setup

### 1. Clone the Repository

```bash
git clone https://github.com/mistahgreek/modela.gr.git
cd modela.gr
```

### 2. Build and Start Docker Containers

```bash
# Build the Docker images
docker-compose build

# Start the containers
docker-compose up -d
```

### 3. Install Dependencies

```bash
# PHP dependencies
docker-compose exec php composer install

# Node dependencies  
npm install
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
docker-compose exec php php artisan key:generate

# Configure your Stripe keys in .env
# STRIPE_KEY=pk_test_...
# STRIPE_SECRET=sk_test_...
# STRIPE_WEBHOOK_SECRET=whsec_...
```

### 5. Run Database Migrations

```bash
docker-compose exec php php artisan migrate --seed
```

### 6. Create Storage Link

```bash
docker-compose exec php php artisan storage:link
```

### 7. Build Frontend Assets

```bash
npm run dev
# or for production
npm run build
```

### 8. Access the Application

- **Application**: http://localhost:8080
- **Mailpit (Email Testing)**: http://localhost:8025
- **MySQL**: localhost:3306

### 9. Start Queue Worker (Optional for development)

```bash
docker-compose exec php php artisan queue:work
```

## Makefile Commands

The project includes a Makefile for common tasks:

```bash
make help           # Show all available commands
make up             # Start Docker containers
make down           # Stop Docker containers
make shell          # Access PHP container shell
make migrate        # Run migrations
make migrate-fresh  # Fresh database with seed data
make test           # Run tests
make logs           # Show Docker logs
make clear          # Clear all caches
```

## Project Structure

```
modela.gr/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Application controllers
│   │   └── Requests/        # Form validation requests
│   ├── Models/              # Eloquent models
│   ├── Jobs/                # Queue jobs for processing
│   └── Services/            # Business logic services
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Demo data seeders
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Tailwind CSS
│   └── js/                 # Alpine.js & Three.js
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
├── public/
│   └── storage/            # Public file storage
├── storage/
│   └── app/                # Private file storage
├── docker/
│   ├── nginx/              # Nginx configuration
│   └── php/                # PHP-FPM Dockerfile
└── docker-compose.yml      # Docker services
```

## Environment Variables

### Required Configuration

```env
# Application
APP_NAME="Modela.gr"
APP_URL=http://localhost:8080

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=modela
DB_USERNAME=modela
DB_PASSWORD=secret

# Stripe
STRIPE_KEY=pk_test_your_key
STRIPE_SECRET=sk_test_your_secret
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# File Upload
MAX_UPLOAD_SIZE=104857600
ALLOWED_MODEL_EXTENSIONS=stl,obj,3mf
```

See `.env.example` for all available options.

## One-command CloudPanel Install (existing domain)

Run the unattended installer over SSH on a CloudPanel server where the domain already exists:

```bash
APP_DOMAIN=yourdomain.com bash <(curl -fsSL https://raw.githubusercontent.com/mistahgreek/modela.gr/main/scripts/cloudpanel-install.sh)
```

Optional variables: `APP_DIR` (custom path), `APP_URL`, `DB_NAME`, `DB_USER`, `DB_PASS`.

This script will:
- Detect the CloudPanel docroot for the domain (falls back to `/home/cloudpanel/htdocs/<domain>`).
- Install PHP 8.3, MySQL, Redis, Composer, Node 20.
- Clone the repo, set `.env`, run migrations/seeders, build assets.
- Keep the existing CloudPanel Nginx vhost (only reloads nginx).
- Create queue worker service and cron scheduler.

Default credentials after seeding (passwords are generated and printed at the end of the installer):
- Admin: `admin@modela.gr / <generated>`
- Demo: `demo@modela.gr / <generated>`

## Production Deployment (Ubuntu 22.04/24.04)

### 1. Server Prerequisites

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
    php8.3-gd php8.3-bcmath php8.3-redis php8.3-curl php8.3-zip

# Install MySQL 8
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Application Setup

```bash
# Create application directory
sudo mkdir -p /var/www/modela.gr
sudo chown -R $USER:$USER /var/www/modela.gr
cd /var/www/modela.gr

# Clone repository
git clone https://github.com/mistahgreek/modela.gr.git .

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
nano .env  # Configure production settings

# Generate application key
php artisan key:generate

# Setup database
php artisan migrate --force
php artisan db:seed --force

# Set permissions
sudo chown -R www-data:www-data /var/www/modela.gr
sudo chmod -R 755 /var/www/modela.gr
sudo chmod -R 775 /var/www/modela.gr/storage
sudo chmod -R 775 /var/www/modela.gr/bootstrap/cache

# Create storage link
php artisan storage:link

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Nginx Configuration

Create `/etc/nginx/sites-available/modela.gr`:

```nginx
server {
    listen 80;
    server_name modela.gr www.modela.gr;
    root /var/www/modela.gr/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/modela.gr /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. SSL Certificate (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d modela.gr -d www.modela.gr
```

### 5. Queue Worker Service

Create `/etc/systemd/system/modela-queue.service`:

```ini
[Unit]
Description=Modela Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/modela.gr
ExecStart=/usr/bin/php /var/www/modela.gr/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
sudo systemctl enable modela-queue
sudo systemctl start modela-queue
sudo systemctl status modela-queue
```

### 6. Scheduler Cron

Add to crontab (`sudo crontab -e`):

```cron
* * * * * cd /var/www/modela.gr && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Stripe Webhook Setup

1. Go to Stripe Dashboard → Developers → Webhooks
2. Add endpoint: `https://modela.gr/webhook/stripe`
3. Select events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`
4. Copy webhook secret to `.env` as `STRIPE_WEBHOOK_SECRET`

### 8. Database Backups

Create `/usr/local/bin/modela-backup.sh`:

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/modela"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u modela -p'your_password' modela > $BACKUP_DIR/db_$DATE.sql

# File backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/modela.gr/storage/app

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

Make executable and add to cron:

```bash
chmod +x /usr/local/bin/modela-backup.sh
# Add to crontab: 0 2 * * * /usr/local/bin/modela-backup.sh
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=QuoteServiceTest
```

## Security Considerations

- All file uploads are validated for type and size
- CSRF protection on all forms
- Rate limiting on upload and API endpoints
- Signed URLs for private file downloads
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- File scanning hooks available for antivirus integration

## Troubleshooting

### Storage Permission Issues
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Queue Not Processing
```bash
# Check queue worker status
sudo systemctl status modela-queue
# Restart queue worker
sudo systemctl restart modela-queue
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Support

For issues and feature requests, please use the GitHub issue tracker.

## License

This project is proprietary software.
