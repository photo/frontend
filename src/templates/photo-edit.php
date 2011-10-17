<div class="owner-edit">
	<h1>This photo belongs to you. You can edit it.</h1>
	<div class="delete">
	  <form method="post" action="<?php Url::photoDelete($photo['id']); ?>">
	    <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
	    <button type="submit" class="delete photo-delete-click">Delete this photo</button>
	  </form>
	</div>
   <div class="detail-form">
     <form method="post" action="<?php Url::photoUpdate($photo['id']); ?>">
		<ul>
			<li>
				<input type="hidden" name="crumb" value="<?php Utility::safe($crumb); ?>">
				<label for="title">Title</label>
				<input type="text" name="title" id="title" placeholder="A title to describe your photo" value="<?php Utility::safe($photo['title']); ?>">
			</li>
			<li>
				<label for="description">Description</label>
				<textarea name="description" id="description" placeholder="A description of the photo (typically longer than the title)"><?php Utility::safe($photo['description']); ?></textarea>
			</li>
			<li>
				<label for="tags">Tags</label>
				<input type="text" name="tags" id="tags" placeholder="A comma separated list of tags" value="<?php Utility::safe(implode(',', $photo['tags'])); ?>">
			</li>
			<li>
				<label for="latitude">Latitude</label>
				<input type="text" name="latitude" id="latitude" placeholder="A latitude value for the location of this photo (i.e. 49.7364565)" value="<?php Utility::safe($photo['latitude']); ?>">
			</li>
			<li>
				<label for="longtitude">Longitude</label>
				<input type="text" name="longitude" id="longtitude" placeholder="A longitude value for the location of this photo (i.e. 181.34523224)" value="<?php Utility::safe($photo['longitude']); ?>">
			</li>
			<li>
				<label>Permission</label>
				<ul>
					<li>
						<input type="radio" name="permission" id="private" value="0" <?php if($photo['permission'] == 0) { ?> checked="checked" <?php } ?>>
						<label for="private">Private</label>
					</li>
					<li>
						<input type="radio" name="permission" id="public" value="1" <?php if($photo['permission'] == 1) { ?> checked="checked" <?php } ?>>
						<label for="public">Public</label>
					</li>
				</ul>
			</li>
			<?php if(count($groups) > 0) { ?>
			<li class="groups">
				<label>Groups</label>
				<ol>
					<li>
				  		<?php foreach($groups as $group) { ?>
				    		<input type="checkbox" name="groups[]" value="<?php Utility::safe($group['id']); ?>" <?php if(isset($photo['groups']) && in_array($group['id'], $photo['groups'])) { ?> checked="checked" <?php } ?>>
				    	<?php Utility::licenseLong($group['name']); ?>
				  	<?php } ?>
					</li>
				</ol>
			</li>
			<?php } ?>
			<li>
				<label>License</label>
				<ol>
					<?php foreach($licenses as $code => $license) { ?>
					<li>
				    	<input type="radio" name="license" value="<?php Utility::safe($code); ?>" <?php if($license['selected']) { ?> checked="checked" <?php } ?>>
				    	<?php Utility::licenseLong($code); ?>
					</li>
					<?php } ?>
				</ol>
			</li>
			<li>
       			<button type="submit">Update photo</button>
			</li>
     </form>
   </div>
</div>
