<?php
/**
 * Messages
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class MessagesPlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf()
  {
    return array('c' => null, 'e' => null, 't' => null);
  }

  public function renderBody()
  {
    $conf = $this->getConf();
    if(isset($_GET['c']) && isset($conf->c[$_GET['c']]))
    {
      return <<<MKP
			<div class="alert alert-tip">
				<a class="close" data-dismiss="alert">&times;</a>
        {$conf->c[$_GET['c']]}
			</div>
MKP;
    }
    elseif(isset($_GET['e']) && isset($conf->e[$_GET['e']]))
    {
      return <<<MKP
			<div class="alert alert-tip">
				<a class="close" data-dismiss="alert">&times;</a>
        {$conf->e[$_GET['e']]}
			</div>
MKP;
    }
  }
}

