<style type="text/css">
  ul.photos {
    list-style-type:none;
    margin:0;
    padding:0;
  }

  ul.photos li {
    float:left;
    width:200px;
    height:200px;
    padding: 25px;
  } 
</style>
<ul class="photos">
  <?php foreach($photos as $photo) { ?>
  <li>
    (<a href="/photo/<?php echo $photo['id']; ?>/delete" class="delete">delete</a>)
    <br>
    <a href="/photo/<?php echo $photo['id']; ?>"><img src="<?php echo Photo::generateUrlPublic($photo, 200, 200); ?>"></a>
    Tags:
    <?php foreach((array)$photo['tags'] as $tag) { ?>
      
    <?php } ?>
    <br/>
    Taken: <?php echo date('D M j, Y', $photo['dateTaken']); ?>
  </li>
  <?php } ?>
</ul>
<script>
$('ul.photos li a.delete').click(function(e) {
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
