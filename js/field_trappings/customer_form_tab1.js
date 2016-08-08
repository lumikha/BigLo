function checkFields_cust_tab1(){
	var checkError = new Array();
	var cID = document.getElementById("cID").value;

	if (cID) {
	    $('#hido1').addClass('hido'); }
	else {
	    $('#hido1').removeClass('hido');
	    $('#hido1').removeClass('hidob'); 
	    document.getElementById("error1").innerHTML = "No customer selected";
	    checkError.push("1"); }

	if(checkError != "")
    {
        if(checkError[0] == "1" || checkError[0] == "1a") { $('#cID').focus(); }
        return false;
    }
    else
    {
    	return true;
    }
}