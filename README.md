# PERK PHP REST API

## TO INSTALL

### Database
>Import the coupons.sql file in your database
>Configure the db params in the config/Database.php file to your own

### RUN Server
>Alternatively run docker-compose up -d
	Which uses port  :9906 
    private $db_name = 'discounts';
    private $username = 'perk_user';
    private $password = 'perk321';

>IN POSTMAN RUN GET http://localhost:8100/api/getcoupons
Alternative can RUN php -S localhost:80 inside the directory eg http://localhost:8100/api/getcoupons.php

### Aunthentication
>To request a token 
set Secret in header use key: secret value: secretcode
this will return a token
add the token along with the secret in headers
e.g. key: token value: RANDOM2131

>Once Aunthenticated continue with your query 
 e.g. GET http://localhost/api/getcoupons?brand=tesco&limit=1


