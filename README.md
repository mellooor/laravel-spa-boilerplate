# Laravel SPA Boilerplate
Laravel SPA Boilerplate provides a pre-configured copy of Laravel, complete with out-of-the-box ready auth, basic user related functions + web roots (with the help of [Laravel Fortify](https://laravel.com/docs/8.x/fortify)), API doumentation (via [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)), token-based SPA authentication (via [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum)) and testing (via [PHPUnit](https://phpunit.de/)).

## List of Features
- Login ✔️
- Register ✔️
- Logout ✔️
- Forgot Password ✔️
- Update Password ✔️
- Reset Password ✔️
- Update User Profile Information ✔️
- Delete User ✔️
- Get All Users ✔️
- Get Individual Users ✔️
- Confirm Password ❌ (to be Implemented Later on)
- Two-Factor Auth ❌ (to be Implemented Later on)
- Email Verification ❌ (to be Implemented Later on)

API documentation - generated via L5-Swagger through code annotations and can be found at the `/[API_VERSION_NUMBER]/api/documentation` URI for the API domain by default.

## Installation and Set Up

1. Run ```git clone https://github.com/mellooor/laravel-spa-boilerplate.git```.
2. Run ```composer install``` in the freshly cloned directory to install the vendor dependencies.
3. Update the value of the `domain` variable in `config\session.php` to your domain without the trailing sub domain (i.e. - `.some-domain.com`).
4. Run ```php artisan key:generate``` in the freshly cloned directory to generate your app key.

**NOTE - The boilerplate by default uses a subdomain for all API routes and a version prefix (i.e. `api.some-domain.com/v1.0`) rather than a prefix and the app’s default sub domain (`www.some-domain.com/api`) but this can be altered in the boot method of `App/Providers/RouteServiceProvider.php`, as shown in the examples below**

#### API Subdomain and Version Prefix Set Up:
```
public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::domain(config('app.api_domain'))
                ->prefix(config('app.api_version'))
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }
```

#### API and API Version Prefix Set Up:
```
public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api/' . config('app.api_version'))
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }
```

#### As well as this, please see below for instructions on how to configure Laravel SPA Boilerplate for either set up:

##### API Sub Domain and API Version Prefix
* Add an `API_DOMAIN` variable to your .env file and give it a value of your api domain (i.e. `api.your-website.com`).
* Add an `API_VERSION` variable to your .env file and give it a relevant value (i.e. `v1.0`).
* Create a .env.testing file:
  * set the `APP_URL` inside to the api base URL (the value of `API_DOMAIN` in your .env file), otherwise all of the tests that access any of the API routes will fail.
  * Even with this change, still set up a `API_DOMAIN` variable and set it to a value of your API domain (same value as above) - This is because it is used in the Fortify config in order to generate the Fortify routes.
  * Also, the `API_VERSION` variable still needs to be set in .env.testing (in the same format as for .env) if you are using it in your route prefixes.
  * Make sure to copy over the app key value to the `APP_KEY` variable, if it hasn't already been copied over to the .env.testing file.

##### API and API Version Prefix
* Update the api value in the `routes` variable array in `config/l5-swagger.php` to prevent repeating the _‘api/’_ prefix for the L5 Swagger API documentation endpoint.
* In `config/fortify.php`, update the `prefix` variable to include `'api/'` and change the `domain` variable to `config(app.url)`.
* In `config/l5-swagger.php`, update the `group_options` variable array to exclude any domain rules and to include `‘api/’` in the prefix.

## Testing
Laravel SPA Boilerplate has tests already set up to use straight away. Before starting these tests, make sure to update the value of the `$apiBasePrefix` property in the `tests/TestCase.php` `setUp()` method to match the fixed structure of your API route URIs (i.e. `/api/v1.0`). When configured, the testing can be starting by running `vendor/bin/phpunit` in the CLI.

&nbsp;

The API routes can also be tested via [Postman](https://www.postman.com/) - A collection file can be found in the root directory (`Laravel SPA Boilerplate End Points.postman_collection`) which can be imported into Postman. In order to configure this for your installation of Laravel SPA Boilerplate, follow the steps below:
1. Update the `url` value in the collections pre-request scripts section to match your sub domain + domain **(make sure to leave the /sanctum/csrf-cookie URI however, otherwise all HTTP requests will return a 419 "CSRF token mistmatch." response)**.
2. Create an environment for the collection with the following variables and values:
    * `API_BASE_URL` - Your base URL for the API end points, whether that is `http://api.some-domain.com` or `http://www.some-domain.com/api` for example.
    * `xsrf-token` - This is left blank and will be populated with the CSRF token by the pre-request script.
    * `API_VERSION` - Only use if you are including an API version prefix in your routes. Set the value to be in the same format as the similar variable from your .env file (i.e. `v1.0`).
