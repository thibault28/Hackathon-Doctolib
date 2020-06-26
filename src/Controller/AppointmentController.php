<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Service\Chars;
use App\Service\Medecins;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;

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
        $appointments = $this->getDoctrine()->getRepository(Appointment::class)->findBy(['patient' => $user],['date' => 'ASC','schedule'=>'ASC']);


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
        $appointments = $this->getDoctrine()->getRepository(Appointment::class)->findBy(['medecin' => $user],['date' => 'ASC','schedule'=>'ASC']);


        return $this->render('appointment/medecin.html.twig', [
            'monday'=>$monday,
            'appointments'=> $appointments,
        ]);
    }




    /**
     * @Route("/new", name="appointment_new", methods={"GET","POST"})
     */
    public function new(Request $request, \Swift_Mailer $mailer): Response
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

                $nameMedecin = $appointment->getMedecin()->getFirstname().' '. $appointment->getMedecin()->getLastname();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($appointment);
                $entityManager->flush();

                try{

                    
                $medecin = new Medecins();
                $char = new Chars();
                $info = $medecin->getInfosMedecin($char->removeAccent($nameMedecin));

                

                $message = (new \Swift_Message('Object'))
                ->setFrom('contact@docty.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        // templates/emails/example.html.twig
                        'emails/new-appointment.html.twig',[
                            'medecin'=>$nameMedecin,
                            'date'=>$appointment->getDate(),
                            'schedule'=>$appointment->getSchedule(),
                            'info'=>$info,
                        ]
                ),
                'text/html'
            );

            $mailer->send($message);

                }catch(Exception $e){

                }finally{

                    return $this->redirectToRoute('appointment_patient');
                }

    

            }

        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}/{url}", name="appointment_delete")
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

     /**
     * @Route("/pdf/{id}", name="cc")
     */
    public function pdf(Request $request, Appointment $appointment)
    {
        $nameMedecin = $appointment->getMedecin()->getFirstname().' '. $appointment->getMedecin()->getLastname();
        $medecin = new Medecins();
        $char = new Chars();
        $info = $medecin->getInfosMedecin($char->removeAccent($nameMedecin));

        $dompdf = new DOMPDF();

        $html = ob_get_clean();
        $html .= $html = $this->renderView('pdf/info-medecin.html.twig',[
            'medecin'=>$nameMedecin,
            'date'=>$appointment->getDate(),
            'schedule'=>$appointment->getSchedule(),
            'info'=>$info,
        ]);

        $dompdf->load_html($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        //Attachment => true Téléchargement
        //Attachment => false Vue dans le navigateur

        $dompdf->stream("test.pdf", ["Attachment" => false]);
    }
}
