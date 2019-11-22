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
use App\Models\Parameters;


class ExternalUserController extends BaseController
{
    public function resellerShowForm(Request $request, $hash) {
        $sql = "SELECT 
                    f.created_at,
                    u.id, u.email, u.dba, u.business_name, u.name, u.last_name, 
                    p.extended_support, p.onsite_training
                FROM forms_links f
                INNER JOIN users u
                ON f.user_id = u.id
                INNER JOIN payment_info p
                ON u.id = p.user_id
                WHERE f.link_hash = ?";
        $result = DB::select($sql, [$hash]);

        // Not valid result => Expired/Not available
        if (!$result) {
            return view('external.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // No rows returned => Expired/Not available
        if (count($result) == 0) {
            return view('external.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // Expired => Expired page
        if (time() - $result[0]->created_at > 48 * 3600 * 1000) {
            return view('external.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        $basic = $result[0];
        $id = $basic->id;

        $sql = "SELECT a.name AS app_name, pl.hardware, pl.payment_freq, pl.longevity_months, pl.cost
                FROM plans_x_objects pxo
                INNER JOIN plans pl
                ON pl.guid = pxo.plan_guid
                INNER JOIN apps a
                ON a.guid = pl.app
                WHERE pxo.user_id = ?";
        $plans = DB::select($sql, [$id]);

        // Not valid result => Expired/Not available
        if (!$plans) {
            return view('external.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // No rows returned => Expired/Not available
        if (count($plans) == 0) {
            return view('external.resellers.fill', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // Everything OK => Go to fill the forms
        return view('external.resellers.fill', compact('basic', 'plans', 'hash'));
    }

    public function resellerAgreementPDF(Request $request, $hash) {
        $reseller = $this->getReseller($hash);
        if (!$reseller) {
            return view('external.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        }

        $pdf = $this->createAgreement($reseller["id"]);
        if (!$pdf) {
            return view('external.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        } else { 
            return $pdf->output();
        }
    }

    public function resellerDisplayAgreement(Request $request, $hash) {
        return view('external.resellers.agreement', compact('hash'));
    }

    public function resellerUpdateInfo(Request $request, $hash) {
        // Get User Id
        $reseller = $this->getReseller($hash);

        // Not valid result => Expired/Not available
        if ($reseller === false) {
            return view('external.resellers.agreement', ["error" => "The page you are trying to access has expired or is not available."]);
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
                                            $request->company_country,
                                            $request->company_zipcode,
                                            $request->company_phone,
                                            $user_id]);

        // Add shipping contact information, if any
        if (isset($request->chk_shipping)) {
            // Make sure that (initially) there is only one SHIPPING/BILLING address
            DB::delete("DELETE FROM contact_info WHERE user_id = ? AND address_type = 'SHIPPING'", [$user_id]);
            $sql = "INSERT INTO contact_info(user_id, address_type, care_of, address_1, address_2, 
                                                city, state, country, zipcode, email, phone, updated_at, updated_by)
                    VALUES(?, 'SHIPPING', ?, ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), 0)";
            DB::insert($sql, [$user_id, 
                                $request->shipping_careof, 
                                $request->shipping_address1,
                                $request->shipping_address2,
                                $request->shipping_city,
                                $request->shipping_state,
                                $request->shipping_zipcode,
                                $request->shipping_country,
                                $request->shipping_email,
                                $request->shipping_phone]);
        }

        // Add billing contact information, if any
        if (isset($request->chk_billing)) {
            // Make sure that (initially) there is only one SHIPPING/BILLING address
            DB::delete("DELETE FROM contact_info WHERE user_id = ? AND address_type = 'BILLING'", [$user_id]);
            $sql = "INSERT INTO contact_info(user_id, address_type, care_of, address_1, address_2, 
                                                city, state, country, zipcode, email, phone, updated_at, updated_by)
                    VALUES(?, 'BILLING', ?, ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), 0)";
            DB::insert($sql, [$user_id, 
                                $request->billing_careof, 
                                $request->billing_address1,
                                $request->billing_address2,
                                $request->billing_city,
                                $request->billing_state,
                                $request->billing_country,
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
            return view('external.resellers.agreement', ["error" => "An error occurred while updating your data. Please try again later."]);
        }

        return redirect(route('resellers.show_agreement', [$hash]));
    }

    public function resellerAcceptAgreement(Request $request, $hash) {
        $reseller = $this->getResellerUser($hash);
        if (!$reseller) {
            return view('external.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        }
        
        if ($request->agree === "ok") {
            $now = time();

            // Save to database
            $sql = "INSERT INTO agreement_acceptance (email, ip, user_agent, accepted_at) VALUES(?, ?, ?, $now)";
            $affected_rows = DB::insert($sql, [$reseller->email, $request->server("REMOTE_ADDR"), $request->server("HTTP_USER_AGENT")]);

            if ($affected_rows <= 0) {
                return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            }

            // Create PDF, stamp the electronic signature and save as file
            $pdf = $this->createAgreement($reseller->id, $now);
            $filename = "./agreements/%HASH%.pdf";

            if (!$pdf->output($filename, "%HASH%")) {
                return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            } else {
                $data = [];
                $data["%HASH%"] = $hash;
                $data["%ID%"] = $reseller->id;
                $data["%NAME%"] = $reseller->name;
                $data["%LAST_NAME%"] = $reseller->last_name;
                $data["%EMAIL%"] = $reseller->email;
                $data["%BUSINESS_NAME%"] = $reseller->business_name;
                $data["%DBA%"] = strlen($reseller->dba) > 0 ? " dba " . $reseller->dba : "";
                $data["%PHONE%"] = $reseller->phone_number;
                $data["%ADDRESS%"] = $reseller->address;
                $data["%ADDRESS_2%"] = $reseller->address2;
                $data["%CITY%"] = $reseller->city;
                $data["%STATE%"] = $reseller->state;
                $data["%COUNTRY%"] = $reseller->country;
                $data["%ZIPCODE%"] = $reseller->zipcode;

                if (!$this->sendEmailToCustomerService($data)) {
                    return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
                }
                return view('external.resellers.agreement', ["accepted" => true]);
            }
        } else {
            return view('external.resellers.agreement', ["error" => "We're sorry, but you need to accept the agreement to use our services."]);
        }
    }


    //
    // Support functions
    //
    
    private function sendEmailToCustomerService($data) {
        $to = Parameters::getValue("@reseller_form_email_customer_support", "");
        if ($to == "") {
            return false;
        }
        
        $headers = "From: " . Parameters::getValue("@reseller_link_email_from", "system@kdsgo.com") . "\r\n";
        $headers .= "Reply-To: " . Parameters::getValue("@reseller_link_email_reply_to", "do-not-reply@kdsgo.com") . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $subject = Parameters::getValue("@reseller_form_email_customer_support_subject", "KitchenGo: Action needed");
        $message = file_get_contents(Parameters::getValue("@reseller_form_email_customer_support_body_html_file", 
                                                          "assets/includes/email_approve_reseller.html"));
        
        $data["%URL%"] = url(Parameters::getValue("@reseller_form_email_customer_link_authorize_prepend", "authorize")) . "/" . $data["%HASH%"];

        $this->replaceParamData($subject, $data);
        $this->replaceParamData($message, $data);

        return mail($to, $subject, $message, $headers);
    }

    // data: key will be replaced by its value. e.g. ["%URL%" => "www..."]
    function replaceParamData(&$string, $data) {
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $string = str_replace($key, $value, $string);
            }
        }
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
            $company["state"] = $user->state;
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
        if (!$pdf->setFile("./models/reseller_subscription_agreement.pdf")) {
            return false;
        }
        
        // 1. SUBSCRIPTION: Price agreement
        $payment_frequency = "";
        $longevity_months = 0;
        foreach ($reseller_prices as $plan) {
            $longevity_months = $plan->longevity_months;
            switch ($plan->payment_freq) {
                case "ONE-TIME":
                    $payment_frequency = "";
                    break;

                case "YEARLY":
                    $payment_frequency = "/Year";
                    break;

                case "MONTHLY":
                    $payment_frequency = "/Month";
                    break;

                default:
                    $payment_frequency = "";
                    break;
            }

            $price = "$" . number_format($plan->cost, 2) . "/Station" . $payment_frequency;

            switch ($plan->app . "-" . $plan->hardware) {
                // Allee
                case "0fbaafa7-7194-4ce7-b45d-3ffc69b2486f-0":
                    $pdf->box(1, 130, 44.5, 50, 3.2, $color_white);
                    $pdf->writeAt(1, 130, 44.2 + 2, $price, $fsNormal);
                    break;

                // Premium
                case "bc68f95c-1af5-47b1-a76b-e469f151ec3f-0":
                    $pdf->box(1, 130, 48.7, 50, 3.8, $color_white);
                    $pdf->writeAt(1, 130, 49.2 + 2, $price, $fsNormal);
                    break;
                
                // Premium + Hardware
                case "bc68f95c-1af5-47b1-a76b-e469f151ec3f-1":
                    $pdf->box(1, 70, 48.7, 42, 3.8, $color_white);
                    $pdf->writeAt(1, 70, 49.2 + 2, $price, $fsNormal);
                    break;
            }
        }
        
        // 1. SUBSCRIPTION: Extended Support Agreement
        $extendedSupportAgreementPrice = number_format(Parameters::getValue("@reseller_external_support_price", 10), 2);
        $pdf->box(1, 53.5, 59.2, 100, 3.5, $color_white);
        $pdf->writeAt(1, 53.9, 59.2 + 1.9, "Extended Support Package - Extra US$" . $extendedSupportAgreementPrice . "/Month", $fsNormal);
        if ($payment->extended_support) {
            $pdf->box(1, 40, 59.7, 3, 3, $color_black);
        }
        
        // 1. SUBSCRIPTION: On site training
        if ($payment->onsite_training) {
            $pdf->box(1, 40, 63.7, 3, 3, $color_black);
        }

        // 1. SUBSCRIPTION: Length of Agreement
        $pdf->writeAt(1, 38, 70.4, $longevity_months, $fsNormal);

        // 2. SITE INFORMATION
        $userFullName = $user->name . " " . $user->last_name;
        $pdf->writeAt(1, 58, 145 + 0.5, "BN" . $user->business_name, $fsNormal);
        $pdf->writeAt(1, 58, 152 + 0.5, $company["address1"], $fsNormal);
        $pdf->writeAt(1, 58, 157 + 0.5, $company["address2"], $fsNormal);
        $pdf->writeAt(1, 58, 161.5 + 1, $company["city"], $fsNormal);
        $pdf->writeAt(1, 119, 161.5 + 1, $company["state"], $fsNormal);
        $pdf->writeAt(1, 153, 161.5 + 1, $company["zipcode"], $fsNormal);
        $pdf->writeAt(1, 58, 167 + 0.5, $userFullName, $fsNormal);
        $pdf->writeAt(1, 58, 172 + 0.5, $company["email"], $fsNormal);
        $pdf->writeAt(1, 58, 177 + 0.5, $company["phone"], $fsNormal);

        // 2. SITE INFORMATION: Shipping
        if (array_key_exists("shipping", $contact_info)) {
            $pdf->writeAt(1, 58, 187 + 1, $contact_info["shipping"]->address_1, $fsNormal);
            $pdf->writeAt(1, 58, 192 + 1, $contact_info["shipping"]->address_2, $fsNormal);
            $pdf->writeAt(1, 58, 197.5 + 1, $contact_info["shipping"]->city, $fsNormal);
            $pdf->writeAt(1, 119, 197.5 + 1, $contact_info["shipping"]->state, $fsNormal);
            $pdf->writeAt(1, 154, 197.5 + 1, $contact_info["shipping"]->zipcode, $fsNormal);
        }

        // 2. SITE INFORMATION: Bill-To Address
        if (array_key_exists("billing", $contact_info)) {
            $pdf->writeAt(1, 58, 223 + 1, $user->business_name, $fsNormal);
            $pdf->writeAt(1, 58, 230 + 1, $contact_info["billing"]->address_1, $fsNormal);
            $pdf->writeAt(1, 58, 235 + 1, $contact_info["billing"]->address_2, $fsNormal);
            $pdf->writeAt(1, 58, 239.8 + 1, $contact_info["billing"]->city, $fsNormal);
            $pdf->writeAt(1, 119.5, 239.8 + 1, $contact_info["billing"]->state, $fsNormal);
            $pdf->writeAt(1, 154, 239.8 + 1, $contact_info["billing"]->zipcode, $fsNormal);
            $pdf->writeAt(1, 58, 245 + 1, $contact_info["billing"]->care_of, $fsNormal);
            $pdf->writeAt(1, 58, 250 + 1, $contact_info["billing"]->email, $fsNormal);
            $pdf->writeAt(1, 58, 255 + 1, $contact_info["billing"]->phone, $fsNormal);
        }

        // 3. CREDIT CARD - Brand
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

        // 3. CREDIT CARD - Credit Card information
        $pdf->writeAt(2, 45, 59, $payment->card_exp_date, $fsNormal); // Exp. Date
        $pdf->writeAt(2, 115, 59, $payment->card_cvv, $fsNormal); // CVV
        $pdf->writeAt(2, 89, 67, "**** **** **** " . $payment->card_last4, $fsNormal); // Last 4 digits
        
        // 3. CREDIT CARD - Frequency
        if ($payment_frequency == "ONE-TIME") {
            $pdf->box(2, 87, 73, 3, 3, $color_black);
        } else {
            $pdf->box(2, 151, 73, 3, 3, $color_black);
        }

        // 3. CREDIT CARD - Billing Address
        if (array_key_exists("billing", $contact_info)) {
            $pdf->writeAt(2, 26, 104 + 8, $contact_info["billing"]->address_1 . " " . $contact_info["billing"]->address_2, $fsNormal);
            $pdf->writeAt(2, 35, 110.5 + 6.8, $contact_info["billing"]->city, $fsNormal);
            $pdf->writeAt(2, 110, 110.5 + 6.8, $contact_info["billing"]->state, $fsNormal);
            $pdf->writeAt(2, 153, 110.5 + 6.8, $contact_info["billing"]->zipcode, $fsNormal);
        }

        // 3. CREDIT CARD - Name, Date and Electronic Signature box
        $pdf->writeAt(2, 30, 120 + 8, $userFullName, $fsNormal);
        $pdf->writeAt(2, 142, 142 + 7.5, date("d F Y"), $fsNormal);
        $pdf->box(2, 30, 149, 60, 8, [150, 200, 255], 0.2, [0, 0, 0]);
        $pdf->writeAt(2, 33, 153, ($signature_timestamp > 0 ? "SIGNED ELECTRONICALLY" : "TO BE SIGNED ELECTRONICALLY"), $fsSmall);

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
        $pdf->writeAt(9, 123, 172, $company["email"], $fsSmall); 

        // AGREEMENT page 8
        $pdf->writeAt(10, 27.5, 150, $user->business_name, $fsNormal);
        $pdf->writeAt(10, 37, 168, $userFullName, $fsNormal);
        $pdf->writeAt(10, 20, 178.5, date("d F Y"), $fsNormal);
        $pdf->writeAt(10, 120, 178.5, date("d F Y"), $fsNormal);

        $title = $signature_timestamp > 0 ? "DOCUMENT SIGNED ELECTRONICALLY" : "";
        $pdf->stampElectronicSignature($id, $signature_timestamp, 10, 8, 200, $title);

        return $pdf;
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
        $result = DB::select("SELECT * FROM $mainDB.users u WHERE u.id = ?", [$id]);
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
        $result = DB::select("SELECT * FROM $mainDB.contact_info WHERE user_id = ?", [$id]);
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

        $sql = "SELECT app, hardware, payment_freq, longevity_months, cost
                FROM plans p
                INNER JOIN plans_x_objects pxo
                ON p.guid = pxo.plan_guid
                WHERE pxo.user_id = ?";

        $result = DB::select($sql, [$id]);

        if (!$result) {
            return false;
        }

        if (count($result) == 0) {
            return false;
        }

        return $result;
    }

    public function getReseller($hash) {
        $sql = "SELECT u.id, u.email 
                FROM forms_links f 
                INNER JOIN users u 
                ON f.user_id = u.id 
                WHERE link_hash = ?";
        $result = DB::select($sql, [$hash]);
        if (!$result) return false;
        return ["id" => $result[0]->id, "email" => $result[0]->email];
    }

    public function getResellerUser($hash) {
        $sql = "SELECT u.* 
                FROM users u 
                INNER JOIN forms_links f 
                ON f.user_id = u.id 
                WHERE link_hash = ?";
        $result = DB::select($sql, [$hash]);
        if (!$result) return false;
        return $result[0];
    }
}







