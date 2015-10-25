<?php namespace App\Services\Gapi;

interface GapiInterface{

	public function getTopKeywords($numberOfDays, $maxResults);

	public function getTopBrowsers($numberOfDays, $maxResults);

	public function getMostVisitedPages($numberOfDays, $maxResults);

	public function getActiveUsers($others);

	public function getVisitorsAndPageViews($numberOfDays, $groupBy);

	public function getEvents($numberOfDays, $maxResults);

	public function getTopReferrers($numberOfDays, $maxResults);
}