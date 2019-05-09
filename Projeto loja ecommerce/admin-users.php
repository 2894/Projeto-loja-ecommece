<?php

require_once("vendor/autoload.php");

use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app->get('/admin', function() {

    User::verifyLogin();

    $page = new PageAdmin(); 
    
    $page->setTpl("index");

});

$app->get('/admin/login', function() {

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("login");

});

$app->post('/admin/login', function() {

    User::login($_POST['login'], $_POST['password']);

    header('Location: /admin');
    exit;

});

$app->get('/admin/logout', function() {

    User::logout();

    header('Location: /admin');
    exit;

});

$app->get('/admin/users', function (){

    User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl("users", array(
        "users"=>$users
    ));
});

$app->get('/admin/users/create', function (){

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("users-create");

});

$app->get("/admin/users/:idusers/delete", function ($iduser){

    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;
});

$app->get("/admin/users/:iduser", function ($iduser){

    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl("users-update", array(
        "user"  =>  $user->getValues()
    ));

});

$app->post("/admin/users/create", function (){

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = isset($_POST['inadmin'])?1:0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;

});

$app->post("/admin/users/:idusers", function ($iduser){

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = isset($_POST['inadmin'])?1:0;

    $user->get((int)$iduser);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;

});

$app->get("/admin/forgot", function (){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot");

});

$app->post("/admin/forgot", function (){

    $user = User::getForgot($_POST['email']);

    header("Location: /admin/forgot/sent");
    exit;
});

$app->get("/admin/forgot/sent", function (){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function (){

    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset",
        array(
            "name"=>$user["desperson"],
            "code"=>$_GET["code"]
        )
    );
});

$app->post("/admin/forgot/reset", function (){

    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    //Hash de password
    $options = [
        'cost' => 12,
    ];
    $new_password   =   password_hash($_POST["password"], PASSWORD_DEFAULT, $options);
    
    $user->setPassword($new_password);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset-success");

});