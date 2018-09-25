<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use DateTime;
use DateTimeZone;
use PhpParser\Builder\Use_;

class StoreController extends Controller
{
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $storegroupId)
    {
        $accessDenied = Controller::canIsee(Auth::user(), $storegroupId);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        $stores = Controller::filterUsers($request, 4, $storegroupId, $request->filter);
        
        return view('admin.stores.index', ['stores' => $stores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, User $storegroup)
    {
        $store = new User;
        $store->active = true;
        
        // StoreGroups ------------------------------------------------------- //
        $storegroups = array();
        
        $me = Auth::user();
        echo "role_id: " . $me->roles[0]->id ."<br>";
        if ($me->roles[0]->id == 3) {
            $storegroups[0] = $me;
            
        } else {
            $storegroups  = Controller::filterUsers(null, 3, $me->id, $request->filter);
        }
        // ------------------------------------------------------- StoreGroups //
        
        $countries = DB::select("select * from countries order by name");
        
        $store->country = 231;   // United States
        
        return view('admin.form', ['obj' => 'store', 'user' => $store, 'parents' => $storegroups, 
            'countries' => $countries, 'me' => $me]);
    }
    
    
    public function insert(Request $request)
    {
        $created_at = new DateTime();
        $created_at->setTimezone(new DateTimeZone("America/New_York"));
        
        $usersTable = DB::table('users');
        
        $data = [
            'parent_id'       => $request->get('parent_id'),    // Store Group ID
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
        
        // Insert Settings ------------------------------------------------------------ //
        $settingsTable = DB::table('settings');
        
        $data = [
            'guid'                     => Uuid::uuid4(),
            'store_guid'               => $data['store_guid'],
            'server_address'           => "",
            'server_username'          => "",
            'server_password'          => "",
            'socket_port'              => 1111,
            'auto_done_order_hourly'   => 0,
            'auto_done_order_time'     => 0,
            'timezone'                 => "America/New_York",
            'smart_order'              => 0,
            'licenses_quantity'        => 0,
            'store_key'                => substr(Uuid::uuid4(), 0, 8)
        ];

        $settingsTable->insert($data);
        // ---------------------------------------------------------------------------- //

        //return redirect()->intended(route('admin.stores.edit', [$id, 'filter' => false])); // keep on the page
        return redirect()->intended(route('admin.stores', [0, 'filter' => false])); // go to the list
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
        $accessDenied = Controller::canIsee(Auth::user(), $store->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        $state   = DB::table('states')->where(['id' => $store->state])->first();
        $country = DB::table('countries')->where(['id' => $store->country])->first();
        
        $store->state   = $state->name;
        $store->country = $country->name;
        
        return view('admin.stores.show', ['store' => $store]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $store)
    {
        $accessDenied = Controller::canIsee(Auth::user(), $store->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        // StoreGroups ------------------------------------------------------- //
        $storegroups = array();
        
        $me = Auth::user();
        //echo "role_id: " . $me->roles[0]->id ."<br>";
        if ($me->roles[0]->id == 3) {
            $storegroups[0] = $me;
            
        } else {
            $storegroups  = Controller::filterUsers(null, 3, $me->id, $request->filter);
        }
        // ------------------------------------------------------- StoreGroups //
        
        $countries  = DB::select("select * from countries order by name");
        
        $states     = [];
        if (isset($store->country) && $store->country != "") {
            $states     = DB::select("select * from states where country_id = $store->country order by name");
        }
        
//         $cities = [];
//         if (isset($store->state) && $store->state != "") {
//             $cities     = DB::select("select * from cities where state_id = $store->state order by name");
//         }
        
        return view('admin.form', ['obj' => 'store', 'user' => $store, 'parents' => $storegroups, 
            'countries' => $countries, 'states' => $states, 'me' => $me]);
    }


    public function config(Request $request, User $store)
    {
        $accessDenied = Controller::canIsee(Auth::user(), $store->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        if(isset($store->store_guid) and $store->store_guid != '') {
             $settings = DB::table('settings')->where(['store_guid' => $store->store_guid])->first();
             
             $devices  = DB::table('devices')
             ->where(['store_guid' => $store->store_guid])
             ->where('is_deleted', '<>',  1)
             ->orderBy('license','desc')
             ->orderBy('id','asc')->paginate(50);
        }
     
        if(!isset($settings)) {
            $settings = null;
        }
        
        if(!isset($devices)) {
            $devices = [];
        }
        
        $activeLicenses = 0;
        foreach ($devices as &$device) {
            $activeLicenses += $device->split_screen_parent_device_id == 0 ? $device->license : 0;
        }
        $licenseInfo = "Licenses: $activeLicenses / $settings->licenses_quantity";
        
        $adminSettings = DB::table('admin_settings')->first();
        
        return view('admin.stores.config', [
            'store' => $store, 
            'devices'=> $devices, 
            'settings' => $settings, 
            'licenseInfo' => $licenseInfo, 
            'selected' => $request->selected, 
            'adminSettings' => $adminSettings
        ]);
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
        $store->parent_id       = $request->get('parent_id');
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
        
        if ($store->id == Auth::user()->id) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // return redirect()->intended(route('admin.stores.edit', [$store->id, 'filter' => false])); // keep on the same page
        return redirect()->intended(route('admin.stores', [0, 'filter' => false])); // go to the list
    }
    
    
    public function updateSettings(Request $request, User $store)
    {
        
        if(isset($store->store_guid) and $store->store_guid != '') {
            $settings = DB::table('settings')->where(['store_guid' => $store->store_guid])->first();
        }
        
        $settingsTable = DB::table('settings');
        
        // auto_done_order_time
        $auto_done_order_time = explode(":", $request->get('auto_done_order_time'));
        $kdsTime = new DateTime();
        $kdsTime->setTimezone(new DateTimeZone(isset($store->timezone_) ? $store->timezone_ : "America/New_York"));
        $kdsTime->setTime($auto_done_order_time[0], $auto_done_order_time[1]);
        $auto_done_order_time = $kdsTime->getTimestamp();
        
        $data = [
                    'server_address'           => $request->get('server_address'),
                    'server_username'          => $request->get('server_username'),
                    'server_password'          => $request->get('server_password'),
                    'socket_port'              => $request->get('socket_port'),
                    'auto_done_order_hourly'   => $request->get('auto_done_order_hourly'),
                    'auto_done_order_time'     => $auto_done_order_time,
                    //'timezone'                 => $request->get('timezone'),
                    'smart_order'              => $request->get('smart_order'),
                    'licenses_quantity'        => $request->get('licenses_quantity'),
                    'update_time'              => time()
                ];


        if (empty($request->get('store_key'))) {
            $data['store_key'] = substr(Uuid::uuid4(), 0, 8);
        }

        if(!isset($settings)) {
            
            $data['store_guid'] = $store->store_guid; 
            $settingsTable->insert($data);
            
        } else {
            
            $settingsTable->where('store_guid', $store->store_guid)->update($data);
            
        }
        
        return redirect()->intended(route('admin.stores.config', [$store->id]));
    }
    
    
    public function updateTwilio(Request $request, User $store)
    {
        
        $settingsTable = DB::table('settings');
        
        $sms_start_enable = $request->get('sms_start_enable') !== null ? $request->get('sms_start_enable') : 0;
        $sms_ready_enable = $request->get('sms_ready_enable') !== null ? $request->get('sms_ready_enable') : 0;
        $sms_done_enable  = $request->get('sms_done_enable') !== null ? $request->get('sms_done_enable') : 0;
        
        $sms_start_use_default = $request->get('sms_start_use_default') !== null ? $request->get('sms_start_use_default') == 'on' ? 0: 1 : 1;
        $sms_ready_use_default = $request->get('sms_ready_use_default') !== null ? $request->get('sms_ready_use_default') == 'on' ? 0: 1 : 1;
        $sms_done_use_default  = $request->get('sms_done_use_default') !== null ? $request->get('sms_done_use_default') == 'on' ? 0: 1 : 1;
        
        $sms_start_use_default = $sms_start_enable == 1 ? $sms_start_use_default : 1;
        $sms_ready_use_default = $sms_ready_enable == 1 ? $sms_ready_use_default : 1;
        $sms_done_use_default  = $sms_done_enable  == 1 ? $sms_done_use_default : 1;

        $sms_start_use_default = $request->get('sms_start_custom') == "" ? 1 : $sms_start_use_default;
        $sms_ready_use_default = $request->get('sms_ready_custom') == "" ? 1 : $sms_ready_use_default;
        $sms_done_use_default  = $request->get('sms_done_custom')  == "" ? 1 : $sms_done_use_default;
        
        $data = [
            'sms_account_sid'           => $request->get('sms_account_sid'),
            'sms_token'                 => $request->get('sms_token'),
            'sms_phone_from'            => $request->get('sms_phone_from'),
            
            'sms_start_enable'          => $sms_start_enable,
            'sms_start_use_default'     => $sms_start_use_default,

            'sms_ready_enable'          => $sms_ready_enable,
            'sms_ready_use_default'     => $sms_ready_use_default,

            'sms_done_enable'           => $sms_done_enable,
            'sms_done_use_default'      => $sms_done_use_default,
            
            'update_time'              => time()
        ];
        
        if ($sms_start_enable == 1 && $sms_start_use_default == 0) {
            $data['sms_start_custom'] = $request->get('sms_start_custom');
        }
        if ($sms_ready_enable == 1 && $sms_ready_use_default == 0) {
            $data['sms_ready_custom'] = $request->get('sms_ready_custom');
        }
        if ($sms_done_enable == 1 && $sms_done_use_default == 0) {
            $data['sms_done_custom'] = $request->get('sms_done_custom');
        }
        
        $settingsTable->where('store_guid', $store->store_guid)->update($data);
        
        return redirect()->intended(route("admin.stores.config#marketplace", [$store->id]));
    }
    
    
    public function report(Request $request, User $store)
    {
        if(isset($store->store_guid) and $store->store_guid != '') {
            $devices  = DB::table('devices')
            ->where(['store_guid' => $store->store_guid])
            ->where('is_deleted', '<>',  1)
            ->orderBy('license','desc')
            ->orderBy('id','asc')->paginate(50);
        }
        
        if(!isset($devices)) {
            $devices = [];
        }
        
        return view('admin.stores.report', ['store' => $store, 'devices' => $devices]);
    }
    
    
    public function reportByStation(Request $request)
    {
        $devicesIds = $request->get('devicesIds');
        
        $startDatetime = strtotime($request->get('startDatetime'));
        $endDatetime   = strtotime($request->get('endDatetime'));
        
        // *** must to be divided by orders_count beacause it already was divided by item_count
        $sql = "SELECT 
                    	select_orders.device_name AS device_name,
                    SUM(select_orders.order_count) AS order_count,
                    SUM(select_orders.item_count) AS item_count,
                    SUM(select_orders.order_avg_time) / SUM(select_orders.order_count) AS order_avg_time,
                    SUM(select_orders.item_avg_time) / SUM(select_orders.order_count) AS item_avg_time, -- ***
                    MAX(select_orders.active) AS active
                FROM
                    	(SELECT 
                        	dn.name AS device_name,
                        	count(distinct i.order_guid) AS order_count,
                        	count(distinct i.guid) AS item_count,
                            
                        	max((case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_local_time else ib.prepared_local_time end) - 
                        		(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then 
                                (case when ib.prepared_local_time = 0 then ib.create_local_time else ib.prepared_local_time end) else ib.create_local_time end)) / 
                                count(distinct i.order_guid) AS order_avg_time,
                                
                        	max((case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_local_time else ib.prepared_local_time end) - 
                        		(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then 
                                (case when ib.prepared_local_time = 0 then ib.create_local_time else ib.prepared_local_time end) else ib.create_local_time end)) / 
                                count(distinct i.guid) AS item_avg_time,
                                
                        	dn.login AS active
                            
                        FROM item_bumps ib
                        JOIN items i ON ib.guid = i.item_bump_guid
                        JOIN devices d ON d.id <> 0 AND d.is_deleted = 0
                        
                        JOIN devices dn ON dn.store_guid = d.store_guid AND dn.id = 
                        	(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id else ib.prepared_device_id end)
                              
                        JOIN users u ON u.store_guid = d.store_guid
                        
                        WHERE u.id = " . $request->get('storeId') . 
                        " AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id
                              else ib.prepared_device_id end) != 0";
        
        $sql .=         " AND ( (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') 
                            then ib.done_local_time else ib.prepared_local_time end) >= $startDatetime 
                            AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') 
                            then ib.done_local_time else ib.prepared_local_time end) <= $endDatetime)";
        
        if($devicesIds != "") {
            $sql .=     " AND d.id IN (" . implode(",", $devicesIds) . ") ";
        }
        
        $sql .=     "GROUP BY dn.name, dn.login, i.order_guid) select_orders
                GROUP BY select_orders.device_name";
        
        $reportData = DB::select($sql);

        return $reportData;
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



