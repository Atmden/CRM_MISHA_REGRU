<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Account\AccountResource;
use App\Http\Resources\Account\AccountToEditResource;
use App\Models\User;
use App\Notifications\AttachUserNotification;
use App\Notifications\CommentPlanNotification;
use App\Notifications\NewUserNotification;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Validator;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function password_reset_post(Request $request)
    {
        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)->first();
        if (!$tokenData) return $this->sendError('Произошла ошибка, обратитесь к Администратору', 'Произошла ошибка, обратитесь к Администратору', 200);

        $user = User::where('email', $tokenData->email)->first();
        if (!$user) return $this->sendError('Произошла ошибка, обратитесь к Администратору', 'Произошла ошибка, обратитесь к Администратору', 200);

        if ($request->newpassword == $request->retrypassword)
        {
            $user->password = Hash::make($request->newpassword);
            $user->open_pass =$request->newpassword;
            $user->save();
        }

        DB::table('password_resets')
            ->where('token', $request->token)->delete();

        return $this->sendResponse($request->toArray(), 'Пароль успешно изменен');
    }

    public function password_reset($token, Request $request)
    {

        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)->first();
        if (!$tokenData) return redirect('/');

        $user = User::where('email', $tokenData->email)->first();
        if (!$user) return redirect('/');

        return view('vue.passwordReset', compact('user','token'));

    }

    private function sendResetEmail($email, $token)
    {
        //Retrieve the user from the database
        $user = User::where('email', $email)->first();
        //Generate, the password reset link. The token generated is embedded in the link
        $link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
            //Here send the link with CURL with an external email API
            Notification::send($user, new ResetPasswordNotification($link));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function send_email(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_email' => 'email',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Не корректный Email', 'Не корректный Email', 200);
        }

        $user = User::where('email', $request->user_email)->first();

        if ($user == null) {
            return $this->sendError('Пользователь с данным Email не найден', 'Пользователь с данным Email не найден', 200);
        }


        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->user_email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);

        //Get the token just created above
        $tokenData = DB::table('password_resets')
            ->where('email', $request->user_email)->first();

        if ($this->sendResetEmail($request->user_email, $tokenData->token)) {
            return $this->sendResponse($request->toArray(), 'Письмо с инструкцией отправлено на почту');
        } else {
            return $this->sendError('Произошла ошибка, обратитесь к Администратору', 'Произошла ошибка, обратитесь к Администратору', 200);
        }


    }

    public function upload_avatar(Request $request)
    {

        $file = $request->file;
        $name = $file->hashName();
        $request->file->move(public_path('user_avatars'), $name);

        $user = User::find($request->user_id);

        if ($user->avatar != null) {
            if (file_exists(public_path() . '/user_avatars/' . $user->avatar)) {
                unlink(public_path() . '/user_avatars/' . $user->avatar);
            }
        }

        $user->avatar = $name;
        $user->save();

        return $this->sendResponse($user->toArray(), 'Avatar change successfully.');
    }

    public function change_password(Request $request)
    {
        $user = $request->user();
        $user->password = Hash::make($request->newpassword);
        $user->phone = $request->newpassword;
        $user->save();
        return $this->sendResponse($user->toArray(), 'Password change successfully.');
    }

    public static function random_number($length)
    {
        return join('', array_map(function ($value) {
            return $value == 1 ? mt_rand(1, 9) : mt_rand(0, 9);
        }, range(1, $length)));
    }

    public function save_accounts_notify(Request $request)
    {
        $user = $request->user();
        foreach ($request->all() as $item) {
            $account = Account::find($item['id']);
            $user->accounts()->updateExistingPivot(
                $account,
                [
                    'notify' => $item['notify']
                ]);
        }
        return $this->sendResponse($user->toArray(), 'Notify save successfully.');
    }

    public function edit_user(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'email|unique:users,email,' . $request->id,
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user = User::find($request->id);
        if ($user != null) {
            $user->email = $request->email;
            $user->can_edit = $request->can_edit;
            $user->can_comment = $request->can_comment;

            if (strlen(trim($request->new_password)) > 0 )
            {
                $user->open_pass = trim($request->new_password);
                $user->password = Hash::make($request->new_password);
            }

            $user->save();
        }
        return $this->sendResponse($user->toArray(), 'User save successfully.');
    }

    public function deattach_user(Request $request, $id)
    {
        $user = User::find($request->id);
        if ($user != null) {
            $user->accounts()->detach($id);
        }
        return $this->sendResponse($user->toArray(), 'User deattach successfully.');
    }

    public function add_attach_user(Request $request, $id)
    {
        $input = $request;

        // Если пользователя с таким именем нет - создаем, иначе аттач к аккаунту

        $user = User::where('email', $input->email)->first();

        if ($user == null) {
            $user = new User;
            $user->email = $input->email;
            $user->send_email = $input->send_email;
            $user->can_edit = $input->can_edit;
            $user->can_comment = $input->can_comment;
            $pass = self::random_number(5);
            $user->password = Hash::make($pass);
            $user->open_pass = $pass;
            $user->save();
            $user->accounts()->attach($id);
            if ($input->send_email) {
                Notification::send($user, new NewUserNotification($user));
            }
        } else {
            $account = Account::find($id);
            $user->accounts()->sync([$id], false);
            Notification::send($user, new AttachUserNotification($account));
        }


        return $this->sendResponse($user->toArray(), 'User created successfully.');
    }

    public function index()
    {
        $accounts = Account::all();

        return $this->sendResponse(AccountResource::collection($accounts), 'Accounts retrieved successfully.');
    }

    public function my_accounts()
    {
        $user = Auth::user();

        return $this->sendResponse(AccountResource::collection($user->accounts), 'Accounts retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'inn' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $company = Account::create($input);

        return $this->sendResponse($company->toArray(), 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = Account::find($id);

        if (is_null($account)) {
            return $this->sendError('Account not found.');
        }

        return $this->sendResponse(AccountResource::make($account), 'Account retrieved successfully.');
    }

    public function account_to_edit($id)
    {
        $account = Account::find($id);

        if (is_null($account)) {
            return $this->sendError('Account not found.');
        }

        return $this->sendResponse(AccountToEditResource::make($account), 'Account retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $company)
    {
        $input = $request->all();

        $company->update($input);

        return $this->sendResponse($company->toArray(), 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $company)
    {
        $company->delete();

        return $this->sendResponse($company->toArray(), 'Account deleted successfully.');
    }

}
