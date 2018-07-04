<?php 
  //Deny unverified direct access 
  include_once 'authenticate.php';
  if(!$verified_authen){
    die();
  }
  include_once 'config/Database.php';
  include_once 'models/Coupon.php';

   // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: GET');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate coupon object
  $coupon = new Coupon($db);

  // GET parameters
  if (isset($_GET)){
    $coupon->value = (int)filter_input(INPUT_GET, 'value', FILTER_SANITIZE_URL);
    $coupon->coupon_code = filter_input(INPUT_GET, 'coupon_code', FILTER_SANITIZE_URL);
    $coupon->brand_name = filter_input(INPUT_GET, 'brand', FILTER_SANITIZE_URL);
    $coupon->limit = (int)filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_URL);
  }
  //Retrive voucher information
  $response = $coupon->getCouponInfo();

  //Return json response
  if($response) {
    echo json_encode(
      $response
    );
  } else {
    http_response_code(400);  
    headers_sent();
    echo json_encode(
      array('status' => 400, 'status_message' =>'ERROR: Invalid Query')
    );
    die();
  }
