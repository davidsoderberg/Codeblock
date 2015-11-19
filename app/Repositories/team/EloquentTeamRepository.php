<?php namespace App\Repositories\Team;

use App\Team;
use App\Repositories\CRepository;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;

/**
 * Class EloquentTeamRepository
 * @package App\Repositories\Team
 */
class EloquentTeamRepository extends CRepository implements TeamRepository {

	/**
	 * Fetch one or all teams.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get($id = null)
	{
		if(is_null($id)){
			return $this->cache('all', Team::where('id', '!=', 0));
		}else{
			if(is_numeric($id)) {
				return CollectionService::filter($this->get(), 'id', $id, 'first');
			}else{
				return CollectionService::filter($this->get(), 'name', $id, 'first');
			}
		}
	}

	/**
	 * Creates or updates a team.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool
	 */
	public function createOrUpdate($input, $id = null)
	{
		if(is_null($id)) {
			$Team = new Team();
		} else {
			$Team = $this->get($id);
		}

		if(isset($input['name'])){
			$Team->name = $this->stripTrim($input['name']);
			$Team->owner_id = Auth::user()->id;
		}

		if($Team->save()){
			return true;
		}else{
			$this->errors = $Team::$errors;
			return false;
		}
	}

	/**
	 * Deletes a team.
	 *
	 * @param $id
	 *
	 * @return bool|mixed
	 */
	public function delete($id){
		$Team = $this->get($id);
		if($Team != null) {
			return $Team->delete();
		}
		return false;
	}

	/**
	 * Make user leave a team.
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function leave($id){
		return Auth::user()->detachTeam($id);
	}

}