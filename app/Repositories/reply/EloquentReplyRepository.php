<?php namespace App\Repositories\Reply;

use App\Models\Reply;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CRepository;
use App\Services\CollectionService;

/**
 * Class EloquentReplyRepository
 * @package App\Repositories\Reply
 */
class EloquentReplyRepository extends CRepository implements ReplyRepository
{

	/**
	 * Property to store reply object in.
	 *
	 * @var
	 */
	public $Reply;

	/**
	 * Fetch one or all replies.
	 *
	 * @param null $id
	 *
	 * @return \App\Services\Model|array|\Illuminate\Database\Eloquent\Collection|null|static
	 */
	public function get($id = null)
	{
		if (is_null($id)) {
			return $this->cache('all', Reply::where('id', '!=', 0));
		} else {
			return CollectionService::filter($this->get(), 'id', $id, 'first');
		}
	}

	/**
	 * Creates or updates a reply.
	 *
	 * @param $input
	 * @param null $id
	 *
	 * @return bool
	 */
	public function createOrUpdate($input, $id = null)
	{
		if (!is_numeric($id)) {
			$Reply = new Reply;
		} else {
			$Reply = $this->get($id);
		}

		if (isset($input['reply'])) {
			$Reply->reply = $this->stripTrim($input['reply']);
		}

		if (isset($input['topic_id'])) {
			$Reply->topic_id = $this->stripTrim($input['topic_id']);
		}

		if (!isset($Reply->user_id)) {
			$Reply->user_id = Auth::user()->id;
		}

		if ($Reply->save()) {
			$this->Reply = $Reply;
			return true;
		} else {
			$this->errors = $Reply::$errors;
			return false;
		}
	}

	/**
	 * Deletes a reply.
	 *
	 * @param $id
	 *
	 * @return bool|mixed
	 */
	public function delete($id)
	{
		$Reply = $this->get($id);
		if ($Reply == null) {
			return false;
		}
		return $Reply->delete();
	}

}