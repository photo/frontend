<ul class="photos">
  <?php foreach($photos as $photo) { ?>
  <li>
    (<a href="/photo/<?php echo $photo['id']; ?>/delete">delete</a>)
    <br>
    <img src="<?php echo Photo::generateUrlPublic($photo, 200, 200); ?>">
  </li>
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
