<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/post')]
class PostController extends AbstractController
{


    //////////////////// Client functions //////////////////////
    #[Route('/User', name: 'Client_post_index', methods: ['GET'])]
    public function indexClient(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('post/ClientView/PostClient.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route('/User/{id}', name: 'user_post_show', methods: ['GET'])]
    public function showUser(Post $post): Response
    {
        return $this->render('post/ClientView/show.html.twig', [
            'post' => $post,
        ]);
    }

///////////////////////// Admin functions ////////////////
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $post = new Post();
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $filename = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
                $post->setImage($filename);
            }
    
            $entityManager->persist($post);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_post_index');
        }
    
        $errors = $validator->validate($post);
    
        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }


    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
{
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form['image']->getData();
        if ($imageFile) {
            $filename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('images_directory'),
                $filename
            );
            $post->setImage($filename);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

    $errors = $validator->validate($post);

    return $this->renderForm('post/edit.html.twig', [
        'post' => $post,
        'form' => $form,
        'errors' => $errors,
    ]);
}


    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
