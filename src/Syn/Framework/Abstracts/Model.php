<?php namespace Syn\Framework\Abstracts;

use App;
use Illuminate\Database\Eloquent\Model as BaseModel, URL, Validator, Redirect;
use Input;
use Image;
use Log;
use Syn\Framework\Exceptions\MissingImplementationException;
use Syn\Framework\Exceptions\MissingMethodException;
use View;

abstract class Model extends BaseModel
{

	private $_public_media_dir = 'media';

	protected $_sanitize = ['wysiwyg','ckeditor','summernote'];
	/**
	 * Array of relations of current object
	 * @var array
	 */
	public $_breadcrumbs = [];

	public $_validation = [];
	public $_validation_on_create = [];
	public $_types = [];
	public $_types_on_create = [];
	public $_select_values = [];
	public $_upload_targets = [];

	/**
	 * Increases amount by N
	 * @param     $attribute
	 * @param int $by
	 */
	public function increase($attribute, $by = 1)
	{
		$this -> {$attribute} = intval($this -> {$attribute}) + $by;
		if($this -> exists)
			$this -> save();
	}

	/**
	 * @param $route
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function redirectRouted($route)
	{
		return Redirect::route($route, ['id' => $this -> id, 'name' => $this -> linkName]);
	}

	/**
	 * @param $route
	 * @return string
	 */
	public function linkRoute($route)
	{
		return URL::route($route, ['id' => $this -> id, 'name' => $this -> linkName]);
	}
	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getRedirectViewAttribute()
	{
		return $this -> redirectRouted("{$this->classBaseName}@view");
	}

	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getRedirectCreateAttribute()
	{
		return $this -> redirectRouted("{$this->classBaseName}@create");
	}
	/**
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getRedirectEditAttribute()
	{
		return $this -> redirectRouted("{$this->classBaseName}@edit");
	}

	/**
	 * Generates a complete url based on the current class to a view
	 * @return string
	 */
	public function getLinkViewAttribute()
	{
		return $this -> linkRoute("{$this->classBaseName}@view");
	}
	/**
	 * Generates a complete url based on the current class to a edit
	 * @return string
	 */
	public function getLinkEditAttribute()
	{
		return $this -> linkRoute("{$this->classBaseName}@edit");
	}
	/**
	 * Generates a complete url based on the current class to a delete
	 * @return string
	 */
	public function getLinkDeleteAttribute()
	{
		return $this -> linkRoute("{$this->classBaseName}@delete");
	}
	/**
	 * Generates a complete url based on the current class to a edit
	 * @return string
	 */
	public function getLinkListAttribute()
	{
		return URL::route("{$this->classBaseName}@list");
	}

	/**
	 * Returns the parameter of an object to use in a SEO url
	 *
	 * @throws \Syn\Framework\Exceptions\MissingMethodException
	 * @return mixed
	 */
	public function getLinkNameAttribute()
	{
		return $this->name ? str_replace(['/', ' ','\''], ['-','-',''], $this->name) : '-';
	}

	/**
	 * Gets the last part of a name-spaced class
	 * @return string
	 */
	public function getClassBaseNameAttribute()
	{
		return class_basename($this->className);
	}
	/**
	 * Gets the full class name of this object
	 * @return string
	 */
	public function getClassNameAttribute()
	{
		return get_called_class();
	}

	/**
	 * Validator for this object
	 *
	 * @param array      $values
	 * @param array $strictValidate only validate the input, not other rules
	 * @internal param array $keys
	 * @return \Illuminate\Validation\Validator
	 */
	public function getValidator($values = [], $strictValidate = [])
	{
		$rules = $this -> _validation;

		if(!$this -> exists && count($this -> _validation_on_create))
			$rules = array_merge($rules, $this -> _validation_on_create);
//		elseif($this -> exists)
//			$rules = array_intersect_key($rules, $this -> getDirty());

		$values = array_intersect_key($values, $rules);

		// removes all validation rules that have no input posted
		if(count($strictValidate))
			$rules = array_only($rules, $strictValidate);
		// overrules unique for model that already exists, allowing the validator to check for unique with Id
		foreach($rules as $attribute => &$ruleSet)
		{
			foreach($ruleSet as $i => &$rule)
			{
				if($this -> exists && preg_match('/^unique:[a-z0-9_]+,[a-z0-9_]+/', $rule))
					$rule = $rule . ',' . $this -> id;
				if($rule == "disabled")
					unset($rules[$attribute][$i]);

			}
			if(preg_match('/^visitor\.(.*)/i', array_get($this->_types, $attribute), $m))
			{
				$model_attribute = $m[1];
				$values[$attribute] = App::make('Visitor') -> {$model_attribute};
			}
			if(in_array(array_get($this->_types, $attribute), $this -> _sanitize))
				$values[$attribute]	= e($values[$attribute]);

			if(array_get($this->_types, $attribute) == 'toggle')
				$values[$attribute]	= (bool) array_get($values, $attribute);

		}
		// return a completely constructed validator object
		return Validator::make(
			array_merge(Input::only(array_keys($rules)), $values),
			$rules
		);
	}

