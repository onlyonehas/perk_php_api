<?php 
  include_once 'authenticate.php';
  if(!$verified_authen){
    die();
  }

  class Coupon {
    // DB stuff
    private $conn;
    private $table = 'coupon';

    // Table Properties
    public $id;
    public $coupon_code;
    public $value;
    public $brand_id;
    public $brand_name;
    public $limit; 

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get Vochers
    public function getCouponInfo() {
      // Create query
      $query = 'SELECT 
            v.id,
            b.name as brand,
            v.coupon_code,
            v.value
          FROM
            ' . $this->table . ' v
          LEFT JOIN
            brand b ON v.brand_id = b.id';

    $params =array();
    
    if($this->coupon_code){
      $where_query['coupon_code'] = "v.coupon_code = :coupon_code";
      $this->coupon_code = htmlspecialchars(strip_tags($this->coupon_code));
      $params[':coupon_code'] = $this->coupon_code;
    }

    if($this->value){
      $where_query['value'] = "v.value = :value";
      $this->value = htmlspecialchars(strip_tags($this->value));
      $params[':value'] = $this->value;
    }

     if($this->brand_name){
      $where_query['brand_name'] = "b.name =:brand_name";
      $this->brand_name = htmlspecialchars(strip_tags($this->brand_name));
      $params[':brand_name'] = $this->brand_name;
    }
    
    if(isset($where_query)){
      $query .=' WHERE ';
      $counter = 0;
      foreach ($where_query as $key => $whr) {
         $query .= $whr;
         $counter++;
         if(count($where_query) !== $counter){
            $query .= ' AND ';
         }
      }    
    } 

    if($this->limit){
      $query .= ' LIMIT '.$this->limit;
    }

    //prepare query
    $stmt = $this->conn->prepare($query);
     // Execute query
    $stmt->execute($params);
      // Get row count
    $num = $stmt->rowCount();

    if($num > 0) {
      $retrived_data = array();

      //$posts_arr['data'] = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $this->id=$id;
        //delete id 
        $this->delete();

        $coupon_info = array(
          'coupon_code' => $coupon_code,
          'brand_name' => $brand,
          'value' => $value,
        );

        // Push to "data"
        array_push($retrived_data, $coupon_info);
      }
      //deleteListedVouchers($ids);
      return $retrived_data;
    } else{
      return false;
    }
  }

    // Create Voucher 
    public function create() {
    }

    // Update Voucher
    public function update() {
    }

    // Delete Voucher 
    public function delete() {
      // Create query
      $query = 'DELETE FROM ' . $this->table . ' WHERE id in (:id)';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->id = htmlspecialchars(strip_tags($this->id));

      // Bind data
      $stmt->bindParam(':id', $this->id);

      // Execute query
      if($stmt->execute()) {
        return 'true';
      }

      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
    }

}
