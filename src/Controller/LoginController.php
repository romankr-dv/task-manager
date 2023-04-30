<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_task_index');
        }
        $loginForm = $this->createForm(LoginFormType::class);
        $loginForm->setData(['email' => $authenticationUtils->getLastUsername()]);

        return $this->render('security/login.html.twig', [
            'loginForm' => $loginForm->createView(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }
}
