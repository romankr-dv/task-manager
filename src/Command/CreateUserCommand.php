<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    private const EMAIL_ARGUMENT = 'email';
    private const PASSWORD_ARGUMENT = 'password';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(self::EMAIL_ARGUMENT, InputArgument::REQUIRED);
        $this->addArgument(self::PASSWORD_ARGUMENT, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument(self::EMAIL_ARGUMENT);
        $plainPassword = $input->getArgument(self::PASSWORD_ARGUMENT);
        $user = new User();
        $user->setEmail($email);
        $password = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln("User '{$user->getEmail()}' was created!");
        return Command::SUCCESS;
    }
}
