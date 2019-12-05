<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Parameters;

class ReportElementReseller {
    private $business_name = "";
    private $store_groups = [];

    public function setBusinessName($val) {
        $this->business_name = $val;
        return $this;
    }

    public function getBusinessName() {
        return $this->business_name;
    }

    public function insertStoreGroup(ReportElementStoreGroup $val) {
        $this->store_groups[] = $val;
        return $this;
    }

    // @return ReportElementStore
    public function getStoreGroups() {
        return $this->store_groups;
    }

    public function getFrequencyText() {
        return $this->capitalize($this->store_groups[0]->getStores()[0]->getApp()->getPaymentFreq());
    }

    private function capitalize($str) {
        return strtoupper(substr($str, 0, 1)) . strtolower(substr($str, 1));
    }
}

class ReportElementStoreGroup {
    private $business_name = "";
    private $stores = [];

    public function setBusinessName($val) {
        $this->business_name = $val;
        return $this;
    }

    public function getBusinessName() {
        return $this->business_name;
    }

    public function insertStore(ReportElementStore $val) {
        $this->stores[] = $val;
        return $this;
    }

    // @return ReportElementStore
    public function getStores() {
        return $this->stores;
    }
}

class ReportElementStore {
    private $business_name = "";
    private $licenses = 0;
    private $app = null;
    private $live = false;
    private $days_since_live = 0;
    private $referenceTimestamp = 0;
    private $accepted_at = 0;
    private $pilot_to_live = 60;

    public function setBusinessName($val) {
        $this->business_name = $val;
        return $this;
    }

    public function getBusinessName() {
        return $this->business_name;
    }

    public function calculateDaysSinceLive($accepted_at, $referenceTimestamp = 0, $pilot_to_live = 60) {
        $this->accepted_at = $accepted_at;
        $this->pilot_to_live = $pilot_to_live;
        $this->referenceTimestamp = ($referenceTimestamp <= 0 ? time() : $referenceTimestamp);
        $this->days_since_live = floor(($this->referenceTimestamp - $this->getLiveStoreFirstTimestamp()) / (3600 * 24));
        return $this;
    }

    public function getDaysSinceLive() {
        if (!$this->live) return 0;
        return max(0, $this->days_since_live);
    }

    private function getLiveStoreFirstTimestamp() {
        $date_live = new \DateTime();
        $date_live->setTimestamp($this->accepted_at);
        $date_live->add(date_interval_create_from_date_string($this->pilot_to_live . ' days'));
        return $date_live->getTimestamp();
    }

    public function setLiveStore($val) {
        $this->live = $val;
        return $this;
    }

    public function getIsLiveStore() {
        return ($this->live) || ($this->getDaysSinceLive() > 0);
    }

    public function setLicenses($val) {
        $this->licenses = $val;
        return $this;
    }

    public function getLicenses() {
        return $this->licenses;
    }

    public function setApp(ReportElementApp $app) {
        $this->app = $app;
        return $this;
    }

    // @return ReportElementApp
    public function getApp() {
        return $this->app;
    }

    // @returns int 0 if error ocurred
    public function getTotalCost() {
        $total_cost = 0;
        if (isset($this->app)) {
            $total_cost = $this->app->getCost() * $this->licenses;
        }

        if ($this->getDaysSinceLive() > 0) {
            switch ($this->app->getPaymentFreq()) {
                case "ONE-TIME":
                    if ($this->isMonthChargeable()) {
                        return $total_cost;
                    } else {
                        return 0;
                    }
                break;

                case "YEARLY":
                    if ($this->isMonthChargeable()) {
                        return $total_cost * ($this->getBilledDays(true) / 365);
                    } else {
                        return 0;
                    }
                break;

                case "MONTHLY":
                    if ($this->isMonthChargeable()) {
                        return $total_cost * ($this->getBilledDays(false) / 30);
                    } else {
                        return 0;
                    }
                break;
                
                default: 
                    return 0;
                break;
            }
        }
    }

