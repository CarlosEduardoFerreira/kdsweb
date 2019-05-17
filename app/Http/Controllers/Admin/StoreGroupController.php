<?php

namespace App\Http\Controllers\Admin;


use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Settings\Plan;
use App\Models\Settings\PlanXObject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vars;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use DateTime;
use DateTimeZone;


class StoreGroupController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $resellerId)
    {

        $accessDenied = Controller::canIsee(Auth::user(), $resellerId);
        if ($accessDenied) {
            return $accessDenied;
        }

        $sort = null;
        $order = null;

        if(isset($_GET['sort']) && ($_GET['sort'] == "apps" || $_GET['sort'] == "envs")) {
            $sort = $_GET['sort'];
            unset($_GET['sort']);

            $order = $_GET['order'];
            unset($_GET['order']);
        }

        $storegroups = Controller::filterUsers($request, 3, $resellerId,
                                        $request->filter, $ignorePaginator = isset($sort) && isset($order));

        $storeGroupIds = [];
        foreach ($storegroups as &$storegroup) {
            array_push($storeGroupIds, $storegroup->id);
        }

        $apps = $this->getAppsByStoreGroup($storeGroupIds);
        $envs = $this->getEnvsByStoreGroup($storeGroupIds);

        foreach ($storegroups as &$storegroup) {

            $storegroup->apps = "";
            foreach ($apps as &$app) {
                if ($app->storeGroupId == $storegroup->id) {
                    if (!empty($storegroup->apps)) {
                        $storegroup->apps .= " / ";
                    }

                    $storegroup->apps .= $app->name;
                }
            }

            $storegroup->envs = "";
            foreach ($envs as &$env) {
                if ($env->storeGroupId == $storegroup->id) {
                    if (!empty($storegroup->envs)) {
                        $storegroup->envs .= " / ";
                    }

                    $storegroup->envs .= $env->name;
                }
            }
        }

        if (isset($sort) && isset($order)) {
            usort($storegroups, function($a, $b) use ($order, $sort) {
                return $order == "asc" ? strcmp($a->{$sort} , $b->{$sort}) : strcmp($b->{$sort} , $a->{$sort});
            });

            $storegroups = $this->arrayPaginator($storegroups, $request, 10);
        }

        return view('admin.storegroups.index', ['obj' => 'storegroup', 'storegroups' => $storegroups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, User $reseller)
    {
        $storegroup = new User;
        $storegroup->active = true;
        
        // Resellers ------------------------------------------------------- //
        $resellers = array();
        
        $me = Auth::user();
        
        if ($me->roles[0]->id == 2) {
            $resellers[0] = $me;
            
        } else {
            $resellers  = Controller::filterUsers($request, 2, $me->id, true);
        }
        // ------------------------------------------------------- Resellers //
        
        $countries = DB::select("select * from countries order by name");
        
        $storegroup->country = 231;   // United States
        
        return view('admin.form', ['obj' => 'storegroup', 'user' => $storegroup , 'parents' => $resellers, 
            'countries' => $countries, 'me' => $me]);
    }
    
    
    public function insert(Request $request)
    {
        $me = Auth::user();
        
        $created_at = new DateTime();
        $created_at->setTimezone(new DateTimeZone(Vars::$timezoneDefault));
        
        $usersTable = DB::table('users');
        
        $data = [
            'parent_id'       => $request->get('parent_id'),
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
        
        if ($request->get('password') != "") {
            $data['password'] = bcrypt($request->get('password'));
        }
        
        $id = $usersTable->insertGetId($data);
        DB::table('users_roles')->insert(['user_id' => $id, 'role_id' => 3]);
        
        // Relation between Plans and Storegroups -------------------------------------------- //
        $plans = Plan::where([['delete_time', '=', 0], ['owner_id', '=', $me->id]])->get();
        
        foreach($plans as $planRes) {
            $dataPlan = [
                'guid'          => Uuid::uuid4(),
                'base_plan'     => $planRes->guid,
                'name'          => $planRes->name,
                'cost'          => $planRes->cost,
                'payment_type'  => $planRes->payment_type,
                'app'           => empty($planRes->app) ? $request->get('app') : $planRes->app,
                'status'        => 1,
                'default'       => empty($planRes->default) ? 0 : 1,
                'create_time'   => time(),
                'update_time'   => time(),
                'update_user'   => $me->id,
                'owner_id'      => $id
            ];
            
            Plan::create($dataPlan);
            
            $data = [
                'plan_guid' => $planRes->guid,
                'user_id'   => $id
            ];
            
            PlanXObject::create($data);
        }
        // -------------------------------------------- Relation between Plans and Storegroups //
        
        return redirect()->intended(route('admin.storegroups', [0, 'filter' => false])); // go to the list
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
        $accessDenied = Controller::canIsee(Auth::user(), $storegroup->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        $state   = DB::table('states')->where(['id' => $storegroup->state])->first();
        $country = DB::table('countries')->where(['id' => $storegroup->country])->first();
        
        $storegroup->state   = $state->name;
        $storegroup->country = $country->name;
        
        $reseller = DB::table('users')->where(['id' => $storegroup->parent_id])->first();
        
        return view('admin.storegroups.show', ['obj' => 'storegroup', 'storegroup' => $storegroup, 'reseller' => $reseller]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $storegroup)
    {
        $accessDenied = Controller::canIsee(Auth::user(), $storegroup->id);
        if ($accessDenied) {
            return $accessDenied;
        }
        
        // Resellers ------------------------------------------------------- //
        $resellers = array();
        
        $me = Auth::user();
        //echo "role_id: " . $me->roles[0]->id ."<br>";
        if ($me->roles[0]->id == 2) {
            $resellers[0] = $me;
            
        } else {
            $resellers  = Controller::filterUsers(null, 2, $me->id, true);
        }
        // ------------------------------------------------------- Resellers //
        
        $countries  = DB::select("select * from countries order by name");
        
        $states     = [];
        if (isset($storegroup->country) && $storegroup->country != "") {
            $states     = DB::select("select * from states where country_id = $storegroup->country order by name");
        }
        
        return view('admin.form', ['obj' => 'storegroup', 'user' => $storegroup, 'parents' => $resellers,
            'countries' => $countries, 'states' => $states, 'me' => $me]);
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
        $storegroup->parent_id       = $request->get('parent_id');

        if ($request->get('password') != "") {
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
        
        if ($storegroup->id == Auth::user()->id) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // return redirect()->intended(route('admin.storegroups.edit', [$storegroup->id, 'filter' => false])); // keep on the same page
        return redirect()->intended(route('admin.storegroups', [0, 'filter' => false])); // go to the list
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
    
    
    public function getAppsByStoreGroup(array $storeGroupIds) {
        
        $apps = [];
        
        if(empty($storeGroupIds)) {
            return $apps;
        }
        
        $apps = DB::select("SELECT distinct
                                    apps.name, stores.parent_id as storeGroupId
                                FROM apps 
                                JOIN users AS stores ON stores.parent_id IN (".implode(',',$storeGroupIds).")
                                JOIN store_app AS store_app ON store_app.store_guid = stores.store_guid
                                WHERE stores.active = 1 AND apps.guid = store_app.app_guid
                                ORDER BY name");
        
        return $apps;
    }
    
    
    public function getEnvsByStoreGroup(array $storeGroupIds) {
        
        $envs = [];

        if(empty($storeGroupIds)) {
            return $envs;
        }
        
        $envs = DB::select("SELECT distinct
                                    environments.name, stores.parent_id as storeGroupId
                                FROM environments
                                JOIN users AS stores ON stores.parent_id IN (".implode(',',$storeGroupIds).")
                                JOIN store_environment AS store_env ON store_env.store_guid = stores.store_guid
                                WHERE stores.active = 1 AND environments.guid = store_env.environment_guid
                                ORDER BY name");
        
        return $envs;
    }
    
    
}






