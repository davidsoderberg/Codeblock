<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Services\Transformer;
use Illuminate\Support\Facades\Input;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends ApiController
{

    /**
     * Shows a user.
     *
     * @param UserRepository $user
     * @param null           $id
     *
     * @return mixed
     *
     * @ApiDescription(section="User", description="Get all or one user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/api/v1/users/{id?}")
     * @ApiParams(name="id", type="integer", nullable=true, description="user id")
     */
    public function Users(UserRepository $user, $id = null)
    {
        $users = $this->getCollection($user, $id);
        Transformer::walker($users);

        return $this->response([$this->stringData => $users], 200);
    }

    /**
     * Creating a user.
     *
     * @param UserRepository $user
     *
     * @return mixed
     *
     * @ApiDescription(section="User", description="Create user")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/users")
     * @ApiParams(name="username", type="string", nullable=false, description="username")
     * @ApiParams(name="password", type="string", nullable=false, description="password")
     * @ApiParams(name="email", type="string", nullable=false, description="email")
     */
    public function createUser(UserRepository $user)
    {
        return $this->createOrUpdateUser($user);
    }

    /**
     * Updating a user.
     *
     * @param UserRepository $user
     * @param null           $id
     *
     * @return mixed
     *
     * @ApiDescription(section="User", description="Update user")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/api/v1/users/{id}")
     *
     * @ApiParams(name="id", type="integer", nullable=false, description="user id")
     * @ApiParams(name="username", type="string", nullable=false, description="username")
     * @ApiParams(name="password", type="string", nullable=false, description="password")
     * @ApiParams(name="email", type="string", nullable=false, description="email")
     * @ApiParams(name="role", type="integer", nullable=true, description="user role")
     * @ApiParams(name="active", type="integer", nullable=true, description="If user active")
     */
    public function updateUser(UserRepository $user, $id)
    {
        return $this->createOrUpdateUser($user, $id);
    }

    /**
     * Creating or updating a user.
     *
     * @param UserRepository $user
     * @param null           $id
     *
     * @return mixed
     */
    private function createOrUpdateUser(UserRepository $user, $id = null)
    {
        if (! is_null($id)) {
            if ($id != Auth::user()->id && ! Auth::user()->hasPermission('update_users', false)) {
                return $this->response([$this->stringErrors => [$this->stringUser => 'You are not that user']], 400);
            }
        }
        if ($user->createOrUpdate(Input::all(), $id)) {
            if (is_null($id)) {
                return $this->response([$this->stringMessage => 'Your user has been created, use the link in the mail to activate your user.'],
                    201);
            } else {
                return $this->response([$this->stringMessage => 'Your user has been saved.'], 201);
            }
        }

        return $this->response([$this->stringErrors => $user->getErrors()], 400);
    }
}
