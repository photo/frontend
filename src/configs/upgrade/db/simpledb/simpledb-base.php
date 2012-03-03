<?php
$domains = $this->db->get_domain_list("/^{$this->domainPhoto}(Action|Credential|Group|Tag|User|Webhook)?$/");
if(count($domains) == 7)
  return true;

$domainsToCreate = array($this->domainAction, $this->domainActivity, $this->domainAlbum, $this->domainCredential, $this->domainGroup, 
  $this->domainPhoto, $this->domainTag, $this->domainUser, $this->domainWebhook);

$queue = new CFBatchRequest();
foreach($domainsToCreate as $domainToCreate)
{
  if(!in_array($domainToCreate, $domains))
  {
    $this->db->batch($queue)->create_domain($domainToCreate);
    getLogger()->info(sprintf('Queueing request to create domain: %s', $domainToCreate));
  }
}

$responses = $this->db->batch($queue)->send();
getLogger()->info(sprintf('Attempting to create %d domains.', count($responses)));
$this->logErrors($responses);
$status = $responses->areOK();
return $status;
