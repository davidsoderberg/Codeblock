<?php namespace App\Repositories;

// bas interfacet
interface IRepository {

	public function get($id = null);

	public function createOrUpdate($input, $id = null);

	public function delete($id);

}