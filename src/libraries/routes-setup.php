<?php
getRoute()->get('/setup', array('SetupController', 'setup'));
getRoute()->post('/setup', array('SetupController', 'setupPost'));
