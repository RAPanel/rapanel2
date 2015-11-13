<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 13.11.2015
 * Time: 13:46
 */

namespace ra\admin\filter;


use Yii;
use yii\base\Action;
use yii\base\ActionFilter;

class ReturnUrl extends ActionFilter
{
    public $returnUrlParam;
    public $ajaxRequest = true;

    public function init()
    {
        if ($this->returnUrlParam)
            Yii::$app->user->returnUrlParam = $this->returnUrlParam;
        parent::init();
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->request->getIsAjax() || $this->ajaxRequest) {
            $url = Yii::$app->request->getUrl();
            if (Yii::$app->request->getIsAjax())
                $url = preg_replace('#ajax=[^&]*&?#', '', $url);


            Yii::$app->user->setReturnUrl(Yii::$app->request->getUrl());
        }
        return true;
    }

}