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
use App\PDFWriter\PDFWriter;
use App\PDFWriter\PDFWriter\PDFWriter as AppPDFWriter;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\URL;
use App\Models\Parameters;

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
        
        return view('admin.resellers.form', ['type' => 'new', 'user' => $reseller]);
    }

    public function getPlans() {
        // Not admin
        $user = Auth::user();
        if ($user->roles[0]->weight !== 1000) {
            return false;
        }
        
        $plans = DB::select("SELECT `guid`, 
                                    CONCAT(`name`, ' (', CONCAT(UCASE(LEFT(payment_freq, 1)), LCASE(SUBSTRING(payment_freq, 2))),
                                             ', ', longevity_months, 'mo, US$', cost, ')') AS `name`, 
                                    `cost`, `app`, `hardware`
                                FROM plans
                                WHERE (owner_id = 0 OR owner_id = ?) AND delete_time = 0
                                ORDER BY `default` DESC, `name`", [$user->id]);
        return $plans;
    }

    public function addPlan(Request $request) {
        if ((!isset($request->name)) || (!isset($request->cost)) || 
            (!isset($request->longevity)) || (!isset($request->frequency)) ||
            (!isset($request->app)) || (!isset($request->hardware))) {
                return response('{"success": false, "error": "Invalid parameters"}', 200)
                        ->header('Content-Type', 'application/json');
        }

        // Plan cost must be a positive number
        if (!is_numeric($request->cost)) {
            return response('{"success": false, "error": "Plan cost must be a positive number"}', 200)
                    ->header('Content-Type', 'application/json');
        }

        if ($request->cost < 0) {
            return response('{"success": false, "error": "Plan cost must be a positive number"}', 200)
                    ->header('Content-Type', 'application/json');
        }

        // Plan longevity must be > 0 months
        if (!is_numeric($request->longevity)) {
            return response('{"success": false, "error": "Plan Longevity must be numeric"}', 200)
                    ->header('Content-Type', 'application/json');
        }

        if ($request->longevity < 1) {
            return response('{"success": false, "error": "Plan Longevity must be higher than 0"}', 200)
                    ->header('Content-Type', 'application/json');
        }

        // Create plan
        $guid = Uuid::uuid4();
        $sql = "INSERT INTO plans (`owner_id`, `guid`, `cost`, `name`, `app`, `hardware`, 
                                    `payment_freq`, `longevity_months`, `payment_type`, `create_time`) 
                VALUES  (?, ?, ?, ?, ?, ?, ?, ?, '', UNIX_TIMESTAMP())";
        $result = DB::insert($sql, [Auth::user()->id, $guid, $request->cost, $request->name, $request->app, 
                                    $request->hardware, $request->frequency, $request->longevity]);
        if (!$result) {
            return response('{"success": false, "error": "An error occurred while saving the new plan"}', 200)
                    ->header('Content-Type', 'application/json');
        }

        return response('{"success": true, "id": "' . $guid . '"}', 200)->header('Content-Type', 'application/json');
    }
    
    public function insert(Request $request)
    {
        $id = Auth::user()->id;
        $created_at = new DateTime();
        $business_name = $request->business_name;
        $dba = $request->dba;
        $contact_first_name = $request->name;
        $contact_last_name = $request->last_name;
        $email = $request->email;
        $plan_allee = $request->plan_allee;
        $plan_premium = $request->plan_premium;
        $plan_premium_hardware = $request->plan_premium_hardware;

        if (($plan_allee == "add_new") || ($plan_premium == "add_new") || ($plan_premium_hardware == "add_new")) {
            return response('{"success": false, "error": "One or more plans were not selected. Please try again."}', 200)
                        ->header('Content-Type', 'application/json');
        }

        $plan_premium = $request->plan_premium;
        $plan_premium_hardware = $request->plan_premium_hardware;

        $data = ["%ID%" => $id,
                "%BUSINESS_NAME%" => $business_name,
                "%DBA%" => strlen($dba) > 0 ? " dba. $dba" : "",
                "%FIRST_NAME%" => $contact_first_name,
                "%LAST_NAME%" => $contact_last_name,
                "%EMAIL%" => $email];
        
        // Check if the reseller is already registered
        $registered = DB::select("SELECT COUNT(1) AS cnt FROM users WHERE email = ?", [$email])[0]->cnt > 0;
        if ($registered) {
            return response('{"success": false, "error": "There is already a reseller registered with the e-mail \'' . $email . '\'"}', 200)
                        ->header('Content-Type', 'application/json');
        }

        // Insert new reseller into User's table
        $inserted = DB::insert("INSERT INTO users (`parent_id`, `name`, `last_name`, `email`, `active`, `created_at`, 
                                        `updated_at`, `business_name`, `dba`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                                [$id, $contact_first_name, $contact_last_name, $email, 1, $created_at, 
                                $created_at, $business_name, $dba]);
        
        $reseller_id = DB::select("SELECT LAST_INSERT_ID() AS id")[0]->id;

        if ((!$inserted) || ($reseller_id === 0)) {
            // DB insert error
            return response('{"success": false, "error": "An error ocurred while saving the new reseller."}', 200)
                        ->header('Content-Type', 'application/json');
        }

        // Set up the 3 plans
        $inserted = DB::insert("INSERT INTO plans_x_objects (`plan_guid`, `user_id`) VALUES (?, ?), (?, ?), (?, ?)",
                        [$plan_allee, $reseller_id, $plan_premium, $reseller_id, $plan_premium_hardware, $reseller_id]);

        if ((!$inserted) || ($reseller_id === 0)) {
            // DB insert error
            return response('{"success": false, "error": "An error ocurred while saving the new reseller\'s plans."}', 200)
                        ->header('Content-Type', 'application/json');
        }

        // Set up user role
        $inserted = DB::insert("INSERT INTO users_roles (user_id, role_id) VALUES (?, 2)", [$reseller_id]);

        if (!$inserted) {
            // DB insert error
            return response('{"success": false, "error": "An error ocurred while saving the new reseller\'s plans."}', 200)
                        ->header('Content-Type', 'application/json');
        }

        // Set up payment info
        $extended_support = isset($request->check_extended_support) ? 1 : 0;
        $onsite_training = isset($request->check_onsite_training) ? 1 : 0;
        $inserted = DB::insert("INSERT INTO payment_info (`user_id`, `extended_support`, `onsite_training`, `authorized`) VALUES (?, ?, ?, 0)",
                        [$reseller_id, $extended_support, $onsite_training]);

        if (!$inserted) {
            // DB insert error
            return response('{"success": false, "error": "An error ocurred while saving the new reseller\'s plans."}', 200)
                        ->header('Content-Type', 'application/json');
        }

        // Create & Send form link
        $link_sent = $this->create_and_send_link($reseller_id, $email, $data);
        
        if (!$link_sent) {
            // Create/Send link error
            return response('{"success": false, "error": "An error ocurred while creating the link."}', 200)
                        ->header('Content-Type', 'application/json');
        }

        return response('{"success": true}', 200)->header('Content-Type', 'application/json');
    }

    function create_and_send_link($reseller_id, $email, $data) {
        $expiration_date = strtotime(date('Y-m-d 23:59:59', time() + 48 * 3600));
        
        $hash = sha1(time() . $reseller_id . $email);
        $inserted = DB::insert("INSERT INTO forms_links (`user_id`, `link_hash`, `created_at`) VALUES (?, ?, ?)",
                                [$reseller_id, $hash, time()]);
        if (!$inserted) {
            return false;
        }

        $headers = "From: " . Parameters::getValue("@reseller_link_email_from", "system@kdsgo.com") . "\r\n";
        $headers .= "Reply-To: " . Parameters::getValue("@reseller_link_email_reply_to", "do-not-reply@kdsgo.com") . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $subject = Parameters::getValue("@reseller_link_email_subject", "KitchenGo: Action needed until %EXPIRATION%");
        $message = file_get_contents(Parameters::getValue("@reseller_link_email_body_html_file", "assets/includes/email_new_reseller.html"));
        
        $data["%URL%"] = url(Parameters::getValue("@reseller_link_form_prepend", "forms")) . "/" . $hash;
        $data["%EXPIRATION%"] = date('m/d/Y', $expiration_date);

        $this->replaceResellerData($subject, $data);
        $this->replaceResellerData($message, $data);

        return mail($email, $subject, $message, $headers);
    }

    // data: key will be replaced by its value. e.g. ["%URL%" => "www..."]
    function replaceResellerData(&$string, $data) {
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $string = str_replace($key, $value, $string);
            }
        }
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
        
        return view('admin.form', ['obj' => 'reseller', 'user' => $reseller, 
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
