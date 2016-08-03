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
      $doc->customer_first_name = @$_POST['bfname'];
      $doc->customer_last_name = @$_POST['blname'];
      $doc->customer_email = @$_POST['c-eadd'];
      $doc->customer_phone_no = @$btn_number ;
      $doc->customer_billing_address = @$_POST['c-street'];
      $doc->customer_suite_no = @$_POST['c-street2'];
      $doc->customer_billing_city = @$_POST['c-city'];
      $doc->customer_billing_state = @$_POST['c-state'];
      $doc->customer_billing_zip = @$_POST['c-zip'];
      $doc->customer_card_last_four = substr($_POST['card-number'], -4);
      $doc->customer_card_cvc = @$_POST['card-cvc'];
      $doc->customer_card_expire_month = @$_POST['card-expiry-month'];
      $doc->customer_card_expire_year = @$_POST['card-expiry-year'];
      $doc->sale_date = date("m/d/Y");
      $doc->sale_center = @$_POST['sales-center'];
      $doc->sale_agent = @$_POST['sales-agent'];

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
              <label>State</label>
              <select class="form-control" id="biz-state" name="biz-state">
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
          <input type="hidden" name="product-handle" value="plan_003">
          <input type="hidden" name="sales-center" value="BIGLO_SALES_CENTER">
          <input type="hidden" id="option_1_hidden_value" 
          value="<?php if(empty($err_msg)){echo $p2_state;}else{echo $_POST['c-state'];} ?>">
          <fieldset>
            <h2>Billing Information</h2><br> 

            <div class="form-group">
              <div class="col-lg-12">
                <label>Business Name</label>
                <input type="text" class="form-control" id="bussiness-name" name="bussiness-name" value="<?php if(empty($err_msg)){echo $p2_bname;}else{echo $_POST['bussiness-name'];} ?>" >
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>First Name</label>&nbsp;&nbsp;<span class="hido" id="hido1"><p id="error1" class="error"></p></span>
                <input type="text" class="form-control" id="bfname" name="bfname" value="<?php if(!empty($err_msg)){echo $_POST['bfname'];} ?>">
              </div>
              <div class="col-lg-6">
                <label>Last Name</label>&nbsp;&nbsp;<span class="hido" id="hido2"><p id="error2" class="error"></p></span>
                <input type="text" class="form-control" id="blname" name="blname" value="<?php if(!empty($err_msg)){echo $_POST['blname'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Email</label>&nbsp;&nbsp;<span class="hido" id="hido3"><p id="error3" class="error"></p></span>
                <input type="text" class="form-control" id="c-eadd" name="c-eadd" value="<?php if(empty($err_msg)){echo $p2_email;}else{echo $_POST['c-eadd'];} ?>" readonly>
              </div>
              <div class="col-lg-6">
                <label>Contact Number</label>&nbsp;&nbsp;<span class="hido" id="hido4"><p id="error4" class="error"></p></span>
                <input type="text" class="form-control" id="c-phone" name="c-phone" maxlength="10" value="<?php if(!empty($err_msg)){echo $_POST['c-phone'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Billing Address 1</label>&nbsp;&nbsp;<span class="hido" id="hido5"><p id="error5" class="error"></p></span>
                <input type="text" class="form-control" id="c-street" name="c-street" value="<?php if(empty($err_msg)){echo $p2_street;}else{echo $_POST['c-street'];} ?>">
              </div>
              <div class="col-lg-6">
                <label>Suite/Apartment Number</label>
                <input type="text" class="form-control" name="c-street2" value="<?php if(!empty($err_msg)){echo $_POST['c-street2'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>City</label>&nbsp;&nbsp;<span class="hido" id="hido6"><p id="error6" class="error"></p></span>
                <input type="text" class="form-control" id="c-city" name="c-city" value="<?php if(empty($err_msg)){echo $p2_city;}else{echo $_POST['c-city'];} ?>">
              </div>
              <div class="col-lg-3">
               <label> State</label>
                <select class="form-control" name="c-state">
                  <?php if(empty($err_msg) && !empty($p2_state)) {
                    echo "<option value='' id='option_1'>".$p2_state."</option>";
                  } else if(!empty($err_msg) && empty($p2_state)) {
                    echo "<option value='' id='option_1'>".$_POST['c-state']."</option>";
                  } else { ?>
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
                  <?php } ?>
                </select>
              </div>
              <div class="col-lg-3">
                <label>Zip</label>&nbsp;&nbsp;<span class="hido" id="hido7"><p id="error7" class="error"></p></span>
                <input type="text" class="form-control" id="c-zip" name="c-zip" maxlength="6" value="<?php if(empty($err_msg)){echo $p2_zip;}else{echo $_POST['c-zip'];} ?>">
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Card Number</label>&nbsp;&nbsp;<span class="hido" id="hido8"><p id="error8" class="error"></p></span>
                <input type="text" class="form-control" id="card-number" name="card-number" value="<?php if(!empty($err_msg)){echo $_POST['card-number'];} ?>">
              </div>
              <div class="col-lg-2">
               <label> CVC </label>
                <input type="text" class="form-control" id="card-cvc" name="card-cvc" maxlength="4" value="<?php if(!empty($err_msg)){echo $_POST['card-cvc'];} ?>">
              </div>
              <div class="col-lg-4">
                <label>Exp. Date (mm/yy)</label>
                <div style="display: inline;">
                  <input type="text" class="form-control" style="float: left; width: 45%;"  maxlength="2" id="card-expiry-month" name="card-expiry-month" value="<?php if(!empty($err_msg)){echo $_POST['card-expiry-month'];} ?>">
                  <input type="text" class="form-control" style="float: left; width: 45%; margin-left: 5%;" maxlength="2" id="card-expiry-year" name="card-expiry-year" value="<?php if(!empty($err_msg)){echo $_POST['card-expiry-year'];} ?>">
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-lg-6">
                <label>Sales Agent</label>
                <select class="form-control" id="sales-agent" name="sales-agent">
                <?php 
                  if(!empty($err_msg)) {
                  echo "
                    <optgroup>
                      <option value='".$_POST['sales-agent']."'>".$_POST['sales-agent']."</option>
                    </optgroup>"; 
                  }
                ?>
                  <optgroup>
                    <option value="Juan">Juan</option>
                    <option value="Jose">Jose</option>
                  </optgroup>
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