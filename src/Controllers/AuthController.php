<?php
namespace Minhbang\User\Controllers;

use Minhbang\Kit\Extensions\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Auth;
use Cache;
use Illuminate\Http\Request;

/**
 * Class AuthController
 *
 * @package Minhbang\User\Controllers
 */
class AuthController extends Controller
{
    use ThrottlesLogins;

    //Maximum number of login attempts for delaying further attempts.
    public $maxLoginAttempts = 5;

    //The number of seconds to delay further login attempts.
    public $lockoutTime = 120;

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLogin()
    {
        return view('user::login');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, ['username' => 'required', 'password' => 'required']);
        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->lockoutResponse($request);
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request);
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        $this->incrementLoginAttempts($request);

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors(['msg' => trans('user::account.credentials_invalid')]);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request)
    {
        $this->clearLoginAttempts($request);

        return redirect()->intended('/');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'username';
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function lockoutResponse(Request $request)
    {
        $seconds = (int)Cache::get($this->getLoginLockExpirationKey($request)) - time();

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors(
                [
                    'msg' => trans('user::account.throttle', compact('seconds')),
                ]
            );

    }
}
