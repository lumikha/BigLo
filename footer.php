</div>
</body>
</html>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/angular.min.js"></script>

<script type="text/javascript">
	$(document).ready(function () {
        check();
    });

	var myapp = angular.module("myapp",[]);
		myapp.controller("newController", function($scope,$http){
			$http.get("couchDB/search.php").success(function(response){
				$scope.users = response;
			});
			$scope.num = 5;
		});

	function check(){
		var search = document.forms["myForm"]["search"].value;
		if(search) {
			document.getElementById('output').style.visibility = "visible";
			document.getElementById("search_result_view").style.zIndex = "10";
		} else {
			document.getElementById('output').style.visibility = "hidden";
			document.getElementById("search_result_view").style.zIndex = "0";
		}
	}
		
	function getSearch(){
		var search = document.forms["myForm"]["search"].value;
		window.location.href = "results.php?search="+search;
	}
</script>

<?php
if(isset($_SESSION['user_now_db_customer_id'])) {
	$char_state = $result_customer_id[0]->state;
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
	</script>
		<script>
		    $(document).ready(function () {
		        $('#acc_tab1').addClass('active');
		        document.getElementById('acc_dashboard_form').style.display = 'none';
		    });

		    function acc_onNavTab1() {
		    	$('#acc_tab1').addClass('active');
		    	$('#acc_tab2').removeClass('active');
		    	document.getElementById('acc_account_form').style.display = 'block';
		    	document.getElementById('acc_dashboard_form').style.display = 'none';
		    }

		    function acc_onNavTab2() {
		    	$('#acc_tab1').removeClass('active');
		    	$('#acc_tab2').addClass('active');
		    	$('#acc_dashboard_form').removeClass('hidden');
		    	document.getElementById('acc_account_form').style.display = 'none';
		    	document.getElementById('acc_dashboard_form').style.display = 'block';
		    }
		</script>
	<?php
}
?>

<script>
	function CapCom(e) {
		var keyCom = window.event? event : e
		if (keyCom.ctrlKey && keyCom.keyCode == 88){ //combination is ctrl + q
			window.location = "logout.php";
		}
	}
	document.onkeydown = CapCom;
</script>

<?php
	if(basename($_SERVER['PHP_SELF']) == "customer.php" || "customer2.php") {
	?>
		<script>
		    $(document).ready(function () {
		        $('#cust_tab1').addClass('active');
		        document.getElementById('cust_provisioning_form').style.display = 'none';
		        document.getElementById('cust_billing_form').style.display = 'none';
		        document.getElementById('cust_support_form').style.display = 'none';
		        document.getElementById('cust_dashboard_form').style.display = 'none';
		        document.getElementById('cust_admin_form').style.display = 'none';
		    });

		    function cust_onNavTab1() {
		    	$('#cust_tab1').addClass('active');
		    	$('#cust_tab2').removeClass('active');
		    	$('#cust_tab3').removeClass('active');
		    	$('#cust_tab4').removeClass('active');
		    	$('#cust_tab5').removeClass('active');
		    	$('#cust_tab6').removeClass('active');
		    	document.getElementById('cust_account_form').style.display = 'block';
		    	document.getElementById('cust_provisioning_form').style.display = 'none';
		    	document.getElementById('cust_billing_form').style.display = 'none';
		    	document.getElementById('cust_support_form').style.display = 'none';
		        document.getElementById('cust_dashboard_form').style.display = 'none';
		        document.getElementById('cust_admin_form').style.display = 'none';
		    }

		    function cust_onNavTab2() {
		    	$('#cust_tab1').removeClass('active');
		    	$('#cust_tab2').addClass('active');
		    	$('#cust_tab3').removeClass('active');
		    	$('#cust_tab4').removeClass('active');
		    	$('#cust_tab5').removeClass('active');
		    	$('#cust_tab6').removeClass('active');
		    	document.getElementById('cust_account_form').style.display = 'none';
		    	document.getElementById('cust_provisioning_form').style.display = 'block';
		    	document.getElementById('cust_billing_form').style.display = 'none';
		    	document.getElementById('cust_support_form').style.display = 'none';
		        document.getElementById('cust_dashboard_form').style.display = 'none';
		        document.getElementById('cust_admin_form').style.display = 'none';
		    }

		    function cust_onNavTab3() {
		    	$('#cust_tab1').removeClass('active');
		    	$('#cust_tab2').removeClass('active');
		    	$('#cust_tab3').addClass('active');
		    	$('#cust_tab4').removeClass('active');
		    	$('#cust_tab5').removeClass('active');
		    	$('#cust_tab6').removeClass('active');
		    	document.getElementById('cust_account_form').style.display = 'none';
		    	document.getElementById('cust_provisioning_form').style.display = 'none';
		    	document.getElementById('cust_billing_form').style.display = 'block';
		    	document.getElementById('cust_support_form').style.display = 'none';
		        document.getElementById('cust_dashboard_form').style.display = 'none';
		        document.getElementById('cust_admin_form').style.display = 'none';
		    }

		    function cust_onNavTab4() {
		    	$('#cust_tab1').removeClass('active');
		    	$('#cust_tab2').removeClass('active');
		    	$('#cust_tab3').removeClass('active');
		    	$('#cust_tab4').addClass('active');
		    	$('#cust_tab5').removeClass('active');
		    	$('#cust_tab6').removeClass('active');
		    	document.getElementById('cust_account_form').style.display = 'none';
		    	document.getElementById('cust_provisioning_form').style.display = 'none';
		    	document.getElementById('cust_billing_form').style.display = 'none';
		    	document.getElementById('cust_support_form').style.display = 'block';
		        document.getElementById('cust_dashboard_form').style.display = 'none';
		        document.getElementById('cust_admin_form').style.display = 'none';
		    }

		    function cust_onNavTab5() {
		    	$('#cust_tab1').removeClass('active');
		    	$('#cust_tab2').removeClass('active');
		    	$('#cust_tab3').removeClass('active');
		    	$('#cust_tab4').removeClass('active');
		    	$('#cust_tab5').addClass('active');
		    	$('#cust_tab6').removeClass('active');
		    	document.getElementById('cust_account_form').style.display = 'none';
		    	document.getElementById('cust_provisioning_form').style.display = 'none';
		    	document.getElementById('cust_billing_form').style.display = 'none';
		    	document.getElementById('cust_support_form').style.display = 'none';
		        document.getElementById('cust_dashboard_form').style.display = 'block';
		        document.getElementById('cust_admin_form').style.display = 'none';
		    }

		    function cust_onNavTab6() {
		    	$('#cust_tab1').removeClass('active');
		    	$('#cust_tab2').removeClass('active');
		    	$('#cust_tab3').removeClass('active');
		    	$('#cust_tab4').removeClass('active');
		    	$('#cust_tab5').removeClass('active');
		    	$('#cust_tab6').addClass('active');
		    	document.getElementById('cust_account_form').style.display = 'none';
		    	document.getElementById('cust_provisioning_form').style.display = 'none';
		    	document.getElementById('cust_billing_form').style.display = 'none';
		    	document.getElementById('cust_support_form').style.display = 'none';
		        document.getElementById('cust_dashboard_form').style.display = 'none';
		        document.getElementById('cust_admin_form').style.display = 'block';
		    }
		</script>
	<?php
	}
?>
	