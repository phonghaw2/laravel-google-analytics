<?php

namespace Phonghaw2\Analytics;

use Google\Service\AnalyticsData\RunReportRequest;
use Google_Service_AnalyticsData;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Google\Service\AnalyticsData\Metric;
use Google\Service\AnalyticsData\Dimension;

class Analytics
{
    use Macroable;

    /** @var \Phonghaw2\Analytics\AnalyticsClient */
    protected $client;

    /** @var string */
    protected $propertyId;

    /**
     * @param \Phonghaw2\Analytics\AnalyticsClient $client
     * @param string $propertyId
     */
    public function __construct(AnalyticsClient $client, string $propertyId)
    {
        $this->client = $client;

        $this->propertyId = $propertyId;
    }

    /**
     * @param string $propertyId
     *
     * @return $this
     */
    public function setPropertyId(string $propertyId)
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    public function getPropertyId()
    {
        return $this->propertyId;
    }

    /**
     * Fetch data from report
     * Come https://developers.google.com/analytics/devguides/reporting/data/v1/api-schema for more
     * @param \Phonghaw2\Analytics\Period $period
     * @param \Google\Service\AnalyticsData\Dimension $dimensions
     * @param \Google\Service\AnalyticsData\Metric $metrics
     *
     * @return Collection
     */
    public function fetch(Period $period, Dimension $dimensions, Metric $metrics): Collection
    {
        // Set DateRange
        $dateRange = array(
            array('start_date' => $period->startDate, 'end_date' => $period->endDate),
        );

        $runReport = new RunReportRequest();
        $runReport->setDateRanges($dateRange);
        $runReport->setDimensions($dimensions);
        $runReport->setMetrics($metrics);

        $response = $this->performQuery(
            $runReport
        );

        return new Collection($response->rows);
    }

    public function fetchPageViews(Period $period): Collection
    {
        // Set DateRange
        $dateRange = array(
            array('start_date' => $period->startDate, 'end_date' => $period->endDate),
        );

        // Set Dimensions
        $dimensions = array(
            array('name' => 'pageTitle'),
            array('name' => 'pagePath'),
        );

        // Set Metric
        $metrics = array(
            array('name' => 'screenPageViews'),
        );

        $runReport = new RunReportRequest();
        $runReport->setDateRanges($dateRange);
        $runReport->setDimensions($dimensions);
        $runReport->setMetrics($metrics);

        $response = $this->performQuery(
            $runReport
        );

        $data = new Collection($response->rows);

        return collect($data->all() ?? [])->map(function (object $item) {
            return [
                'pageTitle' => $item->dimensionValues[0]->value,
                'pagePath'  => $item->dimensionValues[1]->value,
                'pageView'  => $item->metricValues[0]->value,
            ];
        });
    }

    public function fetchUserEngagementReport(Period $period): Collection
    {
        // Set DateRange
        $dateRange = array(
            array('start_date' => $period->startDate, 'end_date' => $period->endDate),
        );

        // Set Dimensions
        $dimensions = array(
            array('name' => 'pageTitle'),
            array('name' => 'pagePath'),
            array('name' => 'deviceCategory'),
        );

        // Set Metric
        $metrics = array(
            array('name' => 'userEngagementDuration'),
            array('name' => 'totalUsers'),
        );

        $runReport = new RunReportRequest();
        $runReport->setDateRanges($dateRange);
        $runReport->setDimensions($dimensions);
        $runReport->setMetrics($metrics);

        $response = $this->performQuery(
            $runReport
        );

        $data = new Collection($response->rows);

        return collect($data->all() ?? [])->map(function (object $item) {
            return [
                'pageTitle'      => $item->dimensionValues[0]->value,
                'pagePath'       => $item->dimensionValues[1]->value,
                'deviceCategory' => $item->dimensionValues[2]->value,
                'UserEngagement' => $item->metricValues[0]->value,
                'totalUsers'     => $item->metricValues[1]->value,
            ];
        });
    }

    /**
     * Call the query method on the authenticated client.
     *
     * @param array $data_ary
     *
     * @return object
     */
    public function performQuery(RunReportRequest $runReport)
    {
        return $this->client->performQuery(
            'properties/' . $this->propertyId,
            $runReport
        );
    }

    /*
     * Get the underlying Google_Service_AnalyticsData object. You can use this
     * to basically call anything on the Google Analytics API.
     */
    public function getAnalyticsService(): Google_Service_AnalyticsData
    {
        return $this->client->getAnalyticsService();
    }
}