    public function getRemarks() {
        $remarks = "[No charge] Store app not defined!";
        if (isset($this->app)) {
            if ($this->getDaysSinceLive() <= 0) {
                $remarks = "[No charge] Not a live store yet";
            } else {
                switch ($this->app->getPaymentFreq()) {
                    case "ONE-TIME":
                        $date = date('d F Y', $this->getLiveStoreFirstTimestamp());
                        if ($this->isMonthChargeable()) {
                            $remarks = "Charged once at $date";
                        } else {
                            $remarks = "[No charge] Single quota charged at $date";
                        }
                    break;

                    case "YEARLY":
                        $date = date('F', $this->getLiveStoreFirstTimestamp());
                        if ($this->isMonthChargeable()) {
                            $billed_days = $this->getBilledDays(true);
                            if ($billed_days < 365) {
                                $remarks = "[Partial] Charged for $billed_days days out of 365, at $date";
                            } else {
                                $remarks = "[Full price] Charged every $date";
                            }
                        } else {
                            $remarks = "[No charge] To be charged in $date";
                        }

                    break;

                    case "MONTHLY":
                        $billed_days = $this->getBilledDays(false);
                        if ($billed_days < 30) {
                            $remarks = "[Partial] Charged for $billed_days days out of 30";
                        } else {
                            $remarks = "[Full price]";
                        }

                    break;
                    
                    default: 
                        $remarks = "[Error] !!!";
                    break;
                }
            }
        }
        return $remarks;
    }

    public function getBilledDays($yearly = false) {
        $max = $yearly ? 365 : 30;
        return max(0, min($max, $this->getDaysSinceLive()));
    }

    // Checks [Reference timestamp] x [Live store date] x [Payment frequency]
    // @returns bool
    private function isMonthChargeable() {
        switch ($this->app->getPaymentFreq()) {
            case "MONTHLY":
                // Always true
                return true;
            break;

            case "YEARLY":
                // Only if month matches
                return date('F', $this->referenceTimestamp) == date('F', $this->getLiveStoreFirstTimestamp());
            break;

            case "ONE-TIME":
                // Only if this is the first payment
                return $this->getDaysSinceLive() <= 30;
            break;

            default:
                return true;
            break;
        }
    }
}

class ReportElementApp {
    private $name = "";
    private $payment_freq = "";
    private $cost = 0;
    private $hardware = false;

    public function setName($val) {
        $this->name = $val;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setPaymentFreq($val) {
        $this->payment_freq = $val;
        return $this;
    }

    public function getPaymentFreq() {
        return $this->payment_freq;
    }

    public function setCost($val) {
        $this->cost = $val;
        return $this;
    }

    public function getCost() {
        return $this->cost;
    }

    public function setHasHardware($val) {
        $this->hardware = $val;
        return $this;
    }

    public function getHasHardware() {
        return $this->hardware;
    }
}

class ReportCostByStatementController extends Controller {
    
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function index(Request $request) {
        $me = Auth::user();
        
        if(!isset($request->month)) {
            $request->month = date('Y-m');
        }

        $report = ReportCostByStatementController::getAdminReport($me, $request);
        
        return view('admin.reports.cost-by-statement', ['me' => $me, 'report' => $report, 
                                                        'month' => $request->month, 
                                                        'search' => $request->search]);
    }
    
    private static function test_deleteRandomResellers() {
        DB::delete("DELETE FROM plans_x_objects WHERE user_id IN (SELECT id FROM users WHERE last_name = 'DUMMY_TEST_PHP')");
        DB::delete("DELETE FROM users WHERE last_name = 'DUMMY_TEST_PHP'");
        DB::delete("DELETE FROM agreement_acceptance WHERE email LIKE '%dummy.com' AND ip = '' AND user_agent = ''");
        DB::delete("DELETE FROM store_environment WHERE store_guid LIKE 'dummy.%'");
        DB::delete("DELETE FROM licenses_log WHERE store_guid LIKE 'dummy.%'");
        DB::delete("DELETE FROM store_app WHERE store_guid LIKE 'dummy.%'");
    }

