<?php
class RefererSite extends CActiveRecord
{
	public $all_geoplaces = null;
	
	private $__geoplaces = null;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'referer_sites';
	}

	public function relations()
	{
		return array();
	}
	
	public function rules()
	{
		return array(
			array('name', 'required', 'message'=>'Укажите название', 'on' => 'admin'),
			array('geoplaces', 'MultiRangeValidator', 'list' => array_keys(Yii::app()->Dir->getList(Dir::DIR_GEOPLACES)), 'message'=>'Укажите города', 'on' => 'admin'),
			array('all_geoplaces', 'boolean', 'on' => 'admin'),
			array('name, description, domen, ip, _geoplaces, all_geoplaces, key', 'safe', 'on' => 'admin'),
		);
	}

	public function MultiRangeValidator($attribute, $params)
	{
		if (is_array($this->$attribute) && sizeof(array_intersect($this->$attribute, $params['list'])) !=  sizeof($this->$attribute))
			$this->addError($attribute, $params['message']);
	}

	public function attributeLabels()
	{
		return array(
			'name' => 'Название',
			'description' => 'Комментарий',
			'domen' => 'Домен',
			'ip' => 'IP',
			'all_geoplaces'=>'Все населенные пункты',
			'geoplaces' => 'Населенные пункты',
		);
	}
	
	protected function beforeSave()
	{
		if ($this->all_geoplaces !== null && $this->all_geoplaces)
			$this->geoplaces = 1;
			
		if (is_array($this->geoplaces) || !$this->geoplaces)
			$this->setAttribute('geoplaces', serialize($this->geoplaces));
			
		return parent::beforeSave();
	}
	
	public function afterFind()
	{
		if ($this->geoplaces == 1)
		{
			$this->all_geoplaces = true;
			$this->geoplaces = array();
		}
		else
		{
			$this->all_geoplaces = false;
			$this->geoplaces = unserialize($this->geoplaces);
			
		}
	}
	
	public function getGeoplacesList()
	{
		if ($this->__geoplaces === null)
		{
			if ($this->all_geoplaces)
				$this->__geoplaces =  array_keys(Yii::app()->Dir->getList(Dir::DIR_GEOPLACES));
			else
			{
				$childs = Yii::app()->Dir->getByParent(Dir::DIR_GEOPLACES, 'pid', $this->geoplaces );
				$this->__geoplaces = array_merge($this->geoplaces, $childs);
			}
		}
		
		return 	$this->__geoplaces;		
	}	
}