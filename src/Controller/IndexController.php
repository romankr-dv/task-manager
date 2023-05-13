<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class IndexController extends AbstractController
{
    #[Route('/{reactRouting}',
        name: 'app_index',
        requirements: ['reactRouting' => '.+'],
        defaults: ['reactRouting' => null],
        methods: ['GET'],
        priority: -1
    )]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
