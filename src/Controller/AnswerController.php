<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Article;
use App\Model\Answer\AnswerDTOUpdate;
use App\Model\Transformer\Answer\AnswerSimpleResponseDTOTransformer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;

class AnswerController extends AbstractController
{
    #[Post('/api/article/{id}/answer', name: 'app_answer', requirements: ['id' => '\d+'])]
    #[View(statusCode: 201)]
    #[ParamConverter("answer", converter: "fos_rest.request_body")]
    public function postAnswerToArticle($id, EntityManagerInterface $entityManager, Answer $answer, ConstraintViolationList $violations)
    {
        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new BadRequestException($message);
        }

        $article = $entityManager->getRepository(Article::class)->find($id);
        if (!$article){
            throw new NotFoundHttpException();
        }
        $answer->setAuthor($this->getUser());
        $answer->setArticle($article);

        $entityManager->persist($answer);
        $entityManager->flush();

        return AnswerSimpleResponseDTOTransformer::transformFromObject($answer);

    }

    #[Patch(path:"/api/answer/{id}", name:"app_answer_update", requirements: ['id' => '\d+'])]
    #[View(statusCode: 200)]
    #[ParamConverter('answerDTOUpdate', converter: "fos_rest.request_body")]
    public function updateAnswerById(AnswerDTOUpdate $answerDTOUpdate, $id, ConstraintViolationList $violations, EntityManagerInterface $entityManager)
    {
        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new BadRequestException($message);
        }
        $answer = $entityManager->getRepository(Answer::class)->find($id);

        if (!$answer){
            throw new NotFoundHttpException();
        }

        $answer->setComment(
            $answerDTOUpdate->getComment()
        );
        $entityManager->flush();

        return AnswerSimpleResponseDTOTransformer::transformFromObject($answer);

    }

    #[Delete(path:"/api/answer/{id}", name:"app_answer_delete", requirements: ['id' => '\d+'])]
    #[View]
    public function deleteAnswerById( $id, AnswerRepository $answerRepository)
    {

        $answer = $answerRepository->find($id);

        if (!$answer){
            throw new NotFoundHttpException();
        }

        $answerRepository->remove($answer);

    }



}
