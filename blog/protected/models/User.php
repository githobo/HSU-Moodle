<?php

class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'User':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var string $profile
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
		return 'User';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('username','length','max'=>128),
			array('password','length','max'=>128),
			array('email','length','max'=>128),
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
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'profile' => 'Profile',
		);
	}
}