	/**
	 * Builds an intelligent form
	 *
	 * @param array $attributes
	 * @param array $only
	 * @return \Illuminate\View\View
	 */
	public function form($attributes = [], $only = [])
	{
		$types = $this -> _types;
		if(!$this -> exists)
			$types = array_merge($types, $this -> _types_on_create);


		if(count($only))
			$types = array_only($types, $only);

		return View::make('forms.model-builder', array_merge(compact('types'), $attributes, ['model' => $this]));
	}

	/**
	 * Validates and saves an object
	 * @return mixed
	 */
	public function validateAndSave()
	{
		$attributes = array_merge($this -> getAttributes(), Input::all());
		$validator = $this -> getValidator($attributes);

		if($validator->fails())
		{
			if($this -> exists)
				return $this -> redirectEdit -> withInput() -> withErrors($validator);
			else
				return $this -> redirectCreate -> withInput() -> withErrors($validator);
		}

		// get valid fields
		$fields = $validator->valid();
		
		foreach($this->_types as $attribute => $type)
		{
			if($type == 'file')
			{
				if(Input::hasFile($attribute) && Input::file($attribute) -> isValid())
				{
					Image::make(Input::file($attribute))
						->encode('png', 100)
						->save(public_path(sprintf("/%s/%s/%d.png",
							$this -> _public_media_dir,
							$this -> _upload_targets[$attribute],
							$this -> id
						)));
				}
				unset($fields[$attribute]);
			}
		}
		$this -> unguard();
		$this -> fill($fields);
		$this -> reguard();
		$this -> save();
		// redirects with a flashed message on edit/create
		return $this -> redirectView -> with('notification', trans(
			sprintf("%s.%s-%s-notification",
				strtolower($this -> classBaseName),
				strtolower($this -> classBaseName),
				$this -> exists ? 'edit' : 'create'
			)));
	}

	/**
	 * @param $attribute
	 * @return string
	 */
	public function fileUploadPath($attribute)
	{
		return public_path(sprintf("/%s/%s/%d.png", $this -> _public_media_dir, $this -> _upload_targets[$attribute], $this -> id));
	}

	/**
	 * @param $attribute
	 * @return string
	 */
	public function fileUploadUri($attribute)
	{
		return sprintf("/%s/%s/%d.png", $this -> _public_media_dir, $this -> _upload_targets[$attribute], $this -> id);
	}

	/**
	 * @return array
	 */
	public function getValidationRules()
	{
		$rules = $this -> _validation;
		if(!$this -> exists)
			$rules = array_merge($rules, $this -> _validation_on_create);

		return $rules;
	}

	/**
	 * Finds a value of a validation rule based on a regular expression
	 * @param $attribute
	 * @param $regex
	 * @return mixed
	 */
	public function getValidationValue($attribute, $regex)
	{
		$rules = array_get($this -> getValidationRules(), $attribute, []);
		foreach($rules as $rule)
			if(preg_match("/{$regex}/", $rule, $m))
				return $m['value'];
	}

	/**
	 * Loads select input values based on $_select_values
	 * @param $attribute
	 * @return array
	 */
	public function getSelectValues($attribute)
	{
		$select_query = array_get($this -> _select_values, $attribute, []);
		if(array_get($this->_types, $attribute) == 'auto-complete')
		{
			return $select_query;
		}
		if(is_array($select_query))
		{
			list($class, $method) = $select_query;

			// property of current gamer
			if($class == "Visitor")
				return App::make('Visitor') -> {$method};
			// direct method
			elseif(method_exists($class, $method))
				return call_user_func([$class, $method]);
			// property
			elseif(isset($class->{$method}))
				return $class->{$method};
			// try for scope
			elseif(method_exists($class, sprintf('scope%s', ucfirst($method))))
				return call_user_func([$class, $method]) -> get();

		}

		Log::error("Cannot parse select value for {$attribute} in class: {$this->className}");
		return [];
	}

	/**
	 * Whether editing or creating is allowed, based on existance of model
	 * @return bool
	 */
	public function allowEditOrCreate()
	{
		return $this -> exists ? $this -> allowEdit() : $this -> allowCreate();
	}

	/**
	 * Whether the user is allowed to delete the object
	 * @throws \Syn\Framework\Exceptions\MissingMethodException
	 * @return bool
	 */
	public function allowDelete()
	{
		throw new MissingMethodException(sprintf("%s::%s", $this->className,__FUNCTION__));
	}

	/**
	 * Whether the user is allowed to create the object
	 * @throws \Syn\Framework\Exceptions\MissingMethodException
	 * @return bool
	 */
	public function allowCreate()
	{
		throw new MissingMethodException(sprintf("%s::%s", $this->className,__FUNCTION__));
	}

	/**
	 * Whether the user is allowed to edit the object
	 * @throws \Syn\Framework\Exceptions\MissingMethodException
	 * @return bool
	 */
	public function allowEdit()
	{
		throw new MissingMethodException(sprintf("%s::%s", $this->className,__FUNCTION__));
	}

	/**
	 * Whether the user is allowed to view the object
	 * @return bool
	 */
	public function allowView()
	{
		return true;
	}

	protected function getVisitorAttribute()
	{
		return App::make('Visitor');
	}

	public function getSentinelAttribute()
	{
		return App::make('Sentinel');
	}

	public function getTranslatePrefixAttribute()
	{
		return snake_case($this->classBaseName) . '.' . snake_case($this->classBaseName);
	}
}