    private static function test_createRandomResellers($total) {
        ReportCostByStatementController::test_deleteRandomResellers();
        $log = "<PRE>-- Deleted previous random data\n";

        $now = time();
        $envs = ["b78ba4b7-6534-4e3e-87a5-ee496b1b4264", "750375a5-699a-4e76-beda-1ba82233fade"]; # Live/Pilot
        $apps = ["0fbaafa7-7194-4ce7-b45d-3ffc69b2486f", "bc68f95c-1af5-47b1-a76b-e469f151ec3f"]; # Allee/Premium
        $plans = []; # Yearly, Monthly, One-time / [Allee, Premium w/o hardware, Premium]
        $plans[0] = ["bce0f25c-e295-4d3a-97df-3b265aef7a13", "d3319061-56e2-43c5-9f11-e6dc14e2e135", "28a4dc22-b2c6-48d8-9b56-5f4609d3d75f"];
        $plans[1] = ["2d3ecbb0-3359-47bf-ae93-e2b1c80c58ff", "3e04b0e8-d924-4ffc-a281-71c377f777ab", "59dc0fcc-b813-456b-bf9b-259f2dcf9e7f"];
        $plans[2] = ["86692916-f002-450b-8ed1-2d3a7de492e1", "36282caf-a305-44e3-994f-72f2e3f94f5a", "2945f80c-2471-417f-832e-ec620a9bba8d"];

        for ($t = 0; $t < $total; $t++) {
            $id_rs = ($t + 1) * 10000;
            $sql = "INSERT INTO users (id, parent_id, email, business_name, last_name) 
                    VALUES ($id_rs, 0, 'rs$id_rs@dummy.com', 'Reseller $id_rs', 'DUMMY_TEST_PHP')";
            $log .= $sql . "\n";
            DB::insert($sql);

            $accepted_at = time() - 3600 * 24 * rand(1, 365*3);
            $sql = "INSERT INTO agreement_acceptance VALUES (NULL, 'rs$id_rs@dummy.com', '', '', $accepted_at)";
            DB::insert($sql);
            $log .= $sql . "\n";

            $frequency = rand(0, 2); // Yearly, Monthly, One-time
            $p1 = $plans[$frequency][0];
            $p2 = $plans[$frequency][1];
            $p3 = $plans[$frequency][2];
            $sql = "INSERT INTO plans_x_objects VALUES ('$p1', $id_rs), ('$p2', $id_rs), ('$p3', $id_rs)";
            DB::insert($sql);
            $log .= $sql . "\n";

            $total_storegroups = rand(1, 2);
            for ($sg = 0; $sg < $total_storegroups; $sg++) {
                $id_sg = ($sg + 1) * 100 + $id_rs;
                $sql = "INSERT INTO users (id, parent_id, email, business_name, last_name) 
                        VALUES ($id_sg, $id_rs, 'sg$id_sg@dummy.com', 'Store Group $id_sg', 'DUMMY_TEST_PHP')";
                DB::insert($sql);
                $log .= $sql . "\n";

                $total_stores = rand(1, 3);
                
                for ($st = 0; $st < $total_stores; $st++) {
                    $id_st = ($st + 1) * 10 + $id_sg;
                    $store_guid = "dummy.$id_st";
                    $licenses = rand(0, 3);

                    $sql = "INSERT INTO licenses_log VALUES ('$store_guid', $licenses, $now, 1)";
                    DB::insert($sql);
                    $log .= $sql . "\n";

                    $store_app = $apps[rand(0,1)];
                    $hardware = ($store_app != $apps[0]) ? rand(0,1) : 0; # Allee does not have the option 'hardware'

                    $sql = "INSERT INTO users (id, parent_id, email, business_name, last_name, store_guid, hardware) 
                            VALUES ($id_st, $id_sg, 'st$id_st@dummy.com', 'Store $id_st', 
                                    'DUMMY_TEST_PHP', '$store_guid', $hardware)";
                    DB::insert($sql);
                    $log .= $sql . "\n";

                    $store_env = (time() - $accepted_at > 60 * 3600 * 24) ? $envs[0] : $envs[rand(0,1)]; // Live or Random
                    $sql = "INSERT INTO store_environment VALUES ('$store_guid', '$store_env')";
                    DB::insert($sql);
                    $log .= $sql . "\n";

                    $sql = "INSERT INTO store_app VALUES ('$store_guid', '$store_app')";
                    DB::insert($sql);
                    $log .= $sql . "\n\n";
                }
                $log .= "\n";
            }
            $log .= "\n";
        }
        die($log);
    }

