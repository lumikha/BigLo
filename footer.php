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
		/*
		if(search == null || search=="" || search==" ") {
			document.getElementById('output').style.visibility = "hidden";
			document.getElementById("search_result_view").style.zIndex = "0";
			//jQuery("#output").fadeOut();
		}
		else{
			document.getElementById('output').style.visibility = "visible";
			document.getElementById("search_result_view").style.zIndex = "10";
			//jQuery("#output").fadeIn();
		}*/
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

	if($result_customer_id[0]->state == "trialing") {
		?><script>
		document.getElementById("cust_id").title = document.getElementById("char_state").value;
		</script><?php
	} elseif($result_customer_id[0]->state == "active") {
		?><script>
		document.getElementById("cust_id").title = document.getElementById("char_state").value;
		</script><?php
	} elseif($result_customer_id[0]->state == "past_due") {
		?><script>
		document.getElementById("cust_id").title = document.getElementById("char_state").value;
		</script><?php
	} elseif($result_customer_id[0]->state == "unpaid") {
		?><script>
		document.getElementById("cust_id").title = document.getElementById("char_state").value;
		</script><?php
	} elseif($result_customer_id[0]->state == "canceled") {
		?><script>
		document.getElementById("cust_id").title = document.getElementById("char_state").value;
		</script><?php
	} else {
		?><script>
		document.getElementById("cust_id").title = document.getElementById("char_state").value;
		</script><?php
	}
?>

<?php if(basename($_SERVER['PHP_SELF']) == "account.php") { ?>
	<script>
    $(document).ready(function () {
        $('#tab1').addClass('active');
    });
    </script>
<?php } elseif(basename($_SERVER['PHP_SELF']) == "sales.php") { ?>
	<script>
    $(document).ready(function () {
        $('#tab2').addClass('active');
    });
    </script>
<?php } elseif(basename($_SERVER['PHP_SELF']) == "provisioning.php") { ?>
	<script>
    $(document).ready(function () {
        $('#tab3').addClass('active');
    });
    </script>
<?php } else { ?>

<?php	
	}
} 
?>

<script>
	function CapCom(e) {
		var keyCom = window.event? event : e
		if (keyCom.ctrlKey && keyCom.keyCode == 88){ //combination is ctrl + q
			window.location = "logout.php";
		}s
	}
	document.onkeydown = CapCom;
</script>
	