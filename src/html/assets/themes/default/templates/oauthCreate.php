<form method="post">
  <ul>
    <li>Name this app: <input type="text" name="name"></li>
    <li>Permissions:
      <ul>
        <li><input type="checkbox" name="permissions" value="read" checked="true"> Read</li>
        <li><input type="checkbox" name="permissions" value="create"> Create</li>
        <li><input type="checkbox" name="permissions" value="update"> Update</li>
        <li><input type="checkbox" name="permissions" value="delete"> Delete</li>
      </ul>
    </li>
    <input type="hidden" name="oauth_callback" value="<?php Utility::safe($callback); ?>">
  <button type="submit">Create</button>
</form>

