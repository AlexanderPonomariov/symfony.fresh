<?php

namespace Fresh\CalcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="Fresh\CalcBundle\Entity\Repository\SitesTypesRepository")
 * @ORM\Table(name="sites_types")
 */
class SitesTypes
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
    protected $siteType;

    /**
     * @ORM\OneToMany(targetEntity="Parameters", mappedBy="sites_types")
     */
    protected $parameters;


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
     * Set siteType
     *
     * @param string $siteType
     *
     * @return SitesTypes
     */
    public function setSiteType($siteType)
    {
        $this->siteType = $siteType;

        return $this;
    }

    /**
     * Get siteType
     *
     * @return string
     */
    public function getSiteType()
    {
        return $this->siteType;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add parameter
     *
     * @param \Fresh\CalcBundle\Entity\Parameters $parameter
     *
     * @return SitesTypes
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
