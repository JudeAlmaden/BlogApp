<?php

class BlogModel {

  private $conn;

  function connect() {
    if ($this->conn === null) {
      require("connect.php");
      $this->conn = $conn;
    }
    return $this->conn;
  }
}
