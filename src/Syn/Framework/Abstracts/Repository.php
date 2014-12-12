<?php namespace Syn\Framework\Abstracts;

abstract class Repository
{
	/**
	 * @param Model $model
	 */
	public function __construct(Model $model)
	{
		$this -> model = $model;
	}


	/**
	 * Finds object by Id
	 *
	 * @param $id
	 * @return mixed
	 */
	public function findById($id)
	{
		return $this -> model -> find($id);
	}

	/**
	 * Finds all objects
	 *
	 * @return mixed
	 */
	public function findAll()
	{
		return $this -> model -> all();
	}

	public function paginate()
	{
		return $this -> model -> paginate();
	}

	public function getNewModel()
	{
		return $this -> model -> newInstance();
	}
}