<?php namespace App\Repositories\Topic;

use App\Models\Topic;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentTopicRepository
 * @package App\Repositories\Topic
 */
class EloquentTopicRepository extends CRepository implements TopicRepository
{

	/**
	 * Property to store topic in.
	 *
	 * @var
	 */
	public $topic;

	/**
	 * Fetch one or all topics.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get($id = null)
	{
		if (is_null($id)) {
			return $this->cache('all', Topic::where('id', '!=', 0));
		} else {
			return CollectionService::filter($this->get(), 'id', $id, 'first');
		}
	}

	/**
	 * Creates or update a topic.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool
	 */
	public function createOrUpdate($input, $id = null)
	{
		if (!is_numeric($id)) {
			$Topic = new Topic;
		} else {
			$Topic = $this->get($id);
		}

		if (isset($input['title'])) {
			$Topic->title = $this->stripTrim($input['title']);
		}

		if (isset($input['forum_id'])) {
			$Topic->forum_id = $this->stripTrim($input['forum_id']);
		}

		if ($Topic->save()) {
			$this->topic = $Topic;
			return true;
		} else {
			$this->errors = $Topic::$errors;
			return false;
		}
	}

	/**
	 * Deletes a topic.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete($id)
	{
		$Topic = $this->get($id);
		if ($Topic == null) {
			return false;
		}
		return $Topic->delete();
	}

}