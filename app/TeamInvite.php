<?php namespace App;

use App\ModelTraits\TeamInviteTrait;

class TeamInvite extends Model {

	use TeamInviteTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'teaminvites';

	protected $fillable = array('user_id', 'team_id', 'type', 'email', 'accept_token', 'deny_token');

	protected $guarded = array('id');

	public static $rules = array(
		'email'  => 'required|email',
		'type' => 'in:invite, request',
		'accept_token' => 'required',
		'deny_token' => 'required',
		'user_id' => 'integer',
		'team_id' => 'integer'
	);

}