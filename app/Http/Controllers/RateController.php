<?php namespace App\Http\Controllers;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\Rate\RateRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 * Class RateController
 * @package App\Http\Controllers
 */
class RateController extends Controller
{

	/**
	 * Constructor for RateController.
	 *
	 * @param RateRepository $rate
	 */
	public function __construct(RateRepository $rate)
	{
		parent::__construct();
		$this->rate = $rate;
	}

	/**
	 * Adds one on comment rate.
	 *
	 * @param CommentRepository $comment
	 * @param  int $id
	 *
	 * @return object
	 */
	public function plus(CommentRepository $comment, $id)
	{
		try {
			$user_id = $comment->get($id)->user_id;
			if ($user_id != Auth::user()->id) {
				if ($this->rate->rate($id, '+')) {
					return Redirect::back()->with('success', 'You have now + rated a comment.');
				}
			}
		} catch (\Exception $e) {
		}

		return Redirect::back()->with('error', 'You could not rate that comment, please try agian.');
	}

	/**
	 * Substract one for comment rate.
	 *
	 * @param CommentRepository $comment
	 * @param  int $id
	 *
	 * @return object
	 */
	public function minus(CommentRepository $comment, $id)
	{
		try {
			$user_id = $comment->get($id)->user_id;
			if ($user_id != Auth::user()->id) {
				if ($this->rate->rate($id, '-')) {
					return Redirect::back()->with('success', 'You have now - rated a comment.');
				}
			}
		} catch (\Exception $e) {
		}

		return Redirect::back()->with('error', 'You could not rate that comment, please try agian.');
	}

}