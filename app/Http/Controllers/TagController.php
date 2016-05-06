<?php namespace App\Http\Controllers;

use App\Repositories\Tag\TagRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

/**
 * Class TagController
 * @package App\Http\Controllers
 */
class TagController extends Controller
{

	/**
	 * Constructor for TagController.
	 *
	 * @param TagRepository $tag
	 */
	public function __construct(TagRepository $tag)
	{
		parent::__construct();
		$this->tag = $tag;
	}

	/**
	 * Render index view for tags.
	 * @permission view_tags
	 * @param  int $id id for tag to display.
	 * @return object     view object.
	 */
	public function index($id = null)
	{
		$tag = null;

		if (is_numeric($id)) {
			$tag = $this->tag->get($id);
		}

		return View::make('tag.index')->with('title', 'Tags')->with('tags', $this->tag->get())->with('tag', $tag);
	}

	/**
	 * Create or update a tag.
	 * @permission create_update_tags
	 * @param  int $id id for tag to update.
	 * @return object     redirect object.
	 */
	public function createOrUpdate($id = null)
	{
		if ($this->tag->createOrUpdate($this->request->all(), $id)) {
			if (is_null($id)) {
				return Redirect::to('tags')->with('success', 'Your tag has been created.');
			}
			return Redirect::to('tags')->with('success', 'Your tag has been updated.');
		}

		return Redirect::back()->withErrors($this->tag->getErrors())->withInput($this->request->all());
	}

	/**
	 * Delete a tag.
	 * @permission delete_tags
	 * @param  int $id id for tag to delete.
	 * @return object     redirect object.
	 */
	public function delete($id)
	{
		if ($this->tag->delete($id)) {
			return Redirect::to('tags')->with('success', 'The tag has been deleted.');
		}

		return Redirect::back()->with('error', 'The tag could not be deleted.');
	}

}