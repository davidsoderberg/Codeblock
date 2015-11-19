<?php namespace App\Repositories\Permission;

use App\Permission;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentPermissionRepository
 * @package App\Repositories\Permission
 */
class EloquentPermissionRepository extends CRepository implements PermissionRepository {

	/**
	 * Fetch one or all permissions.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get( $id = null ) {
		if ( !is_null( $id ) ) {
			return CollectionService::filter( $this->get(), 'id', $id, 'first' );
		} else {
			return $this->cache( 'all', Permission::where( 'id', '!=', 0 ) );
		}
	}

	/**
	 * Create or update a permission.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool
	 */
	public function createOrUpdate( $input, $id = null ) {
		if ( !is_numeric( $id ) ) {
			$Permission = new Permission;
		} else {
			$Permission = $this->get( $id );
		}

		if ( isset( $input['permission'] ) ) {
			$input['permission'] = $this->stripTrim( str_replace( '_', ' ', strtolower( $input['permission'] ) ) );
			$Permission->permission = str_replace( ' ', '_', $input['permission'] );
		}

		if ( $Permission->save() ) {
			return true;
		} else {
			$this->errors = Permission::$errors;

			return false;
		}
	}

	/**
	 * Deletes a permission.
	 *
	 * @param $id
	 *
	 * @return bool|mixed
	 */
	public function delete( $id ) {
		$Permission = $this->get( $id );
		if ( $Permission == null ) {
			return false;
		}
		$Permission->roles()->detach();

		return $Permission->delete();
	}

}