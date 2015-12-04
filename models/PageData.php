<?php

namespace ra\admin\models;

use Yii;
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

    public function beforeValidate()
    {
        if (empty($this->header) && $this->page->name)
            $this->header = $this->page->name;
        if (empty($this->title) && $this->page->name)
            $this->title = $this->page->name;
        if (empty($this->description) && $this->page->about) {
            $this->description = preg_replace('#[\r\n\s]+#', ' ', trim($this->page->about));
            if (mb_strlen($this->description) > 255)
                $this->description = mb_substr($this->description, 0, mb_strrpos(mb_substr($this->description, 0, 255, 'UTF-8'), ' ', 0, 'UTF-8'), 'UTF-8');
        }

        if (empty($this->keywords) && $this->tags)
            $this->keywords = $this->tags;

        return parent::beforeValidate();
    }

    public function beforeSave()
    {
        if ($this->isAttributeChanged('tags')) {
            $transaction = Yii::$app->db->beginTransaction(Transaction::READ_COMMITTED);
            $tags = array_map('trim', explode(',', $this->getAttribute('tags')));
            $oldTags = array_map('trim', explode(',', $this->getOldAttribute('tags')));

            $addTags = array_diff($tags, $oldTags, ['']);
            $deleteTags = array_diff($oldTags, $tags, ['']);

            $properties = ['type' => 'tags', 'owner_id' => $this->page_id, 'model' => 'Page'];

            if (!empty($addTags)) {
                foreach ($addTags as $row) {
                    $model = new Index();
                    $model->data = $row;
                    $model->setAttributes($properties);
                    $model->save(false);
                }
            }

            if (!empty($deleteTags)) {
                $delete = [];
                foreach ($this->indexes as $row) {
                    if (in_array($row->data->value, $deleteTags)) $delete[] = $row->data->value;
                }
                Index::deleteAll(['data_id' => $delete] + $properties);
            }
            $transaction->commit();
        }
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
