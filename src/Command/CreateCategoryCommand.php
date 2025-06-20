<?php

namespace App\Command;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create-category',
    description: 'Create a new category'
)]
class CreateCategoryCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the new category')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');

        // Check if a category with this name already exists
        $repository = $this->entityManager->getRepository(Category::class);
        $existing = $repository->findOneBy(['name' => $name]);

        if ($existing) {
            $io->error(sprintf('Category "%s" already exists!', $name));

            return Command::FAILURE;
        }

        $category = new Category();
        $category->setName($name);
        $category->setDescription('some description');

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $io->success(sprintf('Category "%s" successfully created!', $name));

        return Command::SUCCESS;
    }
}
