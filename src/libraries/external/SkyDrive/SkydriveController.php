<?php
/**
 * SkyDriveController .php class file
 *
 * This controller serves two functionalities
 * 1.   Implement the Oauth2 Authorization code grant flow which includes wl.offline_access scope.
 *       This means that the user goes through the authorization/authentication process only once,and
 *       gets a  long lived(1year) refresh token at the end.The refresh token is either hard coded in  SkyDriveAPI
 *      class or stored in database and it is used to obtain a short lived (1 hour)  access token which is stored in session
 *      and is used for the api calls.For details see  http://msdn.microsoft.com/en-us/library/live/hh243647.aspx
 *      To start the flow,navigate to /skydrive/getCode
 *
 * 2.   Make api calls for testing purposes.
 *        Start with getting your user and storage info.Then get your root folders info (getMyRootFolders) ,and
 *        copy  some folder ids.Then call getFolderFilesByID to see the contained files ,etc.
 *
 * @author: Spiros Kabasakalis <kabasakalis@gmail.com>
 * @link http://iws.kabasakalis.gr/
 * @link http://www.reverbnation.com/spiroskabasakalis
 * @copyright Copyright &copy; Spiros Kabasakalis 2013
 * @license The MIT License
 * @category Yii
 */


class SkydriveController extends Controller
{

    /*
     * SkyDriveAPI class instance
     */
    public $SD;

    /*
     * Your SkyDrive User ID
     */
    public $me_ID = '';

    /*
     * fill in with IDs to test
     */
    public $root_folder_id = ''; //root	folder
    public $pictures_folder_id = ''; //pictures
    public $folder1_ID = ''; //
    public $file1_ID = ''; //           
    public $file2_ID = ''; //


    public function init()
    {
        Yii::import('protected.extensions.skydrive.SkyDriveAPI');
        parent::init();
    }

    public function actionGetCode()
    {
        $redirect = Yii::app()->request->hostInfo . '/' . $this->id . '/' . 'getAccessTokenFromCode';
        $this->SD = new SkyDriveAPI(array('getcode' => true, 'redirect_uri' => $redirect));
        $this->SD->getAuthorizationCode();
    }

    public function actionGetAccessTokenFromCode()
    {
        $redirect = Yii::app()->request->hostInfo . '/' . $this->id . '/' . 'getAccessTokenFromCode';
        $this->SD = new SkyDriveAPI(array('redirect_uri' => $redirect));
        $this->SD->getAccessTokenFromCode(true);
    }


    /**
     *   Test API calls,examples.Uncomment one every time ,and test.
     */
    public function actionIndex()
    {
        $this->SD = new SkyDriveAPI();

        //$this->SD->getMe(true);
        //$this->SD-> getStorageInfo('true');
        //$this->SD->getPermissions(true);
        //$this->SD->getMyRootFolders(null,true);
        //$this->SD->getAlbumsOfUser($this->me_ID, $querystring = null, $print =true);
        //$this->SD->getMyAlbums(null,true);
        //$this->SD->getObjectByID($this->file1_ID,true);
        //$this->SD->getFileByName('myphoto.jpg',$this->pictures_folder_id,true);
        //$this->SD->getFolderFilesByID($this->folder1_ID ,'filter=audio', true);
        //$this->SD->getPhotosOfAlbum($this->pictures_folder_id,null,true);

        /* $tree_picture_path=  Yii::getPathOfAlias('protected.extensions.skydrive').DIRECTORY_SEPARATOR.'tree.jpg';
         $this->SD->upload( $tree_picture_path  ,'my_tree2.jpg',$this->pictures_folder_id,true);*/

        /* $this->SD->updateFileProperties($this->file1_ID,
                                                                            array(
                                                                                'name'=>'sunset.jpg',
                                                                                'description'=>'Gorgeous Sunset'
                                                                            ),
                                                                         true);*/

        //$this->SD->download($this->file2_ID,true,true);
        //$this->SD->deleteFileByID($this->file2_ID,true);
        //$this->SD->search('tree',true);
        //$this->SD->createFolder($this->folder1_ID,'My new Folder','Description',true);


    }

}
