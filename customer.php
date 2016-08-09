<?php
    require 'header.php';

    if(!isset($_GET['id'])) {
        //echo "null";
        $business_name = "";
        $email = "";
        $fname = "";
        $lname = "";
        $chargifyID = "";
        $char_upd_at = "";
        $billing_sum = "";
        $sales_date = "";
        $sales_agent = "";
        $sales_center = "";
        $cust_search_state ="";
        $cc_last_four = "";
        $cc_exp_mm = "";
        $cc_exp_yy = "";
        $bill_address = "";
        $bill_city = "";
        $bill_state = "";
        $bill_zip = "";
        $bill_country = "";
    } else {
        //echo $_GET['id'];
        $i=0;
        while(isset($result_db_customers->rows[$i])) {
            if($result_db_customers->rows[$i]->value->chargify_id == $_GET['id']) {
                $customer_db_id = $result_db_customers->rows[$i]->value->_id;
                $business_name = $result_db_customers->rows[$i]->value->business_name;
                $email = $result_db_customers->rows[$i]->value->customer_email;
                $fname = $result_db_customers->rows[$i]->value->customer_first_name;
                $lname = $result_db_customers->rows[$i]->value->customer_last_name;
                $chargifyID = $result_db_customers->rows[$i]->value->chargify_id;
                $salutation = $result_db_customers->rows[$i]->value->customer_salutation;
                $title = $result_db_customers->rows[$i]->value->customer_title;
                $sales_date = $result_db_customers->rows[$i]->value->sale_date;
                $sales_agent = $result_db_customers->rows[$i]->value->sale_agent;
                $sales_center = $result_db_customers->rows[$i]->value->sale_center;
                $product_id = $result_db_customers->rows[$i]->value->product_id;
                $product_handle = $result_db_customers->rows[$i]->value->product_handle;
                $product_name = $result_db_customers->rows[$i]->value->product_name;
                $product_component_id = $result_db_customers->rows[$i]->value->product_component_id;
                $product_component_name = $result_db_customers->rows[$i]->value->product_component_name;
                $product_coupon_id = $result_db_customers->rows[$i]->value->product_coupon_id;
                $product_coupon_name = $result_db_customers->rows[$i]->value->product_coupon_name;
                $cc_last_four = "XXXX-XXXX-XXX-".$result_db_customers->rows[$i]->value->customer_card_last_four;
                $cc_exp_mm = $result_db_customers->rows[$i]->value->customer_card_expire_month;
                $cc_exp_yy = $result_db_customers->rows[$i]->value->customer_card_expire_year;
                $bill_address = $result_db_customers->rows[$i]->value->customer_billing_address;
                $bill_city = $result_db_customers->rows[$i]->value->customer_billing_city;
                $bill_state = $result_db_customers->rows[$i]->value->customer_billing_state;
                $bill_zip = $result_db_customers->rows[$i]->value->customer_billing_zip;
                $bill_country = "US";
            }
            $i++;
        }
        $test = true;
        $subscription = new ChargifySubscription(NULL, $test);

        try {
            $result_customer_id_search = $subscription->getByCustomerID($chargifyID);
        } catch (ChargifyValidationException $cve) {
            echo $cve->getMessage();
        }

        if($result_customer_id_search[0]->state == "trialing") {
            ?><style>
                .cust_id {
                    color: #b300b3;
                }
                </style><?php
            } elseif($result_customer_id_search[0]->state == "active") {
                ?><style>
                .cust_id {
                    color: #28B22C;
                }
                </style><?php
            } elseif($result_customer_id_search[0]->state == "past_due") {
                ?><style>
                .cust_id {
                    color: #e6e600;
                }
                </style><?php
            } elseif($result_customer_id_search[0]->state == "unpaid") {
                ?><style>
                .cust_id {
                    color: #ff0000;
                }
                </style><?php
            } elseif($result_customer_id_search[0]->state == "canceled") {
                ?><style>
                .cust_id {
                    color: #000000;
                }
                </style><?php
            } else {
                ?><style>
                .cust_id {
                    color: #cccccc;
                }
                </style><?php
            }

            $billing_sum = "$".number_format(($result_customer_id_search[0]->total_revenue_in_cents /100), 2, '.', ' ');
            $fin = explode('T',$result_customer_id_search[0]->updated_at,-1);
            $fin2 = explode('-',$fin[0]);
            $char_upd_at = $fin2[1].".".$fin2[2].".".$fin2[0];

            if($result_customer_id_search[0]->state == "trialing") {
                $cust_search_state = "Trial Ended: ".$result_customer_id_search[0]->trial_ended_at;
            } elseif($result_customer_id_search[0]->state == "active") {
                $cust_search_state = "Next Billing: ".$result_customer_id_search[0]->next_billing_at;
            } else {
                $cust_search_state = "Cancelled At: ".$result_customer_id_search[0]->canceled_at;
            }
    }

    if(isset($_POST['upd_acc'])) {
        $business_name = stripslashes($_POST['acc-b-name']);
        $prod = $_POST['acc-prod'];
        $salutation = $_POST['acc-salut'];
        $title = $_POST['acc-title'];
        $fname = $_POST['acc-fname'];
        $lname = $_POST['acc-lname'];

        $test = true;
        $customer = new ChargifyCustomer(NULL, $test);
        $upd_subscription = new ChargifySubscription(NULL, $test);

        $customer->id = $_GET['id'];
        $customer->organization = $business_name;
        $customer->first_name = $fname;
        $customer->last_name = $lname;

        if($prod == 'prod_001') {
            $prodID = 3881312;
            $prodName = "Basic Plan";
        } else if($prod == 'plan_002') {
            $prodID = 3881313;
            $prodName = "Start-up Plan";
        } else if($prod == 'plan_005') {
            $prodID = 3881318;
            $prodName = "Upgrade to Start-up Plan";
        } else if($prod == 'plan_003') {
            $prodID = 3881314;
            $prodName = "Business Plan";
        } else if($prod == 'plan_006') {
            $prodID = 3881319;
            $prodName = "Upgrade to Business Plan";
        } else if($prod == 'plan_004') {
            $prodID = 3881316;
            $prodName = "Enterprise Plan";
        } else {
            $prodID = 3881320;
            $prodName = "Upgrade to Enterprise Plan";
        }  


        $upd_subscription->id = @$result_customer_id_search[0]->id; //chargify subscriptionID
        $sub_prod = new stdClass();
        $sub_prod->handle = @$prod;
        $sub_prod->id = @$prodID;
        $upd_subscription->product = $sub_prod;

        try {
            $result_upd_cus = $customer->update();
            $result_upd_sub = $upd_subscription->updateProduct();

            $client_customer = new couchClient ('http://127.0.0.1:5984','bigloco-customers');

            try {
                $doc = $client_customer->getDoc($customer_db_id);
            } catch (Exception $e) {
                echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
            }

            $doc->business_name = @$business_name;
            $doc->customer_salutation = @$salutation;
            $doc->customer_title = @$title;
            $doc->customer_first_name = @$fname;
            $doc->customer_last_name = @$lname;
            $doc->product_id = @$prodID;
            $doc->product_handle = @$prod;
            $doc->product_name = @$prodName;

            try {
                $client_customer->storeDoc($doc);
                $product_handle = $prod;
                $product_name = $prodName;
            } catch (Exception $e) {
                echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
            }

        } catch (ChargifyValidationException $cve) {
            echo $cve->getMessage();
        }
    }
