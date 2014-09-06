<?php  namespace Larabook\Statuses; 
use Eloquent;
use Larabook\Statuses\Events\StatusWasPublished;
use Laracasts\Commander\Events\EventGenerator;

/**
 * Class Status
 * @package Larabook\Statuses
 */
class Status extends Eloquent {

    use EventGenerator;

    /**
     * Fill fields for a new status
     * @var array
     */
    protected $fillable=['body'];

    /**
     * A status belongs to a user
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('Larabook\Users\User');
    }

    /**
     * Publish a Status
     * @param $body
     * @return static
     */
    public static function publish($body)
    {
        $status = new static(compact('body'));

        $status->raise(new StatusWasPublished($body));

        return $status;
    }

} 