<?php echo '<'; ?>?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title type="text"><?php echo $data['title']; ?></title>
  <link rel="self" href="<?php echo $data['link']; ?>"/>
  <updated><?php echo $data['updated']; ?></updated>
  <author>
    <name><?php $this->utility->safe($data['author']['name']); ?></name>
    <email><?php $this->utility->safe($data['author']['email']); ?></email>
  </author>
  <id><?php echo $data['id']; ?></id>
  <generator uri="http://theopenphotoproject.org">
    The OpenPhoto Project
  </generator>
  <?php if($this->config->keywords->default) { ?>
    <?php $keywords = explode(',', $this->config->keywords->default); ?>
    <?php foreach($keywords as $keyword) { ?>
      <category term="<?php $this->utility->safe(trim($keyword)); ?>"/>
    <?php } ?>
  <?php } ?>

  <?php foreach($items as $item) { ?>
    <entry>
      <title><?php $this->utility->safe($item['title']); ?></title>
      <link href="<?php echo $item['link']; ?>"/>
      <id><?php echo $item['link']; ?></id>
      <updated><?php echo gmdate('Y-m-d\TH:i:s\Z', $item['updated']); ?></updated>
      <summary type="html"><![CDATA[
        <?php foreach ($item['photos'] as $photo) { ?>
          <div>
            <a href="<?php echo $photo['url']; ?>"><img src="<?php echo $photo['src'] ?>" alt="<?php $this->utility->safe($photo['title']); ?>" /></a>
            <?php if ($photo['description'] != '') { ?>
              <p><?php $this->utility->safe($photo['description']); ?></p>
            <?php } ?>
          </div>
        <?php } ?>
      ]]></summary>
      <rights type="html"><?php $this->utility->licenseName($item['license']); ?></rights>
      <?php if(count($item['tags']) > 0) { ?>
        <?php foreach($item['tags'] as $tag) { ?>
          <category term="<?php $this->utility->safe($tag); ?>" scheme="<?php echo $data['base_url']; $this->url->photosView("tags-{$tag}"); ?>"/>
        <?php } ?>
      <?php } ?>
    </entry>
  <?php } ?>

</feed>