    // @return false|Array of ReportElementReseller
    public static function getAdminReport($me, Request $request) {
        $live_store_guid = 'b78ba4b7-6534-4e3e-87a5-ee496b1b4264';
        $role = $me->roles[0]->weight;

        if ($role != 1000) return false;

        if (isset($request->test_dummy)) ReportCostByStatementController::test_createRandomResellers(10);

        $searchSQL = "";
        $sqlParams = [];
        if (isset($request->search)) {
            $searchSQL = "AND (u_reseller.business_name LIKE '%?%'
                             OR u_storegroup.business_name LIKE '%?%'
                             OR u.business_name LIKE '%?%')";
            $sqlParams = [$request->search, $request->search, $request->search];
        }
        $sql = "SELECT 
                    -- Ids
                    u_reseller.id AS reseller_id, 
                    u_storegroup.id AS store_group_id, 
                    u.id AS store_id,

                    -- Names
                    u_reseller.business_name AS bn_reseller, 
                    u_storegroup.business_name AS bn_storegroup, 
                    u.business_name AS bn_store,

                    -- App
                    apps.name AS app_name, 
                    p.hardware, 
                    p.cost, 
                    p.payment_freq,

                    -- Contract
                    aa.accepted_at, 
                    p.longevity_months,

                    -- Store
                    se.environment_guid,  
                    (SELECT IFNULL(quantity, 0) FROM licenses_log WHERE store_guid = u.store_guid 
                        ORDER BY update_time DESC LIMIT 1) AS licenses
                FROM
                    users u
                INNER JOIN users u_storegroup
                    ON u.parent_id = u_storegroup.id
                INNER JOIN users u_reseller
                    ON u_reseller.id = u_storegroup.parent_id
                INNER JOIN store_app sa
                    ON sa.store_guid = u.store_guid
                INNER JOIN plans p
                    ON p.app = sa.app_guid AND p.hardware = u.hardware
                INNER JOIN plans_x_objects po
                    ON po.user_id = u_reseller.id AND po.plan_guid = p.guid
                INNER JOIN agreement_acceptance aa
                    ON aa.email = u_reseller.email
                INNER JOIN store_environment se
                    ON se.store_guid = u.store_guid
                INNER JOIN apps
                    ON apps.guid = p.app
                
                WHERE 
                    (u_reseller.deleted_at IS NULL OR u_reseller.deleted_at = 0)
                    AND (u_storegroup.deleted_at IS NULL OR u_storegroup.deleted_at = 0)
                    AND (u.deleted_at IS NULL OR u.deleted_at = 0)

                $searchSQL

                ORDER BY u_reseller.id, u_storegroup.id, u.id";
        
        $result = DB::select($sql, $sqlParams);

        // No result
        if (!$result) return false;
        if (count($result) == 0) return false;

        $answer = [];
        $last_reseller_id = 0;
        $last_store_group_id = 0;
        $last_store_id = 0;
        foreach ($result as $row) {
            $reseller_id = $row->reseller_id;
            if ($last_reseller_id != $reseller_id) {
                $reseller = new ReportElementReseller();
                $reseller->setBusinessName($row->bn_reseller);
                $answer[] = $reseller;
                $last_reseller_id = $reseller_id;
            }

            $store_group_id = $row->store_group_id;
            if ($last_store_group_id != $store_group_id) {
                $store_group = new ReportElementStoreGroup();
                $store_group->setBusinessName($row->bn_storegroup);
                $reseller->insertStoreGroup($store_group);
                $last_store_group_id = $store_group_id;
            }
        
            if (isset($request->month)) {
                $dt = new \DateTime();
                $dt->setTimestamp(strtotime($request->month . "-01"));
                $dt->modify("last day of this month")->setTime(23,59,59);
                $referenceTimestamp = $dt->getTimestamp();
            } else {
                $referenceTimestamp = 0;
            }
            
            $store_id = $row->store_id;
            if ($last_store_id != $store_id) {
                $store = new ReportElementStore();
                $store->setBusinessName($row->bn_store)
                      ->setLiveStore($row->environment_guid == $live_store_guid)
                      ->setLicenses($row->licenses)
                      ->calculateDaysSinceLive($row->accepted_at, $referenceTimestamp, Parameters::getValue("@days_pilot_to_live", 60));
                $store_group->insertStore($store);
                $last_store_id = $store_id;
            }

            $app = new ReportElementApp();
            $app->setName($row->app_name)
                ->setHasHardware($row->hardware == 1)
                ->setCost($row->cost)
                ->setPaymentFreq($row->payment_freq);
            $store->setApp($app);
        }

        return $answer;
    }
}