<?php

namespace Fresh\CalcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Fresh\CalcBundle\Entity\Repository\ParametersRepository")
 * @ORM\Table(name="parameters")
 */
class Parameters
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
    protected $parameterName;

    /**
     * @ORM\ManyToOne(targetEntity="SitesTypes", inversedBy="parameters")
     * @ORM\JoinColumn(name="site_type_id", referencedColumnName="id")
     */
    protected $sites_types;

    /**
     * @ORM\Column(type="string")
     */
    protected $parameterValue;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="WorkTypes", inversedBy="parameters")
     * @ORM\JoinColumn(name="work_type_id", referencedColumnName="id")
     */
    protected $work_types;



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
     * Set parameterName
     *
     * @param string $parameterName
     *
     * @return Parameters
     */
    public function setParameterName($parameterName)
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    /**
     * Get parameterName
     *
     * @return string
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * Set parameterValue
     *
     * @param string $parameterValue
     *
     * @return Parameters
     */
    public function setParameterValue($parameterValue)
    {
        $this->parameterValue = $parameterValue;

        return $this;
    }

    /**
     * Get parameterValue
     *
     * @return string
     */
    public function getParameterValue()
    {
        return $this->parameterValue;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Parameters
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Parameters
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set sitesTypes
     *
     * @param \Fresh\CalcBundle\Entity\SitesTypes $sitesTypes
     *
     * @return Parameters
     */
    public function setSitesTypes(\Fresh\CalcBundle\Entity\SitesTypes $sitesTypes = null)
    {
        $this->sites_types = $sitesTypes;

        return $this;
    }

    /**
     * Get sitesTypes
     *
     * @return \Fresh\CalcBundle\Entity\SitesTypes
     */
    public function getSitesTypes()
    {
        return $this->sites_types;
    }

    /**
     * Set workTypes
     *
     * @param \Fresh\CalcBundle\Entity\WorkTypes $workTypes
     *
     * @return Parameters
     */
    public function setWorkTypes(\Fresh\CalcBundle\Entity\WorkTypes $workTypes = null)
    {
        $this->work_types = $workTypes;

        return $this;
    }

    /**
     * Get workTypes
     *
     * @return \Fresh\CalcBundle\Entity\WorkTypes
     */
    public function getWorkTypes()
    {
        return $this->work_types;
    }
}
