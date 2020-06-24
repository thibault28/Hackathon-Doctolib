<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\Chars;
use App\Service\Medecins;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $entityManager = $this->getDoctrine()->getManager();
            

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
        

            if($user->getStatut()->getName() == "medecin"){

                $medecin = new Medecins();
                $char = new Chars();
                $name = $user->getFirstname().' '.$user->getLastname();
                $name = $char->removeAccent($name);
                var_dump(strtoupper($name));

                $isMedecin = $medecin->getMedecins(strtoupper($name));

                

                if($isMedecin){

                    $user->setRoles(['ROLE_MEDECIN']);

                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success', 'You are registered now log in');
                    return $this->redirectToRoute('homepage');


                }else{
                $this->addFlash('danger', 'We haven\'t found you');
                }

            }else{
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'You are registered now log in');
                return $this->redirectToRoute('homepage');
            }

            
            


            // do anything else you need here, like send an email

            
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
