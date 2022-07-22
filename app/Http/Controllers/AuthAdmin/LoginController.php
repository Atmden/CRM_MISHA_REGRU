<?php
namespace App\Http\Controllers\AuthAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use App\Systempage;
class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/admin';
    protected $redirectAfterLogout = '/admin';
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
    public function showLoginForm()
    {
        return view('admin.login',
            [
                //'content' => Systempage::where('page','login_admin')->first(),
            ]
        );
    }
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(route('auth',''));
    }
}
