Hello,

<?php if(empty($uploader)) { ?>
We wanted to let you know that photos have been uploaded to <?php $this->utility->safe($albumName); ?>.
<?php } else { ?>
We wanted to let you know that photos have been uploaded by <?php $this->utility->safe($uploader); ?> to <?php $this->utility->safe($albumName); ?>.
<?php } ?>

You can view the photos at <?php $this->utility->safe($albumUrl); ?>.

Thanks!

The Trovebox Team
