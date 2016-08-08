<?php 
    require 'functions.php';
    $current_page = basename($_SERVER['PHP_SELF']);
    if(!isset($_SESSION['user_now_id'])) { 
        header("Location: login");
    } else {
        if($_SESSION['user_now_access_level'] == "Customer") {
            if($current_page == "customer.php") {
                header("Location: noaccess");
            }
        } else {
            if($current_page == "account.php" || $current_page == "sales.php" || $current_page == "provisioning.php") {
                header("Location: noaccess");
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LiLDip</title>
</head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">
<body>
<div class="container-full">
    <div class="col-md">
        <p style="float: left;">Hello <?php echo $fname; ?>!</p>
        <div class="user_opt dropdown" style="float: left;">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenu1">
                <li><a href="#">My Profile</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <?php if(!isset($_SESSION['user_now_db_customer_id'])) { ?>
    <div class="col-md-2 col-md-offset-7">
        <div ng-app="myapp" id="search_result_view" style="position: absolute;" >
            <div ng-controller="newController">
                <div id="toggleContainer">
                    <form name="myForm">
                        <input type="text" class="form-control" onkeyup="return check();" id="search" name="search" ng-model="search" placeholder="Search" autocomplete="off" >
                    </form>
                    <div name="output" id="output" ng-cloak>
                        <div class="list-group">
                            <a class="list-group-item" ng-if="search" ng-repeat="user in result = ( users | filter:search | limitTo:num)" href="?id={{ user.chargify_id }}">
                                <span style="font-size:130%" class="text-info"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> {{user.business_name}}</span><br>
                                <span style="font-size:75%"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> {{user.customer_first_name}} {{user.customer_last_name}}, 
                                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>  {{user.business_email}}
                                    </span>
                            </a>
                            <a class="list-group-item" ng-if="search" ng-hide="result.length">Opps, No Results Found ...</a>
                            <a class="list-group-item text-right" ng-if="search" href="#" onclick="getSearch();">View More Results <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a>    
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<div class="container">
    <div class="row">
        <ul class="navtabs nav nav-pills nav-justified">
            <?php 
            if(isset($_SESSION['user_now_db_customer_id'])) { 
                if(basename($_SERVER['PHP_SELF']) == "account.php") { 
                    ?><li id="tab1" class="active"><a href=account>Account</a></li><?php
                } else {
                     ?><li id="tab1"><a href=account>Account</a></li><?php
                } 

                if(basename($_SERVER['PHP_SELF']) == "sales.php") { 
                    ?><li id="tab2" class="alter_tab active"><a href="sales">Sales</a></li><?php
                } else {
                    ?><li id="tab2" class="alter_tab"><a href="sales">Sales</a></li><?php
                }

                if(basename($_SERVER['PHP_SELF']) == "provisioning.php") {
                    ?><li id="tab3" class="active"><a href="provisioning">Provisioning</a></li><?php
                } else {
                    ?><li id="tab3"><a href="provisioning">Provisioning</a></li><?php
                }
            ?>
            <li id="tab4" class="alter_tab"><a href="#">Billing</a></li>
            <li id="tab5"><a href="#">Support</a></li>
            <li id="tab6" class="alter_tab"><a href="#">Dashboard</a></li>
            <li id="tab7"><a href="#">Admin</a></li>
            <?php } ?>
        </ul>
    </div>
    <?php if(isset($_SESSION['user_now_db_customer_id'])) { ?>
    <div class="row">
        <div class="col-md-6">
            <p class="cust_id" id="cust_id" title=""><?php echo $chargifyID; ?></p>
            <p class="bill_sum"><?php echo $billing_sum; ?></p>
            <p class="last_activity"><?php echo $char_upd_at; ?></p>
        </div>
    </div>
    <?php } ?>