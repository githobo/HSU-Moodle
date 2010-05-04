<?php

class Comment extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Comment':
	 * @var integer $id
	 * @var string $content
	 * @var string $contentDisplay
	 * @var integer $status
	 * @var integer $createTime
	 * @var string $author
	 * @var string $email
	 * @var string $url
	 * @var integer $postId
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('author','length','max'=>128),
			array('email','length','max'=>128),
			array('url','length','max'=>128),
			array('status, postId', 'required'),
			array('status, createTime, postId', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'content' => 'Content',
			'contentDisplay' => 'Content Display',
			'status' => 'Status',
			'createTime' => 'Create Time',
			'author' => 'Author',
			'email' => 'Email',
			'url' => 'Url',
			'postId' => 'Post',
		);
	}
}