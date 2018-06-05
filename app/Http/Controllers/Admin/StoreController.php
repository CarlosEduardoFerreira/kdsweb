<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use DateTime;
use DateTimeZone;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $storegroupId)
    {

        $stores = Controller::filterUsers($request, 4, $storegroupId);

        return view('admin.stores.index', ['stores' => $stores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $storegroups = DB::select("select * from users
                                    join users_roles on id = user_id
                                    where role_id = 3
                                    order by name");
        
        $countries = DB::select("select * from countries order by name");
        
        return view('admin.stores.new', ['storegroups' => $storegroups, 'countries' => $countries]);
    }
    
    
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:200',
            'dba'           => 'required|max:200',
            'last_name'     => 'required|max:100',
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
            'parent_id'       => $request->get('storegroup_id'),    // Store Group ID
            'business_name'   => $request->get('business_name'),    // Legal Business Name
            'dba'             => $request->get('dba'),              // DBA: (Doing business as)
            'last_name'       => $request->get('last_name'),
            'name'            => $request->get('name'),             // First Name
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
        
        $data['store_guid'] = Uuid::uuid4();
        
        $id = $usersTable->insertGetId($data);
        DB::table('users_roles')->insert(['user_id' => $id, 'role_id' => 4]);
        
        return redirect()->intended(route('admin.stores', 0));
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
        $storegroups = DB::select("select * from users
                                    join users_roles on id = user_id
                                    where role_id = 3
                                    order by name");
        
        $countries  = DB::select("select * from countries order by name");
        
        $states     = [];
        if (isset($store->country) && $store->country != "") {
            $states     = DB::select("select * from states where country_id = $store->country order by name");
        }
        
        $cities = [];
        if (isset($store->state) && $store->state != "") {
            $cities     = DB::select("select * from cities where state_id = $store->state order by name");
        }
        
        return view('admin.stores.edit', ['store' => $store, 'roles' => Role::get(), 'storegroups' => $storegroups, 
            'countries' => $countries, 'states' => $states, 'cities' => $cities]);
    }


    public function config(User $store)
    {
//         $settings = new Settings;
//         $devices  = new Device;
        
        //echo "store->store_guid: " . $store->store_guid;
        if(isset($store->store_guid) and $store->store_guid != '') {
             $settings = DB::table('settings')->where(['store_guid_' => $store->store_guid])->first();
             $devices  = DB::table('devices')->where(['store_guid_' => $store->store_guid])
             ->orderBy('login_','desc')
             ->orderBy('id_','asc')->paginate(50);
        }
     
        if(!isset($settings)) {
            $settings = null;
        }
        
        if(!isset($devices)) {
            $devices = [];
        }
        
        $activeLicenses = 0;
        foreach ($devices as &$device) {
            $activeLicenses += $device->login_;
        }
        $licenseInfo = "Licenses: $activeLicenses / $settings->licenses_quantity_";
        
        return view('admin.stores.config', ['store' => $store, 'devices'=> $devices, 'settings' => $settings, 'licenseInfo' => $licenseInfo]);
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
            'business_name' => 'required|max:200',
            'dba'           => 'required|max:200',
            'last_name'     => 'required|max:100',
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

        $validator->sometimes('email', 'unique:users', function ($input) use ($store) {
            return strtolower($input->email) != strtolower($store->email);
        });

        $validator->sometimes('password', 'min:6|confirmed', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
        
        $store->parent_id       = $request->get('storegroup_id');
        $store->business_name   = $request->get('business_name');
        $store->dba             = $request->get('dba');
        $store->last_name       = $request->get('last_name');
        $store->name            = $request->get('name');
        $store->email           = $request->get('email');
        $store->phone_number    = $request->get('phone_number');
        $store->address         = $request->get('address');
        $store->city            = $request->get('city');
        $store->state           = $request->get('state');
        $store->country         = $request->get('country');
        $store->zipcode         = $request->get('zipcode');
        $store->username        = $request->get('username');

        if ($request->get('password') != "") {
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
    
    
    public function updateSettings(Request $request, User $store)
    {
        
        if(isset($store->store_guid) and $store->store_guid != '') {
            $settings = DB::table('settings')->where(['store_guid_' => $store->store_guid])->first();
        }
        
        $settingsTable = DB::table('settings');
        
        // auto_done_order_time
        $auto_done_order_time = explode(":", $request->get('auto_done_order_time'));
        $kdsTime = new DateTime();
        $kdsTime->setTimezone(new DateTimeZone(isset($store->timezone_) ? $store->timezone_ : "America/New_York"));
        $kdsTime->setTime($auto_done_order_time[0], $auto_done_order_time[1]);
        $auto_done_order_time = $kdsTime->getTimestamp();
        
        $data = [
                    'server_address_'           => $request->get('server_address'),
                    'server_username_'          => $request->get('server_username'),
                    'server_password_'          => $request->get('server_password'),
                    'socket_port_'              => $request->get('socket_port'),
                    'auto_done_order_hourly_'   => $request->get('auto_done_order_hourly'),
                    'auto_done_order_time_'     => $auto_done_order_time,
                    'timezone_'                 => $request->get('timezone'),
                    'smart_order_'              => $request->get('smart_order'),
                    'licenses_quantity_'        => $request->get('licenses_quantity')
                ];
        
        if(!isset($settings)) {
            
            $data['store_guid_'] = $store->store_guid; 
            $settingsTable->insert($data);
            
        } else {
            
            $settingsTable->where('store_guid_', $store->store_guid)->update($data);
            
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
