<?php

namespace Fresh\CalcBundle\Parameters;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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


    protected $siteType;

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


    protected $parameterType;





}