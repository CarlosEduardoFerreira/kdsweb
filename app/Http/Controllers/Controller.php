<?php

namespace App\Http\Controllers;

use DateTimeZone;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Auth\User\User;
use App\Models\Settings\Plan;
use App\Models\Settings\PlanXObject;
use App\PDFWriter\PDFWriter;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    private $DB;
    private $connection = "mysql";
    
    
    function forbidden() {
        return view('admin.forbidden', []);
    }
    
    function checkResellerAgreement(User $me) {
        if ($me->roles[0]->weight !== 900) return true;
        
        $accepted_at = DB::select("SELECT accepted_at 
                                    FROM agreement_acceptance 
                                    WHERE email = ? 
                                    ORDER BY accepted_at DESC 
                                    LIMIT 1", [$me->email]);

        $agreement_accepted = false;
        if (count($accepted_at) > 0) {
            $agreement_accepted = $accepted_at[0]->accepted_at > 0;
        }

        return $agreement_accepted;
    }

    public function getReseller($formsHash, $expirationDays = 2) {
        $sql = "SELECT f.created_at, u.id, u.email, CONCAT(u.name, ' ', u.last_name) as fullname
                FROM forms_links f 
                INNER JOIN users u 
                ON f.user_id = u.id 
                WHERE link_hash = ?
                LIMIT 1";
        $result = DB::select($sql, [$formsHash]);
        
        if (!$result) return false;
        if (time() > $result[0]->created_at + $expirationDays * 24 * 3600) return false;
        return ["id" => $result[0]->id, "email" => $result[0]->email, "fullname" => $result[0]->fullname];
    }

    public function resellerNewUser(Request $request, $hash) {
        // Get User Id (link valid for 14 days)
        $reseller = $this->getReseller($hash, 14);

        // Not valid result => Expired/Not available
        if ($reseller === false) {
            return view('admin.resellers.newuser', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // Check if the user is authorized (credit card)
        $result = DB::select("SELECT COUNT(1) AS count
                                FROM payment_info 
                                WHERE user_id = ? 
                                AND authorized = 1", [$reseller["id"]]);

        if (!$result) {
            return view('admin.resellers.newuser', ["error" => "The page you are trying to access has expired or is not available."]);
        }
       
        if ($result[0]->count == 0) {
            return view('admin.resellers.newuser', ["error" => "Your card has not yet been authorized. Please try again later."]);
        } else {
            $email = $reseller["email"];
            $fullname = $reseller["fullname"];
            return view('admin.resellers.newuser', compact('hash', 'email', 'fullname'));
        } 
    }

    public function resellerSetPassword(Request $request, $hash) {
        // Get User Id (link valid for 14 days)
        $reseller = $this->getReseller($hash, 14);

        // Not valid result => Expired/Not available
        if ($reseller === false) {
            return view('admin.resellers.newuser', ["error" => "The page you are trying to access has expired or is not available."]);
        }
        
        // Re-check inputs
        if (!isset($request->password) || !isset($request->password2)) {
            return view('admin.resellers.newuser', ["error" => "Invalid request. Please try again later."]);
        }

        if (strlen($request->password) < 6) {
            return view('admin.resellers.newuser', ["error" => "Password must be at least 6 characters long. Please try again."]);
        }

        if ($request->password !== $request->password2) {
            return view('admin.resellers.newuser', ["error" => "Passwords must match. Please try again."]);
        }

        if (!isset($request->email)) {
            return view('admin.resellers.newuser', ["error" => "No e-mail found. Please contact our customer support."]);
        }

        if (strlen($request->email) == 0) {
            return view('admin.resellers.newuser', ["error" => "No e-mail found. Please contact our customer support."]);
        }

        // All inputs OK: hash & save password
        $password_hash = bcrypt($request->password);
        $sql = "UPDATE users
                SET password = ?, updated_at = NOW()
                WHERE id = ?";
        $affected_rows = DB::update($sql, [$password_hash, $reseller["id"]]);

        if ($affected_rows == 0) {
            return view('admin.resellers.newuser', ["error" => "An error occurred while setting your password. Please try again later."]);
        } else {
            return view('admin.resellers.newuser', ["success" => true]);
        }
    }

    public function resellerUpdateInfo(Request $request, $hash) {
        // Get User Id
        $reseller = $this->getReseller($hash);

        // Not valid result => Expired/Not available
        if ($reseller === false) {
            return view('admin.resellers.agreement', ["error" => "The page you are trying to access has expired or is not available."]);
        }
        $user_id = $reseller["id"];

        // Update Reseller Information
        $sql = "UPDATE users
                SET business_name = ?,
                    dba = ?,
                    `name` = ?,
                    last_name = ?,
                    `address` = ?,
                    address2 = ?,
                    city = ?,
                    `state` = ?,
                    country = ?,
                    zipcode = ?,
                    phone_number = ?
                WHERE id = ?";

        $updated_rows = DB::update($sql, [$request->company_business_name,
                                            $request->company_dba,
                                            $request->company_first_name,
                                            $request->company_last_name,
                                            $request->company_address1,
                                            $request->company_address2,
                                            $request->company_city,
                                            $request->company_state,
                                            $request->company_zipcode,
                                            $request->company_state,
                                            $request->company_phone,
                                            $user_id]);

        // Add shipping contact information, if any
        if (isset($request->chk_shipping)) {
            // Make sure that (initially) there is only one SHIPPING/BILLING address
            DB::delete("DELETE FROM contact_info WHERE user_id = ? AND address_type = 'SHIPPING'", [$user_id]);
            $sql = "INSERT INTO contact_info(user_id, address_type, care_of, address_1, address_2, 
                                                city, state, zipcode, email, phone, updated_at, updated_by)
                    VALUES(?, 'SHIPPING', ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), 0)";
            DB::insert($sql, [$user_id, 
                                $request->shipping_careof, 
                                $request->shipping_address1,
                                $request->shipping_address2,
                                $request->shipping_city,
                                $request->shipping_state,
                                $request->shipping_zipcode,
                                $request->shipping_email,
                                $request->shipping_phone]);
        }

        // Add billing contact information, if any
        if (isset($request->chk_billing)) {
            // Make sure that (initially) there is only one SHIPPING/BILLING address
            DB::delete("DELETE FROM contact_info WHERE user_id = ? AND address_type = 'BILLING'", [$user_id]);
            $sql = "INSERT INTO contact_info(user_id, address_type, care_of, address_1, address_2, 
                                                city, state, zipcode, email, phone, updated_at, updated_by)
                    VALUES(?, 'BILLING', ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), 0)";
            DB::insert($sql, [$user_id, 
                                $request->billing_careof, 
                                $request->billing_address1,
                                $request->billing_address2,
                                $request->billing_city,
                                $request->billing_state,
                                $request->billing_zipcode,
                                $request->billing_email,
                                $request->billing_phone]);
        }

        // Add credit card information, making sure that (initially) there is only one credit card information
        DB::delete("DELETE FROM payment_info WHERE user_id = ?", [$user_id]);
        $sql = "INSERT INTO payment_info(user_id, card_type, card_exp_date, card_cvv, card_last4, authorized) 
                VALUES(?, ?, ?, ?, ?, 0)";
        $added_rows = DB::insert($sql, [$user_id, 
                                        $request->card_brand, 
                                        $request->card_expiration_month . "/" . $request->card_expiration_year,
                                        $request->card_cvv,
                                        $request->card_last4]);

        if ($added_rows === 0) {
            return view('admin.resellers.agreement', ["error" => "An error occurred while updating your data. Please try again later."]);
        }

        return redirect(route('resellers.show_agreement', [$hash]));
    }

    public function resellerDisplayAgreement(Request $request, $hash) {
        return view('admin.resellers.agreement', compact('hash'));
    }

    public function resellerShowAgreementPDF(Request $request, $hash) {
        $reseller = $this->getReseller($hash);
        if (!$reseller) {
            return view('admin.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        }

        $pdf = $this->createAgreement($reseller["id"]);
        if (!$pdf) {
            return view('admin.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        } else { 
            return $pdf->output();
        }
    }

    public function resellerAcceptAgreement(Request $request, $hash) {
        $reseller = $this->getReseller($hash);
        if (!$reseller) {
            return view('admin.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        }
        
        if ($request->agree === "ok") {
            $now = time();

            // Save to database
            $sql = "INSERT INTO agreement_acceptance (email, ip, user_agent, accepted_at) VALUES(?, ?, ?, $now)";
            $affected_rows = DB::insert($sql, [$reseller["email"], $request->server("REMOTE_ADDR"), $request->server("HTTP_USER_AGENT")]);

            if ($affected_rows <= 0) {
                return view('admin.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            }

            // Create PDF, stamp the electronic signature and save as file
            $pdf = $this->createAgreement($reseller["id"], $now);
            $filename = "./agreements/%HASH%.pdf";

            if (!$pdf->output($filename, "%HASH%")) {
                return view('admin.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            } else {
                return view('admin.resellers.agreement', ["accepted" => true]);
            }
        } else {
            return view('admin.resellers.agreement', ["error" => "We're sorry, but you need to accept the agreement to use our services."]);
        }
    }
    
    private function getResellerPaymentInfo($id) {
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT * FROM $mainDB.payment_info WHERE user_id = ?", [$id]);
        if (!$result) {
            return false;
        } else {
            return $result[0];
        }
    }

    private function getResellerUserInfo($id) {
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT u.*, s.name AS state_name
                            FROM $mainDB.users u
                            INNER JOIN $mainDB.states s
                            ON s.id = u.state
                            WHERE u.id = ?", [$id]);
        if (!$result) {
            return false;
        } else {
            return $result[0];
        }
    }

    /**
     * Gets contact info for Company, Shipping and/or Billing
     * Returns array ["billing"], ["shipping"] and ["company"]
     *
     * @return Array
     */
    private function getResellerContactInfo($id) {
        $answer = [];
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT *
                            FROM $mainDB.contact_info
                            WHERE user_id = ?", [$id]);
        if (!$result) {
            return false;
        } 
        
        foreach ($result as $info) {
            $answer[strtolower($info->address_type)] = $info;
        }
        return $answer;
    }

    private function getResellerPriceAgreement($id) {
        $mainDB = env('DB_DATABASE', 'kdsweb');
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

    /**
     * @return App\PDFWriter\PDFWriter\PDFWriter
     */
    private function createAgreement($id, $signature_timestamp = 0) {
        $fsNormal = 11;
        $fsSmall = 9;
        $color_white = [255, 255, 255];
        $color_black = [0, 0, 0];
        
        // Get user info including State's name
        $user = $this->getResellerUserInfo($id);
        $payment = $this->getResellerPaymentInfo($id);
        $reseller_prices = $this->getResellerPriceAgreement($id);
        $contact_info = $this->getResellerContactInfo($id);

        if (($user === false) || ($payment === false) || ($reseller_prices === false)) {
            return false;
        }

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
            $pdf->writeAt(1, 154, 197.5, $contact_info["shipping"]->zipcode, $fsNormal);
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

        // Credit Card information
        $pdf->writeAt(2, 45, 59, $payment->card_exp_date, $fsNormal); // Exp. Date
        $pdf->writeAt(2, 115, 59, $payment->card_cvv, $fsNormal); // CVV
        $pdf->writeAt(2, 89, 67, "**** **** **** " . $payment->card_last4, $fsNormal); // Last 4 digits
        $pdf->writeAt(2, 89, 73.5, "N/A", $fsNormal); // One-time payment

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
    
    public function resellerShowForm(Request $request, $hash) {
        $sql = "SELECT f.created_at, u.email, u.dba, u.business_name, p.subscription, p.extended_support, p.onsite_training, p.number_licenses
                FROM forms_links f 
                INNER JOIN users u
                ON f.user_id = u.id
                INNER JOIN payment_info p
                ON f.user_id = p.user_id
                WHERE f.link_hash = ?";
        $result = DB::select($sql, [$hash]);

        // Not valid result => Expired/Not available
        if (!$result) {
            return view('admin.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // No rows returned => Expired/Not available
        if (count($result) == 0) {
            return view('admin.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // Expired => Expired page
        if (time() - $result[0]->created_at > 48 * 3600 * 1000) {
            return view('admin.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // Everything OK => Go to fill the forms
        $email = $result[0]->email;
        $business_name = $result[0]->business_name;
        $dba = $result[0]->dba;
        $subscription = $result[0]->subscription;
        $extended_support = $result[0]->extended_support;
        $onsite_training = $result[0]->onsite_training;
        $number_licenses = $result[0]->number_licenses;
        return view('admin.resellers.fill', compact('hash', 'email', 'business_name', 'dba', 'subscription'));
    }

    public function approvePaymentType(Request $request, $hash, $approve = "") {
        // No hash => Error
        if (!isset($hash)) {
            return view('admin.resellers.authorize', ["error" => true]);
        }

        $sql = "SELECT u.email, u.business_name, p.card_type, p.card_exp_date, p.card_cvv, p.card_last4
                FROM payment_info p
                INNER JOIN users u
                ON u.id = p.user_id
                WHERE p.authorized = 0 AND SHA1(CONCAT(p.user_id, p.card_type, p.card_cvv, p.card_last4)) = ?";
        $result = DB::select($sql, [$hash]);

        // Not valid result => Error
        if (!$result) {
            return view('admin.resellers.authorize', ["error" => true]);
        }

        // No rows returned => Error
        if (count($result) == 0) {
            return view('admin.resellers.authorize', ["error" => true]);
        }

        // Approval request?
        if ($approve == "approve") {
            $affected = DB::update("UPDATE payment_info p
                                    SET p.authorized = 1 
                                    WHERE SHA1(CONCAT(p.user_id, p.card_type, p.card_cvv, p.card_last4)) = ?", [$hash]);
            if ($affected !== 1) $approve = "error";
        }

        // Everything fine
        $email = $result[0]->email;
        $business_name = $result[0]->business_name;
        $card_type = $result[0]->card_type;
        $card_exp_date = $result[0]->card_exp_date;
        $card_cvv = $result[0]->card_cvv;
        $card_last4 = $result[0]->card_last4;
        $card_summary = "$card_type **** $card_last4 (exp $card_exp_date, cvv $card_cvv)";
        return view('admin.resellers.authorize', compact('approve', 'hash','email','business_name','card_summary'));
    }

    function canIsee(User $me, $objectId) {
        $validObj   = $objectId != 0 && $me->id != $objectId;
        $notAdmin   = $me->roles[0]->name != 'administrator';
        $permission = $this->checkPermission($me, $objectId);
        
        // Resellers: Check for user's agreement acceptance
        if (!$this->checkResellerAgreement($me)) {
            return response()->view('admin.agreement');
        }
    
        if ($validObj && !$permission && $notAdmin) {
            return response()->view('admin.forbidden');
        }
    }
    
    
    function checkPermission(User $me, int $objectId) {
        $users =  DB::select("SELECT distinct
                                    stores.*
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                WHERE (stores.id = $objectId OR storegroups.id = $objectId OR resellers.id = $objectId) 
                                   AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)");
        
        return isset($users[0]);
    }
    

    /**
     *  Filter Users
     *  
     * @author carlosferreira
     *  updated 05/13/2018
     *  $filterRole = The role to show. // 1 = admin, 2 = reseller, 3 = storegroup, 4 = store
     *  $parentId   = The Parent User filtered. Even if the actual user is an admin, this can be something.
     */
    public function filterUsers(Request $request = null, int $filterRole, $parentId = null, $all = false, $ignorePaginator = false) {
        
        $me = Auth::user();
        
        $whereRole = (isset($filterRole) && $filterRole != 0) ? "users_roles.role_id = $filterRole" : "users_roles.role_id != 0" ;
        
        $filter = isset($request->filter) ? $request->filter : false;
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if ($me->roles[0]->name == 'administrator' and !$filter) {
            $whereParentId = "";
            
        } else if (isset($parentId) and $parentId != 0) {
            $whereParentId = "AND (stores.parent_id = $parentId OR storegroups.parent_id = $parentId OR resellers.parent_id = $parentId)";
        }
        
        $whereSearch = "";
        $roles = array(2, 3, 4);
        if(!empty($request->search) && contains($filterRole, $roles)) {
            $search = str_replace("\'", "'", $request->search);
            $search = str_replace("'", "\'", $search);
            $whereSearch = "AND ( UPPER(stores.business_name) LIKE UPPER('%$search%') OR UPPER(stores.email) LIKE UPPER('%$search%') )";
        }
        
        // Applications
        $selectsApps    = "";
        $joinApps       = "";
        if($filterRole == 4) { // 1 = admin, 2 = reseller, 3 = storegroup, 4 = store
            $selectsApps    = " , apps.name as app_name ";
            $joinApps = "LEFT JOIN store_app ON store_app.store_guid = stores.store_guid
                            LEFT JOIN apps ON apps.guid = store_app.app_guid ";
        }
        
        // Environments
        $selectsEnvs    = "";
        $joinEnvs       = "";
        if($filterRole == 4) {
            $selectsEnvs    = " , environments.name as env_name ";
            $joinEnvs       = "LEFT JOIN store_environment AS store_env ON store_env.store_guid = stores.store_guid
                                LEFT JOIN environments ON environments.guid = store_env.environment_guid";
        }
        
        $orderBy = "";
        if(isset($_GET['sort'])) {
            $direction =  isset($_GET['order']) ? $_GET['order'] : ( isset($_GET['direction']) ? $_GET['direction'] : "ASC" );
            $orderBy   = "ORDER BY " . $_GET['sort'] . " " . $direction;
        }
        
        $users =  DB::select("SELECT distinct

                                    stores.*,
                                    users_roles.role_id
                                    $selectsApps
                                    $selectsEnvs

                                FROM users AS stores 
                                
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)

                                INNER JOIN users_roles ON users_roles.user_id = stores.id

                                $joinApps
                                $joinEnvs

                                WHERE (stores.deleted_at IS NULL OR stores.deleted_at = '') AND $whereRole $whereParentId $whereSearch

                                $orderBy");
        
        if ($request != null && !$ignorePaginator) {
            $amount = $all ? 1000 : 10;
            $users = $this->arrayPaginator($users, $request, $amount);
        }
        
        return $users;
    }
    
    
    public function getDevicesCount(bool $deleted = false) {
        
        $me = Auth::user();

        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
            
        } else if ($me->roles[0]->name == 'store') {
            $whereParentId = "AND (stores.id = $me->id)";
        }
        
        $whereDeleted = $deleted ? "AND is_deleted = 1" : "AND is_deleted = 0";
        
        $devices =  DB::select("SELECT count(devices.guid) AS count
                                FROM users AS stores
                                JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                JOIN devices ON devices.store_guid = stores.store_guid
                                JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereDeleted $whereParentId");
        
        return $devices[0]->count;
    }
    
    
    public function getActiveInactiveLicenses() {
        
        $me = Auth::user();
        
        $whereParentId = "AND (stores.parent_id = $me->id OR storegroups.parent_id = $me->id OR resellers.parent_id = $me->id)";
        
        if ($me->roles[0]->name == 'administrator') {
            $whereParentId = "";
            
        } else if ($me->roles[0]->name == 'store') {
            $whereParentId = "AND (stores.id = $me->id)";
        }
        
        $licensesActive =  DB::select("SELECT 
                                    sum(case when devices.split_screen_parent_device_id = 0 then 1 else 0 end) AS active
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN devices ON devices.store_guid = stores.store_guid
                                INNER JOIN settings ON settings.store_guid = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereParentId and devices.is_deleted <> 1");
        
        $licensesQuantity =  DB::select("SELECT 
                                	   sum(settings.licenses_quantity) AS quantity
                                FROM users AS stores
                                LEFT JOIN users AS storegroups ON (storegroups.id = stores.parent_id)
                                LEFT JOIN users AS resellers ON (resellers.id = storegroups.parent_id)
                                INNER JOIN settings ON settings.store_guid = stores.store_guid
                                INNER JOIN users_roles ON users_roles.user_id = stores.id
                                WHERE users_roles.role_id = 4 $whereParentId");
        
        $active = 0;
        if (isset($licensesActive[0]->active)) {
            $active = $licensesActive[0]->active;
        }
        
        $quantity = 0;
        if (isset($licensesQuantity[0]->quantity)) {
            $quantity = $licensesQuantity[0]->quantity;
        }
        
        $inactive = $quantity - $active;
        
        $data = [
            'active'    => $active, 
            'inactive'  => $inactive
        ];
        
        return $data;
    }
    
    
    public function arrayPaginator($array, $request, $perPage)
    {
        $page = Input::get('page', 1);
        $offset = ($page * $perPage) - $perPage;
        
        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
    
    
    public function getBasePlans() {
        
        $me = Auth::user();
        
        $plansXObjects = PlanXObject::where('user_id', '=', $me->id)->get();
        $guids = [];
        foreach($plansXObjects as $planXObject) {
            array_push($guids, $planXObject->plan_guid);
        }
        
        $plans = Plan::whereIn('guid', $guids);
        
        return $plans->where('delete_time', '=', 0)->orderBy('name')->get();
    }
    
    
    public function getMyPlanList() {
        
        $me = Auth::user();
        
        $adm = $me->hasRole('administrator');
        
        $plans = [];
        
        if($adm) {
            $plans = Plan::where(function ($query) use ($me) {
                $query->where('owner_id', '=', 0)->orWhere('owner_id', '=', $me->id);
            });
                
        } else {
            $plans = Plan::where('owner_id', '=', $me->id);
        }
        
        return $plans->where('delete_time', '=', 0)->orderBy('name')->get();
    }
    
    
    // Get System Apps
    public function getSystemApps() {
        $apps = DB::select("SELECT * FROM apps WHERE enable = 1 order by name");
        return isset($apps) ? $apps : [];
    }
    
    
    // Get Payment Type
    public function getPlanPaymentTypes() {
        $types = DB::select("SELECT * FROM payment_types WHERE status = 1 order by name");
        return isset($types) ? $types : [];
    }
    
    
    public function readableDatetime(int $datetime) {
        
        $timezone = isset(Auth::user()->timezone) ? Auth::user()->timezone : Vars::$timezoneDefault;
        
        $updateLast = new \DateTime();
        $updateLast = $updateLast->setTimezone(new \DateTimeZone($timezone));
        
        return $updateLast->setTimestamp($datetime)->format('D, d M Y H:i:s');
    }


    public function timezonesByCountry(Request $request) {
        if(empty($request->post('countryCode'))) {
            return [];
        }

        $countryCode = $request->post('countryCode');
        return DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);
    }

}







