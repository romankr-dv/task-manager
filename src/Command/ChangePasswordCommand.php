<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:change-password')]
class ChangePasswordCommand extends Command
{
    private const USER_ID_ARGUMENT = 'userId';
    private const PASSWORD_ARGUMENT = 'password';

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(self::USER_ID_ARGUMENT, InputArgument::REQUIRED);
        $this->addArgument(self::PASSWORD_ARGUMENT, InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int) $input->getArgument(self::USER_ID_ARGUMENT);
        $plainPassword = $input->getArgument(self::PASSWORD_ARGUMENT);

        $user = $this->userRepository->find($userId);
        if (null === $user) {
            $output->writeln("User not found!");
            return Command::FAILURE;
        }
        $password = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $this->entityManager->flush();
        $output->writeln("User '{$user->getEmail()}' password was changed!");
        return Command::SUCCESS;
    }
}
