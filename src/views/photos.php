<ul class="photos">
  <?php foreach($photos as $photo) { ?>
  <li>Photo <?php echo $photo['id']; ?> has url <?php echo $photo['pathOriginal']; ?> (<a href="/photo/<?php echo $photo['id']; ?>/delete">delete</a>)</li>
  <?php } ?>
</ul>
<script>
$('ul.photos li a').click(function(e) {
  var a = this,
    url = $(a).attr('href')+'.json';
  $.post(url, function(response) {
    if(response.code === 200)
      $(a).parent().remove();
    else
      alert('error');
  }, 'json');
  return false;

});
</script>
