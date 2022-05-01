<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Profiler\Profile;

class PublicViewsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('public_views/index.html.twig', [
            'controller_name' => 'PublicViewsController',
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('public_views/index.html.twig', [
            'controller_name' => 'PublicViewsController',
        ]);
    }
    #[Route('/contact', name: 'app_contact_us')]
    public function contact(): Response
    {
        return $this->render('public_views/index.html.twig', [
            'controller_name' => 'PublicViewsController',
        ]);
    }
        #[Route("/article/id",name:"app_article_view")]
        public function articleView(Article $article=null): Response|RedirectResponse {
            if($article) {
                return $this->render("public_views/articles.html.twig",[
                    "article"=>$article
                ]);
            }

                return $this->redirectToRoute('app_home');
    }

    #[Route("/view-profile/id",name:"profile-view")]
    public function profileView(Profile $profile){


    }
}
