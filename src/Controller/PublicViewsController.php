<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentCreationType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\RoutingHelperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Twig\Profiler\Profile;

class PublicViewsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('public_views/index.html.twig', [
            'controller_name' => 'PublicViewsController',
            'articles' => $articles
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

    #[Route("/article/{id}", name: "app_article_view")]
    public function articleView(Article $article = null,UserRepository $userRepository, Request $request , EntityManagerInterface $em,CommentRepository $commentRepository): Response|RedirectResponse
    {

        if ($article) {
            $comment = new Comment();
            $form = $this->createForm(CommentCreationType::class, $comment);
            $form->remove('owner');
            $form->remove('article');
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setArticle($article);
                $owner=$userRepository->findOneBy(["email"=>$this->getUser()->getUserIdentifier()]);
                $comment->setOwner($owner);
                $comment->setCreatedAt(date_create_immutable('now'));
                $comment->setUpdatedAt(date_create_immutable('now'));
                $em->persist($comment);
                $em->flush();
            }
            $comments= $commentRepository->findBy(["article"=>$article]);
            return $this->render("public_views/article.html.twig", [
                "addCommentForm" => $form->createView(),
                "article" => $article,
                "comments"=>$comments
            ]);
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route("/comment/delete/{id}",name:"comment_delete")]
    public function deleteComment(Comment $comment=null,EntityManagerInterface $em,Request $request, UrlMatcherInterface $matcher,RoutingHelperService $routingHelperService){
        if($comment&&$this->getUser()->getUserIdentifier()===$comment->getOwner()->getUserIdentifier()){
            $em->remove($comment);
            $em->flush();
            $this->addFlash("success","Your comment was deleted successfully");
        }
        else {

            $this->addFlash("error","Error happened while performing the action");
        }

        $backUrl = $routingHelperService->getBackUrl($request, $matcher);
        if($backUrl){
        return $this->redirect($backUrl);
        }
        else {
            return $this->redirectToRoute("app_home");
        }
    }
    #[Route("/view-profile/id", name: "profile-view")]
    public function profileView(Profile $profile)
    {


    }
}
