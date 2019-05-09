<?php

require_once("vendor/autoload.php");

use \MAcgolddard\Page;
use \MAcgolddard\Model\Product;

$app->get('/', function() {

	$products = Product::listAll();

    $page = new Page();
    
    $page->setTpl("index", 
		array(
			'products'=>Product::checkList($products)
		)
	);

});


$app->get("/categories/:idcategory", function ($idcategory){

    
    $category = new Category();

    $category->get((int)$idcategory);

    $page = new Page();

    $page->setTpl("category",[
        'category'=>$category->getValues(),
        'products'=>[]

    ]);
});