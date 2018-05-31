<?php

namespace App\Http\Controllers\Admin;


use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateTimeZone;


class StoreGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $resellerId)
    {

        $storegroups = Controller::filterUsers(3, $resellerId);

        return view('admin.storegroups.index', ['storegroups' => $storegroups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resellers  = DB::select("select * from users
                                    join users_roles on id = user_id
                                    where role_id = 2
                                    order by name");
        
        return view('admin.storegroups.new', ['resellers' => $resellers]);
    }
    
    
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:200',
            'name'          => 'required|max:200',
            'email'         => 'required|email|max:255',
            'phone_number'  => 'required|max:45',
            'address'       => 'required',
            'city'          => 'required|max:100',
            'state'         => 'required|max:100',
            'country'       => 'required|max:100',
            'zipcode'       => 'required|max:30'
        ]);
        
        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });
            
        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
        
        $created_at = new DateTime();
        $created_at->setTimezone(new DateTimeZone("America/New_York"));
        
        $usersTable = DB::table('users');
        
        $data = [
            'parent_id'       => $request->get('reseller_id'),
            'business_name'   => $request->get('business_name'),    // Store Group Name
            'name'            => $request->get('name'),             // Contact Name
            'email'           => $request->get('email'),
            'phone_number'    => $request->get('phone_number'),
            'address'         => $request->get('address'),
            'address2'        => $request->get('address2'),
            'city'            => $request->get('city'),
            'state'           => $request->get('state'),
            'country'         => $request->get('country'),
            'zipcode'         => $request->get('zipcode'),
            'created_at'      => $created_at,
            'updated_at'      => $created_at
        ];
        
        if ($request->has('password')) {
            $data['password'] = bcrypt($request->get('password'));
        }
        
        $id = $usersTable->insertGetId($data);
        DB::table('users_roles')->insert(['user_id' => $id, 'role_id' => 3]);
        
        return redirect()->intended(route('admin.storegroups', 0));
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
    public function show(User $storegroup)
    {
        return view('admin.storegroups.show', ['storegroup' => $storegroup]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $storegroup)
    {
        $resellers  = DB::select("select * from users 
                                    join users_roles on id = user_id 
                                    where role_id = 2
                                    order by name");
        return view('admin.storegroups.edit', ['storegroup' => $storegroup, 'roles' => Role::get(), 'resellers' => $resellers]);
        //return view('admin.users.edit', ['user' => $user, 'roles' => Role::get()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $storegroup)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:200',
            'name'          => 'required|max:200',
            'email'         => 'required|email|max:255',
            'phone_number'  => 'required|max:45',
            'address'       => 'required',
            'city'          => 'required|max:100',
            'state'         => 'required|max:100',
            'country'       => 'required|max:100',
            'zipcode'       => 'required|max:30'
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($storegroup) {
            return strtolower($input->email) != strtolower($storegroup->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $storegroup->business_name   = $request->get('business_name');
        $storegroup->name            = $request->get('name');
        $storegroup->email           = $request->get('email');
        $storegroup->phone_number    = $request->get('phone_number');
        $storegroup->address         = $request->get('address');
        $storegroup->address2        = $request->get('address2');
        $storegroup->city            = $request->get('city');
        $storegroup->state           = $request->get('state');
        $storegroup->country         = $request->get('country');
        $storegroup->zipcode         = $request->get('zipcode');
        $storegroup->parent_id       = $request->get('reseller_id');

        if ($request->has('password')) {
            $storegroup->password = bcrypt($request->get('password'));
        }

        $storegroup->active      = $request->get('active', 0);

        $storegroup->save();

        //roles
        if ($request->has('roles')) {
            $storegroup->roles()->detach();

            if ($request->get('roles')) {
                $storegroup->roles()->attach($request->get('roles'));
            }
        }

        return redirect()->intended(route('admin.storegroups', 0));
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
