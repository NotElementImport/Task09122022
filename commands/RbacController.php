<?php
namespace app\commands;
 
use Yii;
use yii\console\Controller;
use app\models\Users;
 
class RbacController extends Controller
{
    public function actionInit()
    {
        $AuthData = Yii::$app->authManager;
        $AuthData->removeAll();

        $BasicPermission = $AuthData->createPermission('BasicPermission');
        $BasicPermission->description = 'Use a site';
        $AuthData->add($BasicPermission);

        $RoleUser = $AuthData->createRole('User');
        $RoleUser->description = "Basic user a site";
        $AuthData->add($RoleUser);
        $AuthData->addChild($RoleUser, $BasicPermission);

        $AuthData->assign($RoleUser, 1);

        $UserOne = new Users();
        $UserOne->username = "jonhdoe_1";
        $UserOne->email = "jonh1@email";
        $UserOne->setPassword("jonh_1");
        $UserOne->generateAuthKey();
        if(!$UserOne->save())
        {
        }

        $UserRole = $AuthData->getRole('User');

        $AuthData->assign($UserRole, $UserOne->getId());

        $UserTwo = new Users();
        $UserTwo->username = "jonhdoe_2";
        $UserTwo->email = "jonh2@email";
        $UserTwo->setPassword("jonh_2");
        $UserTwo->generateAuthKey();
        if(!$UserTwo->save())
        {
        }

        $AuthData->assign($UserRole, $UserTwo->getId());

        $UserThree = new Users();
        $UserThree->username = "jonhdoe_3";
        $UserThree->email = "jonh3@email";
        $UserThree->setPassword("jonh_3");
        $UserThree->generateAuthKey();
        if(!$UserThree->save())
        {
            
        }

        $AuthData->assign($UserRole, $UserThree->getId());
    }
}