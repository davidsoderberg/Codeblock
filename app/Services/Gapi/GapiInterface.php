<?php namespace App\Services\Gapi;

/**
 * Interface GapiInterface
 * @package App\Services\Gapi
 */
interface GapiInterface {

	/**
	 * Fetch top keywords.
	 *
	 * @param $numberOfDays
	 * @param $maxResults
	 *
	 * @return mixed
	 */
	public function getTopKeywords( $numberOfDays, $maxResults );

	/**
	 * Fetch top used browsers.
	 *
	 * @param $numberOfDays
	 * @param $maxResults
	 *
	 * @return mixed
	 */
	public function getTopBrowsers( $numberOfDays, $maxResults );

	/**
	 * Fetch most visisted pages.
	 *
	 * @param $numberOfDays
	 * @param $maxResults
	 *
	 * @return mixed
	 */
	public function getMostVisitedPages( $numberOfDays, $maxResults );

	/**
	 * Fetch all active users.
	 *
	 * @param $others
	 *
	 * @return mixed
	 */
	public function getActiveUsers( $others );

	/**
	 * Fetch all visitors and page views.
	 *
	 * @param $numberOfDays
	 * @param $groupBy
	 *
	 * @return mixed
	 */
	public function getVisitorsAndPageViews( $numberOfDays, $groupBy );

	/**
	 * Fetch all events.
	 *
	 * @param $numberOfDays
	 * @param $maxResults
	 *
	 * @return mixed
	 */
	public function getEvents( $numberOfDays, $maxResults );

	/**
	 * Fetch top referrers
	 *
	 * @param $numberOfDays
	 * @param $maxResults
	 *
	 * @return mixed
	 */
	public function getTopReferrers( $numberOfDays, $maxResults );
}