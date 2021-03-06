<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PinType;


class PinsController extends AbstractController
{
    // PHP 8 = #[Route('/', name:'app_home', methods:['GET'])] !!!!
    
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }
 
    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_USER') && user.isVerified() == false")     
     */
    public function create(Request $request, EntityManagerInterface $em) : Response
    {  
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', [
            'monFormulaire' => $form->createView()
        ]);
    }



    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET", "PUT"})
     * @Security("is_granted('ROLE_USER') && user.isVerified() == false && pin.getUser() == user")
     * @Security("is_granted('PIN_MANAGE', pin)")
     */
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            $this->addFlash('success', 'Pin successfully updated!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'monFormulaire' => $form->createView()
        ]);
    }
    

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_USER') && user.isVerified() == false && pin.getUser() == user")     
     * @Security("is_granted('PIN_MANAGE', pin)")
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        if($this->isCsrfTokenValid('pin_deletion_' . $pin->getId(), $request->request->get('csrf_token'))){
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'Pin successfully deleted!');
        }

        return $this->redirectToRoute('app_home');

    }
}



