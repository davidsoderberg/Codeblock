<?php namespace App\Models\Traits;

/**
 * Class TeamInviteTrait
 * @package App\Models\Traits
 */
trait TeamInviteTrait
{

    /**
     * Fetch invites team.
     *
     * @return mixed
     */
    public function team()
    {
        return $this->hasOne('App\Models\Team', 'id', 'team_id');
    }

    /**
     * Fetch invites user.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'email', 'email');
    }
}
