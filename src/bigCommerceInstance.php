<?php

$BIGCOMMERCE_CLIENT_ID="";
$BIGCOMMERCE_TOKEN="";
$BIGCOMMERCE_STORE="";
$bigCommerce = new IntuitSolutions\BigCommerce\BigCommerceAPI($BIGCOMMERCE_STORE, $BIGCOMMERCE_TOKEN, $BIGCOMMERCE_CLIENT_ID);

function getProductByIds ($array) {
    global $bigCommerce;
    $filter = 'id:in=';
    foreach($array as $id){
        $filter.= $id .',';
    }
    $result = $bigCommerce->get('catalog/products', trim($filter,','), ['version' => 3]);
    return $result;
}

function searchProductsByName ($keyword) {
    global $bigCommerce;
    $filter = 'keyword='.$keyword;
    $result = $bigCommerce->get('catalog/products', $filter, ['version' => 3]);
    return $result;
}

function getStoreInfo(){
    global $bigCommerce;
    $store = $bigCommerce->get('store');
    return $store;
}