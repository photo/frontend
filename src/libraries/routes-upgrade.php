<?php
$routeObj->get('/upgrade', array('UpgradeController', 'upgrade'));
$routeObj->post('/upgrade', array('UpgradeController', 'upgradePost'));
