<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $surname;

    #[ORM\Column(type: 'integer')]
    private $indexNumber;

    /*#[ORM\ManyToMany(targetEntity: Classes::class, inversedBy: 'students')]
    private $classes;*/
    
     #[ORM\ManyToOne(targetEntity: Classes::class, inversedBy: "students")]
     #[ORM\JoinColumn(nullable: true, onDelete:"CASCADE")]
    private $classes;

    #[ORM\Column(type: 'array', nullable: true)]
    private $presenceTable = [];

    #[ORM\Column(type: 'array', nullable: true)]
    private $absenceTable = [];

    #[ORM\Column(type: 'array', nullable: true)]
    private $marksTable = [];

    #[ORM\Column(type: 'integer', nullable: true)]
    private $pluses;

    #[ORM\Column(type: 'float', nullable: true)]
    private $mark;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->pluses = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getIndexNumber(): ?int
    {
        return $this->indexNumber;
    }

    public function setIndexNumber(int $indexNumber): self
    {
        $this->indexNumber = $indexNumber;

        return $this;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function setClasses(Classes $classes): self
    {
        $this->classes=$classes;
        return $this;
    }

    public function getPresenceTable(): ?array
    {
        return $this->presenceTable;
    }

    public function setPresenceTable(?array $presenceTable): self
    {
        $this->presenceTable = $presenceTable;

        return $this;
    }
    public function addPresence(string $checkPresence)
    {
        $this->presenceTable[] = $checkPresence;
    }

    public function getAbsenceTable(): ?array
    {
        return $this->absenceTable;
    }

    public function setAbsenceTable(?array $absenceTable): self
    {
        $this->absenceTable = $absenceTable;

        return $this;
    }
    public function addAbsence(string $checkPresence)
    {
        $this->absenceTable[] = $checkPresence;
    }
    public function removePresence(string $checkPresence)
    {
        if(($key = array_search($checkPresence, $this->presenceTable)) !== false)
        {
            unset($this->presenceTable[$key]);
        }
    }
    public function removeAbsence(string $checkPresence)
    {
        if(($key = array_search($checkPresence, $this->absenceTable)) !== false)
        {
            unset($this->absenceTable[$key]);
        }
    }

    public function getMarksTable(): ?array
    {
        return $this->marksTable;
    }

    public function setMarksTable(?array $marksTable): self
    {
        $this->marksTable = $marksTable;

        return $this;
    }

    public function getPluses(): ?int
    {
        return $this->pluses;
    }

    public function setPluses(?int $pluses): self
    {
        $this->pluses = $pluses;

        return $this;
    }

    public function getMark(): ?float
    {
        return $this->mark;
    }

    public function setMark(?float $mark): self
    {
        $this->mark = $mark;

        return $this;
    }
    public function addMark(string $date, float $mark): self
    {
            $this->marksTable[$date] = $mark;
            return $this;
    }
    public function removeMark(string $date): self
    {
            unset($this->marksTable[$date]);
            return $this;
    }
    
    
}
