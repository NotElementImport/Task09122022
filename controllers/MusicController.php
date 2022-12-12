<?php

namespace app\controllers;

use Yii;
use app\models\Genres;
use app\models\LoginForm;
use yii\web\Controller;
use app\models\MusicPlaylist;
use yii\helpers\Url;
use yii\db\Query;

class MusicController extends Controller
{
    const GET_PAGE = 'page';
    const GET_GENRE = 'genre';

    const JSON_HEADER = 'Header';
    const JSON_CONTENT = 'Message';

    public function actionIndex()
    {
        $Playlist = MusicPlaylist::find();

        // FETCH API
        // Load Track from Database
        if($this->request->isPost)
        {
            $Limit = 5;

            $Page = $this->request->post(static::GET_PAGE);
            $Genre = $this->request->post(static::GET_GENRE);
            /*
            SELECT 
                music_playlist.songname as name, 
                music_playlist.songfile as track, 
                genres.name, 
                music_playlist.imagefile as image 
            FROM music_playlist 
            LEFT JOIN 
                genres 
                    ON music_playlist.genre = genres.id
            LEFT JOIN 
                user 
                    ON music_playlist.whoupload = user.id
            LIMIT 5 
            OFFSET 0
            */
        
            $DataTo = (new Query())->select(
                [
                    'music_playlist.songname AS name',
                    'music_playlist.songfile as track',
                    'genres.name as genre',
                    'music_playlist.imagefile as image',
                    'user.username as who',
                ]
            )->from(
                'music_playlist'
            )->leftJoin(
                'genres',
                'music_playlist.genre = genres.id'
            )->leftJoin(
                'user',
                'music_playlist.whoupload = user.id'
            )->where(
                ($Genre != 0 ? "music_playlist.genre = $Genre" : '')
            )->orderBy(
                'music_playlist.id DESC'
            )->limit(
                $Limit
            )->offset(
                $Page * $Limit
            )->all();

            return json_encode($DataTo);
        }

        // If Link not hasn't Page index
        if(!$this->request->get(static::GET_PAGE))
        {
            return $this->redirect(Url::toRoute(['/', 'page' => '1', 'filter' => '0']));
        }

        $Genre = $this->request->get('filter');
        $PageCount = $Playlist->where(
            ($Genre != 0 ? "genre = $Genre" : '')
        )->count() * (1 / 5);
        $PageCount = ceil($PageCount);


        $AllGenres = Genres::find()->all();

        return $this->render('index', compact('PageCount', 'AllGenres'));
    }

    public function actionAuthorization()
    {
        if(!Yii::$app->user->isGuest)
        {
            Yii::$app->user->logout();
            return $this->goHome();
        }
        else
        {
            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
            }

            $model->password = '';
            return $this->render('auth', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpload()
    {
        // If user Guest or not have Permission;
        if(Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        else
        {
            if(Yii::$app->user->can('BasicPermission') == false)
            {
                return $this->goHome();
            }
        }

        // FETCH API
        // Load song in to the Database
        if($this->request->isPost)
        {
            // JSON Answer
            $Output = [ "Header" => [ "Code" => 0, "Message" => "Ok!" ], "Content" => "" ];

            // Song Name and Original not Redacted
            $NameSong = $this->request->post('name');
            $NameSong_Orig = trim($this->request->post('name'));

            // Blob file with mp3 128kbps
            $Mp3Data = $_FILES['song'];

            $Genre = $this->request->post('genre');

            // Filter Name;
            $NameSong = str_replace(' ', '_', $NameSong);
            $NameSong = str_replace('%', '_', $NameSong);
            $NameSong = str_replace('#', '_', $NameSong);
            $NameSong = str_replace('$', '_', $NameSong);
            $NameSong = str_replace('&', '_', $NameSong);
            $NameSong = str_replace('*', '_', $NameSong);
            $NameSong = str_replace('@', '_', $NameSong);
            $NameSong = str_replace('!', '_', $NameSong);
            $NameSong = str_replace('?', '_', $NameSong);

            if($Mp3Data != null)
            {
                $Target_dir = "/uploads/";
                $Target_file = $Target_dir . basename($Mp3Data["name"]);

                $FileType = strtolower(pathinfo($Target_file,PATHINFO_EXTENSION));

                $Target_file = $_SERVER['DOCUMENT_ROOT'] . $Target_dir . basename("$NameSong.$FileType");
                
                $Output[static::JSON_CONTENT] = $Target_file;

                if($Mp3Data["size"] > 3000000)
                {
                    $Output[static::JSON_HEADER]["Code"] = -1;
                    $Output[static::JSON_HEADER]["Message"] = "File is bigger than 3mb!";
                }

                if($FileType != "mp3") {
                    $Output[static::JSON_HEADER]["Code"] = -1;
                    $Output[static::JSON_HEADER]["Message"] = "File is not mp3!";
                }

                if (file_exists($Target_file)) {
                    $Output[static::JSON_HEADER]["Code"] = -1;
                    $Output[static::JSON_HEADER]["Message"] = "File with this name exists";
                }

                if($Output[static::JSON_HEADER]["Code"] != -1)
                {
                    if (move_uploaded_file($_FILES['song']["tmp_name"], $Target_file))
                    {
                        $AppMusic = new MusicPlaylist();
                        $AppMusic->songname = $NameSong_Orig;
                        $AppMusic->songfile = $Target_dir . basename("$NameSong.$FileType");
                        $AppMusic->imagefile = null;
                        $AppMusic->genre = $Genre;
                        $AppMusic->whoupload = Yii::$app->user->getId();

                        if($AppMusic->save())
                        {
                            $Output[static::JSON_HEADER]["Code"] = 0;
                            $Output[static::JSON_HEADER]["Message"] = "Uploaded!";
                        }
                        else
                        {
                            unlink($Target_file);
                        }                        
                    }
                    else
                    {
                        $Output[static::JSON_HEADER]["Code"] = -1;
                        $Output[static::JSON_HEADER]["Message"] = "File with this name exists";
                    }
                }

                return json_encode($Output);
            }
            else
            {
                $Output[static::JSON_HEADER]["Code"] = -1;
                $Output[static::JSON_HEADER]["Message"] = "File is empty!";
            }

            return json_encode($Output);
        }
        else
        {

            $AllGenres = Genres::find()->all();

            return $this->render('upload', compact('AllGenres'));
        }
    }
}

?>