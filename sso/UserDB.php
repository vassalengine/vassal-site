<?php

require_once(dirname(__FILE__) . '/config.php');

class UserDB {
  private $ds;  

  public function __construct() {
    $this->ds = @ldap_connect(LDAP_HOST);
    if (!$this->ds) {
      throw new ErrorException('Cannot connect to LDAP server.');
    }
  }

  public function __destruct() {
    @ldap_close($this->ds);
  }

  public function search($filter) {
    if (!@ldap_bind($this->ds)) {
      throw new ErrorException(
        'Cannot bind to LDAP server: ' . ldap_error($this->ds)); 
    }

    $r = @ldap_search($this->ds, LDAP_BASE, $filter);
    if (!$r) {
      throw new ErrorException(
        'LDAP search failed: ' . ldap_error($this->ds));
    }

    return @ldap_get_entries($this->ds, $r);
  }

  public function exists($username) {
    $result = $this->search("uid=$username");
    return $result['count'] > 0;
  }

  public function auth($username, $password) {
    if (!@ldap_bind($this->ds, "uid=$username," . LDAP_BASE, $password)) {
      throw new ErrorException(
        'The username or password you entered is incorrect.'
      );
    }
  }

  public function modify($username, $attr) {
    $this->bind(LDAP_USERNAME, LDAP_PASSWORD);
  
    $dn = "uid=$username," . LDAP_BASE;
    if (!@ldap_mod_replace($this->ds, $dn, $attr)) {
      throw new ErrorException(
        'Cannot reset user attributes: ' . ldap_error($this->ds));
    }
  }

  public function create($username, $attr) {
    $this->bind(LDAP_USERNAME, LDAP_PASSWORD);
   
    $dn = "uid=$username," . LDAP_BASE;
    if (!@ldap_add($this->ds, $dn, $attr)) {
      throw new ErrorException(
        'Cannot write entry: ' . ldap_error($ds));
    }
  }

  private function bind($dn = NULL, $pw = NULL) {
    if (!@ldap_bind($this->ds, $dn, $pw)) {
      throw new ErrorException(
        'Cannot bind to LDAP server: ' . ldap_error($this->ds));
    }
  } 
}

?>
