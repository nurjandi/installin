\<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

//product
$app->get("/products/", function (Request $request, Response $response){
    $sql = "SELECT * FROM product";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});


$app->get("/products/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "SELECT * FROM product WHERE ID_PRODUCT=:id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->post("/products/", function (Request $request, Response $response){

    $new_product = $request->getParsedBody();

    $sql = "INSERT INTO product (ID_PRODUCT, NAMA, HARGA, ATRIBUT) VALUE (:id_product, :nama, :harga, :atribut)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":id_product" => $new_product["id_product"],
        ":nama" => $new_product["nama"],
        ":harga" => $new_product["harga"],
        ":atribut" => $new_product["atribut"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->put("/products/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $new_product = $request->getParsedBody();
    $sql = "UPDATE product SET NAMA=:nama, HARGA=:harga, atribut=:atribut WHERE ID_PRODUCT=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id,
        ":nama" => $new_product["nama"],
        ":harga" => $new_product["harga"],
        ":atribut" => $new_product["atribut"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->delete("/products/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "DELETE FROM product WHERE ID_PRODUCT=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

//kurir
$app->get("/kurir/", function (Request $request, Response $response){
    $sql = "SELECT * FROM kurir";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});


$app->get("/kurir/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "SELECT * FROM kurir WHERE ID_KURIR=:id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->post("/kurir/", function (Request $request, Response $response){

    $new_kurir = $request->getParsedBody();

    $sql = "INSERT INTO kurir (ID_KURIR, NAMA, NO_KTP, NO_HP) VALUE (:id_kurir, :nama, :no_ktp, no_hp)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":id_kurir" => $new_kurir["id_kurir"],
        ":nama" => $new_kurir["nama"],
        ":no_ktp" => $new_kurir["no_ktp"],
        ":no_hp" => $new_kurir["no_hp"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->put("/kurir/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $new_kurir = $request->getParsedBody();
    $sql = "UPDATE kurir SET NAMA=:nama, NO_KTP=:no_ktp, NO_HP=:no_hp WHERE ID_KURIR=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id,
        ":nama" => $new_kurir["nama"],
        ":no_ktp" => $new_kurir["no_ktp"],
        ":no_hp" => $new_kurir["no_hp"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->delete("/kurir/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "DELETE FROM kurir WHERE ID_KURIR=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->post('/kurir/foto/{id}', function(Request $request, Response $response, $args) {
    
    $uploadedFiles = $request->getUploadedFiles();
    
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['foto'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        
        // ubah nama file dengan id buku
        $filename = sprintf('%s.%0.8s', $args["id"], $extension);
        
        $directory = $this->get('settings')['upload_directory'];
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        // simpan nama file ke database
        $sql = "UPDATE kurir SET FOTO=:foto WHERE ID_KURIR=:id";
        $stmt = $this->db->prepare($sql);
        $params = [
            ":id" => $args["id"],
            ":foto" => $filename
        ];
        
        if($stmt->execute($params)){
            // ambil base url dan gabungkan dengan file name untuk membentuk URL file
            $url = $request->getUri()->getBaseUrl()."/uploads/".$filename;
            return $response->withJson(["status" => "success", "data" => $url], 200);
        }
        
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    }
});

//order product
$app->get("/order_product/", function (Request $request, Response $response){
    $sql = "SELECT * FROM order_product";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->get("/order_product/{id_user}", function (Request $request, Response $response, $args){
    $id = $args["id_user"];
    $sql = "SELECT * FROM order_product WHERE ID=5";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id_user" => $id]);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

// $app->get("/order_product/{id}", function (Request $request, Response $response, $args){
//     $id = $args["id"];
//     $sql = "SELECT * FROM order_product WHERE ID_ORDER=:id";
//     $stmt = $this->db->prepare($sql);
//     $stmt->execute([":id" => $id]);
//     $result = $stmt->fetch();
//     return $response->withJson(["status" => "success", "data" => $result], 200);
// });

$app->delete("/order_product/{id}", function (Request $request, Response $response, $args){
    $id = $args["id"];
    $sql = "DELETE FROM order_product WHERE ID_ORDER=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->post("/order_product/", function (Request $request, Response $response){
    $new_order = $request->getParsedBody();
    $sql = "INSERT INTO order_product (ID_ORDER, ID_KURIR, ID, id_product, TGL_ORDER, TGL_PENGAMBILAN, WAKTU_PENGAMBILAN, TEMPAT_PENGAMBILAN, STATUS, HARGA_TOTAL) VALUE (:id_order, :id_kurir, :id, :id_product, NOW(), :tgl_pengambilan, :waktu_pengambilan, :tempat_pengambilan, :status, :harga_total )";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id_order" => $new_order["id_order"],
        ":id_kurir" => $new_order["id_kurir"],
        ":id" => $new_order["id"],
        ":id_product" => $new_order["id_product"],
        ":tgl_pengambilan" => $new_order["tgl_pengambilan"],
        ":waktu_pengambilan" => $new_order["waktu_pengambilan"],
        ":tempat_pengambilan" => $new_order["tempat_pengambilan"],
        ":status" => $new_order["status"],
        ":harga_total" => $new_order["harga_total"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->post("/extras/", function (Request $request, Response $response){
    $new_order = $request->getParsedBody();
    $sql = "INSERT INTO order_product (ID_ORDER, NAMA, HARGA, RINCIAN) VALUE (:id_order, :nama, :harga, :rincian)";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id_order" => $new_order["id_order"],
        ":nama" => $new_order["nama"],
        ":harga" => $new_order["harga"],
        ":rincian" => $new_order["rincian"]
    ];
    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->get("/extras/{id_product}", function (Request $request, Response $response, $args){
    $id_product = $args["id_product"];
    $sql = "SELECT * FROM extras WHERE id_product=:id_product";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id_product" => $id_product]);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->put("/order_product/{id_order}", function (Request $request, Response $response, $args){
    $id_order = $args["id_order"]; 
    $new_order = $request->getParsedBody();
    $sql = "UPDATE order_product SET ID_KURIR=:id_kurir, ID=:id, TGL_ORDER=:tgl_order, END_ORDER=:end_order,STATUS=:status, LONGITUDE=:longitude, LATITUDE=:LATITUDE WHERE ID_ORDER=:id_order";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id_order" => $id_order,
        ":id_kurir" => $new_order["id_kurir"],
        ":id" => $new_order["id"],
        ":tgl_order" => $new_order["tgl_order"],
        ":end_order" => $new_order["end_order"],
        ":status" => $new_order["status"],
        ":longitude" => $new_order["longitude"],
        ":latitude" => $new_order["latitude"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "data" => "1"], 200);
    
    return $response->withJson(["status" => "failed", "data" => "0"], 200);
});

$app->put('/notResponsed/{id}', function(Request $result, Response $response, $args){
    $id = $args['id'];
    $sql = "UPDATE order_product set status=:status where id_order=:id";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id" => $id,
        ":status" => "0"
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => "data has been updated"], 200);
});

$app->put('/Progressed/{id}', function(Request $result, Response $response, $args){
    $id = $args['id'];
    $sql = "UPDATE order_product set status=:status where id_order=:id";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id" => $id,
        ":status" => "1"
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => "data has been updated"], 200);
});

$app->put('/Done/{id}', function(Request $result, Response $response, $args){
    $id = $args['id'];
    $sql = "UPDATE order_product set status=:status where id_order=:id";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id" => $id,
        ":status" => "2"
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => "data has been updated"], 200);
});

$app->put('/Rejected/{id}', function(Request $result, Response $response, $args){
    $id = $args['id'];
    $sql = "UPDATE order_product set status=:status where id_order=:id";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":id" => $id,
        ":status" => "3"
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => "data has been updated"], 200);
});


$app->get('/getbyStatus/{status}', function(Request $result, Response $response, $args){
    $id = $args['id'];
    $sql = "SELECT * from order_product where status=:status";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":status" => $status
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});
//user

$app->post('/login/', function(Request $request, Response $response){
    $new_order = $request->getParsedBody();
    $sql = "SELECT * from user where email=:email and password=:password";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":email" => $new_order['email'],
        ":password" => $new_order["password"],
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    if($result){
        return $response->withJson(["status" => "login success", "data" => "1"], 200);
    }
    else{
        return $response->withJson(["status" => "login gagal", "data" => "0"], 200);
    }
});

$app->post("/user/", function (Request $request, Response $response){
    $new_kurir = $request->getParsedBody();
    $sql = "INSERT INTO user (NAMA, NO_HP, EMAIL, ALAMAT, PASSWORD, FOTO) VALUE (:nama, :no_hp, :email, :alamat, :password, :foto)";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":nama" => $new_kurir["nama"],
        ":no_hp" => $new_kurir["no_hp"],
        ":email" => $new_kurir["email"],
        ":alamat" => $new_kurir["alamat"],
        ":password" => $new_kurir["password"],
        ":foto" => $new_kurir["foto"]
    ];

    if($stmt->execute($data)){
        return $response->withJson(["status" => "success", "data" => "1"], 200);
    }
    else{
        return $response->withJson(["status" => "failed", "data" => "0"], 200);
    }
});

$app->put("/user/{id}", function (Request $request, Response $response, $args){
    $id = $args['id'];
    $sql = "UPDATE user set nama=:nama, no_hp=:no_hp, email=:email, alamat=:alamat, password=:password, foto=:foto where id=:id";
    $stmt = $this->db->prepare($sql);
    $data = [
        ":nama" => $new_kurir["nama"],
        ":no_hp" => $new_kurir["no_hp"],
        ":email" => $new_kurir["email"],
        ":alamat" => $new_kurir["alamat"],
        ":password" => $new_kurir["password"],
        ":foto" => $new_kurir["foto"]
    ];
    $stmt->execute($data);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => "data updated"], 200);
});

$app->get("/user/{email}", function (Request $request, Response $response, $args){
    $id = $args["email"];
    $sql = "SELECT * FROM user WHERE EMAIL=:email";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":email" => $id]);
    $result = $stmt->fetch();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});

$app->get("/user/", function (Request $request, Response $response){
    $sql = "SELECT * FROM user";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetchAll();
    return $response->withJson(["status" => "success", "data" => $result], 200);
});
