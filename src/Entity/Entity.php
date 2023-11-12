<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\HasLifecycleCallbacks]
abstract class Entity
{
    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Path\To\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'create')]
    private $createdBy;

    /**
     * @var User|null
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Path\To\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'update')]
    private $updatedBy;

    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATE_MUTABLE)]
    private $created;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'updated', type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable]
    private $updated;

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }


}
