<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="/assets/css/index.css">
    </head>
    <body>
       <?php include("header.php"); ?>
        <main>
			<?php
			ob_start();
			include( "functions.php" );
			$result = call_api( "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_devices?status=both" );
			$resultsArray = json_decode( $result, true );
			$devices = get_msg_data( $resultsArray );

			$manu_result = call_api( "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_manufacturers?status=both" );
			$manu_array = json_decode( $manu_result, true );
			$manufacturers = get_msg_data( $manu_array );
			?>
			<section class="update-home-page">
				<div class="parent">
                    <div class="update-home-grid">
						<div class="card">
                            <h3>Update Equipment</h3>
                            <p><em>Update existing equipment with a valid device, manufacturer, status, and serial number</em></p>
                            <button name="update-serial" onclick="location.href='update_equipment.php'">Click to Update Equipment</button>
                        </div>
                        <div class="card">
                            <h3>Update Device</h3>
                            <p><em>Update an existing device type or device status (all devices are shown)</em></p>
                            <button name="update-device" onclick="toggleNewForms()">Click to Update Device</button>
                        </div>
                        <div class="card">
                            <h3>Update Manufacturer</h3>
                            <p><em>Update an existing manufacturer (all manufacturers are shown)</em></p>
                            <button name="update-manufacturer" onclick="toggleNewForms()">Click to Update Manufacturer</button>
                        </div>
						<div class="card">
                            <h3>Update Serial Number</h3>
                            <p><em>Update an existing Serial number with a valid device id, and manufacturer id</em></p>
                            <button name="update-serial" onclick="toggleNewForms()">Click to Update Serial Number</button>
                        </div>
                    </div>
                </div>
			</section>
			<section class="new-device-manu" id="deviceForms" style="display: none">
				<div class="new-device-manu-grid">
					<div class="new-form-container">
						<form method="POST" class="form" action="">
							<label for="devices">Select Device:</label>
							<select name="device_id">
									<option selected disabled>Choose Here</option>
									<?php
									foreach($devices as $key=>$value)
									{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
							</select>
							<label for="device-input">New Device Name:</label>
							<input type="text" name="updated_str" placeholder="Example: Computer"><br>
							<label for="device-status">Device Status</label>
							<select name="status">
								<option selected disabled>Choose Here</option>
								<option value="ACTIVE">ACTIVE</option>
								<option value="INACTIVE">INACTIVE</option>
							</select>
							<button type="submit" value="submit_update_device" name="update_device">Update Device</button>
						</form>
					</div>
				</div>
			</section>
			<section class="new-device-manu" id="manuForms" style="display: none">
				<div class="new-device-manu-grid">
					<div class="new-form-container">
						<form method="POST" class="form" action="">
							<label for="manufacturers">Select Manufacturer:</label>
							<select name="manufacturer_id">
									<option selected disabled>Choose Here</option>
									<?php
									foreach($manufacturers as $key=>$value)
									{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
							</select>
							<label for="manufacturer-input">Update Manufacturer to:</label>
							<input type="text" name="updated_str" placeholder="Example: Apple"><br>
							<label for="manufacturer-status">Manufacturer Status</label>
							<select name="status">
								<option selected disabled>Choose Here</option>
								<option value="ACTIVE">ACTIVE</option>
								<option value="INACTIVE">INACTIVE</option>
							</select>
							<button type="submit" value="submit_update_manufacturer" name="update_manufacturer">Update Manufacturer</button>
						</form>
					</div>
				</div>
			</section>
			<section class="new-device-manu" id="serialForms" style="display: none">
				<div class="new-form-container">
					<form method="POST" class="form" action="">
						<label for="serial-input">Input Serial Number (exact):</label>
						<input type="text" name="serial_number" id="serialInput" placeholder="Example: SN-XXXXX"><br>
						<label for="device-input">Update Serial Number (exact) to:</label>
						<input type="text" name="updated_str" id="serialInput" placeholder="Example: SN-XXXX"><br>
						<button type="submit" value="submit_new_serial" name="update_serial">Submit</button>
					</form>
					
				</div>
			</section>
			<section class="status-notifications">
				<div class="parent">
					<?php
						ob_start();					
						if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "Error" && $_REQUEST['val'])
						{
							echo "<div class='parent'>";
							echo "<div class='errorNotification'><p>";
							echo $_REQUEST['val'];
							echo "</p></div>";
							echo "</div>";
						}
					?>
				</div>
			</section>
		</main>
    </body>
	<script>
		//https://www.w3schools.com/howto/howto_js_toggle_hide_show.asp
		//todo: refactor
		function toggleNewForms()
		{
			console.log(event.target.name);
			let buttonName = event.target.name;
			if (buttonName == "update-device")
            {
              let div = document.getElementById("deviceForms");
              if (div.style.display === "none") {
                  div.style.display = "flex";
				  div.style.justifyContent = "center";
              } else {
                  div.style.display = "none";
              }
            } else if (buttonName == "update-manufacturer") {
              let div = document.getElementById("manuForms");
              if (div.style.display === "none") {
                  div.style.display = "flex";
				  div.style.justifyContent = "center";
              } else {
                  div.style.display = "none";
              }
            } else if (buttonName == "update-serial") {
			  let div = document.getElementById("serialForms");
              if (div.style.display === "none") {
                  div.style.display = "block";
              } else {
                  div.style.display = "none";
              }
            }
		}
	</script>
</html>
<?php
ob_start();
if (isset($_POST['update_device'])) 
{
	$device_id = $_POST['device_id'];
	$updated_str = $_POST['updated_str'];
	$status = $_POST['status'];
	
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/new_update_device?"
		. "device_id=" . $device_id . "&updated_str=" . urlencode($updated_str) . "&status=" . $status;
	$result = call_api($url);
	$resultsArray = json_decode($result, true);
    $status = trim(get_msg_status($resultsArray));
	$msg = substr($resultsArray[1], 4);
	
	if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=DeviceUpdated"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: update.php?msg=Error&val=$msg");
        die();
    }
}
?>
<?php
ob_start();
if (isset($_POST['update_manufacturer']))
{
	$manufacturer_id = $_POST['manufacturer_id'];
	$updated_str = $_POST['updated_str'];
	$status = $_POST['status'];
	
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/new_update_manufacturer?"
		. "manufacturer_id=" . $manufacturer_id . "&updated_str=" . urlencode($updated_str) . "&status=" . $status;

	$result = call_api($url);
	$resultsArray = json_decode($result, true);
    $status = trim(get_msg_status($resultsArray));
	$msg = substr($resultsArray[1], 4);
	
	if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=ManufacturerUpdated"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: update.php?msg=Error&val=$msg");
        die();
    }
}
?>
