<?php namespace App\Http\Controllers;

use App\Models\Model;
use App\Repositories\CRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller {

	/**
	 * Property to store current page in.
	 *
	 * @var int
	 */
	private $page = 1;

	/**
	 * Property to store limit of posts in.
	 *
	 * @var int
	 */
	private $limit = 0;

	/**
	 * Property to store sort field in.
	 *
	 * @var string
	 */
	private $sort = '';

	/**
	 * Property to store current collection in.
	 *
	 * @var
	 */
	private $collection;

	/**
	 * Propety to store errors string in.
	 *
	 * @var string
	 */
	protected $stringErrors = 'errors';

	/**
	 * Propety to store messsage string in.
	 *
	 * @var string
	 */
	protected $stringMessage = 'messsage';

	/**
	 * Propety to store data string in.
	 *
	 * @var string
	 */
	protected $stringData = 'data';

	/**
	 * Propety to store user string in.
	 *
	 * @var string
	 */
	protected $stringUser = 'user';

	/**
	 * Property to store routes in.
	 *
	 * @var array
	 */
	protected $routes = [];

	/**
	 * Property to store keys in.
	 *
	 * @var array
	 */
	protected $keys = [];

	/**
	 * Constructor for ApiController.
	 */
	public function __construct() {
		parent::__construct();
		Model::$append = true;
		$this->setParams();
		$this->setRoutes();
	}

	/**
	 * Setter for routes.
	 */
	private function setRoutes() {
		$routeCollection = Route::getRoutes(); // RouteCollection object
		$routes = $this->getRoutesByPrefix( $routeCollection->getRoutes(), 'api' );
		$routeGroups = $this->groupByVersion( $routes );
		foreach( $routeGroups as $v => $group ) {
			$this->routes[$v] = $this->groupByUri( $group );
		}
	}

	/**
	 * Checks if requesting browser accepts html.
	 *
	 * @return bool
	 */
	private function acceptsHtml() {
		return str_contains( $this->request->headers->get( 'Accept' ), 'xhtml' );
	}

	/**
	 * Response with correct format.
	 *
	 * @param $response
	 * @param $code
	 *
	 * @return mixed
	 */
	protected function response( $response, $code ) {
		$response = Response::json( $response, $code );
		if ( str_contains( $this->request->headers->get( 'Accept' ), '/xml' ) && !$this->acceptsHtml() ) {
			$response = $response->getData( true );

			return Response::xml( $response, $code );
		}

		return $response;
	}

	/**
	 * Setter for params.
	 */
	private function setParams() {
		if ( isset( $_GET['pagination'] ) ) {
			if ( is_numeric( $_GET['pagination'] ) ) {
				$this->perPage = $_GET['pagination'];
			}
		} else {
			$this->perPage = 10;
		}
		if ( isset( $_GET['page'] ) ) {
			if ( is_numeric( $_GET['page'] ) ) {
				$this->page = $_GET['page'];
			}
		} else {
			$this->page = 1;
		}
		if ( isset( $_GET['limit'] ) ) {
			if ( is_numeric( $_GET['limit'] ) ) {
				$this->limit = $_GET['limit'];
			}
		} else {
			$this->limit = 0;
		}
		if ( isset( $_GET['sort'] ) ) {
			$this->sort = $_GET['sort'];
		} else {
			$this->sort = '';
		}
	}

	/**
	 * Paginates collection.
	 */
	private function paginate() {
		if ( $this->perPage > 0 ) {
			$this->collection = $this->collection->slice( ( ( $this->page - 1 ) * $this->perPage ), $this->perPage, true )
			                                     ->all();
			if ( empty( $this->collection ) ) {
				$this->collection = null;
			} else {
				$this->createNewCollection();
			}
		}
	}

	/**
	 * Sorts collection.
	 */
	private function sort() {
		if ( $this->sort != '' ) {
			if ( in_array( $this->sort, array_keys( $this->collection[0]->toArray() ) ) ) {
				$this->collection = $this->collection->sortBy( $this->sort );
				$this->createNewCollection();
			}
		}
	}

	/**
	 * Limit collection to correct number of items.
	 */
	private function limit() {
		if ( $this->limit > 0 ) {
			$this->collection = $this->collection->slice( $this->limit, $this->limit, true )->all();
			if ( empty( $this->collection ) ) {
				$this->collection = null;
			} else {
				$this->createNewCollection();
			}
		}
	}

	/**
	 * Regenerates new keys for collection.
	 */
	private function createNewCollection() {
		if ( $this->collection instanceof Collection ) {
			$this->collection->values();
		}
	}

	protected function filter( Collection $collection, $property, $matching, $verb = null ) {
		$collection = $collection->filter( function ( $item ) use ( $property, $matching ) {
			if ( $item->$property == $matching ) {
				return $item;
			}
		} );

		if ( !is_null( $verb ) ) {
			return $collection->$verb();
		}

		return $collection;
	}

	protected function hideFields( $data, $fields ) {
		if ( $data instanceof Collection || $data instanceof Model ) {
			$data = $data->toArray();
		}

		foreach( $data as $key => $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				$data[$key] = $this->hideFields( $value, $fields );
			} else {
				if ( in_array( $key, $fields ) ) {
					unset( $data[$key] );
				}
			}
		}

		return $data;
	}

	/**
	 * Fetch collection.
	 *
	 * @param CRepository $repository
	 * @param null $id
	 *
	 * @return mixed
	 */
	protected function getCollection( CRepository $repository, $id = null ) {
		$this->collection = $this->addHidden( $repository->get( $id ) );
		if ( is_null( $id ) ) {
			$this->limit();
			$this->sort();
			$this->paginate();
		}
		$this->createNewCollection();

		return $this->collection;
	}


	/**
	 * Render index view for api.
	 *
	 * @return mixed
	 */
	public function index() {
		if ( $this->acceptsHtml() ) {
			return View::make( 'api' )->with( 'title', 'api' );
		}

		return $this->response( $this->routes, 200 );
	}

	/**
	 * Fetch routes by selected prefix.
	 *
	 * @param $routes
	 * @param $name
	 *
	 * @return Route
	 */
	private function getRoutesByPrefix( $routes, $name ) {
		$routes = array_filter( $routes, function ( $route ) use ( $name ) {
			$action = $route->getAction();
			if ( isset( $action['prefix'] ) ) {
				// for the first level groups, $action['group_name'] will be a string
				// for nested groups, $action['group_name'] will be an array
				if ( is_array( $action['prefix'] ) ) {
					return in_array( $name, $action['prefix'] );
				} else {
					return str_contains( $action['prefix'], $name );
				}
			}

			return false;
		} );

		return array_values( $routes );
	}

	/**
	 * Grouping routes by uri.
	 *
	 * @param $routes
	 *
	 * @return array
	 */
	private function groupByUri( $routes ) {
		$grouped = [];

		foreach( $routes as $route ) {
			$uri = $route->getUri();

			$needsAuth = false;
			if ( in_array( 'jwt', $route->middleware() ) ) {
				$needsAuth = true;
			}

			$parameters = $route->parameterNames();
			foreach( $parameters as $key => $parameter ) {
				$optional = str_contains( $uri, '{' . $parameter . '?}' );
				$parameters[$key] = [
					'name' => $parameter,
					'optional' => $optional,
				];
			}

			$methods = $route->getMethods();
			$head = 'HEAD';
			if ( in_array( $head, $methods ) ) {
				$key = array_keys( $methods, $head )[0];
				unset( $methods[$key] );
			}
			if ( count( $methods ) == 1 ) {
				$methods = $methods[0];
			}

			$valueUri = str_replace( '{', '[', $uri );
			$valueUri = str_replace( '}', ']', $valueUri );
			$valueUri = str_replace( '?', '', $valueUri );

			$value = [
				'uri' => URL::to( '/' ) . '/' . $valueUri,
				'methods' => $methods,
				'auth' => $needsAuth,
				'parameters' => $parameters,
			];


			$uris = explode( '/', $uri );
			if ( count( $uris ) > 1 ) {
				if ( count( $uris ) > 2 ) {
					$grouped[$uris[2]][] = $value;
				} else {
					$grouped[$uris[1]][] = $value;
				}
			} else {
				$grouped[$uris[0]][] = $value;
			}
		}

		$this->keys = array_keys( $grouped );

		for( $i = 0; $i < ( count( $grouped ) - 1 ); $i++ ) {
			usort( $grouped[$this->keys[$i]], function ( $a, $b ) {
				if ( $a['methods'] === $b['methods'] ) {
					return 0;
				}

				if ( $a['methods'] === 'GET' && in_array( $b['methods'], [
						'POST',
						'PUT',
						'DELETE',
					] ) || $a['methods'] === 'POST' && in_array( $b['methods'], [
						'PUT',
						'DELETE',
					] ) || $a['methods'] === 'PUT' && in_array( $b['methods'], ['DELETE'] )
				) {
					return -1;
				}

				return 1;
			} );
		}

		ksort( $grouped );

		return $grouped;
	}

	/**
	 * Grouping routes by version.
	 *
	 * @param $routes
	 *
	 * @return array
	 */
	private function groupByVersion( $routes ) {
		$groupedByVersion = [];

		foreach( $routes as $route ) {
			$action = $route->getAction();
			$uris = explode( '/', $action['prefix'] );
			unset( $uris[0] );
			if ( preg_match( '/^v[0-9]+$/', $uris[1] ) ) {
				$groupedByVersion[$uris[1]][] = $route;
			}
		}

		return $groupedByVersion;
	}

	/**
	 * Creates hateoas for selected route.
	 *
	 * @param $key
	 * @param string $match
	 * @param int $value
	 *
	 * @return array
	 */
	protected function hateoas( $key, $match = '', $value = 0 ) {
		$routes = $this->routes[$key];
		$hateoas = [];

		if ( !str_contains( $match, '[' ) || !str_contains( $match, ']' ) ) {
			$match = str_replace( '[', '', $match );
			$match = str_replace( ']', '', $match );

			$match = '[' . $match . ']';
		}


		foreach( $routes as $route ) {
			$uri = str_replace( $match, $value, $route['uri'] );
			if ( array_key_exists( $route['methods'], $hateoas ) ) {
				$hateoas[$route['methods']] = [$hateoas[$route['methods']], $uri];
			} else {
				$hateoas[$route['methods']] = $uri;
			}
		}

		return $hateoas;
	}

}