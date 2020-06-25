<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/appointment")
 */
class AppointmentController extends AbstractController
{
    /**
     * @Route("/patient/{week}/{year}", name="appointment_patient", methods={"GET"})
     */
    public function patient(AppointmentRepository $appointmentRepository,$year = "",$week = ""): Response
    {


        if($year != "" && $week != ""){
            $numberOfWeek = $week;
            $year = $year;
            $monday = new \DateTime();
            $monday->setISOdate($year, $numberOfWeek);
        }else{

            $numberOfWeek = date("W");
            $year = date('Y');
            $monday = new \DateTime();
            $monday->setISOdate($year, $numberOfWeek);
        }


        $user = $this->getUser();
        $appointments = $this->getDoctrine()->getRepository(Appointment::class)->findBy(['patient' => $user],['date' => 'ASC']);


        return $this->render('appointment/patient.html.twig', [
            'monday'=>$monday,
            'appointments'=> $appointments,
        ]);
    }

     /**
     * @Route("/medecin/{week}/{year}", name="appointment_medecin", methods={"GET"})
     */
    public function medecin(AppointmentRepository $appointmentRepository,$year = "",$week = ""): Response
    {


        if($year != "" && $week != ""){
            $numberOfWeek = $week;
            $year = $year;
            $monday = new \DateTime();
            $monday->setISOdate($year, $numberOfWeek);
        }else{

            $numberOfWeek = date("W");
            $year = date('Y');
            $monday = new \DateTime();
            $monday->setISOdate($year, $numberOfWeek);
        }


        $user = $this->getUser();
        $appointments = $this->getDoctrine()->getRepository(Appointment::class)->findBy(['medecin' => $user],['date' => 'ASC']);


        return $this->render('appointment/medecin.html.twig', [
            'monday'=>$monday,
            'appointments'=> $appointments,
        ]);
    }




    /**
     * @Route("/new", name="appointment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $appointment->setPatient($user);

            $repo = $this->getDoctrine()->getRepository(Appointment::class);
            $checkDate = $repo->checkDispo($appointment->getDate(),$appointment->getSchedule());

            if($checkDate){

                $this->addFlash('danger', 'The appointment is already made');


            }else{

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($appointment);
                $entityManager->flush();
    
                return $this->redirectToRoute('appointment_patient');
            }

        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{url}", name="appointment_delete")
     */
    public function delete(Request $request, Appointment $appointment, $url): Response
    {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appointment);
            $entityManager->flush();
        }catch(Exception $e){

        }
        
        $url = str_replace("-","/",$url);



       return $this->redirect($url);
    }
}
