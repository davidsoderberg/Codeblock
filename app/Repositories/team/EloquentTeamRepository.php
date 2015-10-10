<?php namespace App\Repositories\Team;

use App\Team;
use App\Repositories\CRepository;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;

class EloquentTeamRepository extends CRepository implements TeamRepository {

	// hämtar en eller alla team.
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

	// skapar och uppdaterar ett team.
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

	// tar bort ett team.
	public function delete($id){
		$Team = $this->get($id);
		if($Team != null) {
			return $Team->delete();
		}
		return false;
	}

}