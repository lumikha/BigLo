</div>
</body>
</html>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/angular.min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script type="text/javascript" src="js/jquery.tagsinput.js"></script>

<script type="text/javascript">

		function onAddTag(tag) {
			alert("Added a tag: " + tag);
		}
		function onRemoveTag(tag) {
			alert("Removed a tag: " + tag);
		}

		function onChangeTag(input,tag) {
			alert("Changed a tag: " + tag);
		}

		$(function() {

			$('#k-words').tagsInput({width:'auto'});

		});

</script>

<script type="text/javascript">
    $(function() { 
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            //save the latest tab using a cookie:
            $.cookie('last_tab', $(e.target).attr('href'));
        });
        //activate latest tab, if it exists:
        var lastTab = $.cookie('last_tab');
        if (lastTab) {
            $('a[href=' + lastTab + ']').tab('show');
        }
        else
        {
            // Set the first tab if cookie do not exist
            $('a[data-toggle="tab"]:first').tab('show');
        }
    });

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