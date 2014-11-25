<?php namespace Larabook\Statuses\Ajax;

use Larabook\Statuses\StatusRepository;
use Laracasts\Commander\CommandHandler;

class GetStatusesCommandHandler implements CommandHandler {

    /**
     * @var
     */
    private $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * Handle the command.
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        return $this->statusRepository->getFeed($command->page)->toArray();
    }

}