<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>


# Code Challenge: Geolocation-Weather APIs

### Requirements
- PHP7.1
- composer

### API's basic general documentation

### Endpoints

##### `GET /geolocation`

##### `GET /geolocation/:ip_address`

##### `GET /geolocation/:ip_address?service=freegeoip`

##### `GET /geolocation?service=freegeoip`

##### `GET /geolocation/:ip_address?service=ip-api`

##### `GET /geolocation?service=ip-api`

##### `GET /weather`

##### `GET /weather/:ip_address`

### Sample Requests and responses payload

```
GET /geolocation
```

```json
{
    "ip": "8.8.8.8",
    "geo": {
        "service": "ip-api",
        "city": "Mountain View",
        "region": "California",
        "country": "United States"
    }
}
```

```
GET /weather/8.8.8.8
```

```json
{
    "ip": "8.8.8.8",
    "city": "Mountain View",
    "temperature": {
        "current": 13,
        "low": 11,
        "high": 16,
    },
    "wind": {
        "speed": 11,
        "direction": 240
    }
}
```

### API's basic technical documentation
##### Routes: api routes are used within this API 
- `routes/api.php`
##### Controllers: Geolocation/WeatherController 
- `app/Http/ApiControllers/GeolocationController.php`
- `app/Http/ApiControllers/WeatherController.php`
##### Services: Geolocation ip-api and freegeoip 
- `app/services/IpApiService.php` 
- `app/services/IpStackService.php`
##### Services Providers: Geolocation ip-api and freegeoip 
- `app/providers/IpApiServiceProvider.php`
- `app/services/IpStackServiceProvider.php`


### Installation

##### `git clone repo`
##### `cd  path/to/repo`
##### `composer install`
##### `cp .env.exemple .env` -> if needed
##### `php artisan key:generate` -> if needed
##### `php artisan serve`
##### Boom! access API on localhost `127.0.0.1:8000`

