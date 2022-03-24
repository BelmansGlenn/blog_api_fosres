<?php

namespace App\Controller;

use App\Entity\User;


use App\Model\Transformer\User\UserSimpleResponseDTOTransformer;
use App\Model\User\UserDTOCreate;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegisterController extends AbstractFOSRestController
{

    private $verifyEmailHelper;
    private $mailer;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }


    #[Post('/api/register', name: 'app_register')]
    #[View(statusCode: 201)]
    #[ParamConverter("userDTO", converter: "fos_rest.request_body")]
    public function index(UserDTOCreate $userDTO, EntityManagerInterface $entityManager, ConstraintViolationList $violations, UserPasswordHasherInterface $userPasswordHasher)
    {
        if (count($violations)){
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("\nField %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new BadRequestException($message);
        }
        $user = new User();

        $user->setEmail($userDTO->getEmail());
        $user->setFirstname($userDTO->getFirstname());
        $user->setLastname($userDTO->getLastname());
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
            $userDTO->getPlainPassword())
        );
        $entityManager->persist($user);
        $entityManager->flush();

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'registration_confirmation_route',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );


        $email = (new Email())
            ->from('api@gmail.com')
            ->to($user->getEmail())
            ->html(sprintf('<h1>Bonjour!</h1>

<p>
    Please verify your account by clicking on this link<br><br>
    <a href= "%s" >Confirm your account</a>.
    This link expires in 1h
</p>

<p>
    See you soon!
</p>', $signatureComponents->getSignedUrl())
);
        $this->mailer->send($email);

        return UserSimpleResponseDTOTransformer::transformFromObject($user);
    }


    #[Get("/verify", name:"registration_confirmation_route")]
    #[View(statusCode: 204)]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager)
    {
        $id = $request->get('id');

        if (null === $id)
        {
            throw new ParameterNotFoundException("id");
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (null === $user) {
            throw new NotFoundHttpException();
       }
        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
            $user->setIsVerified(true);
            $entityManager->flush();


        } catch (VerifyEmailExceptionInterface $e) {
            //$this->addFlash('verify_email_error', $e->getReason());
            throw new BadRequestException($e->getReason());

        }



    }

}
