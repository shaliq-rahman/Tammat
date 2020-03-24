<?php
$url = 'https://dealnotdeal.com/api/getCategories';
$ch = curl_init($url);
# Setup request to send json via POST.
$data = array('aa'=>'aa');
$payload = json_encode( array( "customer"=> $data ) );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
# Return response instead of printing.
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
# Send request.
$categoriesOptions = curl_exec($ch);
curl_close($ch);
# Print response.
//print_r($categoriesOptions);
if (isset($categoriesOptions))
{
if ($categoriesOptions['type_of_display'] == 'c_picture_icon')
{
    foreach($categories as $key => $cat)
    {
        ?>
        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 f-category">
								
								<a href="">
									<h6> <?php echo $cat->name ;?> </h6>
								</a>
							</div>
							<?php
							
    }
}
}

?>