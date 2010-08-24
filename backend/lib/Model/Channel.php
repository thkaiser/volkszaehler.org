<?php
/**
 * @copyright Copyright (c) 2010, The volkszaehler.org project
 * @package default
 * @license http://www.opensource.org/licenses/gpl-license.php GNU Public License
 */
/*
 * This file is part of volkzaehler.org
 *
 * volkzaehler.org is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * volkzaehler.org is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with volkszaehler.org. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Volkszaehler\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Channel entity
 *
 * @author Steffen Vogel <info@steffenvogel.de>
 * @package default
 *
 * @Entity
 */
class Channel extends Entity {
	/**
	 * @OneToMany(targetEntity="Data", mappedBy="channel", cascade={"remove"})
	 * @OrderBy({"timestamp" = "ASC"})
	 */
	protected $data = NULL;

	/** @ManyToMany(targetEntity="Group", mappedBy="channels") */
	protected $groups;

	/**
	 * Constructor
	 */
	public function __construct($properties = array()) {
		parent::__construct($properties);

		$this->data = new ArrayCollection();
		$this->groups = new ArrayCollection();
	}

	/**
	 * Add a new data to the database
	 * @todo move to Logger\Logger?
	 */
	public function addData(\Volkszaehler\Model\Data $data) {
		$this->data->add($data);
	}

	/**
	 * Obtain channel interpreter to obtain data and statistical information for a given time interval
	 *
	 * @param Doctrine\ORM\EntityManager $em
	 * @param integer $from timestamp in ms since 1970
	 * @param integer $to timestamp in ms since 1970
	 * @return Interpreter
	 */
	public function getInterpreter(\Doctrine\ORM\EntityManager $em, $from, $to) {
		$interpreterClassName = 'Volkszaehler\Interpreter\\' . ucfirst($this->getType()) . 'Interpreter';
		return new $interpreterClassName($this, $em, $from, $to);
	}

	/**
	 * Validate
	 *
	 * @PrePersist @PreUpdate
	 */
	protected function validate() {
		if ($this->getResolution() <= 0 && $this->getType() == 'meter') {
			throw new Exception('resolution has to be a positive integer');
		}
	}

	/**
	 * getter & setter
	 *
	 * @todo change to new property model
	 */
	public function getName() { return $this->name; }
	public function setName($name) { $this->name = $name; }
	public function getDescription() { return $this->description; }
	public function setDescription($description) { $this->description = $description; }

	public function getResolution() { return $this->resolution; }
	public function setResolution($resolution) { $this->resolution = $resolution; }
	public function getCost() { return $this->cost; }
	public function setCost($cost) { $this->cost = $cost; }
}

?>
