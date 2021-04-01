<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'phone';
    }

    protected function guard()
    {
        return Auth::guard('web');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */

    public function login(Request $request)
    {

        $redirect_url = $this->redirectTo;

        $messages = [
            'phone.*' => "올바른 전화번호를 입력해주세요.",
            'password.*' => "비밀번호를 입력해주세요.",
        ];

        $validator = $request->validate([
            'phone' => 'bail|required',
            'password' => 'bail|required',
        ], $messages);
        $input = $request->all();

        $remember_me = $request->has('remember_me');

        $credentials = $request->only('phone', 'password');
        $credentials['login_available'] = 'Y';

        if ( Auth::attempt($credentials, $remember_me)) {
			$user = Auth::user();
            
            if ($request->ajax()) {
                return response()->json(array('result' => "OK", "action" => 'redirect', "url" => url()->previous()));
            } else {
                return redirect()->to($redirect_url);
            }
        } else {
            return back()->withInput();
            $user = User::where('phone', $input['phone'])->first();
            if (empty($user)) {
                return response()->json(['errors' => ['phone' => ['사용자를 찾을 수 없습니다.']]], 422);
            } else {
                return response()->json(['errors' => ['phone' => ['전화번호와 비밀번호를 확인해주세요.']]], 422);
            }
        }
    }

    public function showLoginForm(Request $request)
    {
		  return view('login');
    }
    public function logout(){
      Auth::logout();
      $redirect_url = $this->redirectTo;
      return redirect()->to($redirect_url);
    }
}
