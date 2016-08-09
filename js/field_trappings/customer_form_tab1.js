function checkFields_cust_tab1(){
	var checkError = new Array();
	var cID = document.getElementById("cID").value,
		bname = document.getElementById("acc-b-name").value,
		fname = document.getElementById("acc-fname").value,
		lname = document.getElementById("acc-lname").value;

	if (cID) {
	    $('#hido1').addClass('hido'); }
	else {
	    $('#hido1').removeClass('hido');
	    $('#hido1').removeClass('hidob'); 
	    document.getElementById("error1").innerHTML = "No customer selected";
	    checkError.push("1"); }

	if (bname) {
	    if(/^\s/.test(bname)) {
	    	$('#acc-b-name').addClass('error_field');
	        checkError.push("2a"); }
	    else {
	        $('#hido2').addClass('hido'); } }
	else {
		$('#acc-b-name').addClass('error_field');
	    checkError.push("2"); }

	if (fname) {
	    if(/^\s/.test(fname)) {
	    	$('#acc-fname').addClass('error_field');
	        checkError.push("3a"); }
	    else {
	        $('#hido3').addClass('hido'); } }
	else {
		$('#acc-fname').addClass('error_field');
	    checkError.push("3"); }

	if (lname) {
	    if(/^\s/.test(lname)) {
	    	$('#acc-lname').addClass('error_field');
	        checkError.push("4a"); }
	    else {
	        $('#hido4').addClass('hido'); } }
	else {
		$('#acc-lname').addClass('error_field');
	    checkError.push("4"); }

	if(checkError != "")
    {
    	/*
        if(checkError[0] == "1") { $('#cID').focus(); }
        if(checkError[0] == "2" || checkError[0] == "2a") { $('#acc-b-name').focus(); }
        if(checkError[0] == "3" || checkError[0] == "3a") { $('#acc-fname').focus(); }
        if(checkError[0] == "4" || checkError[0] == "4a") { $('#acc-lname').focus(); }
        */
        return false;
    }
    else
    {
    	return true;
    }
}

	function BName(){
		$('#acc-b-name').removeClass('error_field');
	}

	function FName(){
		$('#acc-fname').removeClass('error_field');
	}

	function LName(){
		$('#acc-lname').removeClass('error_field');
	}
