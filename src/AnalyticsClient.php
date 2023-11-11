<?php

namespace Phonghaw2\Analytics;

use Google_Service_AnalyticsData;
use Illuminate\Contracts\Cache\Repository;

class AnalyticsClient
{
    /** @var \Google_Service_AnalyticsData */
    protected $service;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var int */
    protected $cacheLifeTimeInMinutes = 0;

    public function __construct(Google_Service_AnalyticsData $service, Repository $cache)
    {
        $this->service = $service;

        $this->cache = $cache;
    }

    /**
     * Set the cache time.
     *
     * @param int $cacheLifeTimeInMinutes
     *
     * @return self
     */
    public function setCacheLifeTimeInMinutes(int $cacheLifeTimeInMinutes)
    {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes * 60;

        return $this;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param string $propertyId
     * @param \Google\Service\AnalyticsData\RunReportRequest $reportRequest
     *
     * @return object response
     */
    public function performQuery(string $propertyId, $reportRequest)
    {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($this->cacheLifeTimeInMinutes == 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember($cacheName, $this->cacheLifeTimeInMinutes, function () use ($propertyId, $reportRequest) {
            $result = $this->service->properties->runReport(
                $propertyId,
                $reportRequest
            );

            return $result;
        });
    }

    public function getAnalyticsService(): Google_Service_AnalyticsData
    {
        return $this->service;
    }

    /*
     * Determine the cache name for the set of query properties given.
     */
    protected function determineCacheName(array $properties): string
    {
        return 'phonghaw2.laravel-analytics.'.md5(serialize($properties));
    }
}
