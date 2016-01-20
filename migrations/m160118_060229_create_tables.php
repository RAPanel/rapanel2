<?php

use yii\db\Migration;

class m160118_060229_create_tables extends Migration
{
    public function up()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'path' => $this->string(64),
            'inputType' => $this->string(8),
            'name' => $this->string(64),
            'value' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->createTable('replaces', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64),
            'value' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        $this->createTable('session', [
            'id' => $this->primaryKey(40),
            'expire' => $this->integer(),
            'data' => $this->binary(),
        ]);

        $this->createTable('reference', [
            'id' => $this->primaryKey(),
            'value' => $this->string(),
        ]);

        $this->createTable('exchange', [
            'id' => $this->primaryKey(),
            'type' => $this->string(12),
            'value' => $this->integer(),
        ]);

        $this->createTable('data_serialize', [
            'id' => $this->primaryKey(),
            'type' => $this->string(8),
            'value' => $this->binary(),
        ]);

        $this->createTable('price_type', [
            'id' => $this->primaryKey(),
            'sort_id' => $this->smallInteger(),
            'is_default' => $this->smallInteger(1),
            'name' => $this->string(),
            'currency' => $this->string(8),
        ]);

        $this->createTable('photo', [
            'id' => $this->primaryKey(),
            'sort_id' => $this->integer(),
            'owner_id' => $this->integer(),
            'model' => $this->string(32),
            'type' => $this->string(8),
            'name' => $this->string(),
            'width' => $this->smallInteger(),
            'height' => $this->smallInteger(),
            'about' => $this->string(1024),
            'cropParams' => $this->string(),
            'hash' => $this->string(32),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        $this->createTable('subscribe', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'email' => $this->string(),
            'data' => $this->binary(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        $this->createTable('index', [
            'owner_id' => $this->integer(),
            'model' => $this->string(16),
            'type' => $this->string(16),
            'data_id' => $this->integer(),
        ]);
        $this->createTable('index_data', [
            'id' => $this->primaryKey(),
            'value' => $this->string(64),
        ]);

        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'category' => $this->string(32),
            'message' => $this->text(),
        ]);
        $this->createTable('message_translate', [
            'id' => $this->primaryKey(),
            'language' => $this->primaryKey(16),
            'translation' => $this->text(),
        ]);
        $this->addForeignKey('message_translate__id', 'message_translate', 'id', 'message', 'id', 'CASCADE');

        $this->createTable('module', [
            'id' => $this->primaryKey(),
            'url' => $this->string(16),
            'name' => $this->string(64),
            'class' => $this->string(256),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->createTable('module_settings', [
            'module_id' => $this->integer(),
            'sort' => $this->smallInteger(),
            'url' => $this->string(16),
            'value' => $this->string(1024),
        ]);
        $this->addPrimaryKey('pk', 'module_settings', ['module_id', 'url']);
        $this->addForeignKey('module_settings__module_id', 'module_settings', 'module_id', 'module', 'id', 'CASCADE');

        $this->createTable('character', [
            'id' => $this->primaryKey(),
            'url' => $this->string(32),
            'type' => $this->string(8),
            'multi' => $this->smallInteger(1),
            'data' => $this->binary(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->createTable('character_reference', [
            'character_id' => $this->primaryKey(),
            'reference_id' => $this->primaryKey(),
        ]);
        $this->addForeignKey('character_reference__character_id', 'character_reference', 'character_id', 'character', 'id', 'CASCADE');
        $this->addForeignKey('character_reference__reference_id', 'character_reference', 'reference_id', 'reference', 'id', 'CASCADE');
        $this->createTable('character_show', [
            'character_id' => $this->integer()->notNull(),
            'module_id' => $this->integer()->notNull(),
            'page_id' => $this->integer()->notNull()->defaultValue(0),
            'filter' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'sort_id' => $this->integer()->notNull()->defaultValue(0),
        ]);
        $this->addPrimaryKey('pk', 'character_show', ['character_id', 'module_id', 'page_id', 'filter']);
        $this->addForeignKey('character_show__character_id', 'character_show', 'character_id', 'character', 'id', 'CASCADE');
        $this->addForeignKey('character_show__module_id', 'character_show', 'module_id', 'module', 'id', 'CASCADE');

        $this->createTable('user_role', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'can_admin' => $this->smallInteger(1),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer(),
            'status' => $this->smallInteger(),
            'email' => $this->string()->unique(),
            'new_email' => $this->string(),
            'username' => $this->string()->unique(),
            'password' => $this->string(),
            'auth_key' => $this->string(),
            'api_key' => $this->string(),
            'login_ip' => $this->string(),
            'create_ip' => $this->string(),
            'ban_reason' => $this->string(),
            'login_time' => $this->dateTime(),
            'ban_time' => $this->dateTime(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('user__role_id', 'user', 'role_id', 'user_role', 'id');
        $this->createTable('user_auth', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'provider' => $this->string(),
            'provider_id' => $this->string(),
            'provider_attributes' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('user_auth__user_id', 'user_auth', 'user_id', 'user', 'id', 'CASCADE');
        $this->createTable('user_key', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'type' => $this->smallInteger(6),
            'key_value' => $this->string(),
            'expire_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
        ]);
        $this->addForeignKey('user_key__user_id', 'user_key', 'user_id', 'user', 'id', 'CASCADE');
        $this->createTable('user_profile', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'full_name' => $this->string(),
            'city' => $this->string(),
            'vk' => $this->string(),
            'fb' => $this->string(),
            'ig' => $this->string(),
            'tw' => $this->string(),
            'options' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('user_key__user_id', 'user_key', 'user_id', 'user', 'id', 'CASCADE');
        $this->createTable('user_remember', [
            'user_id' => $this->integer(),
            'key' => $this->string(32),
            'value' => $this->string(16),
        ]);
        $this->addForeignKey('user_remember__user_id', 'user_remember', 'user_id', 'user', 'id', 'CASCADE');

        $this->createTable('pay_type', [
            'id' => $this->primaryKey(),
            'sort_id' => $this->smallInteger(),
            'name' => $this->string(),
            'type' => $this->string(32),
            'data' => $this->binary(),
        ]);
        $this->createTable('delivery_type', [
            'id' => $this->primaryKey(),
            'sort_id' => $this->smallInteger(),
            'name' => $this->string(),
            'type' => $this->string(32),
            'data' => $this->binary(),
        ]);
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'session_id' => $this->string(40),
            'status' => $this->smallInteger(),
            'item_id' => $this->string(32),
            'order_id' => $this->integer(),
            'data' => $this->binary(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'session_id' => $this->string(40),
            'status_id' => $this->smallInteger(),
            'is_payed' => $this->smallInteger(1),
            'delivery_id' => $this->integer(),
            'pay_id' => $this->integer(),
            'data' => $this->binary(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);

        $this->createTable('page', [
            'id' => $this->primaryKey(),
            'is_category' => $this->smallInteger(1),
            'status' => $this->smallInteger(1),
            'lft' => $this->integer(),
            'rgt' => $this->integer(),
            'level' => $this->smallInteger(),
            'parent_id' => $this->integer(),
            'module_id' => $this->integer(),
            'user_id' => $this->integer(),
            'url' => $this->string(),
            'name' => $this->string(),
            'about' => $this->string(1024),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('page__parent_id', 'page', 'parent_id', 'page', 'id');
        $this->addForeignKey('page__user_id', 'page', 'user_id', 'user', 'id');
        $this->addForeignKey('page__module_id', 'page', 'user_id', 'module', 'id');
        $this->createTable('page_characters', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer(),
            'character_id' => $this->integer(),
            'value' => $this->text(),
        ]);
        $this->addForeignKey('page_characters__page_id', 'page_characters', 'page_id', 'page', 'id', 'CASCADE');
        $this->addForeignKey('page_characters__character_id', 'page_characters', 'character_id', 'character', 'id', 'CASCADE');
        $this->createTable('page_comments', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer(),
            'user_id' => $this->integer(),
            'parent_id' => $this->integer(),
            'rating' => $this->smallInteger(),
            'text' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('page_comments__page_id', 'page_comments', 'page_id', 'page', 'id', 'CASCADE');
        $this->addForeignKey('page_comments__user_id', 'page_comments', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('page_comments__parent_id', 'page_comments', 'parent_id', 'page_comments', 'id');
        $this->createTable('page_counts', [
            'page_id' => $this->primaryKey(),
            'views' => $this->integer(),
            'likes' => $this->integer(),
            'comments' => $this->integer(),
        ]);
        $this->addForeignKey('page_counts__page_id', 'page_counts', 'page_id', 'page', 'id', 'CASCADE');
        $this->createTable('page_price', [
            'id' => $this->primaryKey(),
            'page_id' => $this->integer(),
            'type_id' => $this->integer(),
            'unit' => $this->string(8),
            'value' => $this->float(11.2),
            'count' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('page_price__page_id', 'page_price', 'page_id', 'page', 'id', 'CASCADE');
        $this->addForeignKey('page_price__type_id', 'page_price', 'type_id', 'price_type', 'id', 'CASCADE');
        $this->createTable('page_session_data', [
            'page_id' => $this->integer(),
            'session' => $this->string(40),
            'type' => $this->string(8),
            'value' => $this->string(16),
            'last_visit' => $this->timestamp()->notNull(),
        ]);
        $this->addForeignKey('page_session_data__page_id', 'page_session_data', 'page_id', 'page', 'id', 'CASCADE');
    }

    public function down()
    {
        echo "m160118_060229_create_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}