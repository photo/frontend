<?php
/**
 * FacebookLike
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class FacebookLikePlugin extends PluginBase
{
  public function __construct()
  {
    parent::__construct();
  }

  public function defineConf() { }

  public function renderPhotoDetail($params = null)
  {
    parent::renderPhotoDetail();
    return <<<MKP
<fb:like ref="top_left"></fb:like>
MKP;
  }
}


