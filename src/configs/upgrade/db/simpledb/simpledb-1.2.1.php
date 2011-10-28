<?php
$user = $this->getUser();
$user['version'] = '1.2.1';
$status = $this->putUser($user);
