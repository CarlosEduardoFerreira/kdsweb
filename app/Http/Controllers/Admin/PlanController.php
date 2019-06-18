<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Vars;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User\User;
use App\Models\Settings\Plan;
use App\Models\Settings\PaymentType;
use App\Models\Settings\App;
use App\Models\Settings\PlanXObject;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PlanController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }

    
    public function index() {

        $me = Auth::user();
        
        $plans = $this->getMyPlanList();
        
        foreach($plans as $plan) {
            $plan->payment_type = PaymentType::where('guid', '=', $plan->payment_type)->get()->first()->name;
            $plan->app = App::where('guid', '=', $plan->app)->get()->first()->name;

            $plan->update_time = Controller::readableDatetime($plan->update_time);
        }

        $basePlans = $this->getBasePlans();
        
        return view('admin.settings.plans', ['me' => $me, 'plans' => $plans, 'basePlans' => $basePlans]);
    }
    
    
    public function create() {
        
        $me = Auth::user();
        
        $plan = new Plan();
        
        $types = Controller::getPlanPaymentTypes();
        $apps = Controller::getSystemApps();
        
        $basePlans = $this->getBasePlans();
        
        return view('admin.settings.plans-form', ['me' => $me, 'plan' => $plan, 'basePlans' => $basePlans,
                                                    'apps' => $apps, 'payment_types' => $types]);
    }
    
    
    public function insert(Request $request) {
        
        $me = Auth::user();
        
        $basePlan = Plan::where('guid', '=', $request->get('base_plan'))->get()->first();
        
        $data = [
            'guid'          => Uuid::uuid4(),
            'base_plan'     => empty($basePlan->guid) ? NULL : $basePlan->guid,
            'name'          => $request->get('name'),
            'cost'          => $request->get('cost'),
            'payment_type'  => $request->get('payment_type'),
            'app'           => empty($basePlan->app) ? $request->get('app') : $basePlan->app,
            'status'        => 1,
            'default'       => empty($request->get('default')) ? 0 : 1,
            'create_time'   => time(),
            'update_time'   => time(),
            'update_user'   => $me->id,
            'owner_id'      => $me->hasRole('administrator') ? 0 : $me->id
        ];
        
        $planRes = Plan::create($data);
        
        // Relation between Plans and Storegroups -------------------------------------------- //
        if($me->hasRole('reseller')) {
            $storegroups = Controller::filterUsers(null, 3, $me->id, true);
            
            // Create Plans
            foreach($storegroups as $storegroup){
                $data = [
                    'guid'          => Uuid::uuid4(),
                    'base_plan'     => $planRes->guid,
                    'name'          => $request->get('name'),
                    'cost'          => $request->get('cost'),
                    'payment_type'  => $request->get('payment_type'),
                    'app'           => empty($planRes->app) ? $request->get('app') : $planRes->app,
                    'status'        => 1,
                    'default'       => empty($request->get('default')) ? 0 : 1,
                    'create_time'   => time(),
                    'update_time'   => time(),
                    'update_user'   => $me->id,
                    'owner_id'      => $storegroup->id
                ];
                
                Plan::create($data);
            }
            
            
            // Link Plans
            foreach($storegroups as $storegroup){
                $plans = Plan::where([['delete_time', '=', 0], ['owner_id', '=', $me->id]])->get();
                foreach($plans as $plan) {
                    $data = [
                        'plan_guid' => $plan->guid,
                        'user_id'   => $storegroup->id
                    ];
                    PlanXObject::create($data);
                }
            }
        }
        // -------------------------------------------- Relation between Plans and Storegroups //

    }
    
    
    public function edit(Request $request, Plan $plan) {
        
        $me = Auth::user();
        
        $types = Controller::getPlanPaymentTypes();
        $apps = Controller::getSystemApps();
        
        $plan->update_time = Controller::readableDatetime($plan->update_time);
        
        $plan->update_user = User::whereId($plan->update_user)->get()->first()->name;
        
        $basePlans = $this->getBasePlans();
        
        return view('admin.settings.plans-form', ['me' => $me, 'plan' => $plan, 'basePlans' => $basePlans, 
                                                    'apps' => $apps, 'payment_types' => $types]);
    }
    
    
    public function update(Request $request) {
        
        $me = Auth::user();
        
        $stg = $me->hasRole('storegroup');
        
        $basePlanGuid = $request->get('base_plan');
        if($stg) {
            $basePlanGuid = $request->get('base_plan_hidden');
        }
        $basePlan = Plan::where('guid', '=', $basePlanGuid)->get()->first();
        
        $default = empty($request->get('default')) ? 0 : 1;
        if($stg && $default) {
            Plan::where('owner_id', '=', $me->id)->update(['default' => 0]);
        }
        
        $data = [
            'base_plan'     => empty($basePlan->guid) ? NULL : $basePlan->guid,
            'name'          => $request->get('name'),
            'cost'          => $stg ? $request->get('cost_hidden') : $request->get('cost'),
            'payment_type'  => $stg ? $request->get('payment_type_hidden') :  $request->get('payment_type'),
            'app'           => empty($basePlan->app) ? $request->get('app') : $basePlan->app,
            'status'        => empty($request->get('status')) ? 0 : 1,
            'default'       => $default,
            'update_time'   => time(),
            'update_user'   => $me->id
        ];
        
        Plan::where('guid', '=', $request->get('guid'))->update($data);
    }
    
    
    public function delete(Request $request) {
        
        $me = Auth::user();
        
        $data = [
            'update_time'   => time(),
            'update_user'   => $me->id,
            'delete_time'   => time()
        ];
        
        $guids = $request->get('guids');
        
        foreach($guids as $guid) {
            Plan::where('guid', '=', $guid)->update($data);
            
            // Soft Delete on Plans that have this Plan as Base Plan
            $basePlans = Plan::where('base_plan', '=', $guid)->get();
            foreach($basePlans as $basePlan) {
                $basePlan->update($data);
            }
            
            // Hard Delete on linked Plans X Objects
            $plansXObjects = PlanXObject::where('plan_guid', '=', $guid)->get();
            foreach($plansXObjects as $planXObject) {
                $planXObject->forceDelete();
            }
        }
    }
    
    
    public function getItemsPlans(Request $request) {
        
        $all = empty($request->get('all')) ? false : ($request->get('all') == 'true' ? true : false);
        
        $plans = $this->getMyPlanList();
        
        if($all) {
            return view('admin.settings.plans-objects-items-plans', ['plans' => $plans]);
        }
        
        if(empty($request->get('guid')) || empty($request->get('type'))) {
            return "Error: Empty Field";
        }
        
        $me = Auth::user();
        
        $typePlan = "Plan";
        
        $guid = $request->get('guid');
        $type = $request->get('type');
        
        $primaryKey = $type == $typePlan ? "plan_guid" : "user_id";
        
        $plansXObjects = PlanXObject::where($primaryKey, '=', $guid)->get();
        
        $guids = [];
        foreach($plans as $plan) {
            $isSelected = false;
            foreach($plansXObjects as $planXObject) {
                if($planXObject->plan_guid == $plan->guid) {
                    $isSelected = true;
                    break;
                }
            }
            if(!$isSelected) {
                array_push($guids, $plan->guid);
            }
        }
        
        $plans = Plan::where('delete_time', '=', 0)->whereIn('guid', $guids)->orderBy('name')->get();
        
        return view('admin.settings.plans-objects-items-plans', ['plans' => $plans]);
    }
    
    
    public function getItemsObjects(Request $request) {
        
        $me = Auth::user();
        
        $all = empty($request->get('all')) ? false : ($request->get('all') == 'true' ? true : false);
        
        if($all) {
        
            if(empty($request->get('objName'))) {
                return "Error: Empty Field objName";
            }
            $objName = $request->get('objName');
            
            $userRoleCode = $objName == "Reseller" ? 2 : ($objName == "Store Group" ? 3 : 4);
            $objects = Controller::filterUsers(null, $userRoleCode, $me->id);
            $ids = [];
            foreach($objects as $object) {
                array_push($ids, $object->id);
            }
            $objects = User::where('deleted_at', '=', NULL)->whereIn('id', $ids)->orderBy('business_name')->get();
            
            return view('admin.settings.plans-objects-items-objects', ['objects' => $objects]);
        }
        
        if(empty($request->get('guid')) || empty($request->get('type'))) {
            return "Error: Empty Field guid or type";
        }
        
        $guid = $request->get('guid');
        $type = $request->get('type');
        $objName = $request->get('objName');
        
        $userRoleCode = $objName == "Reseller" ? 2 : ($objName == "Store Group" ? 3 : 4);
        
        $primaryKey = $this->getPlanXObjectPK($type);
        
        $plansXObjects = PlanXObject::where($primaryKey, '=', $guid)->get();
        
        $objects = Controller::filterUsers(null, $userRoleCode, $me->id);
        $ids = [];
        foreach($objects as $object) {
            $isSelected = false;
            foreach($plansXObjects as $planXObject) {
                if($planXObject->user_id == $object->id) {
                    $isSelected = true;
                    break;
                }
            }
            if(!$isSelected) {
                array_push($ids, $object->id);
            }
        }

        $objects = User::where('deleted_at', '=', NULL)->whereIn('id', $ids)->orderBy('business_name')->get();

        return view('admin.settings.plans-objects-items-objects', ['objects' => $objects]);
    }
    
    
    public function getItemsSelected(Request $request) {
        
        $all = empty($request->get('all')) ? false : ($request->get('all') == 'true' ? true : false);
        
        if($all) {
            return view('admin.settings.plans-objects-items-selected', ['objects' => [], 'type' => null]);
        }
        
        if(empty($request->get('guid')) || empty($request->get('type'))) {
            return "Error: Empty Field";
        }
        
        $me = Auth::user();
        
        $guid = $request->get('guid');
        $type = $request->get('type');
        $objName = $request->get('objName');
        
        $userRoleCode = $this->getRoleCode($objName);
        
        $primaryKey = $this->getPlanXObjectPK($type);
        
        $plansXObjects = PlanXObject::where($primaryKey, '=', $guid)->get();
        
        $ids = [];
        foreach($plansXObjects as $planXObject) {
            if($type == "Plan") {
                $user = User::whereId($planXObject->user_id)->first();
                if($user->roles()->first()->id == $userRoleCode) {
                    array_push($ids, $user->id);
                }
                
            } else {
                $plan = Plan::where('guid', '=', $planXObject->plan_guid)->first();
                array_push($ids, $plan->guid);
            }
        }
        
        $objects = [];
        if($type == "Plan") {
            $objects = User::where('deleted_at', '=', NULL)->whereIn('id', $ids)->orderBy('business_name')->get();
        } else {
            $objects = Plan::where('delete_time', '=', 0)->whereIn('guid', $ids)->orderBy('name')->get();
        }
        
        return view('admin.settings.plans-objects-items-selected', ['objects' => $objects, 'type' => $type]);
    }
    
    
    public function validPlanXObject(Request $request) {
        
        $response = [];
        $error    = "";
        
        $response['valid'] = true;
        
        $objName  = empty($request->get('objName'))  ? $error  = "objName "  : $request->get('objName');
        $guid     = empty($request->get('guid'))     ? $error .= "guid "     : $request->get('guid');
        $type     = empty($request->get('type'))     ? $error .= "type "     : $request->get('type');
        $dragGuid = empty($request->get('dragGuid')) ? $error .= "dragGuid " : $request->get('dragGuid');
        
        if($error != "") {
            $response['valid'] = false;
            $response['error'] = "Error: Empty Field $error";
        }
        
        if($objName != 'Store') {
            return $response;
        }
        
        $storeGuid = $type == "Plan" ? $dragGuid : $guid;
        
        $planXObject = PlanXObject::where('user_id', '=', $storeGuid)->get();

        if(count($planXObject) > 0) {
            $response['valid'] = false;
            $response['error'] = "Stores can have just 1 (one) Plan each.";
        }
        
        return $response;
    }
    
    
    public function updateObjects(Request $request) {
        
        if(empty($request->get('guid')) || empty($request->get('type'))) {
            return "Error: Empty Field guid or type";
        }
        $guid = $request->get('guid');
        $type = $request->get('type');
        
        if(empty($request->get('objName'))) {
            return "Error: Empty Field objName";
        }
        $objName = $request->get('objName');
        
        $me = Auth::user();
        
        $selectedGuids = empty($request->get('selectedGuids')) ? [] : $request->get('selectedGuids');
        
        $primaryKey = $this->getPlanXObjectPK($type);
        $plansXObjects = PlanXObject::where($primaryKey, '=', $guid)->get();

        $userRoleCode = $this->getRoleCode($objName);
        $users = Controller::filterUsers(null, $userRoleCode, $me->id);
        
        // Remove based on User Role Code
        $ids = [];
        foreach($users as $user) {
            foreach($plansXObjects as $planXObject) {
                if($planXObject->user_id == $user->id) {
                    array_push($ids, $user->id);
                }
            }
        }
        PlanXObject::whereIn('user_id', $ids)->forceDelete();
        
        // Insert Selected Column
        foreach($selectedGuids as $selectedGuid) {
            $data = [
                'plan_guid' => $type == "Plan" ? $guid : $selectedGuid,
                'user_id'   => $type == "Plan" ? $selectedGuid : $guid
            ];
            
            $plan = PlanXObject::create($data);
        }
    }
    
    
    function getRoleCode($objName) {
        return $objName == "Reseller" ? 2 : ($objName == "Store Group" ? 3 : 4);
    }
    
    
    function getPlanXObjectPK($type) {
        return $type == "Plan" ? "plan_guid" : "user_id";
    }

    
}












