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

    private function getPaymentInfo() {
        $mainDB = env('DB_DATABASE', 'kdsweb');
        return DB::select("SELECT * FROM $mainDB.payment_info WHERE user_id = ?", [Auth::user()->id])[0];
    }

    // private function getLicensesCount() {
    //     $mainDB = env('DB_DATABASE', 'kdsweb');
    //     $id = Auth::user()->id;
    //     $sql = "SELECT quantity 
    //             FROM $mainDB.licenses_log 
    //             WHERE store_guid IN (
    //                                     SELECT DISTINCT store_guid 
    //                                     FROM $mainDB.users
    //                                     WHERE parent_id = ?
    //                                     OR parent_id IN (
    //                                                         SELECT DISTINCT id
    //                                                         FROM $mainDB.users
    //                                                         WHERE parent_id = ?
    //                                                     )
    //                                 )
    //             ORDER BY update_time DESC
    //             LIMIT 1";

    //     return DB::select($sql, [$id, $id])[0]->quantity;
    // }

    private function getUserInfo() {
        $mainDB = env('DB_DATABASE', 'kdsweb');
        return DB::select("SELECT u.*, s.name AS state_name
                            FROM $mainDB.users u
                            INNER JOIN $mainDB.states s
                            ON s.id = u.state
                            WHERE u.id = ?", [Auth::user()->id])[0];
    }

    /**
     * Gets contact info for Company, Shipping and/or Billing
     * Returns array ["billing"], ["shipping"] and ["company"]
     *
     * @return Array
     */
    private function getContactInfo() {
        $answer = [];
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT *
                            FROM $mainDB.contact_info
                            WHERE user_id = ?", [Auth::user()->id]);
        foreach ($result as $info) {
            $answer[strtolower($info->address_type)] = $info;
        }
        return $answer;
    }

    private function getResellerPriceAgreement() {
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $id = Auth::user()->id;
        $sql = "SELECT MAX(p1) AS p1, MAX(p2) AS p2, MAX(p3) AS p3
                FROM ((
                            SELECT price AS p1, 0 AS p2, 0 AS p3 FROM $mainDB.billing 
                            WHERE user_id = ?
                            AND app_guid = 'bc68f95c-1af5-47b1-a76b-e469f151ec3f'
                            AND hardware = 1
                            ORDER BY create_time DESC
                            LIMIT 1
                        ) UNION ALL (
                            SELECT 0, price, 0 FROM $mainDB.billing 
                            WHERE user_id = ?
                            AND app_guid = 'bc68f95c-1af5-47b1-a76b-e469f151ec3f'
                            AND hardware = 0
                            ORDER BY create_time DESC
                            LIMIT 1
                        ) UNION ALL (
                            SELECT 0, 0, price FROM $mainDB.billing 
                            WHERE user_id = ?
                            AND app_guid = '0fbaafa7-7194-4ce7-b45d-3ffc69b2486f'
                            AND hardware = 0
                            ORDER BY create_time DESC
                            LIMIT 1
                    )) prices";

        return DB::select($sql, [$id, $id, $id])[0];
    }

    public function showAgreement(User $reseller) {
        if ($reseller->roles[0]->weight !== 900) {
            return view('admin.forbidden');
        }

        $pdf = $this->createAgreement();
        if ($pdf === false) {
            return view('admin.agreement_continue', ["return" => "error"]);
        } else {
            $pdf->output();
        }
    }

    /**
     * @return App\PDFWriter\PDFWriter\PDFWriter
     */
    private function createAgreement($signature_timestamp = 0) {
        $id = Auth::user()->id;
        $fsNormal = 11;
        $fsSmall = 9;
        $color_white = [255, 255, 255];
        $color_black = [0, 0, 0];
        
        // Get user info including State's name
        $user = $this->getUserInfo();
        $payment = $this->getPaymentInfo();
        $licenses = 5; // $this->getLicensesCount();
        $reseller_prices = $this->getResellerPriceAgreement();
        $contact_info = $this->getContactInfo();

        $company = [];
        if (!array_key_exists("company", $contact_info)) {
            $company["address1"] = $user->address;
            $company["address2"] = $user->address2;
            $company["city"] = $user->city;
            $company["state"] = $user->state_name;
            $company["zipcode"] = $user->zipcode;
            $company["email"] = $user->email;
            $company["phone"] = $user->phone_number;
        } else {
            $company["address1"] = $contact_info["company"]->address_1;
            $company["address2"] = $contact_info["company"]->address_2;
            $company["city"] = $contact_info["company"]->city;
            $company["state"] = $contact_info["company"]->state;
            $company["zipcode"] = $contact_info["company"]->zipcode;
            $company["email"] = $contact_info["company"]->email;
            $company["phone"] = $contact_info["company"]->phone;
        }

        // Start writing on PDF
        $pdf = new PDFWriter();
        if (!$pdf->setFile("./models/reseller_subscription_agreement_template.pdf")) {
            return false;
        }
        
        // 1. SUBSCRIPTION: Price agreement
        $price_1 = $reseller_prices->p1; // Premium + HW
        $price_2 = $reseller_prices->p2; // Premium w/o HW
        $price_3 = $reseller_prices->p3; // Allee w/o HW
        $price_1_string = "$" . number_format($price_1, 2) . "/Station/Month";
        $price_2_string = "$" . number_format($price_2, 2) . "/Station/Month";
        $price_3_string = "$" . number_format($price_3, 2) . "/Station/Month";
        $pdf->box(1, 70, 48.7, 42, 3.8, $color_white);
        $pdf->box(1, 130, 48.7, 42, 3.8, $color_white);
        $pdf->box(1, 130, 44.5, 42, 3.2, $color_white);
        $pdf->writeAt(1, 70, 48.7 + 2, $price_1_string, $fsNormal);
        $pdf->writeAt(1, 130, 48.7 + 2, $price_2_string, $fsNormal);
        $pdf->writeAt(1, 130, 44.2 + 2, $price_3_string, $fsNormal);
        
        // 1. SUBSCRIPTION: Extended Support Agreement
        $extendedSupportAgreementPrice = 10; // TODO: hard-coded value
        if ($payment->extended_support) {
            $pdf->box(1, 40, 58, 3, 3, $color_black);
        }
        
        // 1. SUBSCRIPTION: On site training
        if ($payment->onsite_training) {
            $pdf->box(1, 40, 63, 3, 3, $color_black);
        }

        // 2. SITE INFORMATION
        $pdf->writeAt(1, 38, 69, $licenses, $fsNormal);
        $userFullName = $user->name . " " . $user->last_name;
        $pdf->writeAt(1, 58, 145, $user->business_name, $fsNormal);
        $pdf->writeAt(1, 58, 152, $company["address1"], $fsNormal);
        $pdf->writeAt(1, 58, 157, $company["address2"], $fsNormal);
        $pdf->writeAt(1, 58, 161.5, $company["city"], $fsNormal);
        $pdf->writeAt(1, 119, 161.5, $company["state"], $fsNormal);
        $pdf->writeAt(1, 153, 161.5, $company["zipcode"], $fsNormal);
        $pdf->writeAt(1, 58, 167, $userFullName, $fsNormal);
        $pdf->writeAt(1, 58, 172, $company["email"], $fsNormal);
        $pdf->writeAt(1, 58, 177, $company["phone"], $fsNormal);

        // 2. SITE INFORMATION: Shipping
        if (array_key_exists("shipping", $contact_info)) {
            $pdf->writeAt(1, 58, 187, $contact_info["shipping"]->address_1, $fsNormal);
            $pdf->writeAt(1, 58, 192, $contact_info["shipping"]->address_2, $fsNormal);
            $pdf->writeAt(1, 58, 197.5, $contact_info["shipping"]->city, $fsNormal);
            $pdf->writeAt(1, 119, 197.5, $contact_info["shipping"]->state, $fsNormal);
            $pdf->writeAt(1, 154, 197.5, $contact_info["shipping"]->zip, $fsNormal);
        }

        // 2. SITE INFORMATION: Bill-To Address
        if (array_key_exists("billing", $contact_info)) {
            $pdf->writeAt(1, 58, 223, $user->business_name, $fsNormal);
            $pdf->writeAt(1, 58, 230, $contact_info["billing"]->address_1, $fsNormal);
            $pdf->writeAt(1, 58, 235, $contact_info["billing"]->address_2, $fsNormal);
            $pdf->writeAt(1, 58, 239.8, $contact_info["billing"]->city, $fsNormal);
            $pdf->writeAt(1, 119.5, 239.8, $contact_info["billing"]->state, $fsNormal);
            $pdf->writeAt(1, 154, 239.8, $contact_info["billing"]->zipcode, $fsNormal);
            $pdf->writeAt(1, 58, 245, $contact_info["billing"]->care_of, $fsNormal);
            $pdf->writeAt(1, 58, 250, $contact_info["billing"]->email, $fsNormal);
            $pdf->writeAt(1, 58, 255, $contact_info["billing"]->phone, $fsNormal);
        }

        // 3. CREDIT CARD
        $pdf->writeAt(2, 30, 120, $userFullName, $fsNormal);
        $pdf->writeAt(2, 142, 142, date("d F Y"), $fsNormal);
        switch ($payment->card_type) {
            case "VISA":
                $pdf->box(2, 89, 50, 3, 3, $color_black); // Visa
                break;

            case "AMEX":
                $pdf->box(2, 113, 50, 3, 3, $color_black); // AmEx
                break;

            case "DISCOVER":
                $pdf->box(2, 144, 50, 3, 3, $color_black); // Discover
                break;

            default:
                $pdf->box(2, 51, 50, 3, 3, $color_black); // MasterCard
                break;
        }

        // Calculate the total recurring payment
        $totalPayment = ($payment->extended_support ? $extendedSupportAgreementPrice : 0);


        // Credit Card information
        $pdf->writeAt(2, 45, 59, $payment->card_exp_date, $fsNormal); // Exp. Date
        $pdf->writeAt(2, 115, 59, $payment->card_cvv, $fsNormal); // CVV
        $pdf->writeAt(2, 89, 67, "**** **** **** " . $payment->card_last4, $fsNormal); // Last 4 digits
        $pdf->writeAt(2, 89, 73.5, "N/A", $fsNormal); // One-time payment
        $pdf->writeAt(2, 158, 73.5, number_format($totalPayment, 2), $fsNormal); // Recurring payment

        // Billing Address
        if (array_key_exists("billing", $contact_info)) {
            $pdf->writeAt(2, 26, 104, $contact_info["billing"]->address_1 . " " . $contact_info["billing"]->address_2, $fsNormal);
            $pdf->writeAt(2, 35, 110.5, $contact_info["billing"]->city, $fsNormal);
            $pdf->writeAt(2, 110, 110.5, $contact_info["billing"]->state, $fsNormal);
            $pdf->writeAt(2, 153, 110.5, $contact_info["billing"]->zipcode, $fsNormal);
        }

        // AGREEMENT page 1
        $fullAddress = $company["address1"] . " " . $company["address2"] . " " . $company["city"] . " " . 
                        $company["state"] . " " . $company["zipcode"];
        $fontSizeFullAddress = Min($fsSmall, $fsSmall * 56 / (strlen($fullAddress) + 1)); # Try to optimize address font size
        $pdf->writeAt(3, 90, 55, $user->business_name, $fsSmall);
        $pdf->writeAt(3, 50, 59, $fullAddress, $fontSizeFullAddress);

        // AGREEMENT page 7
        $pdf->writeAt(9, 110, 147, $user->business_name, $fsSmall);
        $pdf->writeAt(9, 110, 152, $company["address1"] . " " . $company["address2"], $fsSmall); 
        $pdf->writeAt(9, 110, 156, $company["city"] . " " . $company["state"], $fsSmall);
        $pdf->writeAt(9, 110, 160.5, $company["zipcode"], $fsSmall); 
        $pdf->writeAt(9, 117, 166, $company["phone"], $fsSmall); 
        $pdf->writeAt(9, 123, 179, $company["email"], $fsSmall); 

        // AGREEMENT page 8
        $pdf->writeAt(10, 27.5, 150, $user->business_name, $fsNormal);
        $pdf->writeAt(10, 37, 168, $userFullName, $fsNormal);
        $pdf->writeAt(10, 20, 178.5, date("d F Y"), $fsNormal);
        $pdf->writeAt(10, 120, 178.5, date("d F Y"), $fsNormal);

        $title = $signature_timestamp > 0 ? "DOCUMENT SIGNED ELECTRONICALLY" : "";
        $pdf->stampElectronicSignature($id, $signature_timestamp, 10, 8, 200, $title);

        return $pdf;
    }

    public function confirmAgreement(Request $request) {
        $me = Auth::User();
        $return = "rejected";

        if ($request->agree === "ok") {
            $now = time();
            $nowYMD = date("Y-m-d", $now);

            // Save to database
            $sql = "INSERT INTO agreement_acceptance (email, ip, user_agent, accepted_at) VALUES(?, ?, ?, $now)";
            DB::statement($sql, [$me->email, $request->server("REMOTE_ADDR"), $request->server("HTTP_USER_AGENT")]);

            // Create PDF, stamp the electronic signature and save as file
            $pdf = $this->createAgreement($now);
            $filename = "./agreements/%HASH%.pdf";

            if (!$pdf->output($filename, "%HASH%")) {
                $return = "error";
            } else {
                $return = "accepted";
            }
        }

        return view('admin.agreement_continue', compact('return'));

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
