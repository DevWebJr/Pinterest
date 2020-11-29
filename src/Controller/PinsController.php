<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('pins/index.html.twig');
    }

    /**
     * @Route("/pins", name="all_pins", methods={"GET"})
     */
    public function showAll(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findby([],['createdAt' => 'DESC']);
        return $this->render('pins/allPins.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="show_pin", methods={"GET"})
     */
    public function show(Pin $pin): Response
    {

        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/create", name="create_pin", methods={"GET|POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

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
     * @Route("/pins/{id<[0-9]+>}/edit", name="edit_pin", methods={"GET|PUT"})
     */
    public function edit(Pin $pin,Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(PinType::class, $pin, [
                'method' => 'PUT'
        ]);

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

    /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="delete_pin", methods={"DELETE"})
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('pin_deletion_'.$pin->getId(), $request->request->get('csrf_token')))
        {
            $em->remove($pin);
            $em->flush();
        }

        return $this->redirectToRoute('all_pins');
    }

}
