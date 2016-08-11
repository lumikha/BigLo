<?php
  //require 'couchDB/dbConnect.php';
  require_once 'couchDB/PHP-on-Couch-master/lib/couch.php';
  require_once 'couchDB/PHP-on-Couch-master/lib/couchClient.php';
  require_once 'couchDB/PHP-on-Couch-master/lib/couchDocument.php';

  $created_doc_id = "";
  $done = 0;
  $error_message = "";
  if(isset($_POST['submit_business_form'])) {

    //adding - to phone number
    $num_arr = array_map('intval', str_split($_POST['biz-pnumber']));
    $fin_num = array();
    array_push($fin_num, '1-');
    $i = 0;
    while($i < 3){
      array_push($fin_num, $num_arr[$i]);
      $i++;
    }
    array_push($fin_num, '-');
    $j = 3;
    while($j < 6){
      array_push($fin_num, $num_arr[$j]);
      $j++;
    }
    array_push($fin_num, '-');
    $k = 6;
    while($k < 10){
      array_push($fin_num, $num_arr[$k]);
      $k++;
    }
    $btn_number = implode("",$fin_num);

    //adding - to alternate mobile number
    if(!empty($_POST['biz-mnumber'])) {
      $num_arr2 = array_map('intval', str_split($_POST['biz-mnumber']));
      $fin_num2 = array();
      array_push($fin_num2, '1-');
      $x = 0;
      while($x < 3){
        array_push($fin_num2, $num_arr2[$x]);
        $x++;
      }
      array_push($fin_num2, '-');
      $y = 3;
      while($y < 6){
        array_push($fin_num2, $num_arr2[$y]);
        $y++;
      }
      array_push($fin_num2, '-');
      $z = 6;
      while($z < 10){
        array_push($fin_num2, $num_arr2[$z]);
        $z++;
      }
      $btn_number2 = implode("",$fin_num2);
    }

    $client_customer = new couchClient ('http://127.0.0.1:5984','bigloco-customers');

    $doc = new stdClass();
    $doc->business_name = @$_POST['biz-name'];
    $doc->business_address = @$_POST['biz-street'];
    $doc->business_suite_no = @$_POST['suite-number'];
    $doc->business_city = @$_POST['biz-city'];
    $doc->business_state = @$_POST['biz-state'];
    $doc->business_zip = @$_POST['biz-zip'];
    $doc->business_phone_no = @$btn_number;
    $doc->business_email = @$_POST['biz-eadd'];
    $doc->business_website = @$_POST['biz-web'];
    if($_POST['allthetime'] == false) {
      $doc->business_hours = @$_POST['biz-hours'];
    } else {
      $doc->business_hours = "24/7";
    }
    $doc->business_alternate_phone_no = @$btn_number2;
    $doc->business_post_address= @$_POST['biz-post-address'];

    $count_paymet = 0;
    $paymethod = "";
    foreach($_POST["payment-method"] as $method) {
      if($count_paymet == 0) {
        $paymethod = $method;
      } else {
        $paymethod .= " $method";
      }
      $count_paymet++;
    }

    $doc->payment_method= @$paymethod;

    try {
      $response = $client_customer->storeDoc($doc);
    } catch ( Exception $e ) {
      die("Unable to store the document : ".$e->getMessage());
    }

    $created_doc_id = $response->id;

    $p2_bname = $_POST['biz-name']; 
    $p2_email = $_POST['biz-eadd'];
    
    if($_POST['same-bill-info'] == "yes") {
      $p2_street = $_POST['biz-street']; 
      $p2_city = $_POST['biz-city'];
      $p2_state = $_POST['biz-state'];
      $p2_zip = $_POST['biz-zip'];
    } else {
      $p2_street = ""; 
      $p2_city = "";
      $p2_state = "";
      $p2_zip = "";
    }

    $done = 1;
  }

