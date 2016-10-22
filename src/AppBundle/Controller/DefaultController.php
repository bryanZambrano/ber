<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
         $objUser =  $this->get('security.token_storage')->getToken()->getUser();
         
           if($objUser!=null)
         {

           $id=$objUser->getId();

         
         $em = $this->getDoctrine()->getManager();
         
         //$listeContact = $em->getRepository('AppBundle:Contact')->find($id);
         //$listeContact  = $this->getDoctrine()->getManager()->getRepository('AppBundle:Contact')->find($id) ;
    
          $listeContact  = $this->getDoctrine()->getManager()->getRepository('AppBundle:Contact')->findBy([
               'User'=> $objUser,
            ]) ;

          return $this->render('default/index.html.twig', [ 
            'listeContact' => $listeContact, 
            ]);
         }

         


        // replace this example code with whatever you need
       //return $this->render('default/index.html.twig', [
            //'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
           // 'listeContact' => $listeContact,
       // ]);
         
        return $this->render('default/index.html.twig', [ 
            'listeContact' => $listeContact, 
            ]);
    }

     /**
     * @Route("/Add/{id}", name="Add")
     */
    public function AddAction(Request $request , $id)
    {  
         $Contact = new Contact() ;
          
        $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:User'); 

        $user = $em->find($id) ;

           
         //$user = new User() ;
          
         
        // $em = $this->getDoctrine()->getManager();

         //$user = $em->find($id) ;
         $Contact->setUser($user) ;
        
         $form = $this->createFormBuilder($Contact)
       
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('numeroTel' , TextType::class)
        //->add('User' , HiddenType::class)
        ->add('save', SubmitType::class, array('label' => 'Creer Contact'))
        
        ->getForm();


         $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
         
         $newContact = $form->getData();
         
         $em = $this->getDoctrine()->getManager();
         $em->persist($newContact);
         $em->flush();

        return $this->redirectToRoute('homepage');
    }

        


        return $this->render('default/Add.html.twig' , array(
        'form' => $form->createView(),
        ));


    }
     
     /**
     * @Route("/Delete/{id}", name="Delete")
     */
    public function DeleteAction($id)
    {    
        $em = $this->getDoctrine()->getManager();
        $contact  = $em->getRepository('AppBundle:Contact')->find($id);
        
        $em->remove($contact) ;

        $em->flush() ;

        return $this->redirectToRoute('homepage');
    }


     /**
     * @Route("/Edit/{id}", name="Edit")
     */
    public function EditAction(Request $request , $id)
    {    
            $em = $this->getDoctrine()->getManager();

            $contact  = $em->getRepository('AppBundle:Contact')->find($id);
               
            $form = $this->createFormBuilder($contact)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('numeroTel' , TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Modifier contact'))
            ->getForm();

             
             $form->handleRequest($request);
             if ($form->isSubmitted() && $form->isValid()) 
             {      
                   $newContact = $form->getData();
                   $em = $this->getDoctrine()->getManager();
                   $contact  = $em->getRepository('AppBundle:Contact')->find($id);
                   
                    $contact->setNom($newContact->getNom()) ;
                     $contact->setPrenom($newContact->getPrenom()) ;
                     $contact->setNumeroTel($newContact->getNumeroTel()) ;
                   
                     $em->flush();

                 return $this->redirectToRoute('homepage');
             }

            
             return $this->render('default/Edit.html.twig' , array(
             'form' => $form->createView(),
             ));
    }


}
