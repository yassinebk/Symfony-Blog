<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleCreationType;
use App\Form\ArticleEditType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\FileUploadService;
use Doctrine\DBAL\Exception\ServerException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class ArticleController extends AbstractController
{


    #[Route('/posts', name: 'app_user_post')]
    public function userPost(UserRepository $userRepository,ArticleRepository $articleRepository):Response{
        $authUser= $this->getUser();
        $userEntity= $userRepository->findBy(['email'=>$authUser->getUserIdentifier()]);
        if($userEntity){
            $articles= $articleRepository->findBy(['owner'=>$userEntity]);
//            dd($articles);


        }
        return $this->render(
            'post/index.html.twig',
            ['articles'=>$articles]
        );
    }

    #[Route('/post/add', name: 'app_post_add')]
    public function addPost(Request $request,EntityManagerInterface $em,UserRepository $userRepository ,FileUploadService $fileUploadService): Response
    {
        $article = new Article();
        $form=$this->createForm(ArticleCreationType::class,$article);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){

            $this->handleUpload($form,$article,$fileUploadService);

            $user= $this->getUser();
            $currentUser= $userRepository->findBy(["email"=>$user->getUserIdentifier()])[0];

            $article->setOwner($currentUser);
            $article->setCreatedAt(date_create_immutable('now'));
            $article->setUpdatedAt(date_create('now'));

            $em->persist($article) ;
            $em->flush();
            $this->addFlash("success","article has been posted with success");
            return $this->redirectToRoute("app_user_post");
        }
        else if($form->isSubmitted()&&!$form->isValid()){
            $this->addFlash("error","Please make sure you filled the fields correctly");
        }
        return $this->render(
            'post/addPost.html.twig', [
            'createArticleForm'=>$form->createView()
        ]);
    }

    #[Route('/post/edit/{id}',name:'app_post_edit')]
        public function editPost ( Article $article=null,Request $request ,EntityManagerInterface $em,FileUploadService $fileUploadService): \Symfony\Component\HttpFoundation\RedirectResponse|Response
    {

        if(!isset($article)){
            $this->redirectToRoute("app_user_post");
        }
        else if($article->getOwner()->getEmail()==$this->getUser()->getUserIdentifier()) {
            $form=$this->createForm(ArticleCreationType::class,$article);
            $form->remove('createdAt');
            $form->remove('updatedAt');
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){
                $this->handleUpload($form,$article,$fileUploadService);
                $article->setUpdatedAt(date_create('now'));
                $em->persist($article);
                $em->flush();
                $this->addFlash('success','Article modified with success');
                return $this->redirectToRoute("app_user_post");
            }
            return $this->render(
                'post/addPost.html.twig',
             [   'createArticleForm'=>$form->createView()]
            );
        }
        return $this->redirectToRoute(
            'app_user_post',
        );
}

#[Route('/post/delete/{id}',name:'app_post_delete')]
    public function deletePost(Article $article=null,Request $request,EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
{
        if($article && $article->getOwner()->getEmail()==$this->getUser()->getUserIdentifier()) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article deleted with success');
        }
    else $this->addFlash('error', 'An error has occurred');

    return $this->redirectToRoute('app_user_post');
}

//    /**
//     * @throws ServerException
//     */
    function handleUpload(FormInterface $form,Article $article, FileUploadService $fileUploadService ){

    $photo = $form->get('image')->getData();
        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
    if ($photo) {
    $directory = $this->getParameter('articles_images');
    $article->setImage($fileUploadService->uploadFile($photo, $directory));
    return ;
    }


}
}
