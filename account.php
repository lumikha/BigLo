<?php
    require 'header.php';
?>

    <form id="caccount_form">
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Business Name" value="<?php echo $business_name; ?>">
            </div>
            <div class="col-md-5">
                <select class="form-control">
                    <optgroup label="Current">
                        <?php echo "<option value='".$result_customer_id[0]->product->handle."'>".$result_customer_id[0]->product->name."</option>"; ?>
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
                <button class="btn btn-danger" type="submit">Ticket</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                 <select class="form-control">
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
                <input type="text" class="form-control" placeholder="First Name" value="<?php echo $fname; ?>">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" placeholder="Last Name" value="<?php echo $lname; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Title">
            </div>
        </div>
    </form>

<?php
    require "footer.php";
?>