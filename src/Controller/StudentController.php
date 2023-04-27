<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/student', name: 'student.')]
class StudentController extends AbstractController
{
    #[Route('/present/{id}/{dateOfClasses}', methods:"POST", name: 'present')]
    #[Route('/absent/{id}/{dateOfClasses}', methods:"POST", name: 'absent')]
    #[Route('/undopresent/{id}/{dateOfClasses}', methods:"POST", name: 'undo_present')]
    #[Route('/undoabsent/{id}/{dateOfClasses}', methods:"POST", name: 'undo_absent')]
    public function checkPresence($id, $dateOfClasses, Request $request, StudentRepository $studentRepo, ManagerRegistry $doctrine)
    {
        $student = $studentRepo->find($id);
        switch($request->get('_route'))
        {
            case 'student.present':
                $result = $this->present($student, $dateOfClasses);
                break;
            case 'student.absent':
                $result = $this->absent($student, $dateOfClasses);
                break;
            case 'student.undo_present':
                $result = $this->undoPresent($student, $dateOfClasses);
                break;
            case 'student.undo_absent':
                $result = $this->undoAbsent($student, $dateOfClasses);
                break;
        }
        return $this->json(['action'=> $result, 'id'=>$student->getId()]);

        /*return $this->render('classes/presence.html.twig', [
            'classes' => $classes,
            'dateOfClasses'=> $dateOfClasses,
        ]);*/
    }
    private function present($student, $dateOfClasses)
    {
        $student->addPresence($dateOfClasses);
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return 'present';
    }
    private function absent($student, $dateOfClasses)
    {
        $student->addAbsence($dateOfClasses);
        $em=$this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return 'absent';
    }
    private function undoPresent($student, $dateOfClasses)
    {
        $student->removePresence($dateOfClasses);
        $em=$this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return 'undo_present';
    }
    private function undoAbsent($student, $dateOfClasses)
    {
        $student->removeAbsence($dateOfClasses);
        $em=$this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return 'undo_absent';
    }
    #[Route('/giveMark/{id}/{dateOfClasses}/{mark}',  methods:"POST",  name: 'giveMark')]
    #[Route('/undoMark/{id}/{dateOfClasses}/{mark}',  methods:"POST",  name: 'undoMark')]
    public function giveMark ($id, $dateOfClasses, $mark, Request $request, StudentRepository $studentRepo, ManagerRegistry $doctrine)
    {
        $student = $studentRepo->find($id);
        switch($request->get('_route'))
        {
            case 'student.giveMark':
                $result = $this->saveMark($student, $dateOfClasses, $mark);
                break;
            case 'student.undoMark':
                $result = $this->removeMark($student, $dateOfClasses);
                break;
        }
        return $this->json(['action'=> $result, 'id'=>$student->getId()]);
    }
    private function saveMark($student, $dateOfClasses, $mark)
    {
        $student->addMark($dateOfClasses, $mark);
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return 'give'.$mark;
    }
    private function removeMark($student, $dateOfClasses)
    {
        $student->removeMark($dateOfClasses);
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return 'undo';
    }
    #[Route('/givePlus/{id}/', methods:"POST", name: 'pluses')]
    public function givePlus($id, StudentRepository $studentRepo, ManagerRegistry $doctrine)
    {
        $student = $studentRepo->find($id);
        $pluses = $student->getPluses();
        $pluses+=1;
        $student->setPluses($pluses);
        $em=$doctrine->getManager();
        $em->persist($student);
        $em->flush();
        return $this->json(['action'=> 'givePlus', 'id'=>$student->getId()]);
    }
    #[Route('/removePlus/{id}/',methods:"POST", name: 'remove_plus')]
    public function removePlus($id, StudentRepository $studentRepo, ManagerRegistry $doctrine)
    {
        $student = $studentRepo->find($id);
        $pluses = $student->getPluses();
        if($pluses >=1 )
        {
             $pluses-=1;
        }    
        $student->setPluses($pluses);
        $em=$doctrine->getManager();
        $em->persist($student);
        $em->flush();
        return $this->json(['action'=> 'removePlus', 'id'=>$student->getId()]);
    }
}
