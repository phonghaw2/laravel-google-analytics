<?php

namespace Phonghaw2\Analytics;

use Google_Client;
use Google_Service_AnalyticsData;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

class AnalyticsClientFactory
{
    public static function createForConfig(array $analyticsConfig): AnalyticsClient
    {
        $authenticatedClient = self::createAuthenticatedGoogleClient($analyticsConfig);

        $googleService = new Google_Service_AnalyticsData($authenticatedClient);

        return self::createAnalyticsClient($analyticsConfig, $googleService);
    }

    public static function createAuthenticatedGoogleClient(array $config): Google_Client
    {
        $client = new Google_Client();

        // Analytics Reporting API
        // See and download your Google Analytics data
        // https://www.googleapis.com/auth/analytics.readonly
        $client->setScopes([
            Google_Service_AnalyticsData::ANALYTICS_READONLY,
        ]);

        $client->setApplicationName("Your Application");

        $client->setAuthConfig($config['service_account_credentials_json']);

        self::configureCache($client, $config['cache']);

        return $client;
    }

    protected static function configureCache(Google_Client $client, $config)
    {
        $config = collect($config);

        $store = Cache::store($config->get('store'));

        $cache = new Psr16Adapter($store);

        $client->setCache($cache);

        $client->setCacheConfig(
            $config->except('store')->toArray()
        );
    }

    protected static function createAnalyticsClient(array $analyticsConfig, Google_Service_AnalyticsData $googleService): AnalyticsClient
    {
        $client = new AnalyticsClient($googleService, app(Repository::class));

        $client->setCacheLifeTimeInMinutes($analyticsConfig['cache_lifetime_in_minutes']);

        return $client;
    }
}
