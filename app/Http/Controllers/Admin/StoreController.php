<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vars;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use DateTime;
use DateTimeZone;
use PhpParser\Builder\Use_;

class StoreController extends Controller {
    
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
        
        $stores = Controller::filterUsers($request, 4, $storegroupId);
        
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
        
        if ($me->roles[0]->id == 3) {
            $storegroups[0] = $me;
            
        } else {
            $storegroups  = Controller::filterUsers($request, 3, $me->id);
        }
        // ------------------------------------------------------- StoreGroups //
        
        $countries = DB::select("select * from countries order by name");
        
        $store->country = 231;   // United States
        $store->timezone = Vars::$timezoneDefault;
        
        return view('admin.form', ['obj' => 'store', 'user' => $store, 'parents' => $storegroups, 
            'countries' => $countries, 'me' => $me]);
    }
    
    
    public function insert(Request $request)
    {
        $now = new DateTime();
        
        $dateTimezone = new DateTime();
        $dateTimezone->setTimezone(new DateTimeZone($request->get('timezone')));
        $dateTimezone->setTime(4,0);
        
        $usersTable = DB::table('users');
        
        $data = [
            'parent_id'       => $request->get('parent_id'),        // Store Group ID
            'business_name'   => $request->get('business_name'),    // Legal Business Name
            'dba'             => $request->get('dba'),              // DBA: (Doing Business As)
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
            'timezone'        => $request->get('timezone'),
            'username'        => $request->get('username'),
            'created_at'      => $now
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
            'auto_done_order_time'     => $dateTimezone->getTimestamp(),
            'smart_order'              => 0,
            'licenses_quantity'        => 0,
            'store_key'                => substr(Uuid::uuid4(), 0, 8),
            'create_time'              => $now->getTimestamp()
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

        $store->timezone = isset($store->timezone) ? $store->timezone : Vars::$timezoneDefault;
        
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
        
        $store->timezone = isset($store->timezone) ? $store->timezone : Vars::$timezoneDefault;
        
        return view('admin.stores.config', [
            'store' => $store, 
            'devices'=> $devices, 
            'settings' => $settings, 
            'licenseInfo' => $licenseInfo, 
            'selected' => $request->selected, 
            'adminSettings' => $adminSettings
        ]);
    }
    
    
    public function loadDevicesTable(Request $request) {
        
        $devices = [];
        
        if(isset($request->storeGuid) and $request->storeGuid != '') {

            $devices = DB::select("SELECT * FROM devices 
                WHERE store_guid =  '$request->storeGuid' 
                AND is_deleted = 0
                order by license desc , id asc");
        }
        
        return response()->json($devices);
    }
    
    
    public function getDeviceSettings(Request $request) {
        
        $deviceSettings = [];
        
        if(!isset($request->deviceGuid) or $request->deviceGuid == '') {
            return $deviceSettings;
        }
        
        if(!isset($request->deviceScreenId) or $request->deviceScreenId == '') {
            return $deviceSettings;
        }
        
        $deviceSettings = DB::select("SELECT * FROM devices 
            WHERE is_deleted = 0 
            AND guid = '$request->deviceGuid'
            AND screen_id = $request->deviceScreenId");
            
        if(count($deviceSettings) > 0) {
            
            // Settings Local
            $settingsLocal = DB::select("SELECT * FROM settings_local 
                WHERE is_deleted = 0 
                AND device_guid = '$request->deviceGuid'
                AND screen_id = $request->deviceScreenId");
            
            $deviceSettings["settings_local"] = $settingsLocal;
            
            // Settings Line Display
            $settingsLineDisplay = DB::select("SELECT * FROM settings_line_display
                WHERE is_deleted = 0
                AND device_guid = '$request->deviceGuid'
                AND screen_id = $request->deviceScreenId
                ORDER BY column_number"); // Order By "column_number" to be easier to handle on JS file
            
            $deviceSettings["settings_line_display"] = array($settingsLineDisplay);
        }
        
        return response()->json($deviceSettings);
    }
    
    
    public function getExpeditors(Request $request) {
        
        $expeditors = [];
        
        if(!isset($request->storeGuid) or $request->storeGuid == '') {
            return $expeditors;
        }
        
        $expeditors = DB::select("SELECT * FROM devices 
                                        WHERE store_guid = '$request->storeGuid' 
                                        AND is_deleted = 0
                                        AND guid != '$request->deviceGuid' 
                                        AND (`function` = 'EXPEDITOR' OR `function` = 'BACKUP_EXPE') 
                                        ORDER BY name");
        
        return response()->json($expeditors);
    }
    
    
    public function getParentsByFunction(Request $request) {
        
        $parents = [];
        
        if(!isset($request->storeGuid) or $request->storeGuid == '') {
            return $parents;
        }
        
        if(!isset($request->deviceGuid) or $request->deviceGuid == '') {
            return $parents;
        }
        
        if(!isset($request->deviceFunction) or $request->deviceFunction == '') {
            return $parents;
        }
        
        if($request->deviceFunction == 'BACKUP_PREP') {
            $parentFunction = "'PREPARATION','BACKUP_PREP'";
            
        } else if($request->deviceFunction == 'BACKUP_EXPE') {
            $parentFunction = "'EXPEDITOR','BACKUP_EXPE'";
            
        } else {
            $parentFunction = "'PREPARATION'";
        }
        
        $parents = DB::select("SELECT * FROM devices 
                                        WHERE store_guid = '$request->storeGuid'
                                        AND is_deleted = 0
                                        AND `function` IN ($parentFunction) 
                                        AND guid != '$request->deviceGuid' 
                                        ORDER BY name");
        
        return response()->json($parents);
    }
    
    
    public function getTransfers(Request $request) {
        
        $transfers = [];
        
        if(!isset($request->storeGuid) or $request->storeGuid == '') {
            return $transfers;
        }
        
        if(!isset($request->deviceGuid) or $request->deviceGuid == '') {
            return $transfers;
        }
            
        $transfers = DB::select("SELECT * FROM devices 
                                    WHERE store_guid = '$request->storeGuid'
                                    AND is_deleted = 0
                                    AND guid != '$request->deviceGuid' 
                                    AND (`function` = 'PREPARATION' OR `function` = 'BACKUP_PREP') 
                                    ORDER BY name");
        
        return response()->json($transfers);
    }
    
    
    public function updateDevice(Request $request) {
        
        // Store Guid
        $response = $this->validationDeviceFieldInUse($request, "device-settings-store-guid", "Store Guid");
        if(!empty($response)) {
            return $response;
        } 
        $storeGuid = $request->device['device-settings-store-guid'];
        
        // Device Guid
        $response = $this->validationDeviceFieldInUse($request, "device-settings-device-guid", "Device Guid");
        if(!empty($response)) {
            return $response;
        }
        $deviceGuid = $request->device['device-settings-device-guid'];
        
        // Screen ID
        $response = $this->validationDeviceFieldInUse($request, "device-settings-device-screen-id", "Screen ID");
        if(!empty($response)) {
            return $response;
        }
        $deviceScreenId = $request->device['device-settings-device-screen-id'];
        
        // ID
        $response = $this->validationDeviceFieldInUse($request, "device-settings-id", "ID", true, $storeGuid, $deviceGuid, "id");
        if(!empty($response)) {
            return $response;
        }
        
        // Host (Read XML Order)
        $response = $this->validationDeviceFieldInUse($request, "device-settings-host", "Host", true, $storeGuid, $deviceGuid, "xml_order");
        if(!empty($response)) {
            return $response;
        }
        
        // Order Status
        $response = $this->validationDeviceFieldInUse($request, "device-settings-order-status-ontime", "On Time Before");
        if(!empty($response)) {
            return $response;
        }
        $response = $this->validationDeviceFieldInUse($request, "device-settings-order-status-almost", "Almost Delayed After");
        if(!empty($response)) {
            return $response;
        }
        $response = $this->validationDeviceFieldInUse($request, "device-settings-order-status-delayed", "Delayed After");
        if(!empty($response)) {
            return $response;
        }

        $devices = DB::table('devices')
        ->where('store_guid', '=', $storeGuid)
        ->where('guid', '=', $deviceGuid)
        ->where('screen_id', '=', $deviceScreenId)
        ->where('is_deleted', '=', 0);

        if(count($devices) > 0) {

            // -- Update Settings Local --------------------------------------------------------------------- //
            $settingsLocal = DB::table('settings_local')
            ->where('store_guid', '=', $storeGuid)
            ->where('device_guid', '=', $deviceGuid)
            ->where('screen_id', '=', $deviceScreenId)
            ->where('is_deleted', '=', 0);

            if(count($settingsLocal) > 0) {
                $settingsLocal->update([
                    'display_orders_columns'        => $request->device['device-settings-orders-columns'],
                    'display_sort_orders'           => $request->device['device-settings-sort-orders'],
                    'display_order_status_ontime'   => $request->device['device-settings-order-status-ontime'],
                    'display_order_status_almost'   => $request->device['device-settings-order-status-almost'],
                    'display_order_status_delayed'  => $request->device['device-settings-order-status-delayed'],
                    'header_top_left'               => $request->device['device-settings-order-header-top-left'],
                    'header_top_right'              => $request->device['device-settings-order-header-top-right'],
                    'header_bottom_left'            => $request->device['device-settings-order-header-bottom-left'],
                    'header_bottom_right'           => $request->device['device-settings-order-header-bottom-right'],
                    'anchor_enable_new'             => $request->device['device-settings-anchor-enable-new'],
                    'anchor_time_new'               => $request->device['device-settings-anchor-seconds-new'],
                    'anchor_enable_prioritized'     => $request->device['device-settings-anchor-enable-prioritized'],
                    'anchor_time_prioritized'       => $request->device['device-settings-anchor-seconds-prioritized'],
                    'anchor_enable_delayed'         => $request->device['device-settings-anchor-enable-delayed'],
                    'anchor_time_delayed'           => $request->device['device-settings-anchor-seconds-delayed'],
                    'anchor_enable_ready'           => $request->device['device-settings-anchor-enable-ready'],
                    'anchor_time_ready'             => $request->device['device-settings-anchor-seconds-ready'],
                    'summary_enable'                => $request->device['device-settings-summary-enable'],
                    'summary_type'                  => $request->device['device-settings-summary-type'],
                    'update_time'   => time()
                ]);
            }
            // --------------------------------------------------------------------- Update Settings Local -- //

            // -- Update Settings Line Display -------------------------------------------------------------- //
            if($request->device['device-settings-line-display-enable']) {
                for($i = 1; $i <= 4; $i++) {
                    DB::table('settings_line_display')
                    ->where('store_guid', '=', $storeGuid)
                    ->where('device_guid', '=', $deviceGuid)
                    ->where('screen_id', '=', $deviceScreenId)
                    ->where('is_deleted', '=', 0)
                    ->where('column_number', '=', $i)
                    ->update([
                        'column_name'       => $request->device["device-settings-line-display-column-$i-text"],
                        'column_percent'    => $request->device["device-settings-line-display-column-$i-percent"],
                        'update_time'       => time()
                    ]);
                }
            }
            // -------------------------------------------------------------- Update Settings Line Display -- //

            // -- Update Device ----------------------------------------------------------------------------- //
            $data = [
                'name'                      => $request->device['device-settings-name'],
                'id'                        => $request->device['device-settings-id'],
                'function'                  => $request->device['device-settings-function'],
                'expeditor'                 => $request->device['device-settings-expeditor'],
                'parent_id'                 => isset($request->device['device-settings-parent-id']) ? $request->device['device-settings-parent-id'] : 0,
                'xml_order'                 => $request->device['device-settings-host'],
                'line_display'              => $request->device['device-settings-line-display-enable'],
                'bump_transfer_device_id'   => $request->device['device-settings-line-display-transfer-device-id'],
                'printer_ethernet_enable'   => $request->device['device-settings-printer-network-enable'],
                'printer_address'           => $request->device['device-settings-printer-network-ip'],
                'printer_port'              => $request->device['device-settings-printer-network-port'],
                'printer_print_receives'    => $request->device['device-settings-printer-network-new-enable'],
                'printer_print_bumps'       => $request->device['device-settings-printer-network-bump-enable'],
                'update_time'   => time()
            ];
            $devices->update($data);
            // ----------------------------------------------------------------------------- Update Device -- //
            
        } else {
            $response = array();
            $response["errorId"]  = "kds-web-error";
            $response["errorMsg"] = "KDS Station not found. (DB).";
            return response()->json($response);
        }
    }
    
    
    function validationDeviceFieldInUse(Request $request, $fieldId, $fieldName, $inUse = false, $storeGuid = "", $deviceGuid = "", $column = "") {
        $response = array();
        
        if(!isset($request->device[$fieldId]) or $request->device[$fieldId] == '') {
            $response["errorId"]  = $fieldId;
            $response["errorMsg"] = "The field $fieldName cannot be empty.";
            return response()->json($response);
            
        } else if($inUse) {
            
            $field   = $request->device[$fieldId];
            
            $sameDeviceId = DB::select("SELECT * FROM devices
                                    WHERE store_guid = '$storeGuid'
                                    AND guid != '$deviceGuid'
                                    AND is_deleted = 0
                                    AND $column = $field");
            
            if(count($sameDeviceId) > 0) {
                $response["errorId"]  = $fieldId;
                $response["errorMsg"] = "This KDS Station $fieldName is already in use.";
                return response()->json($response);
            }
            
        }
        
        return $response;
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
        $store->timezone        = $request->get('timezone');
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
        $kdsTime->setTimezone(new DateTimeZone(isset($store->timezone) ? $store->timezone : Vars::$timezoneDefault));
        $kdsTime->setTime($auto_done_order_time[0], $auto_done_order_time[1]);
        $auto_done_order_time = $kdsTime->getTimestamp();
        
        $data = [
                    'server_address'           => $request->get('server_address'),
                    'server_username'          => $request->get('server_username'),
                    'server_password'          => $request->get('server_password'),
                    'socket_port'              => $request->get('socket_port'),
                    'auto_done_order_hourly'   => $request->get('auto_done_order_hourly'),
                    'auto_done_order_time'     => $auto_done_order_time,
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
        
        return view('admin.stores.report', ['reports' => Vars::$reportIds, 'store' => $store, 
            'devices' => $devices, 'selected' => $request->selected]);
    }
    
    
    public function reportByStation(Request $request)
    {
        $devicesIds = $request->get('devicesIds');
        $reportId   = $request->get('reportId');
        
        $startDatetime = strtotime($request->get('startDatetime'));
        $endDatetime   = strtotime($request->get('endDatetime'));
        
        $sql = "";
        
        // Quantity and Average Time by Order
        if($reportId == $this->reports[0]["id"]) {
            
            $sql = "SELECT
                    	select_orders.device_name AS column_0,
                    SUM(select_orders.order_count) AS column_1,
                    SUM(select_orders.order_avg_time) / SUM(select_orders.order_count) AS column_2,
                    case when MAX(select_orders.active) = 1 then 'true' else 'false' end AS column_3
                FROM
                    	(SELECT
                        	dn.name AS device_name,
                        	count(distinct i.order_guid) AS order_count,
                        	count(distinct i.guid) AS item_count,
                        
                        	max((case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_local_time else ib.prepared_local_time end) -
                        		(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then
                                (case when ib.prepared_local_time = 0 then ib.create_local_time else ib.prepared_local_time end) else ib.create_local_time end)) /
                                count(distinct i.order_guid) AS order_avg_time,
                        
                        dn.login AS active
                        
                        FROM item_bumps ib
                        JOIN items i ON ib.guid = i.item_bump_guid 
                        JOIN orders o ON o.guid = i.order_guid 

                        JOIN devices d ON d.is_deleted = 0 AND d.id <> 0 AND d.store_guid = o.store_guid
                        JOIN devices dn ON dn.is_deleted = 0 AND dn.store_guid = o.store_guid AND dn.id =
                        	(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id else ib.prepared_device_id end)
                
                        JOIN users u ON u.store_guid = d.store_guid AND u.store_guid = dn.store_guid
                
                        WHERE u.id = " . $request->get('storeId') .
                        " AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id
                              else ib.prepared_device_id end) != 0";
            
            $sql .=         " AND ( (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE')
                            then ib.done_local_time else ib.prepared_local_time end) >= $startDatetime
                            AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE')
                            then ib.done_local_time else ib.prepared_local_time end) <= $endDatetime)";
            
            if($devicesIds != "") {
                $sql .=     " AND d.id IN (" . implode(",", $devicesIds) . ") ";
                $sql .=     " AND dn.id IN (" . implode(",", $devicesIds) . ") ";
            }
            
            $sql .=     "GROUP BY dn.name, dn.login, i.order_guid) select_orders
                GROUP BY select_orders.device_name";
        
        // Quantity and Average Time by Item
        } else if($reportId == $this->reports[1]["id"]) { 
            
            $sql = "SELECT 
                        	select_orders.device_name AS column_0,
                        SUM(select_orders.item_count) AS column_1,
                        SUM(select_orders.item_avg_time) / SUM(select_orders.order_count) AS column_2, -- ***
                        case when MAX(select_orders.active) = 1 then 'true' else 'false' end AS column_3
                    FROM
                        	(SELECT 
                            	dn.name AS device_name,
                            	count(distinct i.order_guid) AS order_count,
                            	count(distinct i.guid) AS item_count,
                                    
                            	max((case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_local_time else ib.prepared_local_time end) - 
                            		(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then 
                                    (case when ib.prepared_local_time = 0 then ib.create_local_time else ib.prepared_local_time end) else ib.create_local_time end)) / 
                                    count(distinct i.guid) AS item_avg_time,
                                    
                            	dn.login AS active
                                
                            FROM item_bumps ib
                            JOIN items i ON ib.guid = i.item_bump_guid
                            JOIN orders o ON o.guid = i.order_guid 

                            JOIN devices d ON d.is_deleted = 0 AND d.id <> 0 AND d.store_guid = o.store_guid
                            JOIN devices dn ON dn.is_deleted = 0 AND dn.store_guid = o.store_guid AND dn.id = 
                            	(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id else ib.prepared_device_id end)
                                  
                            JOIN users u ON u.store_guid = d.store_guid AND u.store_guid = dn.store_guid
                            
                            WHERE u.id = " . $request->get('storeId') . 
                            " AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id
                                  else ib.prepared_device_id end) != 0";
            
            $sql .=         " AND ( (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') 
                                then ib.done_local_time else ib.prepared_local_time end) >= $startDatetime 
                                AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') 
                                then ib.done_local_time else ib.prepared_local_time end) <= $endDatetime)";
            
            if($devicesIds != "") {
                $sql .=     " AND d.id IN (" . implode(",", $devicesIds) . ") ";
                $sql .=     " AND dn.id IN (" . implode(",", $devicesIds) . ") ";
            }
            
            $sql .=     "GROUP BY dn.name, dn.login, i.order_guid) select_orders
                    GROUP BY select_orders.device_name";
        
        // Quantity and Average Time by Item Name
        } else if($reportId == $this->reports[2]["id"]) {
            
            $sql = "SELECT
                        	select_orders.device_name AS column_0,
                        select_orders.item_name AS column_1,
                        SUM(select_orders.item_count) AS column_2,
                        SUM(select_orders.item_avg_time) / SUM(select_orders.order_count) AS column_3 -- ***
                    FROM
                        	(SELECT
                            	dn.name AS device_name,
                             i.name AS item_name,
                            	count(distinct i.order_guid) AS order_count,
                            	count(distinct i.guid) AS item_count,
                
                            	max((case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_local_time else ib.prepared_local_time end) -
                            		(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then
                                    (case when ib.prepared_local_time = 0 then ib.create_local_time else ib.prepared_local_time end) else ib.create_local_time end)) /
                                    count(distinct i.guid) AS item_avg_time,
                
                            	dn.login AS active
                
                            FROM item_bumps ib
                            JOIN items i ON ib.guid = i.item_bump_guid
                            JOIN orders o ON o.guid = i.order_guid 

                            JOIN devices d ON d.is_deleted = 0 AND d.id <> 0 AND d.store_guid = o.store_guid
                            JOIN devices dn ON dn.is_deleted = 0 AND dn.store_guid = o.store_guid AND dn.id =
                            	(case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id else ib.prepared_device_id end)
                
                            JOIN users u ON u.store_guid = d.store_guid AND u.store_guid = dn.store_guid
                
                            WHERE u.id = " . $request->get('storeId') .
                            " AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE') then ib.done_device_id
                                  else ib.prepared_device_id end) != 0";
                            
            $sql .=         " AND ( (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE')
                                then ib.done_local_time else ib.prepared_local_time end) >= $startDatetime
                                AND (case when (d.`function` = 'EXPEDITOR' OR d.`function` = 'BACKUP_EXPE')
                                then ib.done_local_time else ib.prepared_local_time end) <= $endDatetime)";
                            
            if($devicesIds != "") {
                $sql .=     " AND d.id IN (" . implode(",", $devicesIds) . ") ";
                $sql .=     " AND dn.id IN (" . implode(",", $devicesIds) . ") ";
            }
            
            $sql .=     "GROUP BY dn.name, i.name, dn.login, i.order_guid) select_orders
                    GROUP BY select_orders.device_name, select_orders.item_name";
            
        }

        return DB::select($sql);
    }
    
    
    public function removeDevice(Request $request, User $store) {
        
        if($request->post('deviceSerial') === null) {
            return "KDS Station Serial Number not provided.";
        }
        
        $storeGuid = $request->post('storeGuid');
        $deviceSerial = $request->post('deviceSerial');
        $deviceGuid = $request->post('deviceGuid');
        
        $devices = DB::table('devices')
            ->where('store_guid', '=', $storeGuid)
            ->where('is_deleted', '=', 0)
            ->where('serial', '=', $deviceSerial);
        
        if(count($devices) > 0) {
            
            // Remove Settings Local
            $settingsLocal = DB::table('settings_local')
                ->where('store_guid', '=', $storeGuid)
                ->where('is_deleted', '=', 0)
                ->where('device_guid', '=', $deviceGuid);
            
            if(count($settingsLocal) > 0) {
                $settingsLocal->update(['is_deleted' => 1]);
            }
            
            // Remove Settings Line Display
            $settingsLineDisplay = DB::table('settings_line_display')
            ->where('store_guid', '=', $storeGuid)
            ->where('is_deleted', '=', 0)
            ->where('device_guid', '=', $deviceGuid);
            
            if(count($settingsLineDisplay) > 0) {
                $settingsLineDisplay->update(['is_deleted' => 1]);
            }
            
            // Remove Device
            $data = [
                'is_deleted' => 1,
                'license' => 0,
                'login' => 0,
                'update_time' => time()
            ];
            
            foreach($devices->get() as $device) {
                
                // ** Remove expeditors ******************************************* //
                $expeditors = DB::select("SELECT * FROM devices 
                    WHERE store_guid = '$storeGuid' 
                    AND is_deleted = 0 
                    AND ( expeditor = '$device->id' 
                            OR expeditor LIKE '%,$device->id,%' 
                            OR expeditor LIKE '%,$device->id' 
                            OR expeditor LIKE '$device->id,%' 
                        )");
                
                if(count($expeditors) > 0) {
                    
                    foreach($expeditors as $expeditor) {
                        
                        $expeditorIds = explode(',', $expeditor->expeditor);
                        $expeditorIdsNew = "";
                        
                        foreach($expeditorIds as $expeditorId) {
                            if($expeditorId == $device->id) {
                                continue;
                            }
                            
                            $expeditorIdsNew .= $expeditorId . ",";
                        }
                        
                        if(substr($expeditorIdsNew, -1) == ',') {
                            $expeditorIdsNew = rtrim($expeditorIdsNew,",");
                        }
                        
                        $sql = "UPDATE devices SET expeditor = '$expeditorIdsNew' WHERE guid = '$expeditor->guid'";
                        $result = DB::statement($sql);
                    }
                }
                // ***************************************** Remove expeditors ** //
                
                // ** Remove parent id ****************************************** //
                $parents = DB::select("SELECT * FROM devices
                    WHERE store_guid = '$storeGuid'
                    AND is_deleted = 0
                    AND ( parent_id = $device->id
                        )");
                
                if(count($parents) > 0) {
                    foreach($parents as $parent) {
                        $sql = "UPDATE devices SET parent_id = NULL WHERE guid = '$parent->guid'";
                        $result = DB::statement($sql);
                    }
                }
                // ***************************************** Remove parent id ** //
                
                // ** Remove transfer ****************************************** //
                $transfers = DB::select("SELECT * FROM devices
                    WHERE store_guid = '$storeGuid'
                    AND is_deleted = 0
                    AND ( bump_transfer_device_id = $device->id
                        )");
                
                if(count($transfers) > 0) {
                    foreach($transfers as $transfer) {
                        $sql = "UPDATE devices SET bump_transfer_device_id = NULL WHERE guid = '$transfer->guid'";
                        $result = DB::statement($sql);
                    }
                }
                // ****************************************** Remove transfer ** //
                
            }
            
            $devices->update($data);
            
        } else {
            return "KDS Station not found.";
        }
        
        return "";
        
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



