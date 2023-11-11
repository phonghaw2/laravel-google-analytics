
#  Retrieve data from Google Analytics (v4)

[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/phonghaw2/laravel-google-analytics.svg?style=flat-square)](https://packagist.org/packages/phonghaw2/laravel-google-analytics)

## Usage
Using this package you can easily retrieve data from Google Analytics.

Here are a few examples of the provided methods:

```php
use Phonghaw2\Analytics\AnalyticsFacade;
use Phonghaw2\Analytics\Period;

// You can set data with
Period::set($startDate, $endDate);
// https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/DateRange
// startDate, $endDate : The format NdaysAgo, yesterday, or today is also accepted

// Fetch the pages view for today and the 20 days ago
AnalyticsFacade::fetchPageViews(Period::set('20daysAgo', 'today'));

// Retrieve pageview data for the current day and the last seven days
$analyticsData = Analytics::fetchPageViews(Period::days(7));

// Retrieve pageviews since the 6 months ago
$analyticsData = Analytics::fetchPageViews(Period::months(6));

// fetch data 
use \Google\Service\AnalyticsData\Dimension;
use \Google\Service\AnalyticsData\Metric;
$dimensions = new Dimension(array('name' => 'pageTitle'));
$metrics = new Metric(array('name' => 'screenPageViews'));
Analytics::fetch(Period::days(7), $dimensions, $metrics);
```

## Provided methods
### Or The total amount of time (in seconds) your website or app was in the foreground of users' devices.
```php
public function fetchUserEngagementReport(Period $period): Collection
```

All methods will return an `\Illuminate\Support\Collection` object containing the results.

## Installation

> For Laravel ^6.0|^7.0|^8.0

This package can be installed through Composer.

``` bash
composer require phonghaw2/laravel-google-analytics
```

Optionally, you can publish the config file of this package with this command:

``` bash
php artisan vendor:publish --provider="Phonghaw2\Analytics\AnalyticsServiceProvider"
```

The following config file will be published in `config/analytics.php`

```php
return [

    /*
     * The property id of which you want to display data.
     */
    'property_id' => env('ANALYTICS_PROPERTY_ID'),

    /*
     * Path to the client secret json file. Take a look at the README of this package
     * to learn how to get this file. You can also pass the credentials as an array
     * instead of a file path.
     */
    'service_account_credentials_json' => storage_path('app/analytics/service-account-credentials.json'),

    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,

    /*
     * Here you may configure the "store" that the underlying Google_Client will
     * use to store it's data.  You may also add extra parameters that will
     * be passed on setCacheConfig (see docs for google-api-php-client).
     *
     * Optional parameters: "lifetime", "prefix"
     */
    'cache' => [
        'store' => 'file',
    ],
];
```

### Getting credentials

Save the json inside your Laravel project at the location specified in the `service_account_credentials_json` key of the config file of this package. 
Because the json file contains potentially sensitive information I don't recommend committing it to your git repository.

### Granting permissions to your Analytics property
I'm assuming that you've already created a Analytics account on the Analytics site and are using the new GA4 properties.

First you will need to know your property ID. In Analytics, go to Settings > Property Settings. 
Here you will be able to copy your property ID. Use this value for the ANALYTICS_PROPERTY_ID key in your .env file.


### All other Google Analytics queries

To perform all other queries on the Google Analytics resource use `performQuery`.  [Google's Core Reporting API](https://developers.google.com/analytics/devguides/reporting/core/v4) provides more information on which metrics and dimensions might be used.

```php
public function performQuery(Period $period, string $metrics, array $others = [])
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [h](https://github.com/phonghaw2)
- [All Contributors](../../contributors)

And a special thanks to [Caneco](https://twitter.com/caneco) for the logo ✨

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