?>
<link rel="stylesheet" type="text/css" href="js/field_trappings/error_msg.css"/>
    <style>
        .error_head {
            color: #e60000;
            font-size: 30px;
            margin-bottom: -5px;
        }
        .error_field {
            border:1px solid #ff4d4d;
            box-shadow: 0 0 5px #ff4d4d;
        }
    </style>

    <div class="row">
        <ul class="navtabs nav nav-pills nav-justified">
            <li id="cust_tab1" class="alter_tab active" onclick="cust_onNavTab1()"><a href=#>Summary</a></li>
            <li id="cust_tab2"><a href="#" onclick="cust_onNavTab2()">Provisioning</a></li>
            <li id="cust_tab3" class="alter_tab" onclick="cust_onNavTab3()"><a href="#">Billing</a></li>
            <li id="cust_tab4"><a href="#" onclick="cust_onNavTab4()">Support</a></li>
            <li id="cust_tab5" class="alter_tab" onclick="cust_onNavTab5()"><a href="#">Dashboard</a></li>
            <li id="cust_tab6"><a href="#" onclick="cust_onNavTab6()">Admin</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-6">
        <span class="hido" id="hido1"><p id="error1" class="error_head"></p></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p class="cust_id" id="cust_id" title=""><?php echo $chargifyID; ?></p>
            <p class="bill_sum"><?php echo $billing_sum; ?></p>
            <p class="last_activity"><?php echo $char_upd_at; ?></p>
        </div>
    </div>

    <!-- Customer's Account Info -->
    <form  action="" method="POST" id="cust_account_form" onsubmit="return checkFields_cust_tab1();">
        <div class="row">
            <input type="text" value="<?php echo $chargifyID; ?>" id="cID" hidden>
            <div class="col-md-6">
                <input type="text" name="acc-b-name" id="acc-b-name" class="form-control" placeholder="Business Name" value="<?php echo $business_name; ?>" onchange="BName()">
            </div>
            <div class="col-md-5">
                <select class="form-control" name="acc-prod" id="acc-prod" placeholder="Product">
                <?php if(isset($_GET['id'])) { ?>
                    <optgroup label="Current">
                    <?php 
                        echo "<option value='".$product_handle."'>".$product_name."</option>"; 
                    ?>
                    </optgroup>
                    <optgroup label="Available Plans">
                        <option value="prod_001">Basic Plan</option>
                        <option value="plan_002">Start-up Plan</option>
                        <option value="plan_005">Upgrade to Start-up Plan</option>
                        <option value="plan_003">Business Plan</option>
                        <option value="plan_006">Upgrade to Business Plan</option>
                        <option value="plan_004">Enterprise Plan</option>
                        <option value="plan_007">Upgrade Enterprise Plan</option>
                    </optgroup>
                <?php } else { 
                    echo "<option value='' disabled selected>Product</option>";
                } ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger" type="submit" name="upd_acc">Ticket</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <select name="acc-salut" class="form-control">
                <?php
                    $arr_sltn = array('Mr','Mrs','Ms','Miss','Dr','Herr','Monsieur','Hr','Frau','A V M','Admiraal','Admiral','Air Cdre','Air Commodore','Air Marshal','Air Vice Marshal','Alderman','Alhaji','Ambassador','Baron','Barones','Brig','Brig Gen','Brig General','Brigadier','Brigadier General','Brother','Canon','Capt','Captain','Cardinal','Cdr','Chief','Cik','Cmdr','Col','Col Dr','Colonel','Commandant','Commander','Commissioner','Commodore','Comte','Comtessa','Congressman','Conseiller','Consul','Conte','Contessa','Corporal','Councillor','Count','Countess','Crown Prince','Crown Princess','Dame','Datin','Dato','Datuk','Datuk Seri','Deacon','Deaconess','Dean','Dhr','Dipl Ing','Doctor','Dott','Dott sa','Dr','Dr Ing','Dra','Drs','Embajador','Embajadora','En','Encik','Eng','Eur Ing','Exma Sra','Exmo Sr','F O','Father','First Lieutient','First Officer','Flt Lieut','Flying Officer','Fr','Frau','Fraulein','Fru','Gen','Generaal','General','Governor','Graaf','Gravin','Group Captain','Grp Capt','H E Dr','H H','H M','H R H','Hajah','Haji','Hajim','Her Highness','Her Majesty','Herr','High Chief','His Highness','His Holiness','His Majesty','Hon','Hr','Hra','Ing','Ir','Jonkheer','Judge','Justice','Khun Ying','Kolonel','Lady','Lcda','Lic','Lieut','Lieut Cdr','Lieut Col','Lieut Gen','Lord','M','M L','M R','Madame','Mademoiselle','Maj Gen','Major','Master','Mevrouw','Miss','Mlle','Mme','Monsieur','Monsignor','Mr','Mrs','Ms','Mstr','Nti','Pastor','President','Prince','Princess','Princesse','Prinses','Prof','Prof Dr','Prof Sir','Professor','Puan','Puan Sri','Rabbi','Rear Admiral','Rev','Rev Canon','Rev Dr','Rev Mother','Reverend','Rva','Senator','Sergeant','Sheikh','Sheikha','Sig','Sig na','Sig ra','Sir','Sister','Sqn Ldr','Sr','Sr D','Sra','Srta','Sultan','Tan Sri','Tan Sri Dato','Tengku','Teuku','Than Puying','The Hon Dr','The Hon Justice','The Hon Miss','The Hon Mr','The Hon Mrs','The Hon Ms','The Hon Sir','The Very Rev','Toh Puan','Tun','Vice Admiral','Viscount','Viscountess','Wg Cdr');

                    if(isset($_GET['id'])) { ?> 
                        <optgroup label="Current"> 
                        <?php 
                            echo "<option value='".$salutation."'>".$salutation."</option>"
                        ?> 
                        </optgroup> 
                        <optgroup label="Salutations">
                        <?php

                        $count_sltn = 0;
                        while(!empty($arr_sltn[$count_sltn])) {
                            echo "<option value='".$arr_sltn[$count_sltn]."'>".$arr_sltn[$count_sltn]."</option>";
                            $count_sltn++;
                        } ?>
                        </optgroup>
                        <?php
                    } else {
                        echo "<option value='' disabled selected>Salutation</option>";
                    }
                ?>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="acc-fname" id="acc-fname" class="form-control" placeholder="First Name" value="<?php echo $fname; ?>" onchange="FName()">
            </div>
            <div class="col-md-5">
                <input type="text" name="acc-lname" id="acc-lname" class="form-control" placeholder="Last Name" value="<?php echo $lname; ?>" onchange="LName()">
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                <select name="acc-title" class="form-control">
                <?php
                    $arr_ttl = array('Accountant','Accountant Systems','Acquisition Management Intern','Actuarial Analyst','Actuary','Administrative Generalist/Specialist','Affordable Housing Specialist','Analyst','Appraiser','Archaeologist','Area Systems Coordinator','Asylum or Immigration Officer','Attorney/Law Clerk','Audience Analyst','Audit Resolution Follow Up','Auditor','Behavioral Scientist','Biologist, Fishery','Biologist, Marine','Biologist, Wildlife','Budget Analyst','Budget Specialist','Business Administration Officer','Chemical Engineer','Chemist','Citizen Services Specialist','Civil Engineer','Civil Penalties Specialist','Civil/Mechanical/Structural','Engineer','Communications Specialist','Community and Intergovernmental','Program Specialist','Community Planner','Community Planning\Development','Specialist','Community Services Program','Specialist','Compliance Specialist','Computer Engineer','Computer Programmer/Analyst','Computer Scientist','Computer Specialist','Consumer Safety Officer','Contract Specialist','Contract Specialist/Grants','Management Specialist','Corporate Management Analyst','Cost Account','Criminal Enforcement Analyst','Criminal Investigator','Customer Account Manager','Customer Acct Mgr\Specialist','Democracy Specialist','Desk Officer','Disaster Operations Specialist','Disbursing Specialist','Ecologist','Economist','Economist, Financial','Education Specialist','Electrical Engineer','Electronics Engineer','Emergency Management Specialist','Employee and Management','Development Specialist','Employee Development Specialist','Employee Relations Specialist','Energy and Environmental Policy','Analyst','Energy Program Specialist','Engineer (General)','Environmental Engineer','Environmental Planning and Policy','Specialist','Environmental Protection Specialist','Environmental Specialist','Epidemiologist','Equal Employment Opportunity','Specialist','Equal Opportunity Specialist','Ethics Program Specialist','Evaluation and Technical Services Generalist','Evaluator','Executive Analyst','Facilities Analyst','Federal Retirement Benefits Specialist','Field Management Assistant','Field Office Supervisor','Financial Management Specialist','Financial Legislative Specialist','Financial Specialist','Financial Systems Analyst','Financial Transactions Examination Officer','Food Safety Coordinator','Food Technologist','Foreign Affairs Officer','Foreign Affairs Specialist','Foreign Assets Control Intelligence Analyst','Foreign Assets Control Terrorist Program Analyst','Functional Area Analyst','General Engineer','Geographer','Geographical Information Systems/Computer Aided','Geophysicist','Grants Program Specialist','Grants Specialist','Hazard Mitigation Specialist','Hazardous Waste Generator Initiative Specialist','Health Communications Specialist','Health Educator','Health Insurance Specialist','Health Scientist','Health Systems Specialist','Hospital Finance Associate','Housing Program Specialist','Housing Project Manager','Human Resources Advisor\Consultant','Human Resources Consultant','Human Resources Development','Human Resources Evaluator','Human Resources Representative','Human Resources Specialist','Hydraulic Engineer','Immigration Officer','Import Policy Analyst','Industrial Hygienist','Information Management Specialist','Information Research Specialist','Information Resource Management Specialist','Information Technology Policy Analyst','Information Technology Security Assistant','Information Technology Specialist','Inspector','Instructional Systems Design Specialist','Instructions Methods Specialist','Insurance Marketing Specialist','Insurance Specialist','Intelligence Analyst','Intelligence Operations Specialist','Intelligence Research Specialist','Intelligence Specialist','Internal Program Specialist','Internal Revenue Agent','International Affairs Specialist','International Aviation Operations Specialist','International Cooperation Specialist','International Economist','International Project Manager','International Relations Specialist','International Trade Litigation Electronic Database C','International Trade Specialist','International Transportation Specialist','Investigator','Junior Foreign Affairs Officer','Labor Relations Specialist','Labor Relations Specialist','Learning Specialist','Legislative Assistant','Legislative Analyst','Legislative Specialist','Lender Approval Analyst','Lender Monitoring Analyst','Licensing Examining Specialist/Offices','Logistics Management Specialist','Managed Care Specialist','Management Analyst','Management and Budget Analyst','Management and Program Analyst','Management Intern','Management Support Analyst ','Management Support Specialist','Manpower Analyst','Manpower Development Specialist','Marketing Analyst','Marketing Specialist','Mass Communications Producer','Mathematical Statistician','Media Relations Assistant','Meteorologist','Microbiologist','Mitigation Program Specialist','National Security Training Technology','Natural Resources Specialist','Naval Architect','Operations Officer','Operations Planner','Operations Research Analyst','Operations Supervisor','Outdoor Recreation Planner','Paralegal Specialis','Passport/Visa Specialist','Personnel Management Specialist','Personnel Staffing and Classification Specialist','Petroleum Engineer','Physical Science Officer','Physical Scientist, General','Physical Security Specialist','Policy Advisor to the Director','Policy Analyst','Policy and Procedure Analyzt','Policy and Regulatory Analyst','Policy Coordinator','Policy/Program Analyst','Population/Family Planning Specialist','Position Classification Specialist','Presidential Management Fellow','Procurement Analyst','Procurement Specialist','Professional Relations Outreach','Program Administrator','Program Analyst','Program and Policy Analyst','Program Evaluation and Risk Analyst','Program Evolution Team Leader','Program Examiner','Program Manager','Program Operations Specialist','Program Specialist','Program Support Specialist','Program/Public Health Analyst','Project Analyst','Project Manager','Prototype Activities Coordinator','Psychologist (General)','Public Affairs Assistant','Public Affairs Intern','Public Affairs Specialist','Public Health Advisor','Public Health Analyst','Public Health Specialist','Public Liaison/Outreach Specialist','Public Policy Analyst','Quantitative Analyst','Real Estate Appraiser','Realty Specialist','Regional Management Analyst','Regional Technician','Regulatory Analyst','Regulatory Specialist','Research Analyst','Restructuring Analyst','Risk Analyst','Safety and Occupational Health Manager','Safety and Occupational Health Specialist','Safety Engineer/Industrial Hygienist','Science Program Analyst','Securities Compliance Examiner','Security Specialist','SeniorManagement Information Specialist','Social Insurance Analyst','Social Insurance Policy Specialist','Social Insurance Specialist','Social Science Analyst','Social Science Research Analyst','Social Scientist','South Asia Desk Officer','Special Assistant','Special Assistant for Foreign Policy Strategy','Special Assistant to the Associate Director','Special Assistant to the Chief Information Office','Special Assistant to the Chief, FBI National Security', 'Special Assistant to the Director','Special Emphasis Program Manager','Special Projects Analyst','Specialist','Staff Associate','Statistician','Supply Systems Analyst','Survey or Mathematical Statistician','Survey Statistician','Systems Accountant','Systems Analyst','Tax Law Specialist','Team Leader','Technical Writer/Editor','Telecommunications Policy Analyst','Telecommunications Specialist','Traffic Management Specialist','Training and Technical Assistant','Training Specialist','Transportation Analyst','Transportation Industry Analyst','Transportation Program Specialist','Urban Development Specialist','Usability Researcher','Veterans Employment Specialist','Video Production Specialist','Visa Specialist','Work Incentives Coordinator','Workers Compensation Specialist','Workforce Development Specialist','Worklife Wellness Specialist','Writer','Writer/Editor');

                    if(isset($_GET['id'])) { ?> 
                        <optgroup label="Current"> 
                        <?php 
                            if(!empty($title)) {
                                echo "<option value='".$title."'>".$title."</option>";
                            } else {
                                echo "<option value=''>None</option>";
                            }
                        ?> 
                        </optgroup> 
                        <optgroup label="Titles">
                        <?php

                        $count_ttl = 0;
                        while(!empty($arr_ttl[$count_ttl])) {
                            echo "<option value='".$arr_ttl[$count_ttl]."'>".$arr_ttl[$count_ttl]."</option>";
                            $count_ttl++;
                        } ?>
                        </optgroup>
                        <?php
                    } else {
                        echo "<option value='' disabled selected>Title</option>";
                    }
                ?>
                </select>
            </div>
        </div>
    </form>

    <!-- Customer's Sales Info -->
