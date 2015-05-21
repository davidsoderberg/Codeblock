<?php namespace App\Repositories\Rate;

interface RateRepository {

	public function check($id);

	public function calc($id);

	public function rate($id, $type);
}