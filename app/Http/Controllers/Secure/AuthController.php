<?php

namespace App\Http\Controllers\Secure;

use App\Helpers\Thumbnail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordConfirmRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\ProfileChangeRequest;
use App\Http\Requests\Secure\UserRequest;
use App\Models\CertificateUser;
use App\Models\Email;
use App\Models\LoginHistory;
use App\Models\User;
use App\Models\Visitor;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Laracasts\Flash\Flash;
use Sentinel;
use Session;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    public function index()
    {
        if (Sentinel::check()) {
            return redirect("/");
        }
        return view('login');
    }

    /**
     * Account sign in.
     *
     * @return View
     */
    public function getSignin()
    {
        if (Sentinel::check()) {
            return redirect("/");
        }
        return view('login');
    }

    /**
     * Account sign up.
     *
     * @return View
     */
    public function getSignup()
    {
        if (Sentinel::check()) {
            return redirect("/");
        }
        return view('register');
    }

    /**
     * Account sign in form processing.
     *
     * @return Redirect
     */
    public function postSignin(LoginRequest $request)
    {
        try {
            if ($user = Sentinel::authenticate($request->only('email', 'password'), $request->has('remember'))) {
                Flash::success(trans('auth.signin_success'));

                $userLogin = new LoginHistory();
                $userLogin->user_id = $user->id;
                $userLogin->ip_address = $request->ip();
                $userLogin->save();

                return redirect("/");
            }
            Flash::error(trans('auth.login_params_not_valid'));
        } catch (NotActivatedException $e) {
            Flash::error(trans('auth.account_not_activated'));
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            Flash::error(trans('auth.account_suspended') . $delay . trans('auth.second'));
        }
        return back()->withInput();
    }

    /**
     * Account sign up form processing.
     *
     * @return Redirect
     */
    public function postSignup(UserRequest $request)
    {
        try {
            $user = Sentinel::registerAndActivate(array(
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'],
                'password' => $request['password'],
            ));
            $role = Sentinel::findRoleBySlug(Settings::get('self_registration_role'));
            if (isset($role)) {
                $role->users()->attach($user);

                if (Settings::get('self_registration_role') == 'visitor') {
                    $visitor = new Visitor();
                    $visitor->user_id = $user->id;
                    $visitor->save();

                    $visitor->visitor_no = Settings::get('visitor_card_prefix') . $visitor->id;
                    $visitor->save();
                }
            }

            Sentinel::loginAndRemember($user);

            Flash::success(trans('auth.signup_success'));
            return redirect('/');

        } catch (UserExistsException $e) {
            Flash::warning(trans('auth.account_already_exists'));
        }
        return back()->withInput();
    }

    /**
     * Account forgot password.
     *
     * @return View
     */
    public function getForgotPassword()
    {
        if (Sentinel::check()) {
            return redirect("/");
        }
        return view('forgot');
    }

    /**
     * Forgot password form processing page.
     *
     * @return Redirect
     */
    public function postForgotPassword(PasswordResetRequest $request)
    {
        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(trans('auth.account_password_recovery'));
        });
        switch ($response) {
            case Password::RESET_LINK_SENT:
                Flash::success(trans($response));
                return redirect()->back();
            case Password::INVALID_USER:
                Flash::error(trans($response));
                return redirect()->back();
        }
    }

    /**
     * Forgot Password Confirmation page.
     *
     * @param  string $passwordResetCode
     * @return View
     */
    public function getForgotPasswordConfirm($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }
        return view('reset')->with('token', $token);
    }

    /**
     * Forgot Password Confirmation form processing page.
     *
     * @param  string $passwordResetCode
     * @return Redirect
     */
    public function postForgotPasswordConfirm(PasswordConfirmRequest $request, $passwordResetCode = null)
    {
        try {
            $user = Sentinel::getUserProvider()->findByResetPasswordCode($passwordResetCode);
            if ($user->attemptResetPassword($passwordResetCode, $request['password'])) {
                Flash::success(trans('auth.forgot-password-confirm-success'));
                return redirect()->route('signin');
            } else {
                Flash::error(trans('auth.forgot-password-confirm-error'));
                return redirect()->route('signin');
            }
        } catch (UserNotFoundException $e) {
            Flash::error(trans('auth.account_not_found'));
            return redirect()->route('forgot-password');
        }
    }

    /**
     * Logout page.
     *
     * @return Redirect
     */
    public function getLogout()
    {
        Sentinel::logout(null, true);
        Flash::success(trans('auth.successfully_logout'));
        Session::flush();
        return redirect('signin');
    }

    /**
     * Profile page.
     *
     * @return Redirect
     */
    public function getProfile()
    {
        if (!Sentinel::check()) {
            return redirect("/");
        }

        $title = trans('auth.user_profile');
        $user = User::find(Sentinel::getUser()->id);
        return view('profile', compact('title', 'user'));
    }

    public function getAccount()
    {
        if (!Sentinel::check()) {
            return redirect("/");
        }
        $title = trans('auth.edit_profile');
        $user = User::find(Sentinel::getUser()->id);

        return view('account', compact('title', 'user'));
    }

    public function postAccount(ProfileChangeRequest $request)
    {
        if (!Sentinel::check()) {
            return redirect("/");
        }

        $user = User::find(Sentinel::getUser()->id);
        if ($request->hasFile('user_avatar_file') != "") {
            $file = $request->file('user_avatar_file');
            $extension = $file->getClientOriginalExtension();
            $picture = str_random(10) . '.' . $extension;

            $destinationPath = public_path() . '/uploads/avatar/';
            $file->move($destinationPath, $picture);
            Thumbnail::generate_image_thumbnail($destinationPath . $picture, $destinationPath . 'thumb_' . $picture);
            $user->picture = $picture;
        }
        if ($request->password != "") {
            $user->password = bcrypt($request->password);
        }
        $user->update($request->except('user_avatar_file', 'password', 'password_confirmation'));
        Flash::success(trans('auth.successfully_change_profile'));
        return redirect('profile');
    }

    public function postWebcam(Request $request)
    {
        $user = User::find(Sentinel::getUser()->id);
        if (isset($request['photo_url'])) {
            $output_file = uniqid() . ".jpg";
            $ifp = fopen(public_path() . '/uploads/avatar/' . $output_file, "wb");
            $data = explode(',', $request['photo_url']);
            fwrite($ifp, base64_decode($data[1]));
            fclose($ifp);
            $user->picture = $output_file;
        }
        $user->update($request->except('photo_url', 'password', 'password_confirmation'));
    }

    public function getCertificate()
    {
        if (!Sentinel::check()) {
            return redirect("/");
        }
        $user = User::find(Sentinel::getUser()->id);
        $certificates = CertificateUser::join('certificates', 'certificates.id', '=', 'certificate_user.certificate_id')
            ->whereNull('certificate_user.deleted_at')
            ->where('user_id', $user->id)
            ->select('certificates.*')->get();
        $title = trans('auth.my_certificate');
        return view('certificate', compact('title', 'certificates','user'));
    }

}
