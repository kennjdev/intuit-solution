<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ .'/bigCommerceInstance.php';
require_once __DIR__ .'/db.php';
require_once __DIR__ .'/cors.php';
use Steampixel\Route as Route;
cors();


function reponseJson ($data){
    header('Content-type: application/json');
    echo json_encode($data);
}
Route::add('/', function() {
    echo 'hello world';
});
Route::add('/store-info', function() {
    return reponseJson(getStoreInfo());
});
Route::add('/get-wishlist-products', function() {
 
    global $bigCommerce,$db ;
    $query = $db->query('select product_id from wishlist'); 
    $wishlist_product_ids = $query->fetchAll(PDO::FETCH_COLUMN); 
    $products = getProductByIds($wishlist_product_ids,$bigCommerce);
    return reponseJson($products);
});
Route::add('/add-to-wishlist', function() {
    global $db ;
    $post_body = json_decode(file_get_contents('php://input'),true);
    $product_id = $post_body['id'];
    if($product_id){
        $query= $db->prepare("INSERT  INTO wishlist (product_id)  VALUES (?)");
        $query->execute([ $product_id]);
    }
    return reponseJson(['status'=>'ok']);
},'post');
Route::add('/remove-wishlist', function() {
    global $db ;
    $post_body = json_decode(file_get_contents('php://input'),true);
    $product_id = $post_body['id'];
    if($product_id){
        $query= $db->prepare("DELETE  FROM wishlist WHERE product_id=?");
        $query->execute([ $product_id]);
    }
    return reponseJson(['status'=>'ok']);
},'post');
Route::add('/search', function() {
    $keyword = (isset($_GET['keyword']) && $_GET['keyword'] ) ? $_GET['keyword'] : ''  ;
    $result = searchProductsByName($keyword);
    return reponseJson($result);
}, 'get');
Route::run('/');
