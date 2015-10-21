<?php

namespace app\admin\models;

use app\admin\behaviors\PageHasManyBehavior;
use app\admin\helpers\RA;
use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%page}}".
 *
 * @property string $id
 * @property integer $is_category
 * @property integer $status
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 * @property string $parent_id
 * @property string $module_id
 * @property integer $user_id
 * @property string $url
 * @property string $name
 * @property string $about
 * @property string $updated_at
 * @property string $created_at
 *
 * @property CharacterShow[] $characterShows
 * @property Page $parent
 * @property Page[] $pages
 * @property User $user
 * @property Module $module
 * @property PageCharacters[] $pageCharacters
 * @property PageComments[] $pageComments
 * @property PageData $pageData
 */
class Page extends \yii\db\ActiveRecord
{
    use \app\admin\traits\PageEdit;

    private $_characters = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'module_id'], 'required'],
            [['is_category', 'status', 'lft', 'rgt', 'level', 'parent_id', 'module_id', 'user_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['url', 'name', 'about'], 'string', 'max' => 255],
            [['url', 'name', 'about'], 'string', 'max' => 255],
            [['url', 'name', 'about'], 'string', 'max' => 255],
            [['pageData', 'pageCharacters', 'photos'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra/model', 'ID'),
            'is_category' => Yii::t('ra/model', 'Is Category'),
            'status' => Yii::t('ra/model', 'Status'),
            'lft' => Yii::t('ra/model', 'Lft'),
            'rgt' => Yii::t('ra/model', 'Rgt'),
            'level' => Yii::t('ra/model', 'Level'),
            'parent_id' => Yii::t('ra/model', 'Parent ID'),
            'module_id' => Yii::t('ra/model', 'Module ID'),
            'user_id' => Yii::t('ra/model', 'User ID'),
            'url' => Yii::t('ra/model', 'Url'),
            'name' => Yii::t('ra/model', 'Name'),
            'about' => Yii::t('ra/model', 'About'),
            'updated_at' => Yii::t('ra/model', 'Updated At'),
            'created_at' => Yii::t('ra/model', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharacterShows()
    {
        return $this->hasMany(CharacterShow::className(), ['page_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Page::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Page::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageCharacters()
    {
        return $this->hasMany(PageCharacters::className(), ['page_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageComments()
    {
        return $this->hasMany(PageComments::className(), ['page_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageData()
    {
        return $this->hasOne(PageData::className(), ['page_id' => 'id']);
    }

    public static function instantiate($row)
    {
        if (!empty($row['module_id'])) {
            $class = RA::module($row['module_id'], 'class');
            return new $class;
        }
        return new static;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['owner_id' => 'id'])
            ->where(['model' => self::tableName()])->orderBy(['sort_id' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(Photo::className(), ['owner_id' => 'id'])
            ->where(['model' => self::tableName()])->orderBy(['sort_id' => SORT_ASC]);
    }

    public function getExistCharacters()
    {
        return $this->hasMany(Character::className(), ['id' => 'character_id'])->viaTable(CharacterShow::tableName(), ['module_id' => 'module_id', 'filter' => 'is_category']);
    }

    public function setPageData($data)
    {
        $this->doEditable();
        $model = $this->pageData;
        if (!$model && ($class = $this->getPageData()->modelClass)) {
            $model = new $class;
            $model->page_id = $this->id;
        }
        $model->setAttributes($data);
        if ($this->isNewRecord) $this->on(self::EVENT_AFTER_INSERT, function ($event) {
            $event->data->setAttributes(['page_id' => $event->sender->id]);
            $event->data->save(false);
        }, $model);
        else $model->save(false);
    }

    public function getHref($normalizeUrl = true, $scheme = false)
    {
        if (strpos($this->url, '/') !== false) return $this->url;
        $module = RA::module($this->module_id);
        $moduleSettings = RA::moduleSetting($this->module_id);
        $action = 'show';
        $additional = [];
        if (!empty($moduleSettings['hasCategory'])) {
            if ($this->is_category) {
                $action = 'category';
            } elseif ($this->parent && $this->parent->is_category) {
                $additional['parent'] = $this->parent->url;
            }
        }
        if (RA::module($this->url)) $url = ["/{$this->url}/index"];
        else $url = ["/{$module}/{$action}", 'url' => $this->url] + $additional;
        return $normalizeUrl ? Url::to($url, $scheme) : $url;
    }

    public function getRoot()
    {
        return $this->hasOne($this, ['module_id' => 'module_id'])->where(['is_category' => 1, 'level' => 0, 'parent_id' => null])->andWhere('rgt>lft');
    }

    public function getData()
    {
        return $this->pageData;
    }

    public function getContent()
    {
        return $this->data ? $this->data->content : null;
    }

    public function getLabel()
    {
        return $this->name;
    }

    public function getActive()
    {
        return $this->getHref() == \Yii::$app->request->pathInfo ?: null;
    }

    public function getCharacterName($url){
        return Yii::t('app/character', Inflector::camel2words($url));
    }

    public function getCharacter($url = null)
    {
        $characters = $this->getCharacters();
        return isset($characters[$url]) ? $characters[$url] : null;
    }

    public function getCharacters($url = null, $refresh = false)
    {
        if (empty($this->_characters) || $refresh) {
            foreach ($this->pageCharacters as $row)
                $this->_characters[RA::character($row->character_id)] = $row->value;
        }
        return is_null($url) ? $this->_characters : (isset($this->_characters[$url]) ? $this->_characters[$url] : null);
    }

    public function getPhotoHref($size, $scheme = false)
    {
        /** @var Photo $photo */
        $photo = $this->photo;
        return $photo ? $photo->getHref($size, $scheme) : '/image/_' . $size . '/default.jpg';
    }

    public function getPhotoImg($size, $options = [])
    {
        /** @var Photo $photo */
        $photo = $this->photo;
        if(empty($options['alt'])) $options['alt'] = $photo ? $photo->about : $this->name;
        return Html::img($this->getPhotoHref($size), $options);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id'])->viaTable(self::tableName(), ['module_id' => 'module_id'],
            function ($query) {
                $query->select('id')->onCondition(['between', 'lft', $this->lft, $this->rgt]);
            })->where(['is_category' => 0, 'module_id' => $this->module_id]);
    }

    public function getModuleUrl()
    {
        return RA::module($this->module_id);
    }

    /**
     * @param $module string|int
     * @param array $condition
     * @param bool $withRoot
     * @param bool $allStatuses
     * @return \yii\db\ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function findActive($module = null, $condition = [], $withRoot = false, $allStatuses = false)
    {
        $query = self::find()->from(['t' => self::tableName()])->orderBy(['t.lft' => SORT_ASC, 't.id' => SORT_ASC]);
        if (!$allStatuses) $query->where(['t.status' => 1]);
        if (!empty($module))
            if (is_array($module)) {
                $subQuery = Module::find()->select('id')->where(['or', ['id' => $module], ['url' => $module]]);
                if (!$withRoot) $query->andWhere(['not', ['t.id' => $subQuery]]);
                $query->andWhere(['t.module_id' => $subQuery]);
            } else {
                if (!$withRoot) $query->andWhere(['!=', 't.id', RA::moduleId($module)]);
                $query->andWhere(['t.module_id' => RA::moduleId($module)]);
            }
        if (!empty($condition)) $query->andWhere($condition);
        return $query;
    }
}
