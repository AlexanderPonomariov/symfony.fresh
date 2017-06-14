<?php
namespace Fresh\PatentingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Fresh\PatentingBundle\Entity\Repository\ContractsRepository")
 * @ORM\Table(name="contracts")
 * @ORM\HasLifecycleCallbacks()
 */
class Contracts

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
    protected $contractNumber;

    /**
     * @ORM\ManyToOne(targetEntity="LegalEntities", inversedBy="contracts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=true)
     */
    protected $entity;

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
     * Set contractNumber
     *
     * @param string $contractNumber
     *
     * @return Contracts
     */
    public function setContractNumber($contractNumber)
    {
        $this->contractNumber = $contractNumber;

        return $this;
    }

    /**
     * Get contractNumber
     *
     * @return string
     */
    public function getContractNumber()
    {
        return $this->contractNumber;
    }

    /**
     * Set entity
     *
     * @param \Fresh\PatentingBundle\Entity\LegalEntities $entity
     *
     * @return Contracts
     */
    public function setEntity(\Fresh\PatentingBundle\Entity\LegalEntities $entity = null)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return \Fresh\PatentingBundle\Entity\LegalEntities
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
