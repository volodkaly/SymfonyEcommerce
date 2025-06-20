<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'create-admin';

    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $user->setEmail('admin@admin');
        $user->setFullName('admin');
        $user->setAddress('Some Street');
        $hashed = $this->passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($hashed);
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('admin created');

        return Command::SUCCESS;
    }
}
