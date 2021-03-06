<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Mail;
use Session;
use Sentinel;
use Activation;
use App\Http\Requests;
use Centaur\AuthManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Centaur\Mail\CentaurWelcomeEmail;

class RegistrationController extends Controller
{
    /** @var Centaur\AuthManager */
    protected $authManager;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager)
    {
        $this->middleware('sentinel.guest');
        $this->authManager = $authManager;
    }

    /**
     * Show the registration form
     * @return View
     */
    public function getRegister()
    {
        return view('Centaur::auth.register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Response|Redirect
     */
    /**
     * @param Request $request
     * @return mixed
     */
    protected function postRegister(Request $request)
    {
        // Validate the form data
        $rules = [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ];
        $messages = [
            'email.unique' => trans('validation.email.unique'),
            'password_confirmation.same' => trans('validation.password.confirmed'),
            'g-recaptcha-response.required' => trans("validation.captcha.required")
        ];
        $this->validate($request, $rules, $messages);

        // Assemble registration credentials
        $credentials = [
            'email' => trim($request->get('email')),
            'password' => $request->get('password'),
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
        ];

        // Attempt the registration
        $result = $this->authManager->register($credentials);
        //$result = $this->authManager->register($credentials, false);

        if ($result->isFailure()) {
            return $result->dispatch();
        }

        // Send the activation email
        $code = $result->activation->getCode();
        //$this->getActivate($request, $code); //activate in registration
        $email = $result->user->email;
        Mail::to($email)->queue(new CentaurWelcomeEmail($email, $code, trans('user.Your_account_has_been_created')));
        //Mail::to($email)->queue(new CentaurWelcomeEmail($email, $code, trans('user.Welcome_To_Javosh')));

        // Ask the user to check their email for the activation link
        $message = trans('user.Registration_Complete'). " ". trans('user.check_your_email_for_activation');
        //$message = trans('user.Registration_Complete'). " ". trans('user.login_to_your_account');
        $result->setMessage($message);

        // There is no need to send the payload data to the end user
        $result->clearPayload();

        // Return the appropriate response
        return $result->dispatch(route('auth.login.form'));
    }

    /**
     * Activate a user if they have provided the correct code
     * @param  string $code
     * @return Response|Redirect
     */
    public function getActivate(Request $request, $code)
    {
        // Attempt the registration
        $result = $this->authManager->activate($code);

        if ($result->isFailure()) {
            // Normally an exception would trigger a redirect()->back() However,
            // because they get here via direct link, back() will take them
            // to "/";  I would prefer they be sent to the login page.
            $result->setRedirectUrl(route('auth.login.form'));
            return $result->dispatch();
        }

        // Ask the user to check their email for the activation link
        $result->setMessage(trans("user.Registration_complete_log_in"));

        // There is no need to send the payload data to the end user
        $result->clearPayload();

        // Return the appropriate response
        return $result->dispatch(route('home'));
    }

    /**
     * Show the Resend Activation form
     * @return View
     */
    public function getResend()
    {
        return view('Centaur::auth.resend');
    }

    /**
     * Handle a resend activation request
     * @return Response|Redirect
     */
    public function postResend(Request $request)
    {
        // Validate the form data
        $result = $this->validate($request, [
            'email' => 'required|email|max:255'
        ]);

        // Fetch the user in question
        $user = Sentinel::findUserByCredentials(['email' => $request->get('email')]);

        // Only send them an email if they have a valid, inactive account
        if (!Activation::completed($user)) {
            // Generate a new code
            $activation = Activation::create($user);

            // Send the email
            $code = $activation->getCode();
            $email = $user->email;
            Mail::to($email)->queue(new CentaurWelcomeEmail($email, $code, 'Account Activation Instructions'));
        }

        $message = 'New instructions will be sent to that email address if it is associated with a inactive account.';

        if ($request->ajax()) {
            return response()->json(['message' => $message], 200);
        }

        Session::flash('success', $message);
        return redirect(route('home'));
    }
}
