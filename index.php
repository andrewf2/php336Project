<?php

session_start();
require 'model.php';
require 'functions.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    $action = "default";
}
//ADD CATEGORY
if (isset($_POST['action']) && $_POST['action'] == 'Add Category') {
    $catname = valString($_POST['name']);
    if (empty($catname)) {
        $error = 'Please Provide Category Name';
    }
    if (!empty($error)) {
        include 'view.php';
        exit;
    }
    $insertResult = addCategory($catname);

    if ($insertResult) {
        $message = $catname . ' was succesfully inserted into the database';
    } else {
        $message = 'THe insert Failed Terribly, it was not pretty';
        include 'view.php';
        exit;
    }
} elseif ($_GET['action'] == 'edit') {
    $id = (int) $_GET['id'];

    $data = getCategoryItem($id);

    if (is_array($data)) {
        include 'edit.php';
        exit;
    } else {
        $message = 'No data found';
        include 'view.php';
        exit;
    }
} elseif ($_POST['action'] == 'Update') {
    $catname = valString($_POST['catname']);
    $catId = (int) $_POST['id'];
    if (empty($catname)) {
        $error = 'Please Provide a category name';
    }
    if (!empty($error)) {
        include 'edit.php';
        exit;
    }
    //UPDATE CATEGORIES

    $result = updateCategory($catname, $catId);
    if ($result) {
        $message = 'Update Sucessful';
    } else {
        $message = 'Update Failed';

        include 'view.php';
        exit;
    }
    //CREATE USER AND VALIDATION
} elseif (isset($_POST['login'])) {
    if (empty($_POST["firstname"])) {
        $errors[] = "Name is required";
    } else {
        $fName = valString($_POST["firstname"]);
    }
    if (empty($_POST["lastname"])) {
        $errors[] = "Last Name Is required";
    } else {
        $lName = valString($_POST["lastname"]);
    }
    if (empty($_POST["email"])) {
        $errors[] = "Email is required";
    } else {
        $email = ($_POST["email"]);
    }
    if (!valEmail($email)) {
        $errors[] = "Give valid email";
    }
    if (empty($_POST["gender"])) {
        $errors[] = "Gender is required";
    } else {
        $gender = valString($_POST["gender"]);
    }
    if (empty($_POST["dob"])) {
        $errors[] = "Date of Birth is a required feild";
    } else {
        $DOB = valString($_POST["dob"]);
    }
    if (empty($_POST["password"])) {
        $errors[] = "Please enter a password";
    } else {
        $pswd = valString($_POST['password']);
    }
    if (!empty($errors)) {
        include "loginView.php";
    } else {
        include "view.php";
        $passwordHashed = hashPassword($pswd);
        $userCreate = createUser($fName, $lName, $DOB, $gender, $passwordHashed);
        exit;
    }
    //UPDATE PRODUCT PRICE
} elseif ($action == 'updatePrice') {
    if (isset($_POST['submit'])) {
        $productID = $_POST['id'];
        $price = $_POST['price'];
        $updatePrice = updateInfo($price, $productID);
        $productPrice = getProductPrice();
        include 'view.php';
        exit;
    } else {
        include 'view.php';
        exit;
    }
} elseif ($_GET['action'] == 'content') {
    $id = (int) $_GET['id'];
    $data = getContent();
    if (is_array($data)) {
        $navigation = navBuilder();
        include 'view.php';
        exit;
    } else {
        $message = 'No data found';
        include 'view.php';
        exit;
    }
} elseif ($_GET['action'] == 'home') {
    $navigation = homePageBuilder();
    include 'view.php';
} elseif($_GET['action'] == 'contact'){
    include 'contact.php';
}elseif($_GET['action'] == 'about'){
    include 'about.php';
}
else {
    $navigation = homePageBuilder();
    include 'view.php';
}



    
   
    