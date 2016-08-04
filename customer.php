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
                //$salutation = $result_db_users->rows[$i]->value->Salutation;
                $sales_date = $result_db_customers->rows[$i]->value->sale_date;
                $sales_agent = $result_db_customers->rows[$i]->value->sale_agent;
                $sales_center = $result_db_customers->rows[$i]->value->sale_center;
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

            //for agent search customerID
            if($result_customer_id_search[0]->state == "trialing") {
                $cust_search_state = "Trial Ended: ".$result_customer_id_search[0]->trial_ended_at;
            } elseif($result_customer_id_search[0]->state == "active") {
                $cust_search_state = "Next Billing: ".$result_customer_id_search[0]->next_billing_at;
            } else {
                $cust_search_state = "Cancelled At: ".$result_customer_id_search[0]->canceled_at;
            }
    }

    if(isset($_POST['upd_acc'])) {
        $bname = stripslashes($_POST['acc-b-name']);
        $prod = $_POST['acc-prod'];
        $fname = $_POST['acc-fname'];
        $lname = $_POST['acc-lname'];

        $test = true;
        $customer = new ChargifyCustomer(NULL, $test);

        $customer->id = $_GET['id'];
        $customer->organization = $bname;
        $customer->first_name = $fname;
        $customer->last_name = $lname;

        try {
            $result_upd_cus = $customer->update();

            $client_customer = new couchClient ('http://127.0.0.1:5984','bigloco-customers');

            try {
                $doc = $client_customer->getDoc($customer_db_id);
              } catch (Exception $e) {
                echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
              }

            $doc->business_name = @$bname;
            $doc->customer_first_name = @$fname;
            $doc->customer_last_name = @$lname;

            try {
                $client_customer->storeDoc($doc);
            } catch (Exception $e) {
                echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
            }

        } catch (ChargifyValidationException $cve) {
            echo $cve->getMessage();
        }
    }
?>
    <div class="row">
        <ul class="navtabs nav nav-pills nav-justified">
            <li id="cust_tab1" class="active" onclick="cust_onNavTab1()"><a href=#>Account</a></li>
            <li id="cust_tab2" class="alter_tab" onclick="cust_onNavTab2()"><a href="#">Sales</a></li>
            <li id="cust_tab3"><a href="#" onclick="cust_onNavTab3()">Provisioning</a></li>
            <li id="cust_tab4" class="alter_tab" onclick="cust_onNavTab4()"><a href="#">Billing</a></li>
            <li id="cust_tab5"><a href="#" onclick="cust_onNavTab5()">Support</a></li>
            <li id="cust_tab6" class="alter_tab" onclick="cust_onNavTab6()"><a href="#">Dashboard</a></li>
            <li id="cust_tab7"><a href="#" onclick="cust_onNavTab7()">Admin</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p class="cust_id" id="cust_id" title=""><?php echo $chargifyID; ?></p>
            <p class="bill_sum"><?php echo $billing_sum; ?></p>
            <p class="last_activity"><?php echo $char_upd_at; ?></p>
        </div>
    </div>

    <!-- Customer's Account Info -->
    <form  action="" method="POST" id="cust_account_form">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="acc-b-name" class="form-control" placeholder="Business Name" value="<?php echo $business_name; ?>">
            </div>
            <div class="col-md-5">
                <select class="form-control" name="acc-prod" placeholder="Product">
                    <optgroup label="Current">
                        <?php 
                        if(isset($_GET['id'])) {
                            echo "<option value='".$result_customer_id_search[0]->product->handle."'>".$result_customer_id_search[0]->product->name."</option>"; 
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
            <div class="col-md-1">
                <button class="btn btn-danger" type="submit" name="upd_acc">Ticket</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                 <select name="acc-salut" class="form-control">
                 <?php
                    if($salutation == "Mr.") {
                        echo "<option value='Mr.'>Mr</option>";
                        echo "<option value='Ms.'>Ms</option>";
                    } else {
                        echo "<option value='Ms.'>Mr</option>";
                        echo "<option value='Mr.'>Ms</option>";
                    }
                 ?>
                 </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="acc-fname" class="form-control" placeholder="First Name" value="<?php echo $fname; ?>">
            </div>
            <div class="col-md-5">
                <input type="text" name="acc-lname" class="form-control" placeholder="Last Name" value="<?php echo $lname; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="acc-title" class="form-control" placeholder="Title">
            </div>
        </div>
    </form>

    <!-- Customer's Sales Info -->
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
                        if(empty($result_customer_id_search[0]->product->handle)) { 
                            echo "<option value=''>None</option>";
                        } else {
                            echo "<option value='".$result_customer_id_search[0]->product->handle."'>".$result_customer_id_search[0]->product->name."</option>";
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
                        if(empty($result_customer_id_search[0]->components)) { 
                            echo "<option value=''>None</option>";
                        } else {
                            echo "<option value='".$result_customer_id_search[0]->components->name."'>".$result_customer_id_search[0]->components->id."</option>";
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
                        if(empty($result_customer_id_search[0]->coupon_code)) { 
                            echo "<option value=''>None</option>";
                        } else {
                            echo "<option value='".$result_customer_id_search[0]->coupon_code."'>".$result_customer_id_search[0]->coupon_code->name."</option>"; 
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


<?php
    require "footer.php";
?>