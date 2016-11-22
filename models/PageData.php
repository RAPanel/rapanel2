<?php

namespace ra\admin\models;

use Yii;
use yii\db\Exception;
use yii\db\Transaction;

/**
 * This is the model class for table "{{%page_data}}".
 *
 * @property string $page_id
 * @property string $header
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $content
 * @property string $tags
 *
 * @property Page $page
 */
class PageData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_data}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id'], 'required'],
            [['page_id'], 'integer'],
            [['content', 'tags'], 'string'],
            [['header', 'title', 'description', 'keywords'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => Yii::t('ra', 'Page ID'),
            'title' => Yii::t('ra', 'Header'),
            'header' => Yii::t('ra', 'Title'),
            'description' => Yii::t('ra', 'Description'),
            'keywords' => Yii::t('ra', 'Keywords'),
            'content' => Yii::t('ra', 'Content'),
            'tags' => Yii::t('ra', 'Tags'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'page_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIndexes()
    {
        return $this->hasMany(Index::className(), ['owner_id' => 'page_id'])->andOnCondition(['indexes.model' => 'Page'])->joinWith('data');
    }

    public function beforeValidate()
    {
        if (empty($this->header) && $this->page->name)
            $this->header = $this->page->name;

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->isAttributeChanged('tags')) {
            $tags = array_map('trim', explode(',', $this->getAttribute('tags')));
            $oldTags = array_map('trim', explode(',', $this->getOldAttribute('tags')));

            $addTags = array_diff($tags, $oldTags, ['']);
            $deleteTags = array_diff($oldTags, $tags, ['']);

            $properties = ['type' => 'tags', 'owner_id' => $this->page_id, 'model' => 'Page'];

            $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
            try {
                if (!empty($deleteTags)) {
                    $delete = [];
                    foreach ($this->indexes as $row) {
                        if (in_array($row->data->value, $deleteTags)) $delete[] = $row->data->id;
                        if (in_array($row->data->value, $addTags)) unset($addTags[array_search($row->data->value, $addTags)]);
                    }
                    Index::deleteAll(['data_id' => $delete] + $properties);
                }

                if (!empty($addTags)) foreach ($addTags as $row) {
                    $model = new Index();
                    $model->data = $row;
                    $model->setAttributes($properties);
                    $model->save(false);
                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                $this->addError('tags', Yii::t('ra', 'Can`t save tags!'));
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        if (strpos($this->content, '<iframe'))
            $this->content = preg_replace('%(<iframe.+)(<\\/iframe>|\\/>)%', '<div class="flex-video">$1$2</div>', $this->content);
    }

    public function getContent()
    {
        $content = $this->content;
        $list = Replaces::find()->select('name')->asArray()->column();
        if (!empty($list)) {
            preg_match_all('@{{(' . implode('|', $list) . ')[^}]*}}@', $this->content, $matches);
            if (!empty($matches[1])) {
                $search = $replace = [];
                $forReplace = Replaces::find()->select('name, value')->where(['name' => $matches[1]])->asArray()->all();
                foreach ($forReplace as $row) {
                    if (strpos($row['value'], '?>') !== false || strpos($row['value'], '<?') !== false) {
                        ob_start();
                        eval (' ?' . '>' . $row['value'] . '<' . '?php ');
                        $replace[$row['name']] = ob_get_clean();
                    } else $replace[$row['name']] = $row['value'];
                }
                foreach ($matches[0] as $key => $value)
                    $search[$matches[1][$key]] = $value;
                $content = str_replace($search, $replace, $content);
            }
        }
        return $content;
    }
}
