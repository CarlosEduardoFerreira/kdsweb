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

        $plans = Plan::where('delete_time', '=', 0)->orderBy('name')->get();
        
        foreach($plans as $plan) {
            $plan->payment_type = PaymentType::where('guid', '=', $plan->payment_type)->get()->first()->name;
            $plan->app = App::where('guid', '=', $plan->app)->get()->first()->name;

            $plan->update_time = Controller::readableDatetime($plan->update_time);
        }
        
        return view('admin.settings.plans', ['plans' => $plans]);
    }
    
    
    public function create() {
        
        $plan = new Plan();
        
        $types = Controller::getPlanPaymentTypes();
        $apps = Controller::getSystemApps();
        
        return view('admin.settings.plans-form', ['plan' => $plan, 'apps' => $apps, 'payment_types' => $types]);
    }
    
    
    public function insert(Request $request) {
        
        $me = Auth::user();
        
        $data = [
            'guid'          => Uuid::uuid4(),
            'name'          => $request->get('name'),
            'cost'          => $request->get('cost'),
            'payment_type'  => $request->get('payment_type'),
            'app'           => $request->get('app'),
            'status'        => 1,
            'create_time'   => time(),
            'update_time'   => time(),
            'update_user'   => $me->id
        ];
        
        $plan = Plan::create($data);
        
        return redirect()->intended(route('admin.settings', [0, 'filter' => false])); // go to the list
    }
    
    
    public function edit(Request $request, Plan $plan) {
        
        $types = Controller::getPlanPaymentTypes();
        $apps = Controller::getSystemApps();
        
        $plan->update_time = Controller::readableDatetime($plan->update_time);
        
        $plan->update_user = User::whereId($plan->update_user)->get()->first()->name;
        
        return view('admin.settings.plans-form', ['plan' => $plan, 'apps' => $apps, 'payment_types' => $types]);
    }
    
    
    public function update(Request $request) {
        
        $me = Auth::user();
        
        $data = [
            'name'          => $request->get('name'),
            'cost'          => $request->get('cost'),
            'payment_type'  => $request->get('payment_type'),
            'app'           => $request->get('app'),
            'status'        => $request->get('status') == null ? 0 : 1,
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
        }
    }
    
    
    public function getItemsPlans(Request $request) {
        
        $all = empty($request->get('all')) ? false : ($request->get('all') == 'true' ? true : false);
        
        if($all) {
            $plans = Plan::where('delete_time', '=', 0)->orderBy('name')->get();
            
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
        
        $plans = Plan::where('delete_time', '=', 0)->get();
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












