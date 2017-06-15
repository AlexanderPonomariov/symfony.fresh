<?php
namespace Fresh\PatentingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Fresh\PatentingBundle\Entity\Repository\PatentingPricesRepository")
 * @ORM\Table(name="patenting_prices")
 * @ORM\HasLifecycleCallbacks()
 */
class PatentingPrices

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
    protected $serviceName;

    /**
     * @ORM\Column(type="string")
     */
    protected $price;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registartionUrgency;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $registartionType;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $isPartner;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    protected $class;
    


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
     * Set serviceName
     *
     * @param string $serviceName
     *
     * @return PatentingPrices
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return PatentingPrices
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set registartionType
     *
     * @param string $registartionType
     *
     * @return PatentingPrices
     */
    public function setRegistartionType($registartionType)
    {
        $this->registartionType = $registartionType;

        return $this;
    }

    /**
     * Get registartionType
     *
     * @return string
     */
    public function getRegistartionType()
    {
        return $this->registartionType;
    }

    /**
     * Set isPartner
     *
     * @param boolean $isPartner
     *
     * @return PatentingPrices
     */
    public function setIsPartner($isPartner)
    {
        $this->isPartner = $isPartner;

        return $this;
    }

    /**
     * Get isPartner
     *
     * @return boolean
     */
    public function getIsPartner()
    {
        return $this->isPartner;
    }

    /**
     * Set class
     *
     * @param boolean $class
     *
     * @return PatentingPrices
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return boolean
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set registartionUrgency
     *
     * @param integer $registartionUrgency
     *
     * @return PatentingPrices
     */
    public function setRegistartionUrgency($registartionUrgency)
    {
        $this->registartionUrgency = $registartionUrgency;

        return $this;
    }

    /**
     * Get registartionUrgency
     *
     * @return integer
     */
    public function getRegistartionUrgency()
    {
        return $this->registartionUrgency;
    }
}
