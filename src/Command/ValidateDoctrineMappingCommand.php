<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'order:validate-mapping', description: 'Validate Doctrine mapping for OrderComponent')]
final class ValidateDoctrineMappingCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em){ parent::__construct(); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $validator = new SchemaValidator($this->em);
        $errors = $validator->validateMapping();
        if (!empty($errors)) {
            foreach ($errors as $class => $errs) {
                $output->writeln("<error>$class</error>");
                foreach ($errs as $err) $output->writeln(" - $err");
            }
            return Command::FAILURE;
        }
        $output->writeln('<info>Mapping OK</info>');
        return Command::SUCCESS;
    }
}
