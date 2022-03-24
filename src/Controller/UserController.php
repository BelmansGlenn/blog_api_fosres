<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\Transformer\Article\ArticleSimpleResponseDTOTransformer;
use App\Model\Transformer\User\UserSimpleResponseDTOTransformer;
use App\Model\User\UserDTOUpdate;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Services\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class UserController extends AbstractController
{

    #[Get('/api/myprofile', name: 'app_user', requirements: ['id ' => "\d+"])]
    #[View]
    public function getUserById(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {

        if (!$this->getUser()){
            throw new UnauthorizedHttpException('You have to be logged in to access your profile page.');
        }
        $user = $userRepository->find($this->getUser()->getId());

        return UserSimpleResponseDTOTransformer::transformFromObject($user);

    }

    #[Patch('/api/myprofile', name: 'app_user_update', requirements: ['id ' => "\d+"])]
    #[View(statusCode: 200)]
    #[ParamConverter("userDTOUpdate", converter: "fos_rest.request_body")]
    public function updateUserById($id, EntityManagerInterface $entityManager, UserDTOUpdate $userDTOUpdate, UserPasswordHasherInterface $userPasswordHasher, ConstraintViolationList $violations )
    {

        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new BadRequestException($message);
        }


        if (!$this->getUser()){
            throw new UnauthorizedHttpException('You have to be logged in to update your profile page.');
        }
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $userDTOUpdate->getPlainPassword()
            )
        );
        $entityManager->flush();

        return UserSimpleResponseDTOTransformer::transformFromObject($user);


    }


    #[Get(path: "/api/user/{id}/article", name: "app_article_user")]
    #[QueryParam(name: "keyword",requirements: "[a-zA-Z0-9]", nullable: true, description: "the keyword to search for")]
    #[QueryParam(name: "order",requirements: "asc|desc", default: "asc", description: "Sort order (asc or desc)")]
    #[QueryParam(name: "limit",requirements: "\d+", default: "10", description: "Max number of articles per page")]
    #[QueryParam(name: "offset",requirements: "\d+", default: "1", description: "the pagination offset")]
    #[View]
    public function getArticleByUserId(ArticleRepository $repository,$id, ParamFetcherInterface $paramFetcher, Request $request) {

        return Pagination::paginate($repository, $paramFetcher, $request, fn($it)=>ArticleSimpleResponseDTOTransformer::transformFromObject($it), ['author' => $id]);

    }

}
