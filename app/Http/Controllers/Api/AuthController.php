<?php namespace App\Http\Controllers\Api;

use App\Services\Jwt;
use App\Http\Controllers\ApiController;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends ApiController
{

    /**
     * Authenticate the api user.
     * @return mixed
     *
     * @ApiDescription(section="Auth", description="Get users token.")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/auth")
     * @ApiParams(name="username", type="string", nullable=false, description="username")
     * @ApiParams(name="password", type="string", nullable=false, description="password")
     */
    public function Auth()
    {
        try {
            Auth::attempt([
                'username' => trim(strip_tags($this->request->get('username'))),
                'password' => trim(strip_tags($this->request->get('password'))),
            ]);
        } catch (\Exception $e) {
        }

        return $this->getJwt();
    }

    /**
     * Sending a new password to user.
     *
     * @param UserRepository $user
     *
     * @return mixed
     *
     * @ApiDescription(section="Auth", description="Get new password for user.")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/api/v1/auth/forgot")
     * @ApiParams(name="email", type="string", nullable=false, description="User email")
     */
    public function forgotPassword(UserRepository $user)
    {
        if ($user->forgotPassword(Input::all())) {
            return $this->response([$this->stringMessage => 'A new password have been sent to you.'], 200);
        }

        return $this->response([$this->stringMessage => "Your email don't exists in our database."], 400);
    }

    /**
     * Creates a json web token.
     * @return mixed
     */
    public function getJwt()
    {
        if (Auth::check()) {
            return $this->response(['token' => Jwt::encode(['id' => Auth::user()->id])], 200);
        }

        return $this->response(['message', 'You could not get your auth token, please try agian'], 400);
    }
}