<!--
    <form id="cust_sales_form" action="" method="POST" style="margin-top: -60px;">
        <div class="row">
            <div class="col-md-1" style="float: right;">
                <button class="btn btn-danger" type="submit" name="submit_ticket">Ticket</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" name="bname" placeholder="Business Name" value="<?php echo $business_name; ?>">
            </div>
            <div class="col-md-2">
                <p><?php echo $sales_date; ?></p>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="tbc_date" placeholder="Trial/Bill/Cancel Date" value="<?php echo $cust_search_state; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" name="sctr" placeholder="Sales Center" value="<?php echo $sales_center; ?>" readonly>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="sagnt" placeholder="Sales Agent" value="<?php echo $sales_agent; ?>" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <select class="form-control">
                    <optgroup label="Current">
                    <?php
                    if(isset($_GET['id'])) {
                        if(empty($product_handle)) { 
                            echo "<option value=''>None</option>";
                        } else {
                            echo "<option value='".$product_handle."'>".$product_name."</option>";
                        }
                    } else {
                        echo "<option value=''>Product</option>";
                    }
                    ?>
                    </optgroup>
                    <optgroup label="Available Plans">
                        <option value="prod_001">Basic Plan</option>
                        <option value="plan_002">Start-up Plan</option>
                        <option value="plan_005">Upgrade to Start-up Plan</option>
                        <option value="plan_003">Business Plan</option>
                        <option value="plan_006">Upgrade to Business Plan</option>
                        <option value="plan_004">Enterprise Plan</option>
                        <option value="plan_007">Upgrade Enterprise Plan</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control">
                    <optgroup label="Current">
                    <?php
                    if(isset($_GET['id'])) {
                        if(empty($product_component_id)) { 
                            echo "<option value=''>None</option>";
                        } else {
                            echo "<option value='".$product_component_id."'>".$product_component_name."</option>";
                        }
                    } else {
                        echo "<option value=''>Component</option>";
                    }
                    ?>    
                    </optgroup>
                    <optgroup label="Available Components">
                        <option value="196368">Custom Company Domain</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control">
                    <optgroup label="Current">
                    <?php
                    if(isset($_GET['id'])) {
                        if(empty($product_coupon_id)) { 
                            echo "<option value=''>None</option>";
                        } else {
                            echo "<option value='".$product_coupon_id."'>".$product_coupon_name."</option>"; 
                        }
                    } else {
                        echo "<option value=''>Coupon</option>";
                    }
                    ?>    
                    </optgroup>
                    <optgroup label="Available Coupons">
                        <option value="SAVE50">Discount Coupon</option>
                        <option value="FREDOM">Domain Coupon</option>
                        <option value="REFER">Referral Coupon</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="dropdown col-md-4">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Cancellation Reason
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Refund Amount">
            </div>
        </div>
    </form>
