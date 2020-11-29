<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index(): Response
    {
        return $this->render('pins/index.html.twig');
    }

    /**
     * @Route("/pins", name="all_pins", methods="GET")
     */
    public function showAll(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findby([],['createdAt' => 'DESC']);
        return $this->render('pins/allPins.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="show_pin", methods="GET")
     */
    public function show(Pin $pin): Response
    {

        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/create", name="create_pin", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createFormBuilder($pin)
                ->add('title', TextType::class)
                ->add('description', TextareaType::class)
                ->getForm()
                ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid()) ) 
        {
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('all_pins');
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="edit_pin", methods="GET|POST")
     */
    public function edit(Pin $pin,Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createFormBuilder($pin)
                ->add('title', TextType::class)
                ->add('description', TextareaType::class)
                ->getForm()
                ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid()) ) 
        {
            $em->flush();

            return $this->redirectToRoute('all_pins');
        }
        
        
                
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

}
