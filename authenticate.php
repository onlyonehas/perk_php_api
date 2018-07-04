<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

    $headers = getRequestHeaders();
    $verified_authen = array();
    $secret = 'secretcode';
    $algorithm = 'HS256';
    $time = time();
    $leeway = 500; // seconds
    $ttl = 6000; // seconds
    $claims = array('sub'=>'1234567890','name'=>'perk user','admin'=>true);

    //throw error if secret not supplied 
    if(!isset($headers['Secret'])){
    http_response_code(400);  
    headers_sent();
    echo json_encode(
        array('status' => 400, 'status_message' =>'Request missing secret header')
      );
    die();
    }


    //verify token if supplied 
    if(isset($headers['Token'])){
        $verified_authen = getVerifiedClaims($headers['Token'],$time,$leeway,$ttl,$algorithm,$secret);
    } 


    //create token if secret is verified 
    if(!isset($headers['Token']) && isset($headers['Secret']) ){   
        if($headers['Secret'] != $secret){
          http_response_code(401);  
          headers_sent();
          echo json_encode(
            array('status' => 401, 'status_message' =>'Unable to authenticate')
          );
          die();
        }
          $token = generateToken($claims,$time,$ttl,$algorithm,$secret);
          echo "$token\n";
           echo json_encode(
            array('status' => 200, 'status_message' =>'Token successfully created','token'=>$token)
          );
          die();
    } 

    //throw error when not supplied or verfication failed  
    if(!isset($headers['Token']) || !$verified_authen){  
        http_response_code(401);  
        headers_sent();
        echo json_encode(
          array('status' => 401, 'status_message' =>'Unable to authenticate')
        );
        die();
    }

    function getVerifiedClaims($token,$time,$leeway,$ttl,$algorithm,$secret) {
        $algorithms = array('HS256'=>'sha256','HS384'=>'sha384','HS512'=>'sha512');
        if (!isset($algorithms[$algorithm])) return false;
        $hmac = $algorithms[$algorithm];
        $token = explode('.',$token);
        if (count($token)<3) return false;
        $header = json_decode(base64_decode(strtr($token[0],'-_','+/')),true);
        if (!$secret) return false;
        if ($header['typ']!='JWT') return false;
        if ($header['alg']!=$algorithm) return false;
        $signature = bin2hex(base64_decode(strtr($token[2],'-_','+/')));
        if ($signature!=hash_hmac($hmac,"$token[0].$token[1]",$secret)) return false;
        $claims = json_decode(base64_decode(strtr($token[1],'-_','+/')),true);
        if (!$claims) return false;
        if (isset($claims['nbf']) && $time+$leeway<$claims['nbf']) return false;
        if (isset($claims['iat']) && $time+$leeway<$claims['iat']) return false;
        if (isset($claims['exp']) && $time-$leeway>$claims['exp']) return false;
        if (isset($claims['iat']) && !isset($claims['exp'])) {
            if ($time-$leeway>$claims['iat']+$ttl) return false;
        }
        return $claims;
    }

    function generateToken($claims,$time,$ttl,$algorithm,$secret) {
        $algorithms = array('HS256'=>'sha256','HS384'=>'sha384','HS512'=>'sha512');
        $header = array();
        $header['typ']='JWT';
        $header['alg']=$algorithm;
        $token = array();
        $token[0] = rtrim(strtr(base64_encode(json_encode((object)$header)),'+/','-_'),'=');
        $claims['iat'] = $time;
        $claims['exp'] = $time + $ttl;
        $token[1] = rtrim(strtr(base64_encode(json_encode((object)$claims)),'+/','-_'),'=');
        if (!isset($algorithms[$algorithm])) return false;
        $hmac = $algorithms[$algorithm];
        $signature = hash_hmac($hmac,"$token[0].$token[1]",$secret,true);
        $token[2] = rtrim(strtr(base64_encode($signature),'+/','-_'),'=');
        return implode('.',$token);
    }


    function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }