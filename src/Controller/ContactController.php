<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $requestC,MailerInterface $mailer,EntityManagerInterface $manager): Response
    {
        $contact = new Contact();
        $contactForm = $this->createForm(ContactFormType::class,$contact);
        $contactForm->handleRequest($requestC);
        if ($contactForm->isSubmitted() ) {

            $contactFormData = $contactForm->getData();
            $email = (new TemplatedEmail())
            ->from(new Address('contact@my_carosse.com', 'City Center'))
            ->to($contact->getEmail())
            ->htmlTemplate('contact/email.html.twig')
            ->context([
                'contact' => $contact,
            ])
        ;

        $mailer->send($email);

            $manager->persist($contact);
            $manager->flush();

            $this->addFlash('success', 'Votre message a été envoyé avec succés');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $contactForm->createView()
        ]);
    }
}
