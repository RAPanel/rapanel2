<?php

namespace ra\admin\models;

use ra\admin\helpers\RA;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Request;

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
 * @property Photo $photo
 * @property Photo[] $photos
 */
class Page extends \yii\db\ActiveRecord
{
    use \ra\admin\traits\PageEdit;

    public $defaultPrice = 1;
    private $_characters = [];

    public static function instantiate($row)
    {
        if (!empty($row['module_id'])) {
            $class = RA::module($row['module_id'], 'class');
            return new $class;
        }
        return parent::instantiate($row);
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
            [['url', 'name'], 'string', 'max' => 255],
            [['about'], 'string', 'max' => 2560],
            [['pageData', 'pageCharacters', 'characters', 'photos'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ra', 'ID'),
            'is_category' => Yii::t('ra', 'Is Category'),
            'status' => Yii::t('ra', 'Status'),
            'lft' => Yii::t('ra', 'Lft'),
            'rgt' => Yii::t('ra', 'Rgt'),
            'level' => Yii::t('ra', 'Level'),
            'parent_id' => Yii::t('ra', 'Parent ID'),
            'module_id' => Yii::t('ra', 'Module ID'),
            'user_id' => Yii::t('ra', 'User ID'),
            'url' => Yii::t('ra', 'Url'),
            'name' => Yii::t('ra', 'Name'),
            'about' => Yii::t('ra', 'About'),
            'updated_at' => Yii::t('ra', 'Updated At'),
            'created_at' => Yii::t('ra', 'Created At'),
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
        return $this->hasMany(PageCharacters::className(), ['page_id' => 'id'])->with(['reference']);
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
    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['owner_id' => 'id'])
            ->andOnCondition(['model' => self::tableName()])->orderBy(['sort_id' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(Photo::className(), ['owner_id' => 'id'])
            ->andOnCondition(['model' => self::tableName(), 'type' => 'main'])->orderBy(['sort_id' => SORT_ASC]);
    }

    public function getExistCharacters()
    {
        return $this->hasMany(Character::className(), ['id' => 'character_id'])->viaTable(CharacterShow::tableName(), ['module_id' => 'module_id', 'filter' => 'is_category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPageData()
    {
        return $this->hasOne(PageData::className(), ['page_id' => 'id']);
    }

    public function getRoot()
    {
        return $this->hasOne($this, ['module_id' => 'module_id'])->andOnCondition(['is_category' => 1, 'level' => 0, 'parent_id' => null])->andOnCondition('rgt>lft');
    }

    public function getData()
    {
        return $this->pageData ?: (new PageData());
    }

    public function getHeader()
    {
        return $this->data && $this->data->header ? $this->data->header : $this->name;
    }

    public function getContent()
    {
        $data = $this->data && $this->data->content ? $this->data->content : null;
        if ($data && preg_match_all('#\{{2,3}([\w\s]+)(\:{[^}]+})?\}{2,3}#u', $data, $matches) && !empty($matches[1])) {
            $list = ArrayHelper::map(Replaces::find()->where(['name' => $matches[1]])->asArray()->select(['name', 'value'])->all(), 'name', 'value');
            foreach ($matches[1] as $key => $name) {
                $function = $list[$name];
                if (strpos($function, '$') !== false || strpos($function, '<?') !== false) {
                    $tmp = tempnam('/tmp', $function);
                    file_put_contents($tmp, $function);
                    $params = ltrim($matches[2][$key], ':');
                    if ($params) $params = (array)json_decode($params);
                    $params = ArrayHelper::merge($params, ['model' => $this, 'this' => Yii::$app->view]);
                    $result = Yii::$app->view->renderPhpFile($tmp, $params);
                } else $result = $function;
                $data = str_replace($matches[0][$key], $result, $data);
            }
        }
        return $data;
    }

    public function getTags()
    {
        return $this->data && $this->data->tags ? array_diff(array_map('trim', explode(',', $this->data->tags)), ['', null, false, 0]) : [];
    }

    public function getLabel()
    {
        return $this->name;
    }

    public function getActive()
    {
        return $this->getHref() == \Yii::$app->request->pathInfo ?: null;
    }

    public function getHref($normalizeUrl = true, $scheme = false, $parent = false)
    {
        if (strpos($this->url, '/') !== false) {
            if ($normalizeUrl) return Url::to($this->url, $scheme);
            $parse = Yii::$app->urlManager->parseRequest(new Request(['url' => $this->url]));
            return array_filter($parse, function ($el) {
                return !empty($el);
            });
        }
        $module = RA::module($this->module_id);
        $moduleSettings = RA::moduleSetting($this->module_id);
        $action = 'show';
        $additional = [];
        if (!empty($moduleSettings['hasCategory'])) {
            if ($this->is_category) {
                $action = 'category';
            } elseif ($this->parent && $this->parent->is_category && $this->parent->level) {
                $additional['parent'] = $this->parent->url;
            }
        } elseif ($parent)
            $additional['parent'] = $parent;
        if (RA::module($this->url)) $url = ["/{$this->url}/index"];
        elseif ($this->url) $url = ["/{$module}/{$action}", 'url' => $this->url] + $additional;
        else $url = ["/{$module}/{$action}", 'id' => $this->id] + $additional;
        return $normalizeUrl ? Url::to($url, $scheme) : $url;
    }

    public function getCharacterName($url)
    {
        return Yii::t('app\character', Inflector::camel2words($url));
    }

    /**
     * @param string $url
     * @return string|array|null|self
     */
    public function getCharacter($url)
    {
        if ($this->isRelationPopulated($relation = 'character' . ucfirst($url)))
            return $this->{$relation} ? $this->{$relation}->value : null;
        $characters = $this->getCharacters();
        return isset($characters[$url]) ? $characters[$url] : null;
    }

    /**
     * @param bool $refresh
     * @return array
     */
    public function getCharacters($refresh = false)
    {
        if (empty($this->_characters) || $refresh) {
            foreach ($this->pageCharacters as $row) {
                $result = $row->value;
                if (RA::character($row->character_id, 'type') == 'dropdown' && $row->reference)
                    $result = $row->reference->value;
                elseif (RA::character($row->character_id, 'type') == 'extend' )
                    $result = is_array($result) ? $this::findAll(array_diff($result, ['', null, false])) : $this::findOne($result);
                $this->_characters[RA::character($row->character_id)] = $result;
            }
        }
        return $this->_characters;
    }

    public function getPhotoImg($size, $options = [])
    {
        /** @var Photo $photo */
        $relation = isset($options['relation']) ? $options['relation'] : 'photo';
        $photo = $this->{$relation};
        if (empty($options['alt'])) $options['alt'] = $photo && trim($photo->about) ? $photo->about : $this->name;
        return Html::img($this->getPhotoHref($size, !empty($options['absoluteUrl']), $relation), $options);
    }

    public function getPhotoHref($size, $scheme = false, $relation = 'photo')
    {
        /** @var Photo $photo */
        $photo = $this->{$relation};
        return $photo ? $photo->getHref($size, $scheme) : '/image/_' . $size . '/default.jpg';
    }

    public function __toString()
    {
        return (string)$this->id ?: '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(self::className(), ['module_id' => 'module_id'])->andOnCondition(['is_category' => 0, 'status' => 1,
            'parent_id' => Page::findActive($this->module_id, ['is_category' => 1], true)->select('id')->orderBy(false)->andWhere(['between', 'lft', $this->lft, $this->rgt])]);
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
        $sort = RA::moduleSetting($module, 'sort') == 0 ? SORT_ASC : SORT_DESC;
        $order = RA::moduleSetting($module, 'hasCategory') ? ['t.is_category' => SORT_DESC] : [];
        $query = self::find()->from(['t' => self::tableName()])->orderBy($order + ['t.lft' => $sort, 't.id' => $sort]);
        if (!$allStatuses) $query->where(['t.status' => [1, 2]]);
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

    public function getModuleUrl()
    {
        return RA::module($this->module_id);
    }

    public function getPagePrices()
    {
        return $this->hasMany(PagePrice::className(), ['page_id' => 'id']);
    }

    public function getPagePrice()
    {
        return $this->hasOne(PagePrice::className(), ['page_id' => 'id'])->andOnCondition(['type_id' => $this->defaultPrice]);
    }

    public function getLastLevel()
    {
        return !$this->is_category || $this->rgt - $this->lft == 1;
    }

    public function getRelation($name, $throwException = true)
    {
        if (preg_match('#^character([\w\d]+)$#', $name, $match) && ($id = Yii::$app->ra->getCharacterId(lcfirst($match[1])))) {
            $relation = $this->hasOne(PageCharacters::className(), ['page_id' => 'id'])
                ->andOnCondition(['character_id' => $id]);
        } else
            $relation = parent::getRelation($name, $throwException);

        /** @var ActiveRecord $model */
        $model = $relation->modelClass;
        return $relation->from([$name => $model::tableName()]);
    }

    public function getCreated()
    {
        return Yii::$app->formatter->asDate($this->created_at);
    }
}
