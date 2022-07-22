<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Account\AccountResource;
use App\Http\Resources\Account\AccountToEditResource;
use App\Http\Resources\Socnet\SocnetResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Socnet;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class SocnetController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function index()
    {
        $socnet = Socnet::all();

        return $this->sendResponse(SocnetResource::collection($socnet), 'Socnets retrieved successfully.');
    }

    public function my_accounts()
    {
        $user = Auth::user();

        return $this->sendResponse(AccountResource::collection($user->accounts), 'Accounts retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'inn' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $company = Account::create($input);

        return $this->sendResponse($company->toArray(), 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $company)
    {
        $company->delete();

        return $this->sendResponse($company->toArray(), 'Account deleted successfully.');
    }

}
