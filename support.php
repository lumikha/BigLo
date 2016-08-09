<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="../style.css">


    <form id="support_form">
        <div class="row">
            <div class="col-md-7">
                <input type="text" class="form-control" placeholder="Payment Processor ID ">
            </div>
            <div class="col-md-3">
                <select class="form-control">
                    <option>Trial</option>
                    <option>Active</option>
                    <option>Delinquent</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" placeholder="Account Activity Status">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                 <select class="form-control">
                 <option>Product 1</option>
                 <option>Product 2</option>
                 <option>Product 3</option>
                 <option>Product 4</option>
                 </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Lifetime Revenue">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Open/Next Task">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Last Update">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="well">
                    <strong>Update History:</strong>
                    <div class="well-lg">
                        <?php
                            for($i=1;$i<=10;$i++){
                                echo "<p>Update ".$i." <sup>( Date & Time )</sup></p>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

