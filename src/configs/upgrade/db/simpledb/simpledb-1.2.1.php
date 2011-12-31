<?php
$user = $this->getUser(1);
$user['version'] = '1.2.1';
$user['id'] = $this->owner;
$status = $this->postUser($user);
$user = $this->getUser($this->owner);
return $status;
