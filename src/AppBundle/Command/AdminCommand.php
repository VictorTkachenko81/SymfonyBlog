<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 07.02.16
 * Time: 13:20
 */

namespace AppBundle\Command;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('blog:addAdminUser')
            ->setDescription('Adding admin user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $this->getUserName($input, $output);
        $email = $this->getEmail($input, $output);
        $password = $this->getPassword($input, $output);

//        $output->writeln($role->getName()."(".$role->getId().")-".$username."-".$email."-".$password);

        $response = $this->save($username, $email, $password);
        $output->writeln("Username: ".$username." (id: ".$response.") was created.");
        $output->writeln("Please go to the admin panel and fill other information.");
    }

    protected function getUserName($input, $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the username (admin): ', 'admin');

        return $helper->ask($input, $output, $question);
    }

    protected function getPassword($input, $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter password: ');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The password can not be empty!');
            }

            return $value;
        });
        $question->setHidden(true);
        $question->setMaxAttempts(20);

        return $helper->ask($input, $output, $question);
    }

    protected function getEmail($input, $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter email: ');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The email can not be empty!');
            }

            return $value;
        });
        $question->setMaxAttempts(20);

        return $helper->ask($input, $output, $question);
    }

    protected function getRole()
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $role = $em->getRepository("AppBundle:Role")
            ->findOneByName('ROLE_ADMIN');
        if (count($role) == 0) {
            $role = new Role();
            $role->setName('ROLE_ADMIN');
            $em->persist($role);
            $em->flush();
        }

        return $role;
    }

    protected function save($username, $email, $password)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $user = new User();

        $encoder = $this->getContainer()->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);
        $user->setPassword($encoded);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->addRoleObject($this->getRole());

        $em->persist($user);
        $em->flush();

        return $user->getId();
    }
}