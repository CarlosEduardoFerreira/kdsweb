<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $storegroupId)
    {

        $store = Controller::filterUsers(4, $storegroupId);

        return view('admin.stores.index', ['stores' => $store]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $store)
    {
        return view('admin.stores.show', ['store' => $store]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $store)
    {
        return view('admin.stores.edit', ['store' => $store, 'roles' => Role::get()]);
    }


    public function config(User $store)
    {
        return view('admin.stores.config', ['store' => $store]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $store)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'active' => 'sometimes|boolean',
            'confirmed' => 'sometimes|boolean',

            // new fields
            'username' => 'required|max:45',
            'last_name' => 'required|max:100',
            'business_name' => 'required|max:200',
            'dba' => 'required|max:200',
            'phone_number' => 'required|max:45',
            'address' => 'required',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'country' => 'required|max:100',
            'zipcode' => 'required|max:30',
            //'timezone' => 'required|max:100',
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($store) {
            return strtolower($input->email) != strtolower($store->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $store->name            = $request->get('name');
        $store->email           = $request->get('email');

        $store->username        = $request->get('username');
        $store->last_name       = $request->get('last_name');
        $store->business_name   = $request->get('business_name');
        $store->dba             = $request->get('dba');
        $store->phone_number    = $request->get('phone_number');
        $store->address         = $request->get('address');
        $store->city            = $request->get('city');
        $store->state           = $request->get('state');
        $store->country         = $request->get('country');
        $store->zipcode         = $request->get('zipcode');

        if ($request->has('password')) {
            $store->password = bcrypt($request->get('password'));
        }

        $store->active      = $request->get('active', 0);
        $store->confirmed   = $request->get('confirmed', 0);

        $store->save();

        //roles
        if ($request->has('roles')) {
            $store->roles()->detach();

            if ($request->get('roles')) {
                $store->roles()->attach($request->get('roles'));
            }
        }

        return redirect()->intended(route('admin.stores', 0));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