-->
    <form id="cust_provisioning_form" action="" method="POST">
        <div class="row">
            <div class="col-md-12">
                <img src="img/web_under_construction.jpg" style="margin-left: 20%;">
            </div>
        </div>
    </form>

    <form id="cust_billing_form" action="" method="POST">
        <div class="row">
            <div class="col-md-7">
                <input type="text" name="ppID" id="ppID" class="form-control" placeholder="Payment Processor ID ">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="bill_stat">
                <?php if(isset($_GET['id'])) { ?>
                    <optgroup label="Current"> 
                        <option id="bill_stat"></option>
                    </optgroup>
                    <optgroup label="Status"> 
                        <option value="trialing">Trialing</option>
                        <option value="active">Active</option>
                        <option value="past_due">Past Due</option>
                        <option value="unpaid">Unpaid</option>
                        <option value="canceled">Canceled</option>
                        <option value="delinquent">Delinquent</option>
                    </optgroup>
                <?php } else {
                    echo "<option value='' disabled selected>Status</option>";
                } ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" placeholder="Successful Billing Cycles">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <select class="form-control">
                <?php if(isset($_GET['id'])) { ?>
                    <optgroup label="Current">
                    <?php 
                        echo "<option value='".$product_handle."'>".$product_name."</option>"; 
                    ?>
                    </optgroup>
                    <optgroup label="Available Plans">
                        <option value="prod_001">Basic Plan</option>
                        <option value="plan_002">Start-up Plan</option>
                        <option value="plan_005">Upgrade to Start-up Plan</option>
                        <option value="plan_003">Business Plan</option>
                        <option value="plan_006">Upgrade to Business Plan</option>
                        <option value="plan_004">Enterprise Plan</option>
                        <option value="plan_007">Upgrade Enterprise Plan</option>
                    </optgroup>
                <?php } else { 
                    echo "<option value='' disabled selected>Product</option>";
                } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Credit Card Masked Number" value="<?php echo $cc_last_four; ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="CC Expiration Month" value="<?php echo $cc_exp_mm; ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="CC Expiration Year" value="<?php echo $cc_exp_yy; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Billing Address Street" value="<?php echo $bill_address; ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Billing City" value="<?php echo $bill_city; ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="State" value="<?php echo $bill_state; ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Billing Postcode" value="<?php echo $bill_zip; ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Billing Country" value="<?php echo $bill_country; ?>">
            </div>
        </div>
    </form>

    <form id="cust_support_form" action="" method="POST">
        <div class="row">
            <div class="col-md-12">
                <img src="img/web_under_construction.jpg" style="margin-left: 20%;">
            </div>
        </div>
    </form>

    <form id="cust_dashboard_form" action="" method="POST">
        <div class="row">
            <div class="col-md-12">
                <img src="img/web_under_construction.jpg" style="margin-left: 20%;">
            </div>
        </div>
    </form>

    <form id="cust_admin_form" action="" method="POST">
        <div class="row">
            <div class="col-md-12">
                <img src="img/web_under_construction.jpg" style="margin-left: 20%;">
            </div>
        </div>
    </form>
<?php
    require "footer.php";
?>

<?php 
    if(isset($_GET['id'])) {
        $char_state = $result_customer_id_search[0]->state;
        $char_state_exp = explode('_', $char_state);
        $count=0;
        $fin_char_state = "";
        while(!empty($char_state_exp[$count])) {
            $fin_char_state .= ucfirst($char_state_exp[$count])."&nbsp;";
            $count++;
        }
        echo "<input type='text' id='char_state' value='".$fin_char_state."' hidden>";

        ?><script>
            document.getElementById("cust_id").title = document.getElementById("char_state").value;
            document.getElementById("bill_stat").innerHTML = document.getElementById("char_state").value;
        </script><?php
    }
?>

<script type="text/javascript" src="js/field_trappings/customer_form_tab1.js"></script>