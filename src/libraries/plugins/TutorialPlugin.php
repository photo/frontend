<?php
/**
 * TutorialPlugin
 *
 * @author Jaisen Mathai - jaisen@jmathai.com
 */
class TutorialPlugin extends PluginBase
{
  private $tutorial;
  public function __construct()
  {
    parent::__construct();
    $this->tutorial = new Tutorial;
  }

  public function renderFooterJavaScript()
  {
    parent::renderFooterJavaScript();
    $unseen = $this->tutorial->getUnseen();
    if(empty($unseen))
      return;

    $init = false;
    $retval = '';
    foreach($unseen as $k => $v) {
      if(isset($unseen['init']) && $unseen['init'])
        $init = true;
      $retval .= getTheme()->get('partials/tutorialJs.php', array_merge(array('step' => $k+1), $v));
    }
    return $retval;
  }

  public function defineApis()
  {
    return array(
      'update' => array('POST', '/update.json', EpiApi::external)
    );
  }

  public function routeHandler($route)
  {
    parent::routeHandler($route);
    switch($route)
    {
      case '/update.json':
        getAuthentication()->requireAuthentication();
        $user = new User;
        $user->setAttribute($_POST['section'], $_POST['key']);
        return array(
          'message' => sprintf('Updated tutorial for %s', $_POST['section']),
          'code' => 200,
          'result' => true
        );
        break;
    }
  }
}

