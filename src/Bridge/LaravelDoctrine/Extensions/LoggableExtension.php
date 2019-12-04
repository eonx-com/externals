<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\LaravelDoctrine\Extensions;

use Closure;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use EoneoPay\Externals\Environment\Interfaces\EnvInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Subscribers\LoggableEventSubscriber;
use LaravelDoctrine\Extensions\GedmoExtension;

final class LoggableExtension extends GedmoExtension
{
    /**
     * @var \EoneoPay\Externals\Auth\Interfaces\AuthInterface
     */
    private $auth;

    /**
     * @var \EoneoPay\Externals\Environment\Interfaces\EnvInterface
     */
    private $env;

    /**
     * Create loggable extension.
     *
     * @param \EoneoPay\Externals\Auth\Interfaces\AuthInterface $auth
     * @param \EoneoPay\Externals\Environment\Interfaces\EnvInterface $env
     */
    public function __construct(AuthInterface $auth, EnvInterface $env)
    {
        $this->auth = $auth;
        $this->env = $env;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscribers(
        EventManager $manager,
        EntityManagerInterface $entityManager,
        ?Reader $reader = null
    ): void {
        $this->addSubscriber(new LoggableEventSubscriber($this->getUsernameResolver()), $manager, $reader);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * Get username resolver closure.
     *
     * @return \Closure
     */
    private function getUsernameResolver(): Closure
    {
        return function (): ?string {
            // Handle command context
            if ((bool)$this->env->get('APP_CONSOLE', false) === true) {
                return 'console';
            }

            // Avoid troubleshooting during unit tests
            if ($this->env->get('APP_ENV') === 'testing') {
                return 'testing';
            }

            $user = $this->auth->user();

            if ($user instanceof EntityInterface === false) {
                return null;
            }

            // Get user id from guard
            $userId = $user->getId();

            return \is_int($userId) ? (string)$userId : null;
        };
    }
}
