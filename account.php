<?php
    require 'header.php';

    if(isset($_POST['upd_acc_account'])) {
        $business_name = stripslashes($_POST['acc-b-name']);
        $prod = $_POST['acc-prod'];
        $salutation = $_POST['acc-salut'];
        $title = $_POST['acc-title'];
        $fname = $_POST['acc-fname'];
        $lname = $_POST['acc-lname'];

        $test = true;
        $customer = new ChargifyCustomer(NULL, $test);
        $upd_subscription = new ChargifySubscription(NULL, $test);

        $customer->id = $chargifyID;
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

        try {
            $result_customer_id_search = $upd_subscription->getByCustomerID($chargifyID);
        } catch (ChargifyValidationException $cve) {
            echo $cve->getMessage();
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

    <form id="caccount_form" action="" method="POST" onsubmit="return checkFields_acc_tab1();">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="acc-b-name" id="acc-b-name" class="form-control" placeholder="Business Name" value="<?php echo $business_name; ?>" onchange="BName()">
            </div>
            <div class="col-md-5">
                <select class="form-control" name="acc-prod" id="acc-prod" placeholder="Product">
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
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger" type="submit" name="upd_acc_account">Ticket</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <select name="acc-salut" class="form-control">
                <?php
                    $arr_sltn = array('Mr','Mrs','Ms','Miss','Dr','Herr','Monsieur','Hr','Frau','A V M','Admiraal','Admiral','Air Cdre','Air Commodore','Air Marshal','Air Vice Marshal','Alderman','Alhaji','Ambassador','Baron','Barones','Brig','Brig Gen','Brig General','Brigadier','Brigadier General','Brother','Canon','Capt','Captain','Cardinal','Cdr','Chief','Cik','Cmdr','Col','Col Dr','Colonel','Commandant','Commander','Commissioner','Commodore','Comte','Comtessa','Congressman','Conseiller','Consul','Conte','Contessa','Corporal','Councillor','Count','Countess','Crown Prince','Crown Princess','Dame','Datin','Dato','Datuk','Datuk Seri','Deacon','Deaconess','Dean','Dhr','Dipl Ing','Doctor','Dott','Dott sa','Dr','Dr Ing','Dra','Drs','Embajador','Embajadora','En','Encik','Eng','Eur Ing','Exma Sra','Exmo Sr','F O','Father','First Lieutient','First Officer','Flt Lieut','Flying Officer','Fr','Frau','Fraulein','Fru','Gen','Generaal','General','Governor','Graaf','Gravin','Group Captain','Grp Capt','H E Dr','H H','H M','H R H','Hajah','Haji','Hajim','Her Highness','Her Majesty','Herr','High Chief','His Highness','His Holiness','His Majesty','Hon','Hr','Hra','Ing','Ir','Jonkheer','Judge','Justice','Khun Ying','Kolonel','Lady','Lcda','Lic','Lieut','Lieut Cdr','Lieut Col','Lieut Gen','Lord','M','M L','M R','Madame','Mademoiselle','Maj Gen','Major','Master','Mevrouw','Miss','Mlle','Mme','Monsieur','Monsignor','Mr','Mrs','Ms','Mstr','Nti','Pastor','President','Prince','Princess','Princesse','Prinses','Prof','Prof Dr','Prof Sir','Professor','Puan','Puan Sri','Rabbi','Rear Admiral','Rev','Rev Canon','Rev Dr','Rev Mother','Reverend','Rva','Senator','Sergeant','Sheikh','Sheikha','Sig','Sig na','Sig ra','Sir','Sister','Sqn Ldr','Sr','Sr D','Sra','Srta','Sultan','Tan Sri','Tan Sri Dato','Tengku','Teuku','Than Puying','The Hon Dr','The Hon Justice','The Hon Miss','The Hon Mr','The Hon Mrs','The Hon Ms','The Hon Sir','The Very Rev','Toh Puan','Tun','Vice Admiral','Viscount','Viscountess','Wg Cdr');
                ?> 
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
                    $arr_ttl = array('Accountant','Accountant Systems','Acquisition Management Intern','Actuarial Analyst','Actuary','Administrative Generalist/Specialist','Affordable Housing Specialist','Analyst','Appraiser','Archaeologist','Area Systems Coordinator','Asylum or Immigration Officer','Attorney/Law Clerk','Audience Analyst','Audit Resolution Follow Up','Auditor','Behavioral Scientist','Biologist, Fishery','Biologist, Marine','Biologist, Wildlife','Budget Analyst','Budget Specialist','Business Administration Officer','Chemical Engineer','Chemist','Citizen Services Specialist','Civil Engineer','Civil Penalties Specialist','Civil/Mechanical/Structural','Engineer','Communications Specialist','Community and Intergovernmental','Program Specialist','Community Planner','Community Planning\Development','Specialist','Community Services Program','Specialist','Compliance Specialist','Computer Engineer','Computer Programmer/Analyst','Computer Scientist','Computer Specialist','Consumer Safety Officer','Contract Specialist','Contract Specialist/Grants','Management Specialist','Corporate Management Analyst','Cost Account','Criminal Enforcement Analyst','Criminal Investigator','Customer Account Manager','Customer Acct Mgr\Specialist','Democracy Specialist','Desk Officer','Disaster Operations Specialist','Disbursing Specialist','Ecologist','Economist','Economist, Financial','Education Specialist','Electrical Engineer','Electronics Engineer','Emergency Management Specialist','Employee and Management','Development Specialist','Employee Development Specialist','Employee Relations Specialist','Energy and Environmental Policy','Analyst','Energy Program Specialist','Engineer (General)','Environmental Engineer','Environmental Planning and Policy','Specialist','Environmental Protection Specialist','Environmental Specialist','Epidemiologist','Equal Employment Opportunity','Specialist','Equal Opportunity Specialist','Ethics Program Specialist');
?> 
                        <optgroup label="Current"> 
                        <?php 
                            echo "<option value='".$title."'>".$title."</option>"
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
                </select>
            </div>
        </div>
    </form>

    <form id="account_dashboard_form" action="" method="POST" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <img src="img/web_under_construction.jpg" style="margin-left: 20%;">
            </div>
        </div>
    </form>

<?php
    require "footer.php";
?>

<script type="text/javascript" src="js/field_trappings/account_form_tab1.js"></script>