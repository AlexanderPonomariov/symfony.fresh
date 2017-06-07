<?php

namespace Fresh\CalcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="Fresh\CalcBundle\Entity\Repository\WorkTypesRepository")
 * @ORM\Table(name="work_types")
 */
class WorkTypes
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $workType;

    /**
     * @ORM\OneToMany(targetEntity="Parameters", mappedBy="work_types")
     */
    protected $parameters;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set workType
     *
     * @param string $workType
     *
     * @return WorkTypes
     */
    public function setWorkType($workType)
    {
        $this->workType = $workType;

        return $this;
    }

    /**
     * Get workType
     *
     * @return string
     */
    public function getWorkType()
    {
        return $this->workType;
    }

    /**
     * Add parameter
     *
     * @param \Fresh\CalcBundle\Entity\Parameters $parameter
     *
     * @return WorkTypes
     */
    public function addParameter(\Fresh\CalcBundle\Entity\Parameters $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove parameter
     *
     * @param \Fresh\CalcBundle\Entity\Parameters $parameter
     */
    public function removeParameter(\Fresh\CalcBundle\Entity\Parameters $parameter)
    {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
