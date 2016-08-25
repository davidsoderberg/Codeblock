<?php namespace App\Services\Gapi;

use Spatie\Analytics\AnalyticsFacade;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Analytics\Period;

/**
 * Class Gapi
 * @package App\Services\Gapi
 */
class Gapi implements GapiInterface
{

    /**
     * Fetch top keywords.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return mixed
     */
    public function getTopKeywords($numberOfDays = 365, $maxResults = 20)
    {
        return AnalyticsFacade::getTopKeywords($numberOfDays, $maxResults);
    }

    /**
     * Fetch top referrers
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return mixed
     */
    public function getTopReferrers($numberOfDays = 365, $maxResults = 20)
    {
        return AnalyticsFacade::getTopReferrers($numberOfDays, $maxResults);
    }

    /**
     * Fetch top used browsers.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return mixed
     */
    public function getTopBrowsers($numberOfDays = 365, $maxResults = 6)
    {
        return AnalyticsFacade::getTopBrowsers($numberOfDays, $maxResults);
    }

    /**
     * Fetch most visisted pages.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return mixed
     */
    public function getMostVisitedPages($numberOfDays = 365, $maxResults = 20)
    {
        return AnalyticsFacade::fetchMostVisitedPages(Period::days($numberOfDays), $maxResults);
    }

    /**
     * Fetch all active users.
     *
     * @param array $others
     *
     * @return mixed
     */
    public function getActiveUsers($others = array())
    {
        return AnalyticsFacade::getActiveUsers($others);
    }

    /**
     * Fetch all visitors and page views.
     *
     * @param int $numberOfDays
     * @param string $groupBy
     *
     * @return mixed
     */
    public function getVisitorsAndPageViews($numberOfDays = 365, $groupBy = 'date')
    {
        $Collection = AnalyticsFacade::fetchVisitorsAndPageViews(Period::days($numberOfDays), $groupBy);

        return $Collection->filter(function ($item) {
            if ($item['visitors'] != "0" || $item["pageViews"] != "0") {
                return $item;
            }
        });
    }

    /**
     * Fetch all events.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return Collection
     */
    public function getEvents($numberOfDays = 365, $maxResults = 0)
    {
        $others = ['dimensions' => 'ga:eventCategory, ga:eventAction, ga:eventLabel'];
        $totalEvents = $this->performQuery(Period::days($numberOfDays), "ga:totalEvents", $others);

        if (is_null($totalEvents->rows)) {
            return new Collection([]);
        }

        foreach ($totalEvents->rows as $row) {
            $data[] = [
                'Category' => $row[0],
                'Action' => $row[1],
                'Value' => $row[2],
                'Times' => $row[3]
            ];
        }

        if ($maxResults > 0) {
            $data = array_slice($data, 0, $maxResults - 1);
        }

        $Collection = new Collection($data);

        $uniqueEvents = $this->performQuery(Period::days($numberOfDays), "ga:uniqueEvents", $others);
        $sessionsWithEvent = $this->performQuery(Period::days($numberOfDays), "ga:sessionsWithEvent", $others);
        $eventsPerSessionWithEvent = $this->performQuery(Period::days($numberOfDays), "ga:eventsPerSessionWithEvent", $others);

        $Collection->put(null, [
            'totalEvents' => $totalEvents->totalsForAllResults['ga:totalEvents'],
            'uniqueEvents' => $uniqueEvents->totalsForAllResults['ga:uniqueEvents'],
            'sessionsWithEvent' => $sessionsWithEvent->totalsForAllResults['ga:sessionsWithEvent'],
            'eventsPerSessionWithEvent' => $eventsPerSessionWithEvent->totalsForAllResults['ga:eventsPerSessionWithEvent'],
        ]);

        return $Collection;
    }

    /**
     * Runs query.
     *
     * @param Period $numberOfDays
     * @param $metrics
     * @param array $others
     *
     * @return mixed
     */
    private function performQuery(Period $numberOfDays, $metrics, $others = array())
    {
        return AnalyticsFacade::performQuery(
            $numberOfDays,
            $metrics,
            $others
        );
    }
}
