<?php namespace App\Http\Controllers;

use App\Services\Gapi\Gapi;
use Khill\Lavacharts\Laravel\LavachartsFacade as Lava;
use Illuminate\Support\Facades\View;

/**
 * Class GapiController
 * @package App\Http\Controllers
 */
class GapiController extends Controller {

	/**
	 * @var Gapi
	 */
	private $gapi;

	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->gapi = new Gapi();
	}

	/**
	 * @permission view_analytics
	 * @return mixed
	 */
	public function mostVisitedPages(){
		$chartName = 'ColumnChart';
		$tableName = 'mostVisitedPages';

		$table = Lava::DataTable();
		$table->addStringColumn('Url')->addNumberColumn('Views');
		foreach($this->gapi->getMostVisitedPages() as $row){
			$table->addRow(array($row['url'], $row['pageViews']));
		}

		$chart = Lava::ColumnChart($tableName);
		$chart->datatable($table);

		return $this->render($chartName, $tableName, 'Most Visited Pages');
	}

	/**
	 * @permission view_analytics
	 * @return mixed
	 */
	public function visitorsAndPageViews(){
		$chartName = 'ColumnChart';
		$tableName = 'visitorsAndPageViews';

		$table = Lava::DataTable();
		$table->addStringColumn('Visitors')->addNumberColumn('Views');
		foreach($this->gapi->getVisitorsAndPageViews() as $row){
			$table->addRow(array($row['visitors'], $row['pageViews']));
		}

		$chart = Lava::ColumnChart($tableName);
		$chart->datatable($table);

		return $this->render($chartName, $tableName, 'Visitors And PageViews');
	}

	/**
	 * @permission view_analytics
	 * @return mixed
	 */
	public function events(){
		$chartName = 'ColumnChart';
		$tableName = 'events';

		$table = Lava::DataTable();
		$table->addStringColumn('Category & Action')->addNumberColumn('Times');
		foreach($this->gapi->getEvents() as $row){
			if(isset($row['Category']) && isset($row['Action']) && isset($row['Times'])) {
				$table->addRow(array($row['Category'].' & ' .$row['Action'].', '.$row['Value'], $row['Times']));
			}
		}

		$chart = Lava::ColumnChart($tableName);
		$chart->datatable($table);

		return $this->render($chartName, $tableName, 'Events');
	}

	/**
	 * @param $chart
	 * @param $table
	 * @param $title
	 * @return mixed
	 */
	private function render($chart, $table, $title){
		return View::make('gapi')->with('title', $title)->with('chart', $chart)->with('table', $table);
	}

}