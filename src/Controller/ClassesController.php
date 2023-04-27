<?php

namespace App\Controller;

use App\Entity\Classes;
use App\Entity\Student;
use App\Form\ClassesDatesType;
use App\Form\ClassesType;
use App\Form\StudentExcelType;
use App\Form\StudentType;
use App\Repository\ClassesRepository;
use App\Repository\StudentRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/classes', name: 'classes.')]
class ClassesController extends AbstractController
{
    #[Route('/show', name: 'show')]
    public function index(ClassesRepository $classesRepo, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesOfSpecifiedUser($userId);
        return $this->render('classes/show.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/create', name: 'create')]
    public function create(ManagerRegistry $doctrine, Request $request, UserInterface $user): Response
    {
        $classes = new Classes();
        $classes->setUser($user);
        $form = $this->createForm(ClassesType::class, $classes);
        $form->handleRequest($request);//to store transmitted data in variable form
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->persist($classes);
            $em->flush();

            return $this->redirect($this->generateUrl('classes.addStudents'));
        }
        return $this->render('classes/create.html.twig', [
            'form' => $form->createView(),
            'create' => true,
        ]);
    }
    #[Route('/start/{id}', name: 'start')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function start($id, Classes $class, ClassesRepository $classesRepo, Request $request, ManagerRegistry $doctrine, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        //formularz do wpisania daty zajęc
        if($classes)
        {
          $classes->setDateOfClasses(new DateTime());  
        }
        else
        {
            $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
            $classes->setDateOfClasses(new DateTime()); 
        }
        
        $form = $this->createForm(ClassesDatesType::class, $classes);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $dateofClasses = $form->get('dateOfClasses')->getData();
            if(! in_array($dateofClasses, $classes->getDatesOfClasses()))
            {
                $classes->addDateOfClasses($dateofClasses);       
                $em->persist($classes);
                $em->flush();
            }
            else
            {
                $this->addFlash('warning', 'The same date has been already used');
            }
            
            return $this->redirect($request->getUri());
        }
        return $this->render('classes/start.html.twig', [
            'classes' => $classes,
            'form'=>$form->createView(),
        ]);
    }
    #[Route('/addStudents', name: 'addStudents')]
    public function addStudents(ClassesRepository $classesRepo, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesOfSpecifiedUser($userId);
        return $this->render('classes/addStudents.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/removeClasses', name: 'removeClasses')]
    public function removeClasses(ClassesRepository $classesRepo, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesOfSpecifiedUser($userId);
        return $this->render('classes/removeClasses.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/removeClass/{id}', name: 'removeClass')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function removeClass($id, Classes $class, ClassesRepository $classesRepo, UserInterface $user, ManagerRegistry $doctrine): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        if(!$classes)
        {
            $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        }
        $em = $doctrine->getManager();
        $em->remove($classes);
        $em->flush();
        return $this->redirect($this->generateUrl('classes.removeClasses'));
    }
    #[Route('/editClasses/{id}', name: 'editClasses')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function editClasses($id, Classes $class, ClassesRepository $classesRepo, UserInterface $user, ManagerRegistry $doctrine, Request $request): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        if(!$classes)
        {
            $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        }
        $form = $this->createForm(ClassesType::class, $classes);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->persist($classes);
            $em->flush();
            $this->addFlash('success', 'Classes successfully edited');
            return $this->redirect($this->generateUrl('classes.removeClasses'));
        }
        return $this->render('classes/create.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
        ]);
        
        
    }
    #[Route('/editStudent/{id}', name: 'editStudent')]
    #[Security("user.getId() == studen.getClasses().getUser().getId()")]
    public function editStudent($id, Student $studen, StudentRepository $studentRepo, UserInterface $user, ManagerRegistry $doctrine, Request $request): Response
    {
        $student = $studentRepo->find($id);
        $classesId=$student->getClasses()->getId(); 
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->persist($student);
            $em->flush();
            $this->addFlash('success', 'Student successfully edited');
            return $this->redirect($this->generateUrl('classes.removeStudents',['id'=> $classesId]));
        }
        return $this->render('classes/editStudent.html.twig', [
            'form' => $form->createView(),
            'edit' => true,
        ]);
        
        
    }
    #[Route('/removeStudents/{id}', name: 'removeStudents')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function removeStudents($id, Classes $class, ClassesRepository $classesRepo, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        if(!$classes)
        {
            $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        }
        return $this->render('classes/removeStudents.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/removeStudent/{id}', name: 'removeStudent')]
    #[Security("user.getId() == studen.getClasses().getUser().getId()")]
    public function removeStudent($id, Student $studen, StudentRepository $studentRepo, UserInterface $user, ManagerRegistry $doctrine, Request $request): Response
    {
        $em=$doctrine->getManager();
        $student = $studentRepo->find($id); 
        $classesId=$student->getClasses()->getId();
        $em->remove($student);
        $em->flush();
        $this->addFlash('success', 'Student successfully removed');
        return $this->redirect($this->generateUrl('classes.removeStudents',['id'=> $classesId]));
    }
    #[Route('/addStudent/{id}', name: 'addStudent')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function addStudent(Classes $class, $id, ClassesRepository $classesRepo, Request $request, ManagerRegistry $doctrine, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $student->setClasses($classes);
            $em->persist($student);
            $em->flush();
            $this->addFlash('success', 'Student successfully added to the course');
            return $this->redirect($request->getUri());
        }
        return $this->render('classes/addStudent.html.twig', [
            'classes' => $classes,
            'form' => $form->createView(),
        ]);

    }
    #[Route('/addStudentExcel/{id}', name: 'addStudentExcel')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function addStudentExcel(Classes $class, $id, ClassesRepository $classesRepo, Request $request, ManagerRegistry $doctrine, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        $form = $this->createFormBuilder()
                ->add('uploaded_file', FileType::class, [
                    'label' => 'Students list from eHMS system (xlsx file)'],
                    ['attr' => array('class' => 'custom-file-input')])
                ->add('Save', SubmitType::class, 
                ['attr' => array('class' => 'btn btn-success')])
                ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('uploaded_file')->getData(); // get the file from the sent request
        
            $fileFolder = __DIR__ . '/../../public/uploads/';  //choose the folder in which the uploaded file will be stored
        
            $filePathName = md5(uniqid()) . $file->getClientOriginalName();
            // apply md5 function to generate an unique identifier for the file and concat it with the file extension  
                    try {
                        $file->move($fileFolder, $filePathName);
                    } catch (FileException $e) {
                        dd($e);
                    }
            $spreadsheet = IOFactory::load($fileFolder . $filePathName); // Here we are able to read from the excel file 
            $row = $spreadsheet->getActiveSheet()->removeRow(1, 13); // I added this to be able to remove the first file line 
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
            $em = $doctrine->getManager();
            $numberOfStudents = 0; 
            foreach ($sheetData as $Row) 
            { 
    
                $parts = preg_split('/\s+/', $Row['B']);
                $name = $parts[1];
                $surname = $parts[0];
                $indexNumber=preg_replace( '/[^0-9]/', '', $Row['C'] );   
                      
                $student = new Student();
                $student->setName($name);
                $student->setSurname($surname);
                $student->setIndexNumber($indexNumber);
                $student->setClasses($classes);
                $em->persist($student); 
                $em->flush(); 
                $numberOfStudents++;
            }
            $this->addFlash('success', $numberOfStudents. ' students successfully added to the "'. $classes->getName(). '" course');
            return $this->redirect($this->generateUrl('classes.addStudents'));
        }
        return $this->render('classes/addStudent.html.twig', [
            'classes' => $classes,
            'form' => $form->createView(),
        ]);

    }
    #[Route('/presence/{id}/{classesNumber}', name: 'presence')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function checkPresence($id, Classes $class, $classesNumber, ClassesRepository $classesRepo, Request $request, UserInterface $user,  ManagerRegistry $doctrine)
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        $datesOfClasses=$classes->getDatesOfClasses();
        $dateOfClasses = $datesOfClasses[$classesNumber-1];
        return $this->render('classes/presence.html.twig', [
            'classes' => $classes,
            'dateOfClasses'=> $dateOfClasses
        ]);
    }
    #[Route('/marks/{id}/{classesNumber}', name: 'marks')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function giveMarks($id, Classes $class, $classesNumber, UserInterface $user, ClassesRepository $classesRepo, Request $request, ManagerRegistry $doctrine)
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        $datesOfClasses=$classes->getDatesOfClasses();
        $dateOfClasses = $datesOfClasses[$classesNumber-1];

        return $this->render('classes/marks.html.twig', [
            'classes' => $classes,
            'dateOfClasses'=> $dateOfClasses,
        ]);
    }
    #[Route('/showPresence', name: 'show_presence')]
    public function showPresence(ClassesRepository $classesRepo, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesOfSpecifiedUser($userId);
        return $this->render('classes/showPresence.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/presenceList/{id}', name: 'presence_list')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function presenceList(ClassesRepository $classesRepo, Classes $class, UserInterface $user,  $id): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        if(!$classes)
        {
            $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        }
        return $this->render('classes/presenceList.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/showMarks', name: 'show_marks')]
    public function showMarks(ClassesRepository $classesRepo, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesOfSpecifiedUser($userId);
        return $this->render('classes/showMarks.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/marksList/{id}', name: 'marks_list')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function marksList(ClassesRepository $classesRepo, Classes $class, $id, UserInterface $user): Response
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        if(!$classes)
        {
            $classes = $classesRepo->findOneClassesOfSpecifiedUser($id, $userId);
        }
        return $this->render('classes/marksList.html.twig', [
            'classes' => $classes,
        ]);
    }
    #[Route('/pluses/{id}/{classesNumber}', name: 'pluses')]
    #[Security("user.getId() == class.getUser().getId()")]
    public function givePluses($id, Classes $class, $classesNumber, ClassesRepository $classesRepo, Request $request, ManagerRegistry $doctrine, UserInterface $user)
    {
        $userId=$user->getId();
        $classes = $classesRepo->findClassesAndStudentsEnrolled($id, $userId);
        $datesOfClasses=$classes->getDatesOfClasses();
        $dateOfClasses = $datesOfClasses[$classesNumber-1];
        return $this->render('classes/pluses.html.twig', [
            'classes' => $classes,
            'dateOfClasses'=> $dateOfClasses
        ]);
    }
}
