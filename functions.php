<?php 
	require 'couchDB/dbConnect.php';
	require 'Chargify-PHP-Client/lib/Chargify.php';

	$result_db_users = $client_users->getView('users','viewAll');
	$result_db_customers = $client_customers->getView('customers','viewAll');

	session_start();

	$found=true;
	if(isset($_POST['login_btn']) || (isset($_GET['e']) && isset($_GET['p']))) {
		if(isset($_GET['e']) && isset($_GET['p'])) {
			$email = $_GET['e'];
			$pass = $_GET['p'];
		} else {
			$email = $_POST['email'];
			$pass = $_POST['password'];
		}

		$i=0;
		while(isset($result_db_users->rows[$i])) {
			if(($result_db_users->rows[$i]->value->email == $email) && ($result_db_users->rows[$i]->value->password == $pass)) {
				$_SESSION['user_now_id'] = $result_db_users->rows[$i]->id;
				$_SESSION['user_now_email'] = $email;
				$_SESSION['user_now_access_level'] = $result_db_users->rows[$i]->value->userType;
				if($result_db_users->rows[$i]->value->userType == 'Customer') {
					$_SESSION['user_now_db_customer_id'] = $result_db_users->rows[$i]->value->customer_id;
					?>
					<script>
						window.location = "account"; //User Dashboard
					</script>
					<?php
				} else {
					$_SESSION['user_now_fname'] = $result_db_users->rows[$i]->value->user_first_name;
					$_SESSION['user_now_lname'] = $result_db_users->rows[$i]->value->user_last_name;
					?>
					<script>
						window.location = "customer"; //Admin/Agent Dashboard
					</script>
					<?php
				}
			} else {
				$found=false;
			}
			$i++;
		}
	}

	if(isset($_SESSION['user_now_id'])) {
		if(isset($_SESSION['user_now_db_customer_id'])) {
			$i=0;
			while(isset($result_db_customers->rows[$i])) {
				if($result_db_customers->rows[$i]->id == $_SESSION['user_now_db_customer_id']) {
					$email = $result_db_customers->rows[$i]->value->customer_email;
					$fname = $result_db_customers->rows[$i]->value->customer_first_name;
					$lname = $result_db_customers->rows[$i]->value->customer_last_name;
					$chargifyID = $result_db_customers->rows[$i]->value->chargify_id;
					$chargifyID = $result_db_customers->rows[$i]->value->chargify_id;
                	$salutation = $result_db_customers->rows[$i]->value->customer_salutation;
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
				}
				$i++;
			}
			
			$test = true;
			$subscription = new ChargifySubscription(NULL, $test);

			try {
				$result_customer_id = $subscription->getByCustomerID($chargifyID);
			} catch (ChargifyValidationException $cve) {
				  echo $cve->getMessage();
			}

			if($result_customer_id[0]->state == "trialing") {
				?><style>
				.cust_id {
					color: #b300b3;
				}
				</style><?php
			} elseif($result_customer_id[0]->state == "active") {
				?><style>
				.cust_id {
					color: #28B22C;
				}
				</style><?php
			} elseif($result_customer_id[0]->state == "past_due") {
				?><style>
				.cust_id {
					color: #e6e600;
				}
				</style><?php
			} elseif($result_customer_id[0]->state == "unpaid") {
				?><style>
				.cust_id {
					color: #ff0000;
				}
				</style><?php
			} elseif($result_customer_id[0]->state == "canceled") {
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

			$billing_sum = "$".number_format(($result_customer_id[0]->total_revenue_in_cents /100), 2, '.', ' ');
			$fin = explode('T',$result_customer_id[0]->updated_at,-1);
			$fin2 = explode('-',$fin[0]);
			$char_upd_at = $fin2[1].".".$fin2[2].".".$fin2[0];
			$business_name = $result_customer_id[0]->customer->organization;


			//for agent search customerID
			if($result_customer_id[0]->state == "trialing") {
				$cust_search_state = "Trial Ended: ".$result_customer_id[0]->trial_ended_at;
			} elseif($result_customer_id[0]->state == "active") {
				$cust_search_state = "Next Billing: ".$result_customer_id[0]->next_billing_at;
			} else {
				$cust_search_state = "Cancelled At: ".$result_customer_id[0]->canceled_at;
			}

		} else {
			$fname = $_SESSION['user_now_fname'];
			$lname = $_SESSION['user_now_lname'];
		}
	}
?>
	