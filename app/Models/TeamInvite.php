<?php namespace App\Models;

use App\Models\Traits\TeamInviteTrait;

/**
 * Class TeamInvite
 * @package App\Models
 */
class TeamInvite extends Model {

	use TeamInviteTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'teaminvites';

	/**
	 * Array with fields that user are allowed to fill.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'team_id', 'type', 'email', 'accept_token', 'deny_token'];

	/**
	 * Array with fields that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * Array with rules for fields.
	 *
	 * @var array
	 */
	public static $rules = [
		'email' => 'required|email',
		'type' => 'in:invite, request',
		'accept_token' => 'required',
		'deny_token' => 'required',
		'user_id' => 'integer',
		'team_id' => 'integer',
	];

}