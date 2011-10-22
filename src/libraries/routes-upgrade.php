<?php
getRoute()->get('/upgrade', array('UpgradeController', 'upgrade'));
getRoute()->post('/upgrade', array('UpgradeController', 'upgradePost'));
