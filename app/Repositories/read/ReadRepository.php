<?php namespace App\Repositories\Read;

interface ReadRepository {

	public function hasRead($topic_id);

	public function UpdatedRead($topic_id);
}