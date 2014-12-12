<?php namespace Syn\Framework\Abstracts;

interface RepositoryInterface
{
	/**
	 * Finds object by Id
	 * @param $id
	 * @return mixed
	 */
	public function findById($id);

	/**
	 * Finds all objects
	 * @return mixed
	 */
	public function findAll();
}