<?php
	$search = @$_GET['search'];
?>
<html>
<head>
	<title></title>
</head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script type="text/javascript" src="js/angular.min.js"></script>


	<style type="text/css">
		.hidden{
	  	display: none; 
		}

	</style>

<script type="text/javascript">
	var myapp = angular.module("myapp",[]);
		myapp.controller("newController", function($scope,$http,$filter){
			$http.get("couchDB/search.php").success(function(response){
				$scope.users = response;
			});
			$scope.num = 10;
			//$scope.search = "<?php echo $search; ?>";

			$scope.currentPage = 0;
    		$scope.pageSize = 10;
    		$scope.q = '<?php echo $search; ?>';

    		$scope.getData = function () {
		      return $filter('filter')($scope.users, $scope.q)
		    }
		    $scope.numberOfPages=function(){
        		return Math.ceil($scope.getData().length/$scope.pageSize);                
    		}
 

		});
		myapp.filter('startFrom', function() {
		    return function(input, start) {
		        start = +start; //parse to int
		        return input.slice(start);
		    }
		});

	function check(){
		var search = document.forms["myForm"]["search"].value;
		if(search == null || search=="" || search==" "){	
			//document.getElementById('output').style.visibility = "hidden";
			//jQuery("#output").fadeOut();
		}
		else{
			//document.getElementById('output').style.visibility = "visible";
			//jQuery("#output").fadeIn();
		}
	}

	$(document).ready(function() {
	  // toggle advanced search
	  $("#toggleContainer").hide();
	  $("#trigger").click(function() {
	    $("#toggleContainer").toggle();
	  });
	});


	
	
</script>
<body onload="check();">

	
		<div ng-app="myapp">
			<div ng-controller="newController">
			<div class="well-lg">
			<div class="row">
				<div class="col-md-offset-1 col-md-6">
					<form name="myForm">
						<input type="text" class="form-control" onkeyup="return check();" id="search" size="30" name="search" ng-model="q" placeholder="Search">
					</form>
					<h4><strong>Results:</strong></h4><br>
					<span name="output" id="output">
							<ul style="list-style: none;">
								<li ng-repeat="user in result = (users | filter:q | startFrom:currentPage*pageSize | limitTo:pageSize)">
									<a href="?id={{ user._id }}" name="value"><h3>
									{{user.business_name}}</h3></a>
									<strong>Chargify ID:</strong> {{user.chargify_id}}, <strong>Customer:</strong> <a href="">{{user.customer_first_name}} {{user.customer_last_name}}</a>, <strong>Email:</strong> {{user.business_email}}
									<hr class="featurette-divider">
								</li>
								<h3><p ng-hide="result.length">Opps, No Results Found ...</p></h3><br>
							</ul>
					</span> 

						<button class="btn btn-info" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1">
					        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
					    </button>
							<span> Page {{currentPage+1}} of {{numberOfPages()}} </span>
						<button class="btn btn-info" ng-disabled="currentPage >= getData().length/pageSize - 1" ng-click="currentPage=currentPage+1">
					        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					    </button>
				</div>
			</div>
			</div>
			</div>
		</div>

</body>
</html>