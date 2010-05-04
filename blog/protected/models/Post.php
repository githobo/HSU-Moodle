<?php

class Post extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'Post':
	 * @var integer $id
	 * @var string $title
	 * @var string $content
	 * @var string $contentDisplay
	 * @var string $tags
	 * @var integer $status
	 * @var integer $createTime
	 * @var integer $updateTime
	 * @var integer $commentCount
	 * @var integer $authorId
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
		return 'Post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('title','length','max'=>128),
			array('status, authorId', 'required'),
			array('status, createTime, updateTime, commentCount, authorId', 'numerical', 'integerOnly'=>true),
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
			'title' => 'Title',
			'content' => 'Content',
			'contentDisplay' => 'Content Display',
			'tags' => 'Tags',
			'status' => 'Status',
			'createTime' => 'Create Time',
			'updateTime' => 'Update Time',
			'commentCount' => 'Comment Count',
			'authorId' => 'Author',
		);
	}
}