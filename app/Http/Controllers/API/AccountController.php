<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Account\AccountForUserResource;
use App\Http\Resources\Account\AccountResource;
use App\Http\Resources\Account\AccountToEditResource;
use App\Http\Resources\Account\AccountToNewResource;
use App\Http\Resources\Socnet\SocnetForNewAccountResource;
use App\Models\Socnet;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Validator;

class AccountController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function add_new_account(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'unique:accounts',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $new_account = new Account();
        $new_account->name = $request->name;
        $new_account->online = $request->online;
        $new_account->auto_apply = $request->auto_apply;
        $new_account->save();
        $user = $request->user();
        $new_account->users()->attach($user->id);
        if ($user->id != 1) {
            $new_account->users()->attach(1);
        }


        foreach ($request->soc_net as $item)
        {
            $new_account->socnets()->attach($item['id'], [
                'URI' => $item['url'],
                'online' => $item['online']
            ]);
        }

        return $this->sendResponse($new_account, 'Account created successfully.');
    }

    public function get_new_account()
    {
        $soc_net = Socnet::all();
        $new_account = [
            'name'          =>  '',
            'online'        =>  (string)'0',
            'soc_net'       =>  SocnetForNewAccountResource::collection($soc_net)

        ];
        return $this->sendResponse($new_account, 'Account save successfully.');
    }

    public function edit_account(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'unique:accounts,name,' . $request->id,
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $account = Account::find($request->id);
        if ($account != null) {
            $account->name = $request->name;
            $account->online = $request->online;
            $account->auto_apply = $request->auto_apply;
            $account->save();
        }

        foreach ($request->soc_net as $item) {


            $soc_net = Socnet::find($item);


            $account->socnets()->updateExistingPivot(
                $soc_net,
                [
                    'URI' => $item['url'],
                    'online' => $item['online']
                ]);
        }

        return $this->sendResponse($account->toArray(), 'Account save successfully.');
    }

    public function index()
    {
        $accounts = Account::all();

        return $this->sendResponse(AccountResource::collection($accounts), 'Accounts retrieved successfully.');
    }

    public function my_accounts()
    {
        $user = Auth::user();

        return $this->sendResponse(AccountForUserResource::collection($user->accounts), 'Accounts retrieved successfully.');
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
