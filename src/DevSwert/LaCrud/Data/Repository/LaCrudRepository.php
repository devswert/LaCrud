<?php

namespace DevSwert\LaCrud\Data\Repository;

use DevSwert\LaCrud\Data\Entity\LaCrudBaseEntity;

final class LaCrudRepository extends LaCrudBaseRepository{

	function __construct(LaCrudBaseEntity $entity){
		$this->entity = $entity;
	}

	public function table($table = null){
		if(is_null($table))
			return $this->entity->table;
		else
			$this->entity->table = $table;
	}

	public function like($field, $value)
	{
		$this->entity = $this->entity->where($field, 'LIKE', '%'.$value.'%');
		return $this;
	}

	public function where($field,$operation, $value)
	{
		$this->queryBuilder = $this->entity->where($field,$operation, $value);
		return $this;
	}

	public function limit($limit)
	{
		$this->queryBuilder = $this->entity->take($limit);
		return $this;
	}

	public function orderBy($field, $order)
	{
		$this->queryBuilder = $this->entity->orderBy($field,$order);
		return $this;
	}

	public function orLike($field, $value)
	{
		$this->queryBuilder = $this->entity->orWhere($field, 'LIKE', '%'.$value.'%');
		return $this;
	}

	public function orWhere($field,$operation,$value)
	{
		$this->queryBuilder = $this->entity->orWhere($field,$operation, $value);
		return $this;
	}

	public function get()
	{
		if(get_class($this->queryBuilder) == "Illuminate\Database\Eloquent\Builder")
			return $this->queryBuilder->get();
		else
			return $this->entity->get();
	}
}
