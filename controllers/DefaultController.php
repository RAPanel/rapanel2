<?php

namespace ra\admin\controllers;

use ra\admin\helpers\RA;
use ra\admin\models\forms\LoginForm;
use ra\admin\models\Index;
use ra\admin\models\PageData;
use ra\admin\models\UserAuth;
use Yii;
use yii\db\Transaction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class DefaultController extends AdminController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'goAdmin', 'auth'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',

            ],
        ];
    }

    /**
     * Display login page
     */
    public function actionLogin()
    {
        $this->layout = 'lock';

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login(RA::config('user')['loginDuration'])) {
            return $this->goBack(['rapanel/default/index']);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Log user out and redirect
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        $logoutRedirect = RA::config('user')['logoutRedirect'];
        if ($logoutRedirect === null)
            return $this->goHome();
        else
            return $this->redirect($logoutRedirect);
    }

    /**
     * @param $client \yii\authclient\clients\YandexOAuth
     */
    public function onAuthSuccess($client)
    {
        UserAuth::add($client->getId(), $client->userAttributes['id'], $client->getAccessToken()->getParams());

        return $this->redirect(['success']);
    }

    public function actionSuccess()
    {
        return $this->renderContent('<h2>Спасибо за авторизацию</h2>');
    }

    public function actionIndex()
    {
        return $this->redirect(['module/index']);
    }

    public function actionUpdate()
    {
        $dir = Yii::getAlias('@app');

        $commands = [
            'update' => PHP_BINDIR . "/php {$dir}/yii admin/update &> /dev/null &",
            'composer' => 'curl -sS https://getcomposer.org/installer | php',
            'fxp' => 'PHP_BINDIR . "/php {$dir}/composer.phar global require "fxp/composer-asset-plugin:~1.1.1"',
            'version' => PHP_BINDIR . "/php {$dir}/composer.phar --version --working-dir={$dir}",
            'info' => PHP_BINDIR . "/php {$dir}/composer.phar show -i --working-dir={$dir}",
        ];

        if (Yii::$app->request->isPost) {
            ignore_user_abort();
            set_time_limit(0);
            exec($commands['update']);
            return $this->refresh();
        } elseif (!file_exists("{$dir}/composer.phar")) {
            chdir(Yii::getAlias('@app'));
            exec($commands['composer']);
            exec($commands['fxp']);
        }

        return $this->render('update', compact('dir', 'commands'));
    }

    public function actionFileManager()
    {
        $dir = Yii::getAlias('@webroot/source');
        if (!file_exists($dir)) mkdir($dir);
        return $this->render('fileManager');
    }

    public function actionGoSite()
    {
        $url = Yii::$app->session->get('siteToAdminUrl', '/');
        Yii::$app->session->set('adminToSiteUrl', Yii::$app->request->referrer);
        if (stripos($url, '/rapanel/') !== false) $url = '/';
        return $this->redirect($url);
    }

    public function actionGoAdmin()
    {
        $url = Yii::$app->session->get('adminToSiteUrl', '/rapanel');
        Yii::$app->session->set('siteToAdminUrl', Yii::$app->request->referrer);
        if (stripos($url, '/rapanel/') === false) $url = '/rapanel';
        return $this->redirect($url);
    }

    public function actionIndexUpdate()
    {
        $transaction = Yii::$app->db->beginTransaction(Transaction::READ_UNCOMMITTED);

        $list = PageData::find()->select(['page_id', 'tags'])->where(['!=', 'tags', ''])->asArray()->all();
        $properties = ['type' => 'tags', 'model' => 'Page'];
        Index::deleteAll($properties);
        foreach ($list as $row) foreach (array_map('trim', explode(',', $row['tags'])) as $tag) {
            $model = new Index();
            $model->data = $tag;
            $model->owner_id = $row['page_id'];
            $model->setAttributes($properties, false);
            $model->save(false);
        }

        Yii::$app->db->createCommand('DELETE `i` FROM `ra_index_data` `i` LEFT OUTER JOIN `ra_index` ON id=data_id WHERE ISNULL(owner_id)')->execute();

        $transaction->commit();

        return $this->goBack();
    }
}