/***** SECOND FORM *****/
$err_msg = "";
  if(isset($_POST['submit_billing_form'])) {
    require 'Chargify-PHP-Client/lib/Chargify.php';

    $client_customer = new couchClient ('http://127.0.0.1:5984','bigloco-customers');

    try {
        $doc = $client_customer->getDoc($_POST['created_doc_id']);
    } catch (Exception $e) {
        echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
    }

    $num_arr = array_map('intval', str_split($_POST["c-phone"]));
    $fin_num = array();
    array_push($fin_num, '1-');
    $i = 0;
    while($i < 3){
      array_push($fin_num, $num_arr[$i]);
      $i++;
    }
    array_push($fin_num, '-');
    $j = 3;
    while($j < 6){
      array_push($fin_num, $num_arr[$j]);
      $j++;
    }
    array_push($fin_num, '-');
    $k = 6;
    while($k < 10){
      array_push($fin_num, $num_arr[$k]);
      $k++;
    }
    $btn_number = implode("",$fin_num);

    if($_POST["product-handle"] == 'prod_001') {
      $prodID = 3881312;
      $prodName = "Basic Plan";
    } else if($_POST["product-handle"] == 'plan_002') {
      $prodID = 3881313;
      $prodName = "Start-up Plan";
    } else if($_POST["product-handle"] == 'plan_005') {
      $prodID = 3881318;
      $prodName = "Upgrade to Start-up Plan";
    } else if($_POST["product-handle"] == 'plan_003') {
      $prodID = 3881314;
      $prodName = "Business Plan";
    } else if($_POST["product-handle"] == 'plan_006') {
      $prodID = 3881319;
      $prodName = "Upgrade to Business Plan";
    } else if($_POST["product-handle"] == 'plan_004') {
      $prodID = 3881316;
      $prodName = "Enterprise Plan";
    } else {
      $prodID = 3881320;
      $prodName = "Upgrade to Enterprise Plan";
    }  

    $test = true;

    $new_customer = new ChargifyCustomer(NULL, $test);
    $new_customer->first_name = $_POST["bfname"];
    $new_customer->last_name = $_POST["blname"];
    $new_customer->email = $_POST["c-eadd"];
    $new_customer->organization = stripslashes($_POST["bussiness-name"]);
    $new_customer->phone = $btn_number;
    $saved_customer = $new_customer->create();
    
    $new_payment_profile = new ChargifyCreditCard(NULL, $test);
    $new_payment_profile->first_name = $_POST["bfname"];
    $new_payment_profile->last_name = $_POST["blname"];
    $new_payment_profile->full_number = $_POST["card-number"];
    $new_payment_profile->expiration_month = $_POST["card-expiry-month"];
    $new_payment_profile->expiration_year = $_POST["card-expiry-year"];
    $new_payment_profile->cvv = $_POST["card-cvc"];
    $new_payment_profile->billing_address = $_POST["c-street"];
    $new_payment_profile->billing_address_2 = $_POST["c-street2"];
    $new_payment_profile->billing_city = $_POST["c-city"];
    $new_payment_profile->billing_state = $_POST["c-state"];
    $new_payment_profile->billing_zip = $_POST["c-zip"];
    
    $new_subscription = new ChargifySubscription(NULL, $test);
    $new_subscription->product_handle = $_POST["product-handle"];
    $new_subscription->customer_id = $saved_customer->id;
    $new_subscription->credit_card_attributes = $new_payment_profile;
    //$new_subscription->coupon_code = $_POST["coupon-code"];
    
    try{
      $saved_subscription = $new_subscription->create();
      $doc->chargify_id = @$saved_customer->id;
      $doc->business_name = @$_POST['bussiness-name'];
      $doc->customer_salutation = @$_POST['salut'];
      $doc->customer_title = @$_POST['title'];
      $doc->customer_first_name = @$_POST['bfname'];
      $doc->customer_last_name = @$_POST['blname'];
      $doc->customer_email = @$_POST['c-eadd'];
      $doc->customer_phone_no = @$btn_number ;
      $doc->customer_billing_address = @$_POST['c-street'];
      $doc->customer_suite_no = @$_POST['c-street2'];
      $doc->customer_billing_city = @$_POST['c-city'];
      $doc->customer_billing_state = @$_POST['c-state'];
      $doc->customer_billing_zip = @$_POST['c-zip'];
      $doc->customer_billing_country = "US";
      $doc->customer_card_last_four = substr($_POST['card-number'], -4);
      $doc->customer_card_cvc = @$_POST['card-cvc'];
      $doc->customer_card_expire_month = @$_POST['card-expiry-month'];
      $doc->customer_card_expire_year = @$_POST['card-expiry-year'];
      if(empty($saved_subscription->credit_card->customer_vault_token)) {
        $doc->payment_processor_id = "N.A. - Bogus";
      } else {
        $doc->payment_processor_id = $saved_subscription->credit_card->customer_vault_token;
      }
      $doc->product_id = $prodID;
      $doc->product_handle = $_POST["product-handle"];
      $doc->product_name = $prodName;
      $doc->product_component_id = "";
      $doc->product_component_name = "";
      $doc->product_coupon_id = "";
      $doc->product_coupon_name = "";
      $doc->sale_date = date("m/d/Y");
      $doc->sale_center = @$_POST['sales-center'];
      $doc->sale_agent = @$_POST['sales-agent'];
      $doc->business_category = "";
      $doc->prov_gmail = "";
      $doc->prov_keywords = "";
      $doc->prov_special_request = "";
      $doc->prov_existing_social1 = "";
      $doc->prov_existing_social2 = "";
      $doc->prov_biglo_website = "";
      $doc->prov_analytical_address = "";
      $doc->prov_google_plus = "";
      $doc->prov_google_maps = "";
      $doc->prov_facebook = "";
      $doc->prov_foursquare = "";
      $doc->prov_twitter = "";
      $doc->prov_linkedin = "";

      // update the document on CouchDB server
      try {
        $response = $client_customer->storeDoc($doc);
        $done = 2;
      } catch (Exception $e) {
        $done = 1;
        $err_msg = "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
      }

        $client_user = new couchClient ('http://127.0.0.1:5984','bigloco-users');
        $doc2 = new stdClass();

        $user_pass_a = mt_rand(0 , 100000);
        $user_pass_b = mt_rand(0 , 100000);
        $user_pass_final = $user_pass_a.$user_pass_b;

        $doc2->customer_id = @$_POST['created_doc_id'];
        $doc2->email = @$_POST['c-eadd'];
        $doc2->password = @$user_pass_final;
        $doc2->userType = "Customer";

        try {
          $response2 = $client_user->storeDoc($doc2);
        } catch ( Exception $e ) {
          die("Unable to store the document : ".$e->getMessage());
        }

          $client_user_id = new couchClient ('http://127.0.0.1:5984','bigloco-customers');
          try {
            $doc3 = $client_user_id->getDoc($_POST['created_doc_id']);
          } catch (Exception $e) {
            echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
          }

          $doc3->user_id = $response2->id;

          try {
            $response3 = $client_user_id->storeDoc($doc3);
          } catch ( Exception $e ) {
            die("Unable to store the document : ".$e->getMessage());
          }

    } catch(Exception $error) {
      $done = 1;
      $err_msg = $error->getMessage();
    }
  }

  if($done == 2) {
    ?>
    <script>
      //window.location = "success_register?e=$_POST['c-eadd']&p=$user_pass_final"; //User Dashboard
      window.location = "success_register.php?e=<?php echo $_POST['c-eadd']; ?>&p=<?php echo $user_pass_final; ?>";
    </script>
    <?php
  }

