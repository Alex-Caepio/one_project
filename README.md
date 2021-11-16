# Oneness

## Requirements

- [Docker]()
- [Docker-compose]()

## Installation

1. Clone backend & frontend repos in one dir 
```
├── back
│   ├── ... repo files
│   └── Dockerfile.local
├── front
│   └── ... repo files
├── front.env
└── docker-compose.yml
```
2. Put `docker-compose.yml` into root directory
3. Run `docker-compose up -d` to build & up application
4. Build frontend
- Run `docker-compose run node bash` to pass into frontend container
- Inside container run `npm i` to install dependencies
- Inside container run `npm run build:prod` to compile frontend application
5. Build backend
- Run `docker-compose exec back bash` to pass into backend container
- Inside container run `composer install` to install dependencies
- Copy config file `cp .env.example .env`
- Inside container run `php artisan key:generate` 
- Inside container run `php artisan migrate:fresh --seed` 
6. Open [localhost](http://localhost) in your browser

## Additional files needed

### docker-compose.yml
```yaml
version: '3.4'

networks:
  oneness-local:
    driver: bridge

services:
  back:
    build:
      context: back
      dockerfile: Dockerfile.local
      args:
        UID: ${UID:-1000}
        GID: ${GID:-1000}
    ports:
      - 8080:80
    user: www-data
    restart: on-failure
    working_dir: /var/www/html
    volumes:
      - ./back:/var/www/html
    networks:
      - oneness-local

  front:
    image: nginx:1.19.0-alpine
    ports:
      - ${WEB_PORT_HOST:-80}:80
    restart: on-failure
    working_dir: /usr/share/nginx/html
    volumes:
      - ./front/dist:/usr/share/nginx/html
      - ./front/nginxdefault.conf:/etc/nginx/conf.d/default.conf
    networks:
      - oneness-local

  node:
    image: node:12.18.4
    volumes:
      - ./front:/usr/share/nginx/html
      - ./front.env:/usr/share/nginx/html/.env.production
    working_dir: /usr/share/nginx/html
    env_file: front.env

  db:
    image: mysql:8.0
    restart: on-failure
    environment:
      MYSQL_DATABASE: tom
      MYSQL_USER: tom
      MYSQL_PASSWORD: fridayiminlove
      MYSQL_ROOT_PASSWORD: fridayiminlove
    volumes:
      - ./volumes/mysql:/var/lib/mysql
    ports:
      - ${DB_HOST_PORT:-3306}:3306
    networks:
      - oneness-local
```
### Dockerfile.local
```Dockerfile
FROM php:7.4-apache
COPY ./config/vhost.conf /etc/apache2/sites-enabled/000-default.conf
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& apt-get update && apt-get install -y git libzip-dev unzip libpng-dev mysql-common default-mysql-client\
&& docker-php-ext-install zip pdo_mysql gd && a2enmod rewrite headers

WORKDIR /var/www/html
```
### front.env
```dotenv
# DOMAIN
DOMAIN=http://localhost:8080
API_PREFIX=/api

PLATFORM_FEE=3

GUEST_GROW_BUSINESS_LINK=https://page.holistify.me/grow-your-business
CLIENT_GROW_BUSINESS_LINK=https://page.holistify.me/grow-your-business

STRIPE_KEY=

GOOGLE_CLIENT_ID=
GOOGLE_API_KEY=
G_CALENDAR_PREFIX=https://www.googleapis.com/calendar/v3
GOOGLE_DISCOVERY_DOCS = https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest
GOOGLE_SCOPE=https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.events
```
