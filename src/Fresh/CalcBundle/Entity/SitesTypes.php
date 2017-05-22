<?php

namespace Fresh\CalcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
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
}
