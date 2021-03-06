<?php namespace GSD\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Todo;

class CreateCommand extends CommandBase {

    protected $name = 'gsd:create';
    protected $description = 'Create new list.';
    protected $nameArgumentDescription = 'List name to create.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // Get options and arguments
        $name = $this->argument('+name');
        $title = $this->option('title');
        $subtitle = $this->option('subtitle');

        // Prompt for everything
        if (all_null($name, $title, $subtitle))
        {
            if ( ! ($name = $this->askForListId(false, true)))
            {
                $this->abort();
            }
            $title = $this->ask("Enter list title (enter to skip)?");
            $subtitle = $this->ask("Enter list subtitle (enter to skip)?");
        }

        // Validate arguments
        else if (is_null($name))
        {
            $this->abort('Must specify +name if title or subtitle used');
        }
        else if ($name[0] != '+')
        {
            $this->abort('The list name must begin with a plus (+)');
        }
        else
        {
            $name = substr($name, 1);
            if ($this->repository->exists($name))
            {
                $this->abort("The list '$name' already exists");
            }
        }

        // Create the list, defaulting title if needed
        $title = ($title) ? : ucfirst($name);
        $list = Todo::makeList($name, $title);

        // Set the subtitle if needed
        if ($subtitle)
        {
            $list->set('subtitle', $subtitle)->save();
        }

        $this->info("List '$name' successfully created");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('title', 't', InputOption::VALUE_REQUIRED,
                'Title of list.', null),
            array('subtitle', 's', InputOption::VALUE_REQUIRED,
                'Subtitle of list.', null),
        );
    }

}