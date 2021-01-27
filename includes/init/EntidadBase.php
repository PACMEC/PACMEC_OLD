<?php 
/**
  * @package PACMEC
  */

interface EntidadBase {
	public function __construct();
	public function __sleep();
	public function __toString();
	public function setTable($t);
	public function modelInitial($s);
	public function loadColumns();
	public function get_value_default_sql($t, $d);
	public function getColumns($i);
	public function filterEq($i);
	public function isValid();
	public function setAll($a);
	public function getRand($l);
	public function getBy($c, $v);
	public function getAdapter();
}