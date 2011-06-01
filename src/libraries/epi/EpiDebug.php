<?php
class EpiDebug
{
  private static $modules = array('EpiRoute');
  private $messages = array();

  public function addMessage($module, $message)
  {
    $this->messages[$module][] = $message;
  }

  public function renderAscii()
  {
    $rowWidth = 100;
    $col1Width = 96;
    $out = "\n" . str_repeat('*', $rowWidth) . "\n";
    $groups = array();
    foreach($this->messages as $module => $messages)
    {
      $out .= str_repeat('~', ($rowWidth/2)-((strlen($module)+2)/2)) . " {$module} " . str_repeat('~', ($rowWidth/2)-((strlen($module)+2)/2)) . "\n";
      foreach($messages as $message)
        $out .= '| ' . $message . str_repeat(' ', ($col1Width-strlen($message))) . " |\n";
    }
    return $out;
  }
}

function getDebug()
{
  static $debug;
  if(!$debug)
    $debug = new EpiDebug();

  return $debug;
}
