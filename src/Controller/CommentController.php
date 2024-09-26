<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{


    ////////////////// Client functions  ///////////////
    #[Route('/User/{postId}', name: 'app_comment_index', methods: ['GET'])]
    public function indexClientComment(EntityManagerInterface $entityManager, int $postId): Response
    {
        $comments = $entityManager
            ->getRepository(Comment::class)
            ->findBy(['idPost' => $postId]);
    
        return $this->render('comment/ClientView/index.html.twig', [
            'comments' => $comments,
            'postId' => $postId, 
        ]);
    }
    
    #[Route('/new/{postId}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $postId): Response
    {
        $comment = new Comment();
        $post = $entityManager->getRepository(Post::class)->find($postId);
        $comment->setIdPost($post); // Set the post ID directly from the parameter
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_comment_index', ['postId' => $postId], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('comment/ClientView/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(), 
            'postId' => $postId, 
            // Pass the form view to the template
        ]);
    }
    

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
    ///////////////// admin functions ////////////////// 
    #[Route('/Admin/{postId}', name: 'admin_comment_index', methods: ['GET'])]
    public function indexAdminComment(EntityManagerInterface $entityManager, int $postId): Response
    {
        $comments = $entityManager
            ->getRepository(Comment::class)
            ->findBy(['idPost' => $postId]);
    
        return $this->render('comment/AdminView/comments.html.twig', [
            'comments' => $comments,
            'postId' => $postId, 
        ]);
    }
    #[Route('/Admin/Delete/{id}', name: 'admin_comment_delete', methods: ['POST'])]
    public function deleteAdmin(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

}
