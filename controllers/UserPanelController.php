<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 30.06.2016
 * Time: 16:27
 */

namespace ra\admin\controllers;


class UserPanelController extends Controller
{
    public function actionPanel()
    {
        return $this->render('panel');
    }

}