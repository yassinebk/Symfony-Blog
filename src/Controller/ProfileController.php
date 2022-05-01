<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\UserProfileCreationType;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserRepository $userRepository,UserProfileRepository $userProfileRepository): Response
    {
        $user = $userRepository->findBy(['email' => $this->getUser()->getUserIdentifier()])[0];
        $userProfile = $userProfileRepository->findBy(['user'=>$user])[0];
        if(empty($userProfile)){
            return $this->redirectToRoute('app_profile_set');
        }
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user'=>$user,
        ]);
    }

    #[Route("/profile/set", name: 'app_profile_set')]
    public function set_profile( Request $request, EntityManagerInterface $em,
                                 UserRepository $userRepository,
    UserProfileRepository $userProfileRepository)
                                 : Response
    {

        $userProfile = new UserProfile();
        $form = $this->createForm(UserProfileCreationType::class, $userProfile);
        $form->remove('user');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userEntity = $userRepository->findBy(['email' => $this->getUser()->getUserIdentifier()])[0];
            $profiles = $userProfileRepository->findBy(['user'=>$userEntity]);
            if (!empty($profiles)) {
                $formData= $form->getData();

                $oldUserProfile=$profiles[0];
                $oldUserProfile->setBirthDate($userProfile->getBirthDate());
                $oldUserProfile->setLastName($userProfile->getLastName());
                $oldUserProfile->setFirstName($userProfile->getFirstName());
                $em->persist($oldUserProfile);
            } else {
                $userProfile->setUser($userEntity);
                $em->persist($userProfile);
            }
            $em->flush();
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/setProfile.html.twig',[
            'setProfileForm'=>$form->createView()
        ]);
    }

}
