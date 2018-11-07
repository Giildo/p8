<?php

namespace Tests\AppBundle\Helpers\Security;

use AppBundle\Entity\Interfaces\UserInterface;
use AppBundle\Entity\User;
use AppBundle\Helpers\Security\FormAuthenticator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FormAuthenticatorTest extends TestCase
{
    /**
     * @var FormAuthenticator
     */
    private $authenticator;

    /**
     * @var UserPasswordEncoderInterface|MockObject
     */
    private $passwordEncoder;

    /**
     * @var UserInterface
     */
    private $user;

    public function setUp()
    {
        $this->passwordEncoder = $this->createMock(UserPasswordEncoder::class);

        $this->authenticator = new FormAuthenticator($this->passwordEncoder);

        $this->user = new User(
            'JohnDoe',
            'ROLE_ADMIN',
            'john@doe.fr'
        );
    }

    public function testStartAuthentication()
    {

        self::assertInstanceOf(
            RedirectResponse::class,
            $this->authenticator->start(
                new Request(),
                $this->createMock(AuthenticationException::class)
            )
        );
    }

    public function testTheCredentialsReturn()
    {
        $request = new Request();
        $request->request->set('connection', [
            'username' => 'JohnDoe',
            'password' => '12345678',
        ]);

        self::assertEquals(
            'JohnDoe',
            $this->authenticator->getCredentials($request)['username']
        );

        self::assertEquals(
            '12345678',
            $this->authenticator->getCredentials($request)['password']
        );
    }

    public function testNullReturnIfCredentialsIsNull()
    {
        self::assertNull($this->authenticator->getUser(
            ['username' => null],
            $this->createMock(UserProviderInterface::class)
        ));
    }

    public function testExceptionIsThrownIfCredentialsIsNotNullAndUsernameIsWrong()
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);

        $provider = $this->createMock(UserProviderInterface::class);
        $provider->method('loadUserByUsername')
                 ->willThrowException(new UsernameNotFoundException());

        $this->authenticator->getUser(
            ['username' => 'JohnDoe'],
            $provider
        );
    }

    public function testUserReturnedIfCredentialsIsNotNullAndUsernameIsCorrect()
    {
        $provider = $this->createMock(UserProviderInterface::class);
        $provider->method('loadUserByUsername')
                 ->willReturn($this->user);

        self::assertInstanceOf(
            UserInterface::class,
            $this->authenticator->getUser(
                ['username' => 'JohnDoe'],
                $provider
            )
        );
    }

    public function testExceptionThrownIfPasswordIsWrong()
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);

        $this->passwordEncoder->method('isPasswordValid')
                              ->willReturn(false);

        $this->authenticator->checkCredentials(
            ['password' => '12345678'],
            $this->user
        );
    }

    public function testReturnTrueIfPasswordIsCorrect()
    {
        $this->passwordEncoder->method('isPasswordValid')
                              ->willReturn(true);

        self::assertTrue(
            $this->authenticator->checkCredentials(
                ['password' => '12345678'],
                $this->user
            )
        );
    }

    public function testNoCreationOfErrorsInRequestIfTheAuthenticationIsFailure()
    {
        $request = new Request();
        $request->attributes = new ParameterBag();

        $exception = new AuthenticationException(
            'Erreur personnalisée',
            1
        );

        self::assertNull($this->authenticator->onAuthenticationFailure($request, $exception));
        self::assertNull($request->attributes->get(Security::AUTHENTICATION_ERROR));
    }

    public function testTheCreationOfErrorsInRequestIfTheAuthenticationIsFailure()
    {
        $request = new Request();
        $request->attributes = new ParameterBag();

        $exception = new AuthenticationException(
            'Erreur personnalisée',
            100
        );

        self::assertNull($this->authenticator->onAuthenticationFailure($request, $exception));
        self::assertEquals(
            'Erreur personnalisée',
            $request->attributes->get(Security::AUTHENTICATION_ERROR)->getMessage()
        );
    }

    public function testTheRedirectResponseOnTheAuthenticationIsASuccess()
    {
        self::assertInstanceOf(
            RedirectResponse::class,
            $this->authenticator->onAuthenticationSuccess(
                new Request(),
                $this->createMock(TokenInterface::class),
                'providerKey'
            )
        );
    }

    public function testTheFalseReturnToRememberMe()
    {
        self::assertFalse($this->authenticator->supportsRememberMe());
    }
}
