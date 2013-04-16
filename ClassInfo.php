<?php

class ClassInfo {
	private $cTitle;
	private $cShortDesc;
	private $cLongDesc;
	private $cCourseLink;
	private $cVideoLink;
	private $cStartDate;
	private $cCourseLength;
	private $cCourseImage;
	private $cCategory;
	private $cSite;
	private $cProfName;
	private $cProfImage;
	
	/**
		Get Title
	*/
	public function getTitle() {
		return $this->cTitle;
	}
	
	/**
		Set Title
	*/
	public function setTitle($value) {
		$this->cTitle = $value;
	}
	
	/**
		Get Short Description
	*/
	public function getShortDescription() {
		return $this->cShortDesc;
	}
	
	/**
		Set Short Description
	*/
	public function setShortDescription($value) {
		$this->cShortDesc = $value;
	}
	
	/**
		Get Long Description
	*/
	public function getLongDescription() {
		return $this->cLongDesc;
	}
	
	/**
		Set Long Description
	*/
	public function setLongDescription($value) {
		$this->cLongDesc = $value;
	}
	
	/**
		Get Course Link
	*/
	public function getCourseLink() {
		return $this->cCourseLink;
	}
	
	/**
		Set Course Link
	*/
	public function setCourseLink($value) {
		$this->cCourseLink = $value;
	}
	
	/**
		Get Video Link
	*/
	public function getVideoLink() {
		return $this->cVideoLink;
	}
	
	/**
		Set Video Link
	*/
	public function setVideoLink($value) {
		$this->cVideoLink = $value;
	}
	
	/**
		Get Start Date
	*/
	public function getStartDate() {
		return $this->cStartDate;
	}
	
	/**
		Set Start Date
	*/
	public function setStartDate($value) {
		$this->cStartDate = $value;
	}
	
	/**
		Get Course Length
	*/
	public function getCourseLength() {
		return $this->cCourseLength;
	}
	
	/**
		Set Course Length
	*/
	public function setCourseLength($value) {
		$this->cCourseLength = $value;
	}
	
	/**
		Get Course Image
	*/
	public function getCourseImage() {
		return $this->cCourseImage;
	}
	
	/**
		Set Course Image
	*/
	public function setCourseImage($value) {
		$this->cCourseImage = $value;
	}
	
	/**
		Get Category
	*/
	public function getCategory() {
		return $this->cCategory;
	}
	
	/**
		Set Category
	*/
	public function setCategory($value) {
		$this->cCategory = $value;
	}
	
	/**
		Get Site
	*/
	public function getSite() {
		return $this->cSite;
	}
	
	/**
		Set Site
	*/
	public function setSite($value) {
		$this->cSite = $value;
	}
	
	/**
		Get Professor Name
	*/
	public function getProfName() {
		return $this->cProfName;
	}
	
	/**
		Set Professor Name
	*/
	public function setProfName($value) {
		$this->cProfName = $value;
	}
	
	/**
		Get Professor Image
	*/
	public function getProfImage() {
		return $this->cProfImage;
	}

	/**
		Set Professor Image
	*/
	public function setProfImage($value) {
		$this->cProfImage = $value;
	}
	
}

?>