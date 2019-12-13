<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\PDFWriter\PDFWriter;
use App\Models\Parameters;


class ExternalUserController extends BaseController
{
    /**
     * Display a pre-filled form to reseller to fill out
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return view (webpage)
     */ 
    public function resellerShowForm(Request $request, $hash) {
        // If user has already agreed on the reseller's agreement, the page should not be displayed
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



    /**
     * Display the reseller's agreement PDF filled out
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return PDF
     */ 
    public function resellerAgreementPDF(Request $request, $hash) {
        $reseller = $this->getResellerUser($hash);
        if (!$reseller) {
            return view('external.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        }

        $pdf = $this->createAgreement($reseller->id);
        if (!$pdf) {
            return view('external.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        } else { 
            return $pdf->output();
        }
    }



    /**
     * Display the reseller's agreement page (PDF + Agreement checkbox)
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return PDF
     */ 
    public function resellerDisplayAgreement(Request $request, $hash) {
        return view('external.resellers.agreement', compact('hash'));
    }



    /**
     * Updates the reseller information from the filled-out form
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return view (webpage) - Either showing error or the Agreement
     */ 
    public function resellerUpdateInfo(Request $request, $hash) {
        // Get User Id
        $reseller = $this->getResellerUser($hash);
        
        // Not valid result => Expired/Not available
        if ($reseller === false) {
            return view('external.resellers.agreement', ["error" => "The page you are trying to access has expired or is not available."]);
        }
        $user_id = $reseller->id;

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

        $updated_rows = DB::update($sql, [$request->business_name,
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

        // Could not update
        if ($updated_rows === 0) {
            return view('external.resellers.agreement', ["error" => "An error occurred while saving your data."]);
        }

        // Add shipping contact information, if any
        if (!isset($request->chk_shipping)) {
            // Make sure that (initially) there is only one SHIPPING/BILLING address
            DB::delete("DELETE FROM contact_info WHERE user_id = ? AND address_type = 'SHIPPING'", [$user_id]);
            $sql = "INSERT INTO contact_info(user_id, address_type, care_of, address_1, address_2, 
                                                city, state, country, zipcode, email, phone, updated_at, updated_by)
                    VALUES(?, 'SHIPPING', ?, ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), ?)";
            DB::insert($sql, [$user_id, 
                                $request->shipping_careof, 
                                $request->shipping_address1,
                                $request->shipping_address2,
                                $request->shipping_city,
                                $request->shipping_state,
                                $request->shipping_country,
                                $request->shipping_zipcode,
                                $request->shipping_email,
                                $request->shipping_phone,
                                $user_id]);
        }

        // Add billing contact information, if any
        if (!isset($request->chk_billing)) {
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
        $sql = "UPDATE payment_info
                SET card_type = ?, card_exp_date = ?, card_cvv = ?, card_last4 = ?, authorized = 0 
                WHERE user_id = ?";
        $added_rows = DB::insert($sql, [$request->card_brand, 
                                        $request->card_expiration_month . "/" . $request->card_expiration_year,
                                        $request->card_cvv,
                                        $request->card_last4,
                                        $user_id]);

        if ($added_rows === 0) {
            return view('external.resellers.agreement', ["error" => "An error occurred while updating your data. Please try again later."]);
        }

        return redirect(route('resellers.show_agreement', [$hash]));
    }



    /**
     * Updates reseller's agreement and save PDF
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return view (webpage) with an OK or any Error explaining what might have gone wrong
     */ 
    public function resellerAcceptAgreement(Request $request, $hash) {
        $reseller = $this->getResellerUser($hash);
        if (!$reseller) {
            return view('external.resellers.agreement', ["error" => "An error occurred while creating your agreement. Please try again later."]);
        }
        
        if ($request->agree === "ok") {
            // Is the user just reloading the page (1h limit)?
            $already_accepted = DB::select("SELECT ip, accepted_at FROM agreement_acceptance WHERE email = ? ORDER BY accepted_at DESC", [$reseller->email]);
            if ($already_accepted) {
                if (count($already_accepted) > 0) {
                    if (time() - $already_accepted[0]->accepted_at < 3600 * 1000) {
                        $page = 10;
                        $ip = $already_accepted[0]->ip;
                        $time = $already_accepted[0]->accepted_at;
                        $sig = sprintf("%08d", $reseller->id) . sprintf("%04d", $page) . md5($reseller->id . $ip . $page . $time);
                        if (file_exists("./agreements/$sig.pdf")) {
                            return view('external.resellers.agreement', ["accepted" => true, "sig" => $sig]);
                        }
                    }
                }
            }         
            
            // Or is it the first time accepting the agreement?
            $now = time();

            // Save to database
            $sql = "INSERT INTO agreement_acceptance (email, ip, user_agent, accepted_at) VALUES(?, ?, ?, $now)";
            $affected_rows = DB::insert($sql, [$reseller->email, $request->server("REMOTE_ADDR"), $request->server("HTTP_USER_AGENT")]);

            if ($affected_rows <= 0) {
                return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            }

            // Create PDF, stamp the electronic signature and save as file
            $pdf = $this->createAgreement($reseller->id, $now);
            $filename = "./agreements/%AGREEMENT%.pdf";
            $page = 10;
            $ip = $_SERVER["REMOTE_ADDR"];
            $sig = sprintf("%08d", $reseller->id) . sprintf("%04d", $page) . md5($reseller->id . $ip . $page . $now);

            if ($pdf === false) {
                return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            }

            if (!$pdf->output($filename, "%AGREEMENT%")) {
                return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
            } else {
                $data = [];

                // For security purposes (clients not trying to guess the authorization address and approve by theirselves)
                // a new hash is created, based on credit card information. This is the hash sent to Customer Support for
                // approval.
                $result = DB::select("SELECT SHA1(CONCAT(p.user_id, p.card_type, p.card_cvv, p.card_last4)) AS hash
                                        FROM payment_info AS p
                                        WHERE p.user_id = ?", [$reseller->id]);
                if (!$result) {
                    return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
                }

                if (count($result) == 0) {
                    return view('external.resellers.agreement', ["error" => "An error occurred while saving your agreement. Please try again later."]);
                }
                
                $data["%HASH%"] = $result[0]->hash;;
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
                return view('external.resellers.agreement', ["accepted" => true, "sig" => $sig]);
            }
        } else {
            return view('external.resellers.agreement', ["error" => "We're sorry, but you need to accept the agreement to use our services."]);
        }
    }



    /**
     * Used by customer support to approve the payment type after authorizing the credit card's
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * @param $approve  If not set, it means to show reseller's information before approval
     *                  If set, it means that customer support has approved (update database and send email)
     * 
     * @return view (webpage)
     */ 
    public function approvePaymentType(Request $request, $hash, $approve = "") {
        // No hash => Error
        if (!isset($hash)) {
            return view('admin.resellers.authorize', ["error" => true]);
        }
        $sql = "SELECT u.email, u.business_name, u.name, u.last_name, u.dba, f.link_hash, 
                                            p.card_type, p.card_exp_date, p.card_cvv, p.card_last4
                FROM payment_info p
                INNER JOIN users u
                ON u.id = p.user_id
                INNER JOIN forms_links f
                ON f.user_id = p.user_id
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
        // Result is OK
        $email = $result[0]->email;
        $business_name = $result[0]->business_name;
        $card_type = $result[0]->card_type;
        $card_exp_date = $result[0]->card_exp_date;
        $card_cvv = $result[0]->card_cvv;
        $card_last4 = $result[0]->card_last4;
        $card_summary = "$card_type **** $card_last4 (exp $card_exp_date, cvv $card_cvv)";

        // Approval request?
        if ($approve == "approve") {
            $affected = DB::update("UPDATE payment_info p
                                    SET p.authorized = 1 
                                    WHERE SHA1(CONCAT(p.user_id, p.card_type, p.card_cvv, p.card_last4)) = ?", [$hash]);
            if ($affected !== 1) {
                $approve = "error";
            } else {
                $data["%EMAIL%"] = $email;
                $data["%BUSINESS_NAME%"] = $business_name;
                $data["%NAME%"] = $result[0]->name;
                $data["%FIRST_NAME%"] = $result[0]->name;
                $data["%LAST_NAME%"] = $result[0]->last_name;
                $data["%DBA%"] = (strlen($result[0]->dba) > 0 ? " dba " . $result[0]->dba : "");
                $data["%HASH%"] = $result[0]->link_hash;

                $user_agreement_info = DB::select("SELECT u.id, aa.ip, aa.accepted_at 
                                                    FROM kdsweb.agreement_acceptance aa
                                                    INNER JOIN users u
                                                    ON u.email = aa.email
                                                    WHERE aa.email = ? 
                                                    ORDER BY aa.accepted_at DESC 
                                                    LIMIT 1", [$email]);
                if (!$user_agreement_info) {
                    return view('admin.resellers.authorize', ["error" => true]);
                }
                if (count($user_agreement_info) == 0) {
                    return view('admin.resellers.authorize', ["error" => true]);
                }

                $page = 10;
                $sig = sprintf("%08d", $user_agreement_info[0]->id) . sprintf("%04d", $page) . 
                            md5($user_agreement_info[0]->id . $user_agreement_info[0]->ip . $page . $user_agreement_info[0]->accepted_at);
                $data["%SIGNATURE_HASH%"] = $sig;
                $data["%LINK_PDF%"] = URL::to("./agreements/$sig.pdf");
                
                $approve = $this->sendEmailToClientSetPasswordAndAccounting($email, $data);
            }
        }
        // Everything fine
        
        return view('admin.resellers.authorize', compact('approve', 'hash','email','business_name','card_summary'));
    }



    /**
     * Display a page where the resellers can set up their password
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return view (webpage)
     */ 
    public function resellerNewUser(Request $request, $hash) {
        // Get User Id (link valid for 14 days)
        $reseller = $this->getResellerUser($hash);

        // Not valid result => Expired/Not available
        if ($reseller === false) {
            return view('admin.resellers.newuser', ["error" => "The page you are trying to access has expired or is not available."]);
        }

        // Check if the user is authorized (credit card)
        $result = DB::select("SELECT COUNT(1) AS count
                                FROM payment_info 
                                WHERE user_id = ? 
                                AND authorized = 1", [$reseller->id]);
        if (!$result) {
            return view('admin.resellers.newuser', ["error" => "The page you are trying to access has expired or is not available."]);
        }
       
        if ($result[0]->count == 0) {
            return view('admin.resellers.newuser', ["error" => "Your card has not yet been authorized. Please try again later."]);
        } else {
            $email = $reseller->email;
            $fullname = $reseller->name . ' ' . $reseller->last_name;
            return view('admin.resellers.newuser', compact('hash', 'email', 'fullname'));
        } 
    }

    /**
     * Updates reseller's password and redirects to login (if successful) or display an error
     *
     * @param $request  HTTP Request
     * @param $hash     Link Hash (sent via email)
     * 
     * @return view (webpage)
     */ 
    public function resellerSetPassword(Request $request, $hash) {
        // Get User Id (link valid for 14 days)
        $reseller = $this->getResellerUser($hash);

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

        $affected_rows = DB::update($sql, [$password_hash, $reseller->id]);
        if ($affected_rows == 0) {
            return view('admin.resellers.newuser', ["error" => "An error occurred while setting your password. Please try again later."]);
        } else {
            return view('admin.resellers.newuser', ["success" => true]);
        }
    }



    //
    // Support functions
    //
    /**
     * Sends an e-mail to the reseller, after payment approval, asking to set up their password
     *
     * @param $email    Reseller's email
     * @param $data     Reseller's information in form of %variable_name%. E.g. $data["%BUSINESS_NAME%"]
     * 
     * @return boolean  Whether the mail was sent or not
     */ 
    private function sendEmailToClientSetPasswordAndAccounting($email, $data) {       
        $data["%URL%"] = url(Parameters::getValue("@reseller_set_password_email_link_prepend", "authorize")) . "/" . $data["%HASH%"];

        // E-mail to the accounting
        $this->sendEmail(Parameters::getValue("@email_system_from", "system@kdsgo.com"), 
                            Parameters::getValue("@email_system_reply_to", "do-not-reply@kdsgo.com"),
                            Parameters::getValue("@email_accounting", "do-not-reply@kdsgo.com"),
                            Parameters::getValue("@accounting_new_reseller_email_subject", "KitchenGo: New Reseller"),
                            Parameters::getValue("@accounting_new_reseller_email_body_html_file", 
                                                        "assets/includes/email_accounting_new_reseller.html"), 
                            $data);
        
        // E-mail to the client. Returns it to ensures that at least the client receives it
        return $this->sendEmail(Parameters::getValue("@email_system_from", "system@kdsgo.com"), 
                                Parameters::getValue("@email_system_reply_to", "do-not-reply@kdsgo.com"),
                                $email,
                                Parameters::getValue("@reseller_set_password_email_subject", "KitchenGo: Set your password"),
                                Parameters::getValue("@reseller_set_password_email_body_html_file", 
                                                          "assets/includes/email_set_password.html"), 
                                $data);
    }
    


    /**
     * Sends an e-mail to the customer support, after reseller fills out the form
     * 
     * @param $data     Reseller's information in form of %variable_name%. E.g. $data["%BUSINESS_NAME%"]
     * 
     * @return boolean  Whether the mail was sent or not
     */ 
    private function sendEmailToCustomerService($data) {
        $data["%URL%"] = url(Parameters::getValue("@reseller_form_email_customer_link_authorize_prepend", "authorize")) . "/" . $data["%HASH%"];
        return $this->sendEmail(Parameters::getValue("@email_system_from", "system@kdsgo.com"), 
                                Parameters::getValue("@email_system_reply_to", "do-not-reply@kdsgo.com"),
                                Parameters::getValue("@email_customer_support", ""),
                                Parameters::getValue("@reseller_form_email_customer_support_subject", "KitchenGo: Action needed"),
                                Parameters::getValue("@reseller_form_email_customer_support_body_html_file", 
                                                    "assets/includes/email_approve_reseller.html"), 
                                $data);
    }


    /**
     * Sends an e-mail
     * 
     * @param $from         From
     * @param $reply_to     Reply-to (optional)
     * @param $to           To
     * @param $subject      Subject
     * @param $body_file    HTML file path for e-mail's body
     * @param $data         Data to be replaced (optional)
     * 
     * @return boolean      Whether the mail was sent or not
     */ 
    private function sendEmail($from, $reply_to = "", $to, $subject, $body_file, $data) {
        if ($to == "") {
            return false;
        }
        
        $headers = "From: $from\r\n";
        if (strlen($reply_to) > 0) $headers .= "Reply-To: $reply_to\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        if (!file_exists($body_file)) return false;

        $message = file_get_contents($body_file);
        
        if (isset($data)) {
            if (count($data) > 0) {
                $this->replaceParamData($subject, $data);
                $this->replaceParamData($message, $data);
            }
        }

        return mail($to, $subject, $message, $headers);
    }


    /**
     * Support function to replace array keys inside a string to its array value.
     * E.g. $string = "Hello, %NAME%" 
     *      $data = ["%NAME%" => "world!"]
     * After calling the function:
     *      $string = "Hello, world!"
     *
     * @param $string   Pre-filled string
     * @param $data     Reseller's information in form of %variable_name%. E.g. $data["%BUSINESS_NAME%"]
     * 
     */ 
    function replaceParamData(&$string, $data) {
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $string = str_replace($key, $value, $string);
            }
        }
    }



    /**
     * Fills out the PDF with reseller's information
     * 
     * @param $id                   Reseller's user id
     * @param $signature_timestamp  If set, it will stamp the signature and save the file.
     *                              If not set, it will not create a file.
     * 
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
       
        if (($user === false) || ($payment === false) || ($reseller_prices === false) || ($contact_info === false)) {
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
        $pdf_page = 1;
        $pdf->box($pdf_page, 130, 45.4, 50, 3.2, $color_white);
        $pdf->box($pdf_page, 130, 50, 50, 3.8, $color_white);
        $pdf->box($pdf_page, 70, 50, 42, 3.8, $color_white);
        $pdf->writeAt($pdf_page, 130, 47.2, "N/A", $fsNormal);
        $pdf->writeAt($pdf_page, 130, 52.5, "N/A", $fsNormal);
        $pdf->writeAt($pdf_page, 79, 52.5, "N/A", $fsNormal);
        $payment_frequency = "";
        $longevity_months = 0;
        foreach ($reseller_prices as $plan) {
            $longevity_months = $plan->longevity_months;
            switch ($plan->payment_freq) {
                case "ONE-TIME":
                    $payment_frequency = "";
                    break;

                case "YEARLY":
                    $payment_frequency = "/Station/Year";
                    break;

                case "MONTHLY":
                    $payment_frequency = "/Station/Month";
                    break;

                default:
                    $payment_frequency = "";
                    break;
            }

            $price = "$" . number_format($plan->cost, 2) . $payment_frequency;
            
            switch ($plan->app . "-" . $plan->hardware) {
                // Allee
                case "0fbaafa7-7194-4ce7-b45d-3ffc69b2486f-0":
                    $pdf->box($pdf_page, 124, 45.5, 3.8, 3.8, $color_black);
                    $pdf->box($pdf_page, 130, 45.5, 50, 3.2, $color_white);
                    $pdf->writeAt($pdf_page, 130, 47.2, $price, $fsNormal);
                    break;

                // Premium
                case "bc68f95c-1af5-47b1-a76b-e469f151ec3f-0":
                    $pdf->box($pdf_page, 124, 50.5, 3.8, 3.8, $color_black);
                    $pdf->box($pdf_page, 130, 50.5, 50, 3.8, $color_white);
                    $pdf->writeAt($pdf_page, 130, 52, $price, $fsNormal);
                    break;
                
                // Premium + Hardware
                case "bc68f95c-1af5-47b1-a76b-e469f151ec3f-1":
                    $pdf->box($pdf_page, 64, 50.5, 3.8, 3.8, $color_black);
                    $pdf->box($pdf_page, 70, 50, 42, 3.8, $color_white);
                    $pdf->writeAt($pdf_page, 70, 52.5, $price, $fsNormal);
                    break;
            }
        }
        
        // 1. SUBSCRIPTION: Extended Support Agreement
        $extendedSupportAgreementPrice = number_format(Parameters::getValue("@reseller_external_support_price", 10), 2);
        $pdf->box($pdf_page, 53.5, 60.3, 100, 3.5, $color_white);
        $pdf->writeAt($pdf_page, 53.9, 61.7, "Extended Support Package - Extra US$" . $extendedSupportAgreementPrice . "/Month", $fsNormal);
        if ($payment->extended_support == 1) {
            $pdf->box($pdf_page, 40, 60.5, 3, 3, $color_black);
        }
        
        // 1. SUBSCRIPTION: On site training
        if ($payment->onsite_training) {
            $pdf->box($pdf_page, 40, 64.5, 3, 3, $color_black);
        }

        // 1. SUBSCRIPTION: Length of Agreement
        $pdf->writeAt($pdf_page, 38, 71.2, $longevity_months, $fsNormal);

        // 2. SITE INFORMATION
        $userFullName = $user->name . " " . $user->last_name;
        $pdf->writeAt($pdf_page, 58, 146, $user->business_name, $fsNormal);
        $pdf->writeAt($pdf_page, 58, 153.3, $company["address1"], $fsNormal);
        $pdf->writeAt($pdf_page, 58, 158.3,  $company["address2"], $fsNormal);
        $pdf->writeAt($pdf_page, 58, 163.8, $company["city"], $fsSmall);
        $pdf->writeAt($pdf_page, 118, 163.8, $company["state"], $fsSmall);
        $pdf->writeAt($pdf_page, 153, 163.8, $company["zipcode"], $fsSmall);
        $pdf->writeAt($pdf_page, 58, 168.5, $userFullName, $fsNormal);
        $pdf->writeAt($pdf_page, 58, 173.5, $company["email"], $fsNormal);
        $pdf->writeAt($pdf_page, 58, 179, $company["phone"], $fsNormal);

        // 2. SITE INFORMATION: Shipping
        if (array_key_exists("shipping", $contact_info)) {
            $pdf->writeAt($pdf_page, 58, 190, $contact_info["shipping"]->address_1, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 195, $contact_info["shipping"]->address_2, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 200, $contact_info["shipping"]->city, $fsNormal);
            $pdf->writeAt($pdf_page, 119, 200, $contact_info["shipping"]->state, $fsNormal);
            $pdf->writeAt($pdf_page, 154, 200, $contact_info["shipping"]->zipcode, $fsNormal);
        }

        // 2. SITE INFORMATION: Bill-To Address
        if (array_key_exists("billing", $contact_info)) {
            $pdf->writeAt($pdf_page, 58, 225, $user->business_name, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 232, $contact_info["billing"]->address_1, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 237, $contact_info["billing"]->address_2, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 242, $contact_info["billing"]->city, $fsNormal);
            $pdf->writeAt($pdf_page, 119.5, 242, $contact_info["billing"]->state, $fsNormal);
            $pdf->writeAt($pdf_page, 154, 242, $contact_info["billing"]->zipcode, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 247, $contact_info["billing"]->care_of, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 252, $contact_info["billing"]->email, $fsNormal);
            $pdf->writeAt($pdf_page, 58, 257, $contact_info["billing"]->phone, $fsNormal);
        }

        // 3. CREDIT CARD - Brand
        $pdf_page = 2;
        switch ($payment->card_type) {
            case "VISA":
                $pdf->box($pdf_page, 89, 50, 3, 3, $color_black); // Visa
                break;

            case "AMEX":
                $pdf->box($pdf_page, 113, 50, 3, 3, $color_black); // AmEx
                break;

            case "DISCOVER":
                $pdf->box($pdf_page, 144, 50, 3, 3, $color_black); // Discover
                break;

            default:
                $pdf->box($pdf_page, 51, 50, 3, 3, $color_black); // MasterCard
                break;
        }

        // 3. CREDIT CARD - Credit Card information
        $pdf->writeAt($pdf_page, 45, 59, $payment->card_exp_date, $fsNormal); // Exp. Date
        $pdf->writeAt($pdf_page, 115, 59, $payment->card_cvv, $fsNormal); // CVV
        $pdf->writeAt($pdf_page, 89, 67, "**** **** **** " . $payment->card_last4, $fsNormal); // Last 4 digits
        
        // 3. CREDIT CARD - Frequency
        if ($payment_frequency == "") {
            $pdf->box($pdf_page, 87, 73, 3, 3, $color_black);
        } else {
            $pdf->box($pdf_page, 152.5, 73, 3, 3, $color_black);
        }

        // 3. CREDIT CARD - Billing Address
        if (array_key_exists("billing", $contact_info)) {
            $pdf->writeAt($pdf_page, 26, 112, $contact_info["billing"]->address_1 . " " . $contact_info["billing"]->address_2, $fsNormal);
            $pdf->writeAt($pdf_page, 35, 118, $contact_info["billing"]->city, $fsNormal);
            $pdf->writeAt($pdf_page, 110, 118, $contact_info["billing"]->state, $fsNormal);
            $pdf->writeAt($pdf_page, 153, 118, $contact_info["billing"]->zipcode, $fsNormal);
        }

        // 3. CREDIT CARD - Name, Date and Electronic Signature box
        $pdf->writeAt($pdf_page, 30, 120 + 8, $userFullName, $fsNormal);
        $pdf->writeAt($pdf_page, 142, 142 + 7.5, date("d F Y"), $fsNormal);
        $pdf->box($pdf_page, 30, 149, 60, 8, [150, 200, 255], 0.2, [0, 0, 0]);
        $pdf->writeAt($pdf_page, 33, 153, ($signature_timestamp > 0 ? "SIGNED ELECTRONICALLY" : "TO BE SIGNED ELECTRONICALLY"), $fsSmall);

        // AGREEMENT page 1
        $pdf_page = 3;
        $fullAddress = $company["address1"] . " " . $company["address2"] . " " . $company["city"] . " " . 
                        $company["state"] . " " . $company["zipcode"];
        $fontSizeFullAddress = Min($fsSmall, $fsSmall * 56 / (strlen($fullAddress) + 1)); # Try to optimize address font size
        $pdf->writeAt($pdf_page, 106, 56.7, $user->business_name, $fsSmall);
        $pdf->writeAt($pdf_page, 48, 61, $fullAddress, $fontSizeFullAddress);

        // AGREEMENT page 7
        $pdf_page = 9;
        $pdf->writeAt($pdf_page, 110, 142, $user->business_name, $fsSmall);
        $pdf->writeAt($pdf_page, 110, 146.5, $company["address1"] . " " . $company["address2"], $fsSmall); 
        $pdf->writeAt($pdf_page, 110, 150.5, $company["city"] . " " . $company["state"], $fsSmall);
        $pdf->writeAt($pdf_page, 110, 155.5, $company["zipcode"], $fsSmall); 
        $pdf->writeAt($pdf_page, 117, 161, $company["phone"], $fsSmall); 
        $pdf->writeAt($pdf_page, 123, 167, $company["email"], $fsSmall); 

        // AGREEMENT page 8
        $pdf_page = 10;
        $pdf->writeAt($pdf_page, 28, 154, $user->business_name, $fsNormal);
        $pdf->writeAt($pdf_page, 37, 173, $userFullName, $fsNormal);
        $pdf->writeAt($pdf_page, 20, 183, date("d F Y"), $fsNormal);
        $pdf->writeAt($pdf_page, 120, 183, date("d F Y"), $fsNormal);

        // AGREEMENT page 10
        $pdf->box($pdf_page, 28, 160, 60, 8, [150, 200, 255], 0.2, [0, 0, 0]);
        $pdf->writeAt($pdf_page, 31, 164, ($signature_timestamp > 0 ? "SIGNED ELECTRONICALLY" : "TO BE SIGNED ELECTRONICALLY"), $fsSmall);
        $title = $signature_timestamp > 0 ? "DOCUMENT SIGNED ELECTRONICALLY" : "";
        $pdf->stampElectronicSignature($id, $signature_timestamp, $pdf_page, 8, 200, $title);

        return $pdf;
    }



    /**
     * Gets data from table payment_info
     * 
     * @param $id       Reseller's user id
     * 
     * @return Boolean FALSE if not found/error, or DB Result
     */ 
    private function getResellerPaymentInfo($id) {
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT * FROM $mainDB.payment_info WHERE user_id = ?", [$id]);
        if (!$result) {
            return false;
        } else {
            return $result[0];
        }
    }



    /**
     * Gets data from table users
     * 
     * @param $id       Reseller's user id
     * 
     * @return Boolean FALSE if not found/error, or DB Result
     */ 
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
     * Gets contact information from tables contact_info
     * 
     * @param $id       Reseller's user id
     * 
     * @return Array or Boolean FALSE if not found/error, or array with DB Result, address_type as key ["billing"], ["shipping"] and ["company"]
     */ 
    private function getResellerContactInfo($id) {
        $answer = [];
        $mainDB = env('DB_DATABASE', 'kdsweb');
        $result = DB::select("SELECT * FROM $mainDB.contact_info WHERE user_id = ?", [$id]);
        if ($result === false) {
            return false;
        } 
       
        foreach ($result as $row) {
            $answer[strtolower($row->address_type)] = $row;
        }
        return $answer;
    }



    /**
     * Gets reseller's price agreement (plans)
     * 
     * @param $id       Reseller's user id
     * 
     * @return Boolean FALSE if not found/error, or DB Result with the plans
     */ 
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



    /**
     * Gets reseller's user information
     * 
     * @param $hash       Reseller's hash to fill out form
     * 
     * @return Boolean FALSE if not found/error, or DB Result with the information
     */ 
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







