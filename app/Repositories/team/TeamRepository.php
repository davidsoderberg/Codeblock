<?php namespace App\Repositories\Team;

use App\Models\Team;
use App\Models\User;
use App\Repositories\IRepository;

/**
 * Interface TeamRepository
 * @package App\Repositories\Team
 */
interface TeamRepository extends IRepository
{

    /**
     * Checks if user is owner for team.
     *
     * @param User $user user th check if is owner for team.
     * @param Team $team team to check if user is owner for.
     * @return bool if user is owner for team.
     */
    public function isOwner(User $user, Team $team);
}
