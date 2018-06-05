<?php

namespace App\Http\Controllers\Admin;


use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateTimeZone;


class ResellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $adminId)
    {

        $resellers = Controller::filterUsers($request, 2, $adminId);

        return view('admin.resellers.index', ['resellers' => $resellers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $admin)
    {
        $countries = DB::select("select * from countries order by name");
        
        return view('admin.resellers.new', ['countries' => $countries]);
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
            'zipcode'       => 'required|max:30',
            'username'      => 'required|max:45'
        ]);
        
        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });
            
        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
        
        $created_at = new DateTime();
        $created_at->setTimezone(new DateTimeZone("America/New_York"));
        
        $usersTable = DB::table('users');
        
        $data = [
            'business_name'   => $request->get('business_name'),    // Reseller Name
            'name'            => $request->get('name'),             // Contact Name
            'email'           => $request->get('email'),
            'phone_number'    => $request->get('phone_number'),
            'address'         => $request->get('address'),
            'address2'        => $request->get('address2'),
            'city'            => $request->get('city'),
            'state'           => $request->get('state'),
            'country'         => $request->get('country'),
            'zipcode'         => $request->get('zipcode'),
            'username'        => $request->get('username'),
            'created_at'      => $created_at,
            'updated_at'      => $created_at
        ];
        
        if ($request->get('password') != "") {
            $data['password'] = bcrypt($request->get('password'));
        }
        
        $id = $usersTable->insertGetId($data);
        DB::table('users_roles')->insert(['user_id' => $id, 'role_id' => 2]);
        
        return redirect()->intended(route('admin.resellers', 0));
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
    public function show(User $reseller)
    {
        return view('admin.resellers.show', ['reseller' => $reseller]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $reseller)
    {
        $countries  = DB::select("select * from countries order by name");
        
        $states     = [];
        if (isset($reseller->country) && $reseller->country != "") {
            $states     = DB::select("select * from states where country_id = $reseller->country order by name");
        }
        
        $cities = [];
        if (isset($reseller->state) && $reseller->state != "") {
            $cities     = DB::select("select * from cities where state_id = $reseller->state order by name");
        }
        
        return view('admin.resellers.edit', ['reseller' => $reseller, 'roles' => Role::get(), 
            'countries' => $countries, 'states' => $states, 'cities' => $cities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $reseller)
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
            'zipcode'       => 'required|max:30',
            'username'      => 'required|max:45'
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($reseller) {
            return strtolower($input->email) != strtolower($reseller->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
        
        $reseller->name            = $request->get('name');
        $reseller->email           = $request->get('email');

        $reseller->username        = $request->get('username');
        $reseller->last_name       = $request->get('last_name');
        $reseller->business_name   = $request->get('business_name');
        $reseller->dba             = $request->get('dba');
        $reseller->phone_number    = $request->get('phone_number');
        $reseller->address         = $request->get('address');
        $reseller->address2        = $request->get('address2');
        $reseller->city            = $request->get('city');
        $reseller->state           = $request->get('state');
        $reseller->country         = $request->get('country');
        $reseller->zipcode         = $request->get('zipcode');
        
        $updated_at = new DateTime();
        $updated_at->setTimezone(new DateTimeZone("America/New_York"));
        $reseller->updated_at      = $updated_at;

        if ($request->get('password') != "") {
            $reseller->password = bcrypt($request->get('password'));
        }

        $reseller->active      = $request->get('active', 0);
        $reseller->confirmed   = $request->get('confirmed', 0);

        $reseller->save();

        //roles
        if ($request->has('roles')) {
            $reseller->roles()->detach();

            if ($request->get('roles')) {
                $reseller->roles()->attach($request->get('roles'));
            }
        }

        return redirect()->intended(route('admin.resellers', 0));
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