?>

<html>
<head>
	<title>Enroll Customer</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
  <link rel="stylesheet" type="text/css" href="js/dataTables/dataTables.bootstrap.min.css"/>
  <link rel="stylesheet" type="text/css" href="js/field_trappings/error_msg.css"/>
  <style>
    .error {
      font-size: 12px;
      font-style: italic; 
      display: inline;
      color: red;
    }

    #error_check_all {
      font-size: 20px;
      font-style: italic;
      font-weight: bold;
      color: red;
      text-align: center;
    }
  </style>
</head>
<body>

<?php if($done == 0 || $done != 1) { ?>
<div id="business_information" class="row">
  <div class="col-md-offset-3 col-md-6" style="">
    <div class="panel-body" id="demo">
      <h2>Enroll Customer</h2>
      <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkFields_enroll1();">
        <fieldset>
          <h4>Business Information</h4><br> 
          <div class="form-group">
            <div class="col-lg-12">
              <label>Business Name</label>&nbsp;&nbsp;<span class="hido" id="hido1"><p id="error1" class="error"></p></span>
              <input type="text" class="form-control" id="biz-name" name="biz-name" onkeypress="return KeyPressBName(event)" onclick="clickField1()">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-6">
              <label>Business Address 1</label>&nbsp;&nbsp;<span class="hido" id="hido2"><p id="error2" class="error"></p></span>
              <input type="text" class="form-control" id="biz-street" name="biz-street" onkeypress="return KeyPressBStreet(event)" onclick="clickField2()">
            </div>
            <div class="col-lg-6">
              <label>Suite/Apartment No.</label>
              <input type="text" class="form-control" name="suite-number">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-6">
              <label>City</label>&nbsp;&nbsp;<span class="hido" id="hido3"><p id="error3" class="error"></p></span>
              <input type="text" class="form-control" id="biz-city" name="biz-city" onkeypress="return KeyPressBCity(event)" onclick="clickField3()">
            </div>
            <div class="col-lg-3">
              <label>State</label>&nbsp;&nbsp;<span class="hido" id="hido4-state"><p id="error4-state" class="error"></p></span>
              <select class="form-control" id="biz-state" name="biz-state" onchange="ChangeState()">
                <option value='' disabled selected>Select</option>
                <option value="AL">AL</option> 
                <option value="AK">AK</option>
                <option value="AZ">AZ</option> 
                <option value="AR">AR</option> 
                <option value="CA">CA</option> 
                <option value="CO">CO</option> 
                <option value="CT">CT</option> 
                <option value="DE">DE</option> 
                <option value="DC">DC</option> 
                <option value="FL">FL</option> 
                <option value="GA">GA</option> 
                <option value="HI">HI</option> 
                <option value="ID">ID</option> 
                <option value="IL">IL</option> 
                <option value="IN">IN</option> 
                <option value="IA">IA</option> 
                <option value="KS">KS</option> 
                <option value="KY">KY</option> 
                <option value="LA">LA</option> 
                <option value="ME">ME</option> 
                <option value="MD">MD</option> 
                <option value="MA">MA</option> 
                <option value="MI">MI</option> 
                <option value="MN">MN</option> 
                <option value="MS">MS</option> 
                <option value="MO">MO</option> 
                <option value="MT">MT</option> 
                <option value="NE">NE</option> 
                <option value="NV">NV</option> 
                <option value="NH">NH</option> 
                <option value="NJ">NJ</option> 
                <option value="NM">NM</option> 
                <option value="NY">NY</option> 
                <option value="NC">NC</option> 
                <option value="ND">ND</option> 
                <option value="OH">OH</option> 
                <option value="OK">OK</option> 
                <option value="OR">OR</option> 
                <option value="PA">PA</option> 
                <option value="RI">RI</option> 
                <option value="SC">SC</option> 
                <option value="SD">SD</option> 
                <option value="TN">TN</option> 
                <option value="TX">TX</option> 
                <option value="UT">UT</option> 
                <option value="VT">VT</option> 
                <option value="VA">VA</option> 
                <option value="WA">WA</option> 
                <option value="WV">WV</option> 
                <option value="WI">WI</option> 
                <option value="WY">WY</option>
              </select>
            </div>
            <div class="col-lg-3">
              <label>Zip</label>&nbsp;&nbsp;<span class="hido" id="hido4"><p id="error4" class="error"></p></span>
              <input type="text" class="form-control" id="biz-zip" name="biz-zip" onkeypress="return KeyPressBZip(event)" onclick="clickField4()">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-6">
              <label>Business Phone</label>&nbsp;&nbsp;<span class="hido" id="hido5"><p id="error5" class="error"></p></span>
              <input type="text" class="form-control" id="biz-pnumber" name="biz-pnumber" maxlength="10" onkeypress="return KeyPressBPNumber(event)" onclick="clickField5()">
            </div>
            <div class="col-lg-6">
              <label>Email Address</label>&nbsp;&nbsp;<span class="hido" id="hido6"><p id="error6" class="error"></p></span>
              <input type="text" class="form-control" id="biz-eadd" name="biz-eadd" onkeypress="return KeyPressBEAdd(event)" onclick="clickField6()">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-12">
              <label> Website</label>
              <input type="text" class="form-control" id="biz-web" name="biz-web">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-6">
              <label>Hours of Operation</label>&nbsp;&nbsp;<span class="hido" id="hido7"><p id="error7" class="error"></p></span>
              <div class="os">
                <label> 24 / 7?</label>
                <input type="radio" id="allthetime_yes" name="allthetime" value="true"  onchange="alltime();">Yes
                <input type="radio" id="allthetime_no" name="allthetime" value="false"  onchange="notalltime();" checked>No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="number" min="0" max="24" class="form-control" style="width: 100px; display: inline;" id="spinner" name="biz-hours" onclick="clickField7()">
              </div>
              
            </div>
            <div class="col-lg-6">
              <label>Alternate/Mobile Number</label>&nbsp;&nbsp;<span class="hido" id="hidomnum"><p id="errormnum" class="error"></p></span>
              <input type="text" class="form-control" id="biz-mnumber" name="biz-mnumber" maxlength="10">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-6">
              <label>Do You Want Your Address Posted?</label>
              <label class="radio-inline"><input type="radio" name="biz-post-address" value="yes" checked="checked">Yes</label>
              <label class="radio-inline"><input type="radio" name="biz-post-address" value="no">No</label>
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-12">
              <label>Do your Office Address the same with your Billing Address?</label>
            </div>
            <div class="col-lg-6">
              <label class="radio-inline"><input type="radio" name="same-bill-info" value="yes" checked="checked">Yes</label>
              <label class="radio-inline"><input type="radio" name="same-bill-info" value="no">No</label>
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-12">
              <label>Payment Accepted</label>&nbsp;&nbsp;<span class="hido" id="hido8"><p id="error8" class="error"></p></span>
              <div class="form-group">
                <div class="col-lg-8">
                  <div>
                    <input type="checkbox" id="paymet_1"  name="payment-method[]" value="Cash" style="margin-left: 30px;" onchange="payment();">&nbsp;Cash
                    <input type="checkbox" id="paymet_2"  name="payment-method[]" value="Check" style="margin-left: 30px;" onchange="payment();">&nbsp;Check
                    <input type="checkbox" id="paymet_3"  name="payment-method[]" value="Visa" style="margin-left: 30px;" onchange="payment();">&nbsp;Visa
                    <input type="checkbox" id="paymet_4"  name="payment-method[]" value="Paypal" style="margin-left: 30px;" onchange="payment();">&nbsp;Paypal
                  </div>
                  <div>
                    <input type="checkbox" id="paymet_5"  name="payment-method[]" value="Amex" style="margin-left: 30px;" onchange="payment();">&nbsp;AMEX
                    <input type="checkbox" id="paymet_6"  name="payment-method[]" value="Mastercard" style="margin-left: 30px;" onchange="payment();">&nbsp;Mastercard
                    <input type="checkbox" id="paymet_7"  name="payment-method[]" value="Discover" style="margin-left: 30px;" onchange="payment();">&nbsp;Discover
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div style="text-align: center; margin-top: 25px;">
            <span class="payment-errors">
            <?php echo $error_message; ?>
              <span class="hido" id="error_check_all"><label id="error_check_all"></label></span>
            </span>
          </div>

          <div class="col-lg-9 col-lg-offset-5">
            <input type="submit" class="btn btn-primary" name="submit_business_form" value="Submit">
          </div>
         

        </fieldset>
      </form>
    </div>
  </div>
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/dataTables/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="js/field_trappings/enroll_form_a.js"></script>
</div>
<?php } else { ?>
<div id="billing_information" class="row">
    <div class="col-md-offset-3 col-md-6" style="">
        <div class="panel-body" id="demo">
          
         <form class="form-horizontal" method="POST" action="" enctype="multipart/form-data" onsubmit="return checkFields_enroll2();">
          <input type="hidden" name="created_doc_id" 
          value="<?php if(empty($err_msg)){echo $created_doc_id;}else{echo $_POST['created_doc_id'];} ?>">
          <input type="hidden" name="product-handle" value="prod_001">
          <input type="hidden" name="sales-center" value="BIGLO_SALES_CENTER">
          <input type="hidden" id="option_1_hidden_value" 
          value="<?php if(empty($err_msg)){echo $p2_state;}else{echo $_POST['c-state'];} ?>">
          <fieldset>
            <h2>Billing Information</h2><br> 

            <div class="form-group">
              <div class="col-lg-12">
                <label>Business Name</label>
                <input type="text" class="form-control" id="bussiness-name" name="bussiness-name" value="<?php if(empty($err_msg)){echo $p2_bname;}else{echo $_POST['bussiness-name'];} ?>" readonly>
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Salutation</label>&nbsp;&nbsp;<span class="hido" id="hido-sal"><p id="error-sal" class="error"></p></span>
                <select name="salut" id="salut" class="form-control" onchange="ChangeSal()">
                <?php 
                  $arr_sltn = array('Mr','Mrs','Ms','Miss','Dr','Herr','Monsieur','Hr','Frau','A V M','Admiraal','Admiral','Air Cdre','Air Commodore','Air Marshal','Air Vice Marshal','Alderman','Alhaji','Ambassador','Baron','Barones','Brig','Brig Gen','Brig General','Brigadier','Brigadier General','Brother','Canon','Capt','Captain','Cardinal','Cdr','Chief','Cik','Cmdr','Col','Col Dr','Colonel','Commandant','Commander','Commissioner','Commodore','Comte','Comtessa','Congressman','Conseiller','Consul','Conte','Contessa','Corporal','Councillor','Count','Countess','Crown Prince','Crown Princess','Dame','Datin','Dato','Datuk','Datuk Seri','Deacon','Deaconess','Dean','Dhr','Dipl Ing','Doctor','Dott','Dott sa','Dr','Dr Ing','Dra','Drs','Embajador','Embajadora','En','Encik','Eng','Eur Ing','Exma Sra','Exmo Sr','F O','Father','First Lieutient','First Officer','Flt Lieut','Flying Officer','Fr','Frau','Fraulein','Fru','Gen','Generaal','General','Governor','Graaf','Gravin','Group Captain','Grp Capt','H E Dr','H H','H M','H R H','Hajah','Haji','Hajim','Her Highness','Her Majesty','Herr','High Chief','His Highness','His Holiness','His Majesty','Hon','Hr','Hra','Ing','Ir','Jonkheer','Judge','Justice','Khun Ying','Kolonel','Lady','Lcda','Lic','Lieut','Lieut Cdr','Lieut Col','Lieut Gen','Lord','M','M L','M R','Madame','Mademoiselle','Maj Gen','Major','Master','Mevrouw','Miss','Mlle','Mme','Monsieur','Monsignor','Mr','Mrs','Ms','Mstr','Nti','Pastor','President','Prince','Princess','Princesse','Prinses','Prof','Prof Dr','Prof Sir','Professor','Puan','Puan Sri','Rabbi','Rear Admiral','Rev','Rev Canon','Rev Dr','Rev Mother','Reverend','Rva','Senator','Sergeant','Sheikh','Sheikha','Sig','Sig na','Sig ra','Sir','Sister','Sqn Ldr','Sr','Sr D','Sra','Srta','Sultan','Tan Sri','Tan Sri Dato','Tengku','Teuku','Than Puying','The Hon Dr','The Hon Justice','The Hon Miss','The Hon Mr','The Hon Mrs','The Hon Ms','The Hon Sir','The Very Rev','Toh Puan','Tun','Vice Admiral','Viscount','Viscountess','Wg Cdr');

                    if(!empty($err_msg)) {
                      echo "<option value='".$_POST['salut']."'>".$_POST['salut']."</option>";
                    } else {
                      echo "<option value='' disabled selected>Select</option>";
                    } ?>
                    <?php
                      $count_sltn = 0;
                      while(!empty($arr_sltn[$count_sltn])) {
                        echo "<option value='".$arr_sltn[$count_sltn]."'>".$arr_sltn[$count_sltn]."</option>";
                        $count_sltn++;
                      } 
                    ?>
                </select>
              </div>
              <div class="col-lg-6">
                <label>Title</label>
                <select name="title" id="title" class="form-control">
                <?php 
                  $arr_ttl = array('Accountant','Accountant Systems','Acquisition Management Intern','Actuarial Analyst','Actuary','Administrative Generalist/Specialist','Affordable Housing Specialist','Analyst','Appraiser','Archaeologist','Area Systems Coordinator','Asylum or Immigration Officer','Attorney/Law Clerk','Audience Analyst','Audit Resolution Follow Up','Auditor','Behavioral Scientist','Biologist, Fishery','Biologist, Marine','Biologist, Wildlife','Budget Analyst','Budget Specialist','Business Administration Officer','Chemical Engineer','Chemist','Citizen Services Specialist','Civil Engineer','Civil Penalties Specialist','Civil/Mechanical/Structural','Engineer','Communications Specialist','Community and Intergovernmental','Program Specialist','Community Planner','Community Planning\Development','Specialist','Community Services Program','Specialist','Compliance Specialist','Computer Engineer','Computer Programmer/Analyst','Computer Scientist','Computer Specialist','Consumer Safety Officer','Contract Specialist','Contract Specialist/Grants','Management Specialist','Corporate Management Analyst','Cost Account','Criminal Enforcement Analyst','Criminal Investigator','Customer Account Manager','Customer Acct Mgr\Specialist','Democracy Specialist','Desk Officer','Disaster Operations Specialist','Disbursing Specialist','Ecologist','Economist','Economist, Financial','Education Specialist','Electrical Engineer','Electronics Engineer','Emergency Management Specialist','Employee and Management','Development Specialist','Employee Development Specialist','Employee Relations Specialist','Energy and Environmental Policy','Analyst','Energy Program Specialist','Engineer (General)','Environmental Engineer','Environmental Planning and Policy','Specialist','Environmental Protection Specialist','Environmental Specialist','Epidemiologist','Equal Employment Opportunity','Specialist','Equal Opportunity Specialist','Ethics Program Specialist','Evaluation and Technical Services Generalist','Evaluator','Executive Analyst','Facilities Analyst','Federal Retirement Benefits Specialist','Field Management Assistant','Field Office Supervisor','Financial Management Specialist','Financial Legislative Specialist','Financial Specialist','Financial Systems Analyst','Financial Transactions Examination Officer','Food Safety Coordinator','Food Technologist','Foreign Affairs Officer','Foreign Affairs Specialist','Foreign Assets Control Intelligence Analyst','Foreign Assets Control Terrorist Program Analyst','Functional Area Analyst','General Engineer','Geographer','Geographical Information Systems/Computer Aided','Geophysicist','Grants Program Specialist','Grants Specialist','Hazard Mitigation Specialist','Hazardous Waste Generator Initiative Specialist','Health Communications Specialist','Health Educator','Health Insurance Specialist','Health Scientist','Health Systems Specialist','Hospital Finance Associate','Housing Program Specialist','Housing Project Manager','Human Resources Advisor\Consultant','Human Resources Consultant','Human Resources Development','Human Resources Evaluator','Human Resources Representative','Human Resources Specialist','Hydraulic Engineer','Immigration Officer','Import Policy Analyst','Industrial Hygienist','Information Management Specialist','Information Research Specialist','Information Resource Management Specialist','Information Technology Policy Analyst','Information Technology Security Assistant','Information Technology Specialist','Inspector','Instructional Systems Design Specialist','Instructions Methods Specialist','Insurance Marketing Specialist','Insurance Specialist','Intelligence Analyst','Intelligence Operations Specialist','Intelligence Research Specialist','Intelligence Specialist','Internal Program Specialist','Internal Revenue Agent','International Affairs Specialist','International Aviation Operations Specialist','International Cooperation Specialist','International Economist','International Project Manager','International Relations Specialist','International Trade Litigation Electronic Database C','International Trade Specialist','International Transportation Specialist','Investigator','Junior Foreign Affairs Officer','Labor Relations Specialist','Labor Relations Specialist','Learning Specialist','Legislative Assistant','Legislative Analyst','Legislative Specialist','Lender Approval Analyst','Lender Monitoring Analyst','Licensing Examining Specialist/Offices','Logistics Management Specialist','Managed Care Specialist','Management Analyst','Management and Budget Analyst','Management and Program Analyst','Management Intern','Management Support Analyst ','Management Support Specialist','Manpower Analyst','Manpower Development Specialist','Marketing Analyst','Marketing Specialist','Mass Communications Producer','Mathematical Statistician','Media Relations Assistant','Meteorologist','Microbiologist','Mitigation Program Specialist','National Security Training Technology','Natural Resources Specialist','Naval Architect','Operations Officer','Operations Planner','Operations Research Analyst','Operations Supervisor','Outdoor Recreation Planner','Paralegal Specialis','Passport/Visa Specialist','Personnel Management Specialist','Personnel Staffing and Classification Specialist','Petroleum Engineer','Physical Science Officer','Physical Scientist, General','Physical Security Specialist','Policy Advisor to the Director','Policy Analyst','Policy and Procedure Analyzt','Policy and Regulatory Analyst','Policy Coordinator','Policy/Program Analyst','Population/Family Planning Specialist','Position Classification Specialist','Presidential Management Fellow','Procurement Analyst','Procurement Specialist','Professional Relations Outreach','Program Administrator','Program Analyst','Program and Policy Analyst','Program Evaluation and Risk Analyst','Program Evolution Team Leader','Program Examiner','Program Manager','Program Operations Specialist','Program Specialist','Program Support Specialist','Program/Public Health Analyst','Project Analyst','Project Manager','Prototype Activities Coordinator','Psychologist (General)','Public Affairs Assistant','Public Affairs Intern','Public Affairs Specialist','Public Health Advisor','Public Health Analyst','Public Health Specialist','Public Liaison/Outreach Specialist','Public Policy Analyst','Quantitative Analyst','Real Estate Appraiser','Realty Specialist','Regional Management Analyst','Regional Technician','Regulatory Analyst','Regulatory Specialist','Research Analyst','Restructuring Analyst','Risk Analyst','Safety and Occupational Health Manager','Safety and Occupational Health Specialist','Safety Engineer/Industrial Hygienist','Science Program Analyst','Securities Compliance Examiner','Security Specialist','SeniorManagement Information Specialist','Social Insurance Analyst','Social Insurance Policy Specialist','Social Insurance Specialist','Social Science Analyst','Social Science Research Analyst','Social Scientist','South Asia Desk Officer','Special Assistant','Special Assistant for Foreign Policy Strategy','Special Assistant to the Associate Director','Special Assistant to the Chief Information Office','Special Assistant to the Chief, FBI National Security', 'Special Assistant to the Director','Special Emphasis Program Manager','Special Projects Analyst','Specialist','Staff Associate','Statistician','Supply Systems Analyst','Survey or Mathematical Statistician','Survey Statistician','Systems Accountant','Systems Analyst','Tax Law Specialist','Team Leader','Technical Writer/Editor','Telecommunications Policy Analyst','Telecommunications Specialist','Traffic Management Specialist','Training and Technical Assistant','Training Specialist','Transportation Analyst','Transportation Industry Analyst','Transportation Program Specialist','Urban Development Specialist','Usability Researcher','Veterans Employment Specialist','Video Production Specialist','Visa Specialist','Work Incentives Coordinator','Workers Compensation Specialist','Workforce Development Specialist','Worklife Wellness Specialist','Writer','Writer/Editor');

                    if(!empty($err_msg)) {
                      echo "<option value='".$_POST['title']."'>".$_POST['title']."</option>";
                    } else {
                      echo "<option value='' disabled selected>Select</option>";
                    } ?>
                    <?php
                      $count_ttl = 0;
                      while(!empty($arr_ttl[$count_ttl])) {
                        echo "<option value='".$arr_ttl[$count_ttl]."'>".$arr_ttl[$count_ttl]."</option>";
                          $count_ttl++;
                      } 
                    ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-6">
                <label>First Name</label>&nbsp;&nbsp;<span class="hido" id="hido1"><p id="error1" class="error"></p></span>
                <input type="text" class="form-control" id="bfname" name="bfname" onkeypress="return KeyPressFName(event)" onclick="clickField1()" value="<?php if(!empty($err_msg)){echo $_POST['bfname'];} ?>">
              </div>
              <div class="col-lg-6">
                <label>Last Name</label>&nbsp;&nbsp;<span class="hido" id="hido2"><p id="error2" class="error"></p></span>
                <input type="text" class="form-control" id="blname" name="blname" onkeypress="return KeyPressLName(event)" onclick="clickField2()" value="<?php if(!empty($err_msg)){echo $_POST['blname'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Email</label>&nbsp;&nbsp;<span class="hido" id="hido3"><p id="error3" class="error"></p></span>
                <input type="text" class="form-control" id="c-eadd" name="c-eadd" value="<?php if(empty($err_msg)){echo $p2_email;}else{echo $_POST['c-eadd'];} ?>" readonly>
              </div>
              <div class="col-lg-6">
                <label>Contact Number</label>&nbsp;&nbsp;<span class="hido" id="hido4"><p id="error4" class="error"></p></span>
                <input type="text" class="form-control" id="c-phone" name="c-phone" maxlength="10" onkeypress="return KeyPressPhone(event)" onclick="clickField4()" value="<?php if(!empty($err_msg)){echo $_POST['c-phone'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Billing Address 1</label>&nbsp;&nbsp;<span class="hido" id="hido5"><p id="error5" class="error"></p></span>
                <input type="text" class="form-control" id="c-street" name="c-street" onkeypress="return KeyPressStreet(event)" onclick="clickField5()" value="<?php if(empty($err_msg)){echo $p2_street;}else{echo $_POST['c-street'];} ?>">
              </div>
              <div class="col-lg-6">
                <label>Suite/Apartment Number</label>
                <input type="text" class="form-control" name="c-street2" value="<?php if(!empty($err_msg)){echo $_POST['c-street2'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>City</label>&nbsp;&nbsp;<span class="hido" id="hido6"><p id="error6" class="error"></p></span>
                <input type="text" class="form-control" id="c-city" name="c-city" onkeypress="return KeyPressCity(event)" onclick="clickField6()" value="<?php if(empty($err_msg)){echo $p2_city;}else{echo $_POST['c-city'];} ?>">
              </div>
              <div class="col-lg-3">
               <label>State</label>&nbsp;&nbsp;<span class="hido" id="hido7-state"><p id="error7-state" class="error"></p></span>
                <select class="form-control" name="c-state" id="c-state" onchange="ChangeState()">
                  <?php if(empty($err_msg) && !empty($p2_state)) {
                    echo "<option value='' id='option_1'>".$p2_state."</option>";
                  } else if(!empty($err_msg) && empty($p2_state)) {
                    echo "<option value='".$_POST['c-state']."' id='option_1'>".$_POST['c-state']."</option>";
                  } else {  
                    echo "<option value='' disabled selected>Select</option>";
                  } ?>
                  
                  <option value="AL">AL</option> 
                  <option value="AK">AK</option>
                  <option value="AZ">AZ</option> 
                  <option value="AR">AR</option> 
                  <option value="CA">CA</option> 
                  <option value="CO">CO</option> 
                  <option value="CT">CT</option> 
                  <option value="DE">DE</option> 
                  <option value="DC">DC</option> 
                  <option value="FL">FL</option> 
                  <option value="GA">GA</option> 
                  <option value="HI">HI</option> 
                  <option value="ID">ID</option> 
                  <option value="IL">IL</option> 
                  <option value="IN">IN</option> 
                  <option value="IA">IA</option> 
                  <option value="KS">KS</option> 
                  <option value="KY">KY</option> 
                  <option value="LA">LA</option> 
                  <option value="ME">ME</option> 
                  <option value="MD">MD</option> 
                  <option value="MA">MA</option> 
                  <option value="MI">MI</option> 
                  <option value="MN">MN</option> 
                  <option value="MS">MS</option> 
                  <option value="MO">MO</option> 
                  <option value="MT">MT</option> 
                  <option value="NE">NE</option> 
                  <option value="NV">NV</option> 
                  <option value="NH">NH</option> 
                  <option value="NJ">NJ</option> 
                  <option value="NM">NM</option> 
                  <option value="NY">NY</option> 
                  <option value="NC">NC</option> 
                  <option value="ND">ND</option> 
                  <option value="OH">OH</option> 
                  <option value="OK">OK</option> 
                  <option value="OR">OR</option> 
                  <option value="PA">PA</option> 
                  <option value="RI">RI</option> 
                  <option value="SC">SC</option> 
                  <option value="SD">SD</option> 
                  <option value="TN">TN</option> 
                  <option value="TX">TX</option> 
                  <option value="UT">UT</option> 
                  <option value="VT">VT</option> 
                  <option value="VA">VA</option> 
                  <option value="WA">WA</option> 
                  <option value="WV">WV</option> 
                  <option value="WI">WI</option> 
                  <option value="WY">WY</option>
                </select>
              </div>
              <div class="col-lg-3">
                <label>Zip</label>&nbsp;&nbsp;<span class="hido" id="hido7"><p id="error7" class="error"></p></span>
                <input type="text" class="form-control" id="c-zip" name="c-zip" maxlength="6" onkeypress="return KeyPressZip(event)" onclick="clickField7()" value="<?php if(empty($err_msg)){echo $p2_zip;}else{echo $_POST['c-zip'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Card Number</label>&nbsp;&nbsp;<span class="hido" id="hido8"><p id="error8" class="error"></p></span>
                <input type="text" class="form-control" id="card-number" name="card-number" onkeypress="return KeyPressCCNumber(event)" onclick="clickField8()" value="<?php if(!empty($err_msg)){echo $_POST['card-number'];} ?>">
              </div>
              <div class="col-lg-2">
               <label> CVC </label>
                <input type="text" class="form-control" id="card-cvc" name="card-cvc" maxlength="3" onkeypress="return KeyPressCVC(event)" onclick="clickField9()" value="<?php if(!empty($err_msg)){echo $_POST['card-cvc'];} ?>">
              </div>
              <div class="col-lg-4">
                <label>Exp. Date (mm/yy)</label>
                <div style="display: inline;">
                  <input type="text" class="form-control" style="float: left; width: 45%;"  maxlength="2" id="card-expiry-month" name="card-expiry-month" onkeypress="return KeyPressCCExpiryMM(event)" onclick="clickField10()" value="<?php if(!empty($err_msg)){echo $_POST['card-expiry-month'];} ?>">
                  <input type="text" class="form-control" style="float: left; width: 45%; margin-left: 5%;" maxlength="2" id="card-expiry-year" name="card-expiry-year" onkeypress="return KeyPressCCExpiryYY(event)" onclick="clickField11()" value="<?php if(!empty($err_msg)){echo $_POST['card-expiry-year'];} ?>">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Sales Agent</label>&nbsp;&nbsp;<span class="hido" id="hido12"><p id="error12" class="error"></p></span>
                <select class="form-control" id="sales-agent" name="sales-agent" onchange="ChangeAgent()">
                <?php 
                  if(!empty($err_msg)) {
                  echo "
                    <optgroup>
                      <option value='".$_POST['sales-agent']."'>".$_POST['sales-agent']."</option>
                    </optgroup>"; 
                  } else {
                    echo "<option value='' disabled selected>Select</option>";
                  }
                ?>
                  <option value="Bethany">Bethany</option>
                  <option value="Gem">Gem</option>
                  <option value="Jasper">Jasper</option>
                </select>
              </div>
              <div class="col-lg-3">
                <span class="hido" id="hido9"><p id="error9" class="error"></p></span>
              </div>
              <div class="col-lg-3">
                <span class="hido" id="hido10"><p id="error10" class="error"></p></span><br />
                <span class="hido" id="hido11"><p id="error11" class="error"></p></span>
              </div>
            </div>

              <div style="text-align: center; margin-top: 25px;">
                <span class="payment-errors">
                  <span id="error_check_all"><?php echo $err_msg; ?></span>
                  <span class="hido" id="error_check_all"><label id="error_check_all"></label></span>
                </span>
              </div>

              <div class="col-lg-9 col-lg-offset-4">
                <input type="submit" class="btn btn-primary" name="submit_billing_form" value="Submit">
              </div>
            </div>

          </fieldset>
        </form>
        </div>
      </div>
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/dataTables/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="js/field_trappings/enroll_form_b.js"></script>
  </div>

  <script>
    var x = document.getElementById("option_1_hidden_value").value;

    function setSelectValue (id, val) {
        document.getElementById(id).value = val;
    }
    setSelectValue('option_1', x);
  </script>
<?php } ?>
</body>
</html>