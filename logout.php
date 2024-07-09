<?php

 require_once("models/Message.php");
 require_once("templates/header.php");

  if($userDao) {
    $userDao->destroyToken();
  }