<?php

namespace App\Http\Controllers\Admin;


use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Settings\Plan;
use App\Models\Settings\PlanXObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vars;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateTimeZone;


class ResellerController extends Controller {
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $adminId)
    {
        $accessDenied = Controller::canIsee(Auth::user(), $adminId);
        if ($accessDenied) {
            return $accessDenied;
        }

        $resellers = Controller::filterUsers($request, 2, $adminId, $request->filter);

        return view('admin.resellers.index', ['obj' => 'reseller', 'resellers' => $resellers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $admin)
    {
        $reseller = new User;
        $reseller->active = true;
        $reseller->country = 231;   // United States
        
        $countries = DB::select("select * from countries order by name");
        $mainDB = env('DB_DATABASE', 'kdsweb');

        $sql_app_prices = "SELECT b1.user_id, b1.app_guid, a.`name`, b1.hardware, b1.price
                            FROM $mainDB.billing b1
                            INNER JOIN $mainDB.apps a
                            ON b1.app_guid = a.guid
                            WHERE b1.create_time = (SELECT MAX(b2.create_time) 
                                                    FROM billing b2 
                                                    WHERE b2.user_id = b1.user_id
                                                    AND b2.app_guid = b2.app_guid 
                                                    AND b2.hardware = b1.hardware)
                            AND b1.user_id = 1
                            ORDER BY b1.app_guid ASC";

        $app_prices = DB::select($sql_app_prices);

        return view('admin.form', ['obj' => 'reseller', 'user' => $reseller, 'countries' => $countries, 
                        'me' => Auth::user(), 'app_prices' => $app_prices]);
    }
    
    public function insert(Request $request)
    {
        $created_at = new DateTime();
        $created_at->setTimezone(new DateTimeZone(Vars::$timezoneDefault));
        
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
        
        // Insert prices
        $me_id = Auth::user()->id;
        $mainDB = env('DB_DATABASE', 'kdsweb');

        foreach($request->all() as $param => $value) {
            if (substr($param, 0, 6) === "price_") {
                $hw = substr($param, -2) === "hw" ? 1 : 0;
                $app_guid = $hw === 1 ? substr($param, 6, -2) : substr($param, 6);
                
                # Only numeric values between (0, 100,000) allowed
                if (!is_numeric($value)) continue;
                $price = 1.0 * $value;
                if ($price < 0) continue;
                if ($price > 100000) continue;

                DB::statement("INSERT INTO $mainDB.billing 
                                    (user_id, app_guid, hardware, price, create_time, create_user_id)
                                VALUES ({$id}, ?, $hw, ?, UNIX_TIMESTAMP(), $me_id)", 
                                [$app_guid, $price]);
            }
        }

        // Link default plans
        $plans = Plan::where([['delete_time', '=', 0], ['default', '=', 1], ['owner_id', '=', 0]])->get();
        foreach($plans as $plan) {
            $data = [
                'plan_guid' => $plan->guid,
                'user_id'   => $id
            ];
            $plan = PlanXObject::create($data);
        }
        
        return redirect()->intended(route('admin.resellers', [0, 'filter' => false])); // go to the list
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
        $accessDenied = Controller::canIsee(Auth::user(), $reseller->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        $state   = DB::table('states')->where(['id' => $reseller->state])->first();
        $country = DB::table('countries')->where(['id' => $reseller->country])->first();
      
        $reseller->state   = $state->name;
        $reseller->country = $country->name;
        
        return view('admin.resellers.show', ['obj' => 'reseller', 'reseller' => $reseller]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $reseller)
    {
        $accessDenied = Controller::canIsee(Auth::user(), $reseller->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        $countries  = DB::select("select * from countries order by name");
        
        $states     = [];
        if (isset($reseller->country) && $reseller->country != "") {
            $states     = DB::select("select * from states where country_id = $reseller->country order by name");
        }
        
        # max 3 different apps/hardware
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $sql_app_prices = "SELECT b1.user_id, b1.app_guid, a.`name`, b1.hardware, b1.price
                            FROM $mainDB.billing b1
                            INNER JOIN $mainDB.apps a
                            ON b1.app_guid = a.guid
                            WHERE b1.create_time = (SELECT MAX(b2.create_time) 
                                                    FROM billing b2 
                                                    WHERE b2.user_id = b1.user_id
                                                    AND b2.app_guid = b2.app_guid 
                                                    AND b2.hardware = b1.hardware)
                            AND b1.user_id = {$reseller->id}

                            UNION ALL (
                                SELECT b1.user_id, b1.app_guid, a.`name`, b1.hardware, b1.price
                                FROM $mainDB.billing b1
                                INNER JOIN $mainDB.apps a
                                ON b1.app_guid = a.guid
                                WHERE b1.create_time = (SELECT MAX(b2.create_time) 
                                                        FROM billing b2 
                                                        WHERE b2.user_id = b1.user_id
                                                        AND b2.app_guid = b2.app_guid 
                                                        AND b2.hardware = b1.hardware)
                                AND b1.user_id = 1
                            )

                            ORDER BY user_id DESC, app_guid ASC
                            LIMIT 3";

        $app_prices = DB::select($sql_app_prices);
        
        return view('admin.form', ['obj' => 'reseller', 'user' => $reseller, 'app_prices' => $app_prices, 
            'countries' => $countries, 'states' => $states, 'me' => Auth::user()]);
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
        $updated_at->setTimezone(new DateTimeZone(Vars::$timezoneDefault));
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
        
        // Update prices
        $me_id = Auth::user()->id;
        $mainDB = env('DB_DATABASE', 'kdsweb');

        foreach($request->all() as $param => $value) {
            if (substr($param, 0, 6) === "price_") {
                $hw = substr($param, -2) === "hw" ? 1 : 0;
                $app_guid = $hw === 1 ? substr($param, 6, -2) : substr($param, 6);
                
                # Only numeric values between (0, 100,000) allowed
                if (!is_numeric($value)) continue;
                $price = 1.0 * $value;
                if ($price < 0) continue;
                if ($price > 100000) continue;

                DB::statement("INSERT INTO $mainDB.billing 
                                    (user_id, app_guid, hardware, price, create_time, create_user_id)
                                VALUES ({$reseller->id}, ?, $hw, ?, UNIX_TIMESTAMP(), $me_id)", 
                                [$app_guid, $price]);
            }
        }

        if ($reseller->id == Auth::user()->id) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        // return redirect()->intended(route('admin.resellers.edit', [$reseller->id, 'filter' => false])); // keep on the same page
        return redirect()->intended(route('admin.resellers', [0, 'filter' => false])); // go to the list
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
