<?php

class RowDbIterator implements Iterator 
{
	protected $db;
	protected $fieldnames;
	protected $position;
	protected $validrow;
	protected $has_rows;
	private $isempty;
	
	public static function zero()
	{
		$e = new RowDbIterator(NULL, NULL);
		$e->isempty = TRUE;
		$e->validrow = FALSE;
		return $e;
	}
	
	function __construct($dataset, $fieldnames)
	{
		$this->db = $dataset;
		$this->fieldnames = $fieldnames;
		$this->position = 0;
	}
	
	public function has_rows()
	{
		if ($this->isempty)
			return FALSE;
		
		if (!isset($this->has_rows))
		{
			$this->position = 0;
			$this->has_rows = $this->validrow = $this->db->next_record();
		}
		return $this->has_rows;
	}
	
	public function rewind()
	{
		$this->has_rows();
	}
	
	public function current()
	{
		$reg = array();
		foreach( $this->fieldnames as $rn )
		{
			$reg[$rn] = $this->db->f($rn);
		}	
		return $reg;
	}
	
	public function key()
	{
		return $this->position;
	}
	
	public function next()
	{
		++$this->position;
		$this->validrow = $this->db->next_record();
	}
	
	public function valid()
	{
		return $this->validrow;
	}
}

?>