<?php

namespace App\Entity;

use App\Repository\ClassesRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassesRepository::class)]
class Classes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $lectureHours;

    #[ORM\Column(type: 'integer')]
    private $exercisesHours;

    #[ORM\Column(type: 'integer')]
    private $laboratoryHours;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $studentsGroup;

    /*#[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'classes')]
    private $students;*/
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy:"classes")]
    private $students;

    #[ORM\Column(type: 'array', nullable: true)]
    private $datesOfClasses = [];

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateOfClasses;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'classes')]
    #[ORM\JoinColumn(nullable: true)]
    private $user;

    public function __construct()
    {
        $this->students = new ArrayCollection();
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

    public function getLectureHours(): ?int
    {
        return $this->lectureHours;
    }

    public function setLectureHours(int $lectureHours): self
    {
        $this->lectureHours = $lectureHours;

        return $this;
    }

    public function getExercisesHours(): ?int
    {
        return $this->exercisesHours;
    }

    public function setExercisesHours(int $exercisesHours): self
    {
        $this->exercisesHours = $exercisesHours;

        return $this;
    }

    public function getLaboratoryHours(): ?int
    {
        return $this->laboratoryHours;
    }

    public function setLaboratoryHours(int $laboratoryHours): self
    {
        $this->laboratoryHours = $laboratoryHours;

        return $this;
    }

    public function getStudentsGroup(): ?string
    {
        return $this->studentsGroup;
    }

    public function setStudentsGroup(?string $studentsGroup): self
    {
        $this->studentsGroup = $studentsGroup;

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setClasses($this);
        }

        return $this;
    }

    /*public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            $student->setClasses(null);
        }

        return $this;
    }*/

    public function getDatesOfClasses(): ?array
    {
        return $this->datesOfClasses;
    }

    public function setDatesOfClasses(?array $datesOfClasses): self
    {
        $this->datesOfClasses = $datesOfClasses;

        return $this;
    }
    public function addDateOfClasses(DateTime $dateOfClasses): self
    {
            $this->datesOfClasses[] = $dateOfClasses;
            return $this;
    }

    public function getDateOfClasses(): ?\DateTimeInterface
    {
        return $this->dateOfClasses;
    }

    public function setDateOfClasses(?\DateTimeInterface $dateOfClasses): self
    {
        $this->dateOfClasses = $dateOfClasses;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
