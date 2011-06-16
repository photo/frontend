<?php
class Photo
{
  public static function delete($id)
  {
    // TODO, validation
    $fs = getFs();
    $db = getDb();
    $fileStatus = $fs->deletePhoto($id);
    $dataStatus = $db->deletePhoto($id);
    return $fileStatus && $dateStatus;
  }

  public static function generatePaths($photoName)
  {
    // TODO, normalize the name
    return array(
      'pathOriginal' => sprintf('/original/%s/%s', date('Ym'), $photoName),
      'pathBase' => sprintf('/base/%s/%s', date('Ym'), $photoName)
    );
  }

  public static function normalize($id, $data)
  {
    return array_merge($data, array('id' => $id));
  }

  public static function upload($localFile, $name)
  {
    $fs = getFs();
    $db = getDb();
    // TODO, needs to be a lookup
    $id = base_convert(rand(1,1000), 10, 35);
    if(is_uploaded_file($localFile))
    {
      $paths = Photo::generatePaths($name);
      $uploadedOriginal = $fs->putPhoto($localFile, $paths['pathOriginal']);
      // TODO, scale down
      $uploadedBase = $fs->putPhoto($localFile, $paths['pathBase']);
      if($uploadedOriginal->isOK() && $uploadedBase->isOK())
      {
        $stored = $db->putPhoto($id, array('host' => getFs()->getHost(), 'pathOriginal' => $paths['pathOriginal'], 'pathBase' => $paths['pathBase']));
        if($stored->isOK())
          return $id;

      }
    }

    return false;
  }
}
