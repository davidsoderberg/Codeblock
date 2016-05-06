<?php namespace App\Services\Gapi;

use Spatie\LaravelAnalytics\LaravelAnalyticsFacade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

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
		return LaravelAnalyticsFacade::getTopKeywords($numberOfDays, $maxResults);
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
		return LaravelAnalyticsFacade::getTopReferrers($numberOfDays, $maxResults);
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
		return LaravelAnalyticsFacade::getTopBrowsers($numberOfDays, $maxResults);
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
		return LaravelAnalyticsFacade::getMostVisitedPages($numberOfDays, $maxResults);
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
		return LaravelAnalyticsFacade::getActiveUsers($others);
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
		$Collection = LaravelAnalyticsFacade::getVisitorsAndPageViews($numberOfDays, $groupBy);

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
		$totalEvents = $this->performQuery($numberOfDays, "ga:totalEvents", $others);

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

		$uniqueEvents = $this->performQuery($numberOfDays, "ga:uniqueEvents", $others);
		$sessionsWithEvent = $this->performQuery($numberOfDays, "ga:sessionsWithEvent", $others);
		$eventsPerSessionWithEvent = $this->performQuery($numberOfDays, "ga:eventsPerSessionWithEvent", $others);

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
	 * @param int $numberOfDays
	 * @param $metrics
	 * @param array $others
	 *
	 * @return mixed
	 */
	private function performQuery($numberOfDays = 365, $metrics, $others = array())
	{
		return LaravelAnalyticsFacade::performQuery(
			Carbon::today()->subDays($numberOfDays),
			Carbon::today(),
			$metrics,
			$others
		);
	}